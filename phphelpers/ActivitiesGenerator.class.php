<?php
/*
Enables to generate a list of activities.
Constructor requires a strategy, and then calls are made by passing a profile, a liveContext and a sequenceContext as arguments.


*/

class ActivitiesGenerator{
    private $strategy;
    private $xpathStrategy;
    private $pedaProp;
    private $resources;
    private $xpathResources;
    
    //argument is the path to the strategy used to generate the activities
    public function __construct($strategyPath){
        $root = 'http://localhost/test/';
        //loading strategy file
        $this->strategy = new DOMDocument();
        $this->strategy->load($strategyPath);
        $this->xpathStrategy = new DOMXPath($this->strategy);
        
        //loading pedagogical properties
        //$pedagogicalPropertiesFile = $this->strategy->getElementsByTagName('pedagogicalProperties')->item(0)->nodeValue;
        $this->pedaProp = new DOMDocument();
        //$this->pedaProp->load($pedagogicalPropertiesFile);
        $this->pedaProp->load($root.'data/models/foveaPedagogicalProperties.xml');
        
        //loading resources XML
        $this->resources = new DOMDocument();
        //$this->resources->load('data/resources/foveaResources.xml');
        $this->resources->load($root.'data/resources/foveaResources.xml');
        $this->xpathResources = new DOMXPath($this->resources);
    }

    //arguments are paths to the different files we want to use to generate the list of activities.
    public function generate($profilePath, $sequenceContextPath, $liveContextPath){
        $root = 'http://localhost/test/';
        //loading the files
        $profile = new DOMDocument();
        $profile->load($profilePath);
        $liveContext = new DOMDocument();
        $liveContext->load($liveContextPath);
        $seqContext = new DOMDocument();
        $seqContext->load($sequenceContextPath);
        //loading the scales (used to get informations about the indicators, for example to know what type of variable the indicator is)
        //TODO : us it as argument (or not ?)
        $profileScales = json_decode(file_get_contents($root.'data/infos/profileScales.json'));
        $contextScales = json_decode(file_get_contents($root.'data/infos/contextScales.json'));
        
        //used to check the conditions contained in the rules ('<if>' parts)
        $checker = new ConditionChecker($profile, $liveContext, $profileScales, $contextScales);
        
        //getting all the rules
        $rules = $this->strategy->getElementsByTagName('rule');
        
        //contains arrays of activities (each array corresponding to the set of activities contained in a consequence ('then' or 'else' part of the rule) applied to the learner)
        $consequences = array();
        //contains the final list of activities, in the right order, that have to be realized by learner
        $activities = array();
        
        //getting the context from which the activities must be taken - not yet used
        $activitiesContext = $seqContext->getElementsByTagName('activitiesContext')->item(0)->nodeValue;
        
        //used to generate lists of activities from the 'then' or 'else' parts of a rule
        $csqMgr = new ConsequenceGenerator($this->pedaProp, $this->xpathStrategy, $this->xpathResources, $activitiesContext);
        
        //going through all the rules to see which activities must be given to the user according to this rule.
        foreach ($rules as $rule){	
            $priority = $this->getPriority($rule);
            
            
            //True iff condition of the rule is verified. If 'if' part is void, evaluate at true by default
            $verified = true; 
            if($rule->getElementsByTagName('if')->length > 0){
                //getting the 'if' element, and using ConditionChecker to know if verified or not.
                $ifElement = $rule->getElementsByTagName('if')->item(0);
                $condition = $ifElement->childNodes->item(0);
                $verified = $checker->checkCondition($condition);
            }
            
            //getting the consequence that applies to the learner : 'then' or 'else' part of the rule.
            $consequence = '';
            if($verified && $rule->getElementsByTagName('then')->length > 0){
                $consequence = $rule->getElementsByTagName('then')->item(0);
            }
            else if($rule->getElementsByTagName('else')->length > 0){
                $consequence = $rule->getElementsByTagName('else')->item(0);
            }
            
            
            //gettting the list of activities generated with this consequence, and storing it together with the priority
            $consequences[] = array('activities' => $csqMgr->generate($consequence), 'priority' => $priority);
            
        }
        
        //sorting consequences by priority
        usort($consequences, function($a, $b){
            return $a['priority'] <= $b['priority'];
        });
            
        //now that everything is ordered according to the priorities, we can simply make an array containing the activities the learner has to realize.
        foreach($consequences as $consequence){
            $csqAct = $consequence['activities'];
            foreach($csqAct as $activity){
                $activities[] = $activity;
            }
        }
        
        //display the activities, filtering it with the sequenceContext
        return $this->getActivities($activities, $seqContext);
    
    }
    
    /*
    Takes a list of activities, and a sequenceContext, and displays the activities the learner has to do, using the global constraints in sequenceContext
    
    Currently only using the min and max of activities and duration
    
    
    */
    private function getActivities($activities, $seqContext){
        //echoing welcome message
        $result = '<span class="toTranslate">boussole.hello</span><br/><ul>';
        
        //getting values of min and max for time and number of activities
        $maxAct = intval($seqContext->getElementsByTagName('numberOfActivities')->item(0)->getElementsByTagName('max')->item(0)->nodeValue);
        $minAct = intval($seqContext->getElementsByTagName('numberOfActivities')->item(0)->getElementsByTagName('min')->item(0)->nodeValue);
        $maxTime = intval($seqContext->getElementsByTagName('activitiesDuration')->item(0)->getElementsByTagName('max')->item(0)->nodeValue);
        $minTime = intval($seqContext->getElementsByTagName('activitiesDuration')->item(0)->getElementsByTagName('min')->item(0)->nodeValue);
        
        //total values of number of activities and time the displayed rules so far.
        $nbAct = 0;
        $time = 0;
        
        //go through activities, display them until one of the max bounds is reached
        foreach($activities as $activity){
            if($nbAct < $maxAct){
                $activityTime = $activity['length'];
                if($time + $activityTime <= $maxTime){
                    $result = $result . '<li>'.$activity['text'].'</li>';
                    //incrementing nbAct, except if 'countActivity' is set to false (eg this is not a 'real' activity, but a message for the user with nothing special to do)
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
        
        $result = $result . '</ul>';
        
        $result = $result . '<p><span class="toTranslate">boussole.conclusion.begin</span>'.$nbAct.'<span class="toTranslate">boussole.conclusion.nbAct</span>'.round($time, -1).'<span class="toTranslate"> minutes.</span></p>';
        return $result;
    }
    
    //get the priority of the rule in argument (default is 0)
    private function getPriority($rule){
        $priority = 0;
        //getting the priority
        if($rule->getElementsByTagName('priority')->length > 0){
            $priority = $rule->getElementsByTagName('priority')->item(0)->nodeValue;
        }
        return intval($priority);
    }
}

?>