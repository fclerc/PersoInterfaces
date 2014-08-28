<?php session_start(); 
    require_once 'phphelpers/langFinder.php';
?>
<!DOCTYPE HTML>
<!-- This file enables the user to modify the content of the resources file : adding resources and editing their parameters.  -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
    <?php 
    //I store these variables here to call them later...could be easier to use directly $_POST['path'],... in the rest of the script but idiot php tells me these indexes no longer exist in the array (well in fact sometimes there is no problem, but some other times, in EXACTLY the same conditions, it tells me they don't exist (even if it is possible to echo and display these array values in the html page, it doesn't let me use it in the other parts of the script)...
    if(!isset($_POST['path'])){//values are in the session variable
        $path = $_SESSION['path'];
        $profilepath = $_SESSION['profilepath'];
        $sequenceContextpath = $_SESSION['sequenceContextpath'];
        $liveContextpath = $_SESSION['liveContextpath'];
        $file = $_SESSION['file'];
        $profilefile = $_SESSION['profilefile'];
        $sequenceContextfile = $_SESSION['sequenceContextfile'];
        $liveContextfile = $_SESSION['liveContextfile'];
        $scales = $_SESSION['scales'];
        $section = $_SESSION['section'];
    }
    else{
        $path = $_POST['path'];
        $profilepath = $_POST['profilepath'];
        $sequenceContextpath = $_POST['sequenceContextpath'];
        $liveContextpath = $_POST['liveContextpath'];
        $file = $_POST['file'];
        $profilefile = $_POST['profilefile'];
        $sequenceContextfile = $_POST['sequenceContextfile'];
        $liveContextfile = $_POST['liveContextfile'];
        $scales = $_POST['scales'];
        $section = $_POST['section'];
    }
    ?>
		<div class="container">
			<h1><span class="toTranslate">strategyTest.h1</span><small><span id="currentFile">strategyTest.currentFileIntro</span><span id="currentFileName"><?php echo $file; ?></span></small></h1>
            <p><span class="toTranslate">You are using files: </span></br/>
                <?php
                    echo '<b><span class="toTranslate">Profile</span></b>: '.$profilefile.'<br/>';
                    echo '<b><span class="toTranslate">Live Context</span></b>: '.$liveContextfile.'<br/>';
                    echo '<b><span class="toTranslate">Sequence Context</span></b>: '.$sequenceContextfile.'<br/>';
                ?>
            </p>
            
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<p id="generalInstructions">strategyTest.instructions</p>
			
			<div id="boussole">
			
			<?php
				$strategyPath = $path.$file;
				$profilePath = $profilepath.$profilefile;
				$sequenceContextPath = $sequenceContextpath.$sequenceContextfile;
				$liveContextPath = $liveContextpath.$liveContextfile;
				$generator = new ActivitiesGenerator($strategyPath);
				$generator->generate($profilePath, $sequenceContextPath, $liveContextPath);
				
				class ActivitiesGenerator{
					private $strategy;
					private $xpathStrategy;
					private $pedaProp;
					private $resources;
					private $xpathResources;
					
					
					public function __construct($strategyPath){
						//loading strategy file
						$this->strategy = new DOMDocument();
						$this->strategy->load($strategyPath);
						$this->xpathStrategy = new DOMXPath($this->strategy);
						
						//pedagogical properties
						$pedagogicalPropertiesFile = $this->strategy->getElementsByTagName('pedagogicalProperties')->item(0)->nodeValue;
						$this->pedaProp = new DOMDocument();
						$this->pedaProp->load($pedagogicalPropertiesFile);
						
						$this->resources = new DOMDocument();
						$this->resources->load('data/resources/foveaResources.xml');
						$this->xpathResources = new DOMXPath($this->resources);
					}
				
					public function generate($profilePath, $sequenceContextPath, $liveContextPath){
						//loading other files used for test
						//TODO : replace it with arguments
						//$exploitedProfileFile = $this->strategy->getElementsByTagName('exploitedProfile')->item(0)->nodeValue;
						//$exploitedContextFile = $this->strategy->getElementsByTagName('exploitedContext')->item(0)->nodeValue;
						$profile = new DOMDocument();
						$profile->load($profilePath);
						$liveContext = new DOMDocument();
						$liveContext->load($liveContextPath);
						$seqContext = new DOMDocument();
						$seqContext->load($sequenceContextPath);
						//TODO : us it as argument (or not ?)
						$profileScales = json_decode(file_get_contents('data/infos/profileScales.json'));
						$contextScales = json_decode(file_get_contents('data/infos/contextScales.json'));
						
						
						
						$checker = new ConditionChecker($profile, $liveContext, $profileScales, $contextScales);
						
						$rules = $this->strategy->getElementsByTagName('rule');
						
						//contains arrays of activities (each array corresponding to the set of activities contained in a consequence to apply to the learner)
						$consequences = array();
						//contains the final list of activities, in the right order, that have to be realized by learner
						$activities = array();
						
						$activitiesContext = $seqContext->getElementsByTagName('activitiesContext')->item(0)->nodeValue;
						$csqMgr = new ConsequenceGenerator($this->pedaProp, $this->xpathStrategy, $this->xpathResources, $activitiesContext);
						
						foreach ($rules as $rule){	
							$priority = $this->getPriority($rule);
						
							$verified = true; //True if condition is verified. If 'if' part is void, evaluate at true by default
							if($rule->getElementsByTagName('if')->length > 0){
								$ifElement = $rule->getElementsByTagName('if')->item(0);
								$condition = $ifElement->childNodes->item(0);
								$verified = $checker->checkCondition($condition);
							}
							$consequence = '';
							if($verified && $rule->getElementsByTagName('then')->length > 0){
								$consequence = $rule->getElementsByTagName('then')->item(0);
							}
							else if($rule->getElementsByTagName('else')->length > 0){
								$consequence = $rule->getElementsByTagName('else')->item(0);
							}
							
							
							//$csqActivities = $csqMgr->generate($consequence);
							
							$consequences[] = array('activities' => $csqMgr->generate($consequence), 'priority' => $priority);
							/* foreach($csqActivities as $act){
								$activities[] = array('activity' => $act, 'priority' => $priority);
							} */
						}
						
							//sorting consequences by priority
							usort($consequences, function($a, $b){
								return $a['priority'] <= $b['priority'];
							});
							
							foreach($consequences as $consequence){
								$csqAct = $consequence['activities'];
								foreach($csqAct as $activity){
									$activities[] = $activity;
								}
							}
						$this->displayActivities($activities, $seqContext);
					
					}
					
					private function displayActivities($activities, $seqContext){
						echo 'Bonjour. Nous vous proposons de réaliser les activités suivantes : <br/><ul>';
						$maxAct = intval($seqContext->getElementsByTagName('numberOfActivities')->item(0)->getElementsByTagName('max')->item(0)->nodeValue);
						$minAct = intval($seqContext->getElementsByTagName('numberOfActivities')->item(0)->getElementsByTagName('min')->item(0)->nodeValue);
						$maxTime = intval($seqContext->getElementsByTagName('activitiesDuration')->item(0)->getElementsByTagName('max')->item(0)->nodeValue);
						$minTime = intval($seqContext->getElementsByTagName('activitiesDuration')->item(0)->getElementsByTagName('min')->item(0)->nodeValue);
						
						//total values for currently displayed rules
						$nbAct = 0;
						$time = 0;
						
						foreach($activities as $activity){
							if($nbAct < $maxAct){
								$activityTime = $activity['length'];
								if($time + $activityTime <= $maxTime){
									echo '<li>'.$activity['text'].'</li>';
									//incrementing nbAct, except if 'countActivity' is set to false
									if(isset($activity['countActivity'])){
										if($activity['countActivity'] == true){
											$nbAct++;
										}
									}
									else{
										$nbAct++;
									}
									$time += $activity['length'];
								}
							}
						}
						
						echo '</ul>';
						
						echo '<p>Vous avez ainsi '.$nbAct.' activités à réaliser, et cela devrait durer environ '.round($time, -1).' minutes</p>';
					}
					
					
					private function getPriority($rule){
						$priority = 0;
						//getting the priority
						if($rule->getElementsByTagName('priority')->length > 0){
							$priority = $rule->getElementsByTagName('priority')->item(0)->nodeValue;
						}
						return intval($priority);
					}
				}
				
				
				
			
				
				/*
				Class used to check conditions
				Constructor arguments : profile, context and scales.
				Then use with bool checkCondition($condition)
				
				*/
				class ConditionChecker{
					private $profile;
					private $xpathProfile;
					private $liveContext;
                    private $xpathLiveContext;
					private $profileScales;
					private $contextScales;
					
					public function __construct($profile, $liveContext, $profileScales, $contextScales){
						$this->profile = $profile;
						$this->xpathProfile = new DOMXPath($this->profile);
						$this->liveContext = $liveContext;
                        $this->xpathLiveContext = new DOMXPath($this->liveContext);
						$this->profileScales = $profileScales;
						$this->contextScales = $contextScales;
					
					}
					
					public function checkCondition($condition){
						
						//simple constraint
						if(strToLower($condition->tagName) == 'constraint'){
							return $this->checkConstraint($condition);
						}
						else if(strToLower($condition->tagName) == 'and'){
							$children = $condition->childNodes;
							return ($this->checkCondition($children->item(0)) && $this->checkCondition($children->item(1)));
						}
						else if(strToLower($condition->tagName) == 'or'){
							$children = $condition->childNodes;
							return ($this->checkCondition($children->item(0)) || $this->checkCondition($children->item(1)));
						}
						
					}
					
					//argument is a constraint element, returns boolean
					private function checkConstraint($constraint){
						$indicatorId = $constraint->getElementsByTagName('indicator')->item(0)->nodeValue;
						$indicator = $this->xpathProfile->query("//*[@id='$indicatorId']")->item(0);
                        
                        if($indicator === null){
                            $indicator = $this->xpathLiveContext->query("//*[@id='$indicatorId']")->item(0);
                        }
                        
						$indicatorValue = $indicator->nodeValue;
						$indicatorName = $indicator->tagName;
						$referenceValue = $this->getReferenceValue($constraint);
						
						//possible conversions : find the type of the indicator in docs
						$indicatorType = $this->getIndicatorType($indicatorName);
						
						if($indicatorType == 'xs:float'){
							$referenceValue = floatval($referenceValue);
							$indicatorValue = floatval($indicatorValue);
						}
						elseif($indicatorType == 'xs:integer'){
							$referenceValue = intval($referenceValue);
							$indicatorValue = intval($indicatorValue);
						}
						
						
						
						//doing the comparison, according to the operator
						$operator  = $constraint->getElementsByTagName('operator')->item(0)->nodeValue;
						if($operator == '='){
							return ($referenceValue == $indicatorValue);
						}
						elseif($operator == '!='){
							return $referenceValue != $indicatorValue;
						}
						else if($operator == '>'){//todo join with next
							return $indicatorValue > $referenceValue ;
						}
						else if($operator == '<'){//todo
							return $indicatorValue < $referenceValue ;
						}
					}
					
					//returns reference value of the constraint
					private function getReferenceValue($constraint){
						$referenceValue = '';
						if($constraint->getElementsByTagName('referenceValue')->item(0) != null){
							$referenceValue = $constraint->getElementsByTagName('referenceValue')->item(0)->nodeValue;
						}
						else{//because of jQUery, case is not always respected
							$referenceValue = $constraint->getElementsByTagName('referencevalue')->item(0)->nodeValue;
						}
						
						return $referenceValue;
					}
					
					//returns type of the indicator (finds it in scales of profile and contexts)
					private function getIndicatorType($indicatorName){
						$indicatorType = '';
						if(isset($this->profileScales->$indicatorName)){
							if(isset($this->profileScales->$indicatorName->typeName)){
								$indicatorType = $this->profileScales->$indicatorName->typeName;
							}
							else if(isset($this->profileScales->$indicatorName->baseTypeName)){
								$indicatorType = $this->profileScales->$indicatorName->baseTypeName;
							}
						}
                        else if(isset($this->contextScales->$indicatorName)){
							if(isset($this->contextScales->$indicatorName->typeName)){
								$indicatorType = $this->contextScales->$indicatorName->typeName;
							}
							else if(isset($this->contextScales->$indicatorName->baseTypeName)){
								$indicatorType = $this->contextScales->$indicatorName->baseTypeName;
							}
						}
						return $indicatorType;
					}
				
				}
				
				//argument is <then> or <else> part of the rule
				//returns array containing the activities to realize (what has to be displayed to the learner).
				class ConsequenceGenerator{
					private $pedaProp;
					private $xpathPedaProp;
					private $xpathStrategy;
					private $paramDictionnary; //contains elements in the form  "name" => "P001"
					private $xpathResources;
					private $activitiesContext;//the context (tag, resource...) in which resources have to be taken from.
					
					public function __construct($pedaProp, $xpathStrategy, $xpathResources, $activitiesContext){
						$this->pedaProp = $pedaProp;
						$this->xpathPedaProp = new DOMXPath($this->pedaProp);
						$this->xpathStrategy = $xpathStrategy;
						$this->paramDictionnary = $this->getParamDictionnary();
						$this->xpathResources = $xpathResources;
						$this->activitiesContext = $activitiesContext;
					}
					
					
					public function generate($consequence){
						$generatedActivities = array();
						if($consequence != ''){
							if($consequence->getElementsByTagName('activity')->length > 0){
								$activities = $consequence->getElementsByTagName('activity');
								foreach($activities as $activity){
									$computedActivities = $this->treatActivity($activity);
									foreach($computedActivities as $acti){
										$generatedActivities[] = $acti;
									}
								}
							}
						}
						return($generatedActivities);
					
					}
					
					
					
					
					
					
					//takes activity as an argument, returns information about what has to be done by learner
					private function treatActivity($activity){
						$activityName = $this->getActivityName($activity);
						//if defined, get the given length
						$length = 0;
						$lengthParam = $this->getParameterByName('Length', $activity);
						if($lengthParam){
							$length = intval($lengthParam->getElementsByTagName('value')->item(0)->nodeValue);
						}
						
						if($activityName == 'Learning' || $activityName == 'Exercise'){
							//the params for which a simple filter is realized. We create an array associating each of the defined parameters to the value given by the teacher
							$filters = array();
							$simpleParams = array('Length', 'Status', 'Difficulty', 'Type', 'Sequence', 'Categories', 'Grade');
                            
							foreach($simpleParams as $param){
								$paramValue = $this->getParameterValueByName($param, $activity);
								if($paramValue !== null){
									$filters[strToLower($param)] =  $paramValue;
								}
							
							}
							
							$nameParam = $this->getParameterByName('Name', $activity);
							if($nameParam){
								$resourceName = $nameParam->getElementsByTagName('value')->item(0)->nodeValue;
								//finding the corresponding resource in the tree
                                $resourceQuery = "//*[local-name()='resource' and ./*[local-name()='name' and .='".$resourceName."']]";
								$resource = $this->xpathResources->query($resourceQuery)->item(0);
								
								return $this->treatLearningActivity($resource, $filters, $activityName);
								
							}
							
						}
                        
						
						else if($activityName == 'Social'){
							$text = 'Allez sur ';
							
							//TODO : enable url as param (or defined somewhere else), to enable learner to lead them directly on the course's page on the social networks
							$toolsText = array(
								'Twitter' => '<a target="_blank" href="http://twitter.com">le réseau social Twitter</a> ',
								'Forum' => 'le forum du cours ',
								'Facebook' => '<a target="_blank" href="http://facebook.com">le réseau social Facebook</a> ',
								'Google+' => '<a target="_blank" href="http://plus.google.com">le réseau social Google+</a> ',
							);
							$toolParam = $this->getParameterByName('Tool', $activity);
							if($toolParam){
								$toolName = $toolParam->getElementsByTagName('value')->item(0)->nodeValue;
								$text = $text . $toolsText[$toolName];
							}
							
							
							$actionsText = array(
								'Answer' => ', et portez secours aux autres apprenants qui y posent des questions ',
								'Read' => ', et lisez les messages qui pourraient vous intéresser ',
								'Create' => ', et posez vos questions ou présentez vos réflexions aux autres participants ',
							);
							
							$actionParam = $this->getParameterByName('Action', $activity);
							if($actionParam){
								$actionName = $actionParam->getElementsByTagName('value')->item(0)->nodeValue;
								$text = $text . $actionsText[$actionName];
							}
							if($length){
								$text = $text . '(passez-y environ '.$length.' minutes).';
							}
							
							return array(array('text' => $text, 'length' => $length));
						
						}
						
						else if($activityName == 'Message'){
							$goalsText = array(
								'Encouraging' => 'Continuez vos efforts, bientôt vous serez un as de la colonne vertébrale !',
								'Greeting' => 'Bonjour, ravi de vous retrouver aujourd\'hui pour étudier cette belle matière qu\'est l\'anatomie !',
							);
							
							$goalParam = $this->getParameterByName('Goal', $activity);
							$text = '';
							if($goalParam){
								$goalName = $goalParam->getElementsByTagName('value')->item(0)->nodeValue;
								$text = $text . $goalsText[$goalName];
							}
							
							//countActivity = false : this is not considered as an activity (and is not considered when doing the sum of activities the learner has to realize)
							return array(array('text' => $text, 'length' => $length, 'countActivity' => false));
						}
						
					}
					
					//argument : resource, issued from resourcesDefinition
					//returns array of array describing activities (but doesn't consider exercises)
					//TODO : use it for each resource identified as having to be done
					//realizes simple filters that are done directly on eahc resource (status, type, difficulty, sequence)
					private function treatLearningActivity($resource, $filters, $activityName){
						$activities = array();
						$URI = $resource->getAttribute('URI');
						
						$name = $this->getResourceProperty($resource, 'name');
						$type = $this->getResourceProperty($resource, 'type');
						$length = intval($this->getResourceProperty($resource, 'length'));
						
						//true iff resource filters applied up now
						$valid = true;
						$valid = $this->checkFilter('status', $resource, $filters) && $this->checkFilter('difficulty', $resource, $filters) && $this->checkFilter('sequence', $resource, $filters) && $this->checkFilter('categories', $resource, $filters);
						
						if($valid){//eg status, type, difficulty, sequence are good
							//group of resource : apply same function to its children, and display iff non void result (at least one activity returned)
							if($type == 'group'){
								
                                //going recursively through children, and getting the activities
								foreach($resource->childNodes as $child){
									if(isset($child->tagName)){
										if($child->tagName == 'resource'){
											$result = $this->treatLearningActivity($child, $filters, $activityName);
											foreach($result as $r){
												$activities[] = $r;
											}
										}
									}
								}
								if(count($activities) > 0){//if at least one child resource has been added : add the current resource also
									array_unshift($activities, array('text' => 'Consultez les ressources de la section <a target="_blank" href="'.$URI.'">'.$name.'</a> (elles sont listées ci-dessous)', 'length' => 0, 'countActivity' => false));
								}
							}
							
							else if($activityName == 'Learning' && $type != 'quiz' && $type != 'assignment'){
								$valid = $this->checkFilter('type', $resource, $filters);
								if($valid){
									$activities[] = array('text' => 'Consultez <a target="_blank" href="'.$URI.'">'.$name.'</a>', 'length' => $length);
								}
							}
                            else if($activityName == 'Exercise' && ($type == 'quiz' || $type == 'assignment')){
                                $valid = $this->checkFilter('grade', $resource, $filters);
                                //graded exercise
                                if($valid){
                                    $activities[] = array('text' => 'Faites le devoir noté <a target="_blank" href="'.$URI.'">'.$name.'</a>', 'length' => $length);
                                }
                            }
						}
						
						
						return $activities;
					
					}
					
					//false iff the resource verifies the value in the filter for the given parameter name
					private function checkFilter($name, $resource, $filters){
						$resourceValues = $this->getResourceProperty($resource, $name);
						if(isset($filters[$name]) && $resourceValues !== null){
							$resourceValues = explode(' ' , $resourceValues);
							$filterValues = explode(' ' , $filters[$name]);
							
							foreach($resourceValues as $resourceValue){
								if($resourceValue != ''){
									foreach($filterValues as $filterValue){
										if($filterValue == $resourceValue){
											return true;
										}
									}
								}
							}
							return false;
						}
						else{
							return true;
						}
					}
					
					
					//Arguments : a resource, and the name of a property
					//returns the value of the property (or 0 if nothing can be found)
					private function getResourceProperty($resource, $propertyName){
						$prop = null;
						$query = "./*[local-name()='".$propertyName."']";
						$element = $this->xpathResources->query($query, $resource, false)->item(0);
						if($element){
							$prop = $element->nodeValue;
						}
						return $prop;
					}
					
					
					
					//arguments are the name of a parameter and an activity (coming from a rule)
					//returns the corresponding parameter, if contained by the activity (otherwise returns null)
					private function getParameterByName($name, $activity){
						if(isset($this->paramDictionnary[$name])){
							$paramId = $this->paramDictionnary[$name];
							$query = ".//*[local-name()='parameter' and .//*[local-name()='id' and .='$paramId']]";
							$param = $this->xpathStrategy->query($query, $activity, false);
							return $param->item(0);
						}
						else{
							return null;
						}
					}
					
					private function getParameterValueByName($name, $activity){
						$param = $this->getParameterByName($name, $activity);
						if($param){
							$value = $param->getElementsByTagName('value')->item(0)->nodeValue;
							return $value;
						}
						else{
							return null;
						}
					}
					
					//gets an activity, and returns the name of this activity
					private function getActivityName($activity){
						$activityId = $this->getActivityId($activity);
						$query = "//TypeOfActivity[@ID='$activityId']";
						$activityDef = $this->xpathPedaProp->query($query)->item(0);
						
						$activityName = $activityDef->getElementsByTagName('Name')->item(0)->nodeValue;
						return $activityName;
					}
				
					private function getActivityId($activity){
						$activityId = '';
						if($activity->getElementsByTagName('typeofactivity')->item(0) != null){
							$activityId = $activity->getElementsByTagName('typeofactivity')->item(0)->nodeValue;
						}
						else{//because of jQUery, case is not always respected
							$activityId = $activity->getElementsByTagName('typeOfActivity')->item(0)->nodeValue;
						}
						return $activityId;
					}
					
					private function getParamDictionnary(){
						$dict = array();
						$params = $this->pedaProp->getElementsByTagName('Parameter');
						foreach($params as $param){
							if(!isset($dict[$param->getElementsByTagName('Name')->item(0)->nodeValue])){
								$dict[$param->getElementsByTagName('Name')->item(0)->nodeValue] = $param->getAttribute('ID');
							}
						}
						return $dict;
					}
				}
				
			?>
			
			</div>
			
			
			
        </div>
		
		
       
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
       
        <script type="text/javascript" src="translation/translate.js"></script>
        <script type="text/javascript" src="translation/icu.js"></script>
        <script type="text/javascript">
        $(function(){  
            var translationFile = 'translation/'+<?php echo "'".$lang."'"; ?>+'.json';
            $.ajax({//loading translation
                type: "GET",
                url: translationFile,
                success: function(data){
                    _.setTranslation(data);
                    
                    $('.toTranslate, #currentFile, #generalInstructions, #mainLink, #sectionName').each(function(){
                        $(this).text(_($(this).text()));
                    });
                   }
            });
        });
        </script>
    </body>
    
</html>