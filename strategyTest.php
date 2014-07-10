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
        $file = $_SESSION['file'];
        $scales = $_SESSION['scales'];
        $section = $_SESSION['section'];
    }
    else{
        $path = $_POST['path'];
        $file = $_POST['file'];
        $scales = $_POST['scales'];
        $section = $_POST['section'];
    }
    ?>
		<div class="container">
			<h1><span class="toTranslate">strategyTest.h1</span><span id="sectionName"><?php echo $section; ?></span><small><span id="currentFile">strategyTest.currentFileIntro</span><span id="currentFileName"><?php echo $file; ?></span></small></h1>
			<p id="generalInstructions">strategyTest.instructions</p>
			<p><a href="index.php" id="mainLink">common.back</a></p>
			
			
			
			<?php
				$strategyPath = $path.$file;
				$generator = new activitiesGenerator($strategyPath);
				$generator->generate('', '', '');
				
				class activitiesGenerator{
					private $strategy;
					private $pedaProp;
					
					public function __construct($strategyPath){
						//loading strategy file
						$this->strategy = new DOMDocument();
						$this->strategy->load($strategyPath);
						
						//pedagogical properties
						$pedagogicalPropertiesFile = $this->strategy->getElementsByTagName('pedagogicalProperties')->item(0)->nodeValue;
						$this->pedaProp = new DOMDocument();
						$this->pedaProp->load($pedagogicalPropertiesFile);
					}
				
					public function generate($profile, $liveContext, $sequenceContext){
						//loading other files used for test
						//TODO : replace it with arguments
						$exploitedProfileFile = $this->strategy->getElementsByTagName('exploitedProfile')->item(0)->nodeValue;
						$exploitedContextFile = $this->strategy->getElementsByTagName('exploitedContext')->item(0)->nodeValue;
						
						$profile = new DOMDocument();
						$profile->load($exploitedProfileFile);
						
						$liveContext = new DOMDocument();
						$liveContext->load($exploitedContextFile);
					
						//TODO ; use $_POST
						$seqContext = new DOMDocument();
						$seqContext->load('data/teacher/sequenceContexts/Sequence1.xml');
					
						//TODO : us it as argument (or not ?)
						$profileScales = json_decode(file_get_contents('data/schemas/profileScales.json'));
						$contextScales = json_decode(file_get_contents('data/schemas/contextScales.json'));
					
					
						
						
						$checker = new ConditionChecker($profile, $liveContext, $profileScales, $contextScales);
						
						$rules = $this->strategy->getElementsByTagName('rule');
						
						//this array will contain the rules that apply to the learner.
						//elements have the form 'then' or 'else' => rule    TODO change if changed
						$rulesToApply = array();
						foreach ($rules as $rule){
							$ifElement = $rule->getElementsByTagName('if')->item(0);
							$condition = $ifElement->childNodes->item(0);
							var_dump($checker->checkCondition($condition));
						}
					
					
					
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
					private $profileScales;
					private $contextScales;
					
					public function __construct($profile, $liveContext, $profileScales, $contextScales){
						$this->profile = $profile;
						$this->xpathProfile = new DOMXPath($this->profile);
						$this->liveContext = $liveContext;
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
						if($this->profileScales->$indicatorName != null){
							if(isset($this->profileScales->$indicatorName->typeName)){
								$indicatorType = $this->profileScales->$indicatorName->typeName;
							}
							else if(isset($this->profileScales->$indicatorName->baseTypeName)){
								$indicatorType = $this->profileScales->$indicatorName->baseTypeName;
							}
						}
						return $indicatorType;
					}
				
				}
				
				
			?>
			
			
			
			
			
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