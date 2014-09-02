<?php
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
    
    //takes all the information required to check a condition : profile, context and scales
    public function __construct($profile, $liveContext, $profileScales, $contextScales){
        $this->profile = $profile;
        $this->xpathProfile = new DOMXPath($this->profile);
        $this->liveContext = $liveContext;
        $this->xpathLiveContext = new DOMXPath($this->liveContext);
        $this->profileScales = $profileScales;
        $this->contextScales = $contextScales;
    
    }
    
    //$condition = the 'if' part of the rule. returns a boolean
    public function checkCondition($condition){
        
        //if simple constraint (eg no 'and' or 'or) : check the constraint
        if(strToLower($condition->tagName) == 'constraint'){
            return $this->checkConstraint($condition);
        }
        //if and : check C1 && C2
        else if(strToLower($condition->tagName) == 'and'){
            $children = $condition->childNodes;
            return ($this->checkCondition($children->item(0)) && $this->checkCondition($children->item(1)));
        }
        //if or : check C1 || C2
        else if(strToLower($condition->tagName) == 'or'){
            $children = $condition->childNodes;
            return ($this->checkCondition($children->item(0)) || $this->checkCondition($children->item(1)));
        }
        
    }
    
    //argument is a constraint element, returns boolean depending on whether the condition is verified or not with the indicatros contained in profile and liveContext
    private function checkConstraint($constraint){
    
        //gettinf the id of indicator, and the element in profile or context
        $indicatorId = $constraint->getElementsByTagName('indicator')->item(0)->nodeValue;
        $indicator = $this->xpathProfile->query("//*[@id='$indicatorId']")->item(0);
        
        if($indicator === null){//not in the profile, get it in liveContext
            $indicator = $this->xpathLiveContext->query("//*[@id='$indicatorId']")->item(0);
        }
        
        //getting the value and name of the indicator
        $indicatorValue = $indicator->nodeValue;
        $indicatorName = $indicator->tagName;
        //getting ref value in the constraint
        $referenceValue = $this->getReferenceValue($constraint);
        
        //find the type of the indicator in docs, and make appropriate conversions
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
    
    //returns reference value of the constraint passed in argument
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
    
    //returns type of the indicator (finds it in scales of profile and contexts)*
    //$indicatorName is a string
    private function getIndicatorType($indicatorName){
        $indicatorType = '';
        if(isset($this->profileScales->$indicatorName)){//it is in profile docs
            if(isset($this->profileScales->$indicatorName->typeName)){
                $indicatorType = $this->profileScales->$indicatorName->typeName;
            }
            else if(isset($this->profileScales->$indicatorName->baseTypeName)){
                $indicatorType = $this->profileScales->$indicatorName->baseTypeName;
            }
        }
        else if(isset($this->contextScales->$indicatorName)){//it is in contextScales (eg in liveContext)
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
?>