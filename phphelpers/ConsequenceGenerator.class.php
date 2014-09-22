<?php
//This class generates lists of activities based on a consequence part of the rule, eg a 'then' or 'else' element

class ConsequenceGenerator{
    private $pedaProp;
    private $xpathPedaProp;
    private $xpathStrategy;
    private $paramDictionnary; //contains elements in the form  "name" => "P001"
    private $xpathResources;
    private $activitiesContext;//the context (tag, resource...) in which resources have to be taken from. TODO : not yet used in the algorithms
    
    //constructor requiring  gets the  informations related to the generation of activities
    public function __construct($pedaProp, $xpathStrategy, $xpathResources, $activitiesContext){
        $this->pedaProp = $pedaProp;
        $this->xpathPedaProp = new DOMXPath($this->pedaProp);
        $this->xpathStrategy = $xpathStrategy;
        $this->paramDictionnary = $this->getParamDictionnary();
        $this->xpathResources = $xpathResources;
        $this->activitiesContext = $activitiesContext;
    }
    
    //argument is <then> or <else> part of the rule
    //returns array containing the activities to realize (what has to be displayed to the learner).
    public function generate($consequence){
        $generatedActivities = array();
        if($consequence != ''){
            if($consequence->getElementsByTagName('activity')->length > 0){
                //getting all activities that are in the consequence
                $activities = $consequence->getElementsByTagName('activity');
                foreach($activities as $activity){
                    $computedActivities = $this->treatActivity($activity);
                    if($computedActivities != null){
                        foreach($computedActivities as $acti){
                            $generatedActivities[] = $acti;
                        }
                    }
                }
            }
        }
        return($generatedActivities);
    
    }
    
    
    //takes activity from a rule as an argument, returns information about what has to be done by learner in an array
    private function treatActivity($activity){
        $activityName = $this->getActivityName($activity);
        //if defined, get the given length
        $length = 0;
        $lengthParam = $this->getParameterByName('Length', $activity);
        if($lengthParam){
            $length = intval($lengthParam->getElementsByTagName('value')->item(0)->nodeValue);
        }
        
        //in case of learning or an exercise : go in the resources file, and use the parameters.
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
            
            //if the name of a resource is given : find it, in resources XML, and treat by applying other filters
            $nameParam = $this->getParameterByName('Name', $activity);
            if($nameParam){
                $resourceName = $nameParam->getElementsByTagName('value')->item(0)->nodeValue;
                //finding the corresponding resource in the tree
                $resourceQuery = "//*[local-name()='resource' and ./*[local-name()='name' and .='".$resourceName."']]";
                $resource = $this->xpathResources->query($resourceQuery)->item(0);
                
                return $this->treatLearningActivity($resource, $filters, $activityName, 1);
                
            }
            
        }
        
        //in case of social : tell the learner to go on the website, with some instructions
        else if($activityName == 'Social'){
            $text = '<hr/><span class="toTranslate">boussole.social.intro</span>';
            
            //tell the learner to go on the website
            //TODO : enable url as param (or defined somewhere else), to enable teacher to lead learners directly on the course's page on the social networks
            $toolsText = array(
                'Twitter' => '<a target="_blank" href="http://twitter.com"><span class="toTranslate">boussole.social.twitter</span></a> ',
                'Forum' => '<span class="toTranslate">boussole.social.forum</span>',
                'Facebook' => '<a target="_blank" href="http://facebook.com"><span class="toTranslate">boussole.social.facebook</span></a> ',
                'Google+' => '<a target="_blank" href="http://plus.google.com"><span class="toTranslate">boussole.social.google</span></a> ',
            );
            $toolParam = $this->getParameterByName('Tool', $activity);
            if($toolParam){
                $toolName = $toolParam->getElementsByTagName('value')->item(0)->nodeValue;
                $text = $text . $toolsText[$toolName];
            }
            
           //tell the learner what to do 
            $actionsText = array(
                'Answer' => '<span class="toTranslate">boussole.social.answer</span>',
                'Read' => '<span class="toTranslate">boussole.social.read</span>',
                'Create' => '<span class="toTranslate">boussole.social.create</span>',
            );
            
            $actionParam = $this->getParameterByName('Action', $activity);
            if($actionParam){
                $actionName = $actionParam->getElementsByTagName('value')->item(0)->nodeValue;
                $text = $text . $actionsText[$actionName];
            }
            //if information about length : give the learner an indication about the time he should spend on it.
            if($length){
                $text = $text . '(<span class="toTranslate">boussole.social.time</span>'.$length.' minutes).';
            }
            
            //returning an array with information
            return array(array('text' => $text, 'length' => $length));
        
        }
        
        //simply displaying an encouragement message to the learner, depending on the nature of the message
        else if($activityName == 'Message'){
            $text = '<hr/>';
            
            $contentParam = $this->getParameterByName('Content', $activity);
            if($contentParam){
                $text = $text . '<span>'.$contentParam->getElementsByTagName('value')->item(0)->nodeValue.'</span>';
            }
            
            else{            
                $goalsText = array(
                    'Encouraging' => '<span class="toTranslate">boussole.message.encouraging</span>',
                    'Greeting' => '<span class="toTranslate">boussole.message.greeting</span>',
                );
                
                $goalParam = $this->getParameterByName('Goal', $activity);
                if($goalParam){
                    $goalName = $goalParam->getElementsByTagName('value')->item(0)->nodeValue;
                    $text = $text . $goalsText[$goalName];
                }
            }
            
            //returning an array with information
            //countActivity = false : this is not considered as an activity (and is not considered when doing the sum of activities the learner has to realize)
            return array(array('text' => $text, 'length' => $length, 'countActivity' => false));
        }
        
    }
    
    //argument : resource, coming from the XML file of resources
    //returns array of arrays describing activities (but doesn't consider exercises)
    //realizes simple filters that are done directly on eahc resource (status, type, difficulty, sequence)
    //$depth : how deep are we in the resources tree ? used to display different titles, h2, h3,... when indicating a group of resources
    //TODO : use it for each resource identified as having to be done
    private function treatLearningActivity($resource, $filters, $activityName, $depth){
        
        $activities = array();
        $URI = $resource->getAttribute('URI');
        
        //getting some infos about the resource
        $name = $this->getResourceProperty($resource, 'name');
        $type = $this->getResourceProperty($resource, 'type');
        $length = intval($this->getResourceProperty($resource, 'length'));
        
        //true iff resource filters applied up now are verified
        $valid = true;
        
        //if defined ,testing these filters
        $valid = $this->checkFilter('status', $resource, $filters) && $this->checkFilter('difficulty', $resource, $filters) && $this->checkFilter('sequence', $resource, $filters) && $this->checkFilter('categories', $resource, $filters);
        
        if($valid){//eg status, type, difficulty, sequence are good
            //group of resource : apply same function to its children, and display iff non void result (at least one activity returned from the children)
            if($type == 'group'){
                
                //going recursively through children, and getting the activities
                foreach($resource->childNodes as $child){
                    if(isset($child->tagName)){
                        if($child->tagName == 'resource'){
                            $result = $this->treatLearningActivity($child, $filters, $activityName, $depth + 1);
                            foreach($result as $r){
                                $activities[] = $r;
                            }
                        }
                    }
                }
                if(count($activities) > 0){//if at least one child resource has been added : add the current resource also at the beginning of the array, and tell the user to consult the ressources below it
                    //array_unshift($activities, array('text' => '<span class="toTranslate">boussole.learning.groupIntro</span><a target="_blank" href="'.$URI.'">'.$name.'</a> (<span class="toTranslate">boussole.learning.groupCcl</span>)', 'length' => 0, 'countActivity' => false));
                    array_unshift($activities, array('text' => '<h'.($depth+1).'><a target="_blank" href="'.$URI.'">'.$name.'</a></h'.($depth+1).'>', 'length' => 0, 'countActivity' => false, 'depth' => $depth));
                }
            }
            
            //if just learning : tell the learner to consult the ressource
            else if($activityName == 'Learning' && $type != 'quiz' && $type != 'assignment'){
                $valid = $this->checkFilter('type', $resource, $filters);
                if($valid){
                    $activities[] = array('text' => '<span class="toTranslate">Consult </span><a target="_blank" href="'.$URI.'">'.$name.'</a>', 'length' => $length, 'depth' => $depth);
                }
            }
            
            
            else if($activityName == 'Exercise' && ($type == 'quiz' || $type == 'assignment')){
                $valid = $this->checkFilter('grade', $resource, $filters);//for a quiz : true iff not graded, for an assignment : true iff graded
                //graded exercise
                if($valid && $this->getResourceProperty($resource, 'grade') == 'true'){
                    $activities[] = array('text' => '<span class="toTranslate">boussole.exercice.grade</span><a target="_blank" href="'.$URI.'">'.$name.'</a>', 'length' => $length, 'depth' => $depth);
                }
                else if($valid){
                    $activities[] = array('text' => '<span class="toTranslate">boussole.exercice.nograde</span><a target="_blank" href="'.$URI.'">'.$name.'</a>', 'length' => $length, 'depth' => $depth);
                }
            }
        }
        
        return $activities;
    
    }
    
    //true iff the resource verifies the value in the filter for the given parameter name
    private function checkFilter($name, $resource, $filters){
        //getting values for the resource
        $resourceValues = $this->getResourceProperty($resource, $name);
        //in case there are several values seperated by blanks
        if(isset($filters[$name]) && $resourceValues !== null){
            $resourceValues = explode(' ' , $resourceValues);
            $filterValues = explode(' ' , $filters[$name]);
            //check if at least one of the values in the resource is corresponding
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
    
    
    //Arguments : a resource (xml), and the name of a property
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
    
    //gets a filter (parameter) name, and returns the value given by user in the activity (xml)
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
    
    //gets an activity in XML, and returns the name of this activity (thanks to the id and pedagogical properties).
    private function getActivityName($activity){
        $activityId = $this->getActivityId($activity);
        $query = "//TypeOfActivity[@ID='$activityId']";
        $activityDef = $this->xpathPedaProp->query($query)->item(0);
        
        $activityName = $activityDef->getElementsByTagName('Name')->item(0)->nodeValue;
        return $activityName;
    }

    //gets an activity in XML, and returns the id of this activity
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
    
    //gets the dictionnary of parameters used in the pedagogical properties, in the form 'name' => id
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