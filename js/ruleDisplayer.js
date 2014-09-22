/*
    Argument is a xml node containing a rule, and returns the html to display it, without any edition possibility
    
*/
function displayRule(rule){
    
   
    //displaying the priority
    var priority = $($(rule).find("priority")[0]).text();
    var priorityContainer = $('<div>').addClass('priority').append(_('Priority: '));
    $(priorityContainer).append(priority);
    
    var ruleContainer = $('<div>').attr('id', $(rule).attr('id')).addClass('ruleContainer').append($('<h4>').append(_('Rule') + $(rule).attr('id'))).append(priorityContainer);
    
    
    /*
    Display of the rule itself
    */
    
    
    //element that will contain the whole condition
    var ifContainer = $('<div>').addClass('ifContainer').append(_('IF'));
    
    //getting the if part of the rule
    var ifElement = $(rule).find("if")[0];
    if($(ifElement).children().length > 0){//there are constraints, display it
        $(ifElement).children().each(function(){
            $(ifContainer).append(getConditionElementContainer(this));
        });
    }
    
    return todoWhenConditionReady();
    
    //element is a condition, or a part of it, eg it can be 3 types of tags : 'constraint', 'or' and 'and'.
    //function returns html to display this condition
    function getConditionElementContainer(element){
        var toReturn;
        if((element.nodeName).toLowerCase() == 'constraint'){
            toReturn = getConstraintContainer(element);
        }
        else{//AND or OR, with 2 condition element children : display '(c1 OR c2)' or '(c1 AND c2)'
            var c1, c2;
            if($(element).children().length >= 1){
                c1 = getConditionElementContainer($(element).children()[0]);
                    if($(element).children().length >= 2){
                        c2 = getConditionElementContainer($(element).children()[1]);
                    }
            }
            
            
            toReturn = $('<span>').addClass('conditionElement');
            
            //conditionTypeContainer contains the name of the operator, 'AND' or 'OR', with eventually forms and buttons
            var conditionTypeContainer = $('<span>').addClass('conditionType');
           
           //just display the operator
            $(conditionTypeContainer).append(' '+_(element.nodeName.toUpperCase()));
            
            $(toReturn).append(' (').append(c1).append(conditionTypeContainer).append(c2).append(' )');
        }
        
        return toReturn;
    }
    
    //constraint : a constraint element.
    //returns the html to display it
    function getConstraintContainer(constraint){
        var indicatorId = $($(constraint).find("indicator")[0]).text();
        //finding the corresponding element in the left part of the page, to display the name of the indicator, and indicate it when hovering the indicator in the page.
        var indicatorName='';
        var indicatorSelectionContainer;
        getIndicatorInfos();
        return todoWhenIndicatorReady();
        
        //last part of function todoWhenIndicatorReady, returns the constraint displayed in html
        function todoWhenIndicatorReady(){
            var operator = $($(constraint).find("operator")[0]).text();
            var referenceValue = $($(constraint).find("referencevalue")[0]).text();
            var indicatorContainer = $('<span>').addClass('indicator').append(' '+indicatorName);
            
            var operatorContainer = $('<span>').addClass('operator').append(' ');
            var referenceValueContainer = $('<span>').addClass('referenceValue').append(' ');
            
            //just add the operator
            $(operatorContainer).append(operator);
            
            //if currently editing the value of this constraint, display a form
            
            //else just display the value
            $(referenceValueContainer).append(referenceValue);
            
            
            
            
            
            
            var constraintContainer = $('<span>').addClass('constraint');
            $(constraintContainer).append(indicatorContainer);
            $(constraintContainer).append(operatorContainer);
            $(constraintContainer).append(referenceValueContainer);
            
           return constraintContainer;
        }
        
        function getIndicatorInfos(){
            indicatorSelectionContainer = $('#ProfileAndContext').find('#'+indicatorId);
            indicatorName = $($(indicatorSelectionContainer).find('.elementName')[0]).text();
        }
    }
   
    
    
    
    
    
    
    //when the html of the condition is generated : genrate the html for the 'then' and 'else' parts, and display all these elements.
    function todoWhenConditionReady(){
        
        var thenContainer = getConsequencesContainer($(rule).find('then')[0], 'then');
        var elseContainer = getConsequencesContainer($(rule).find('else')[0], 'else');
        
        $(ruleContainer).append(ifContainer);
        $(ruleContainer).append(thenContainer);
        $(ruleContainer).append(elseContainer);
        $(ruleContainer).insertBefore($(insBefore));
    
    }
    
    //from one of the 2 tags containing the activities (<then> or <else>), returns the activities contained in it, displayed with their parameters
    //containerName = 'then' or 'else'
    function getConsequencesContainer(consequencesElement, containerName){
    
        var consequenceContainer = $('<div>').addClass(containerName).append($('<span>').append(_(containerName.toUpperCase())+_('strategy.rules.learnerActivity')));
        
        var activitiesContainer = $('<ul>');
        $(consequencesElement).find('activity').each(function(){
            var activity = this;
            var activityContainer = $('<li>').addClass('activityContainer');
            
            
            
            
            var typeOfActivityId = $($(this).find("typeofactivity")[0]).text();
            var typeOfActivityContainer = $('<span>').addClass('typeOfActivity').append(' '+activitiesDictionnary[typeOfActivityId] + _('strategy.rules.parametersListIntro'));
            
            
            
            //link between activity in the rule and activity in the right part when hovering and clicking
            var typeOfActivitySelectionContainer = $('#Activities #'+typeOfActivityId);
            //var typeOfActivitySelectionContainerColor = $(typeOfActivitySelectionContainer).css('background-color');
            
            $(typeOfActivityContainer).hover(function(){
                $(typeOfActivitySelectionContainer).css('background-color', '#FF7F24');
            },
            function(){
                $(typeOfActivitySelectionContainer).css('background-color', 'transparent');
            });
            
            $(typeOfActivityContainer).click(function(){
                typeOfActivitySelectionContainer[0].scrollIntoView(true); 
            });
            
           
            //display the list of parameters
            var parametersContainer =$('<ul>').addClass('parameters');
            $(this).find('parameter').each(function(){
                var parameter = this;
                var paramId = $($(this).find('id')[0]).text();
                var paramValue = $($(this).find('value')[0]).text();
                var paramValueContainer = $('<span>').append(paramValue);
                var parameterContainer = $('<li>').append((_((parametersDictionnary[paramId]).name))+_(': ')).append(paramValueContainer);
                
                paramValueContainer = paramValue;
                    //link between parameter in the rule and parameter in the right part when hovering and clicking
                    var parameterSelectionContainer = $('#Activities #'+typeOfActivityId +' +ul li #'+paramId);
                    //var parameterSelectionContainerColor = $(parameterSelectionContainer).css('background-color');
                    
                    $(parameterContainer).hover(function(){
                        $(parameterSelectionContainer).css('background-color', '#FF7F24');
                    },
                    function(){
                        $(parameterSelectionContainer).css('background-color', 'transparent');
                    });
                    
                    $(parameterContainer).click(function(){
                        parameterSelectionContainer[0].scrollIntoView(true);
                    });
                
                $(parametersContainer).append(parameterContainer);
                
            });
            
            $(activityContainer).append(typeOfActivityContainer);
            $(activityContainer).append(parametersContainer);
            $(activitiesContainer).append(activityContainer);
            
            
            
        });
        
        return $(consequenceContainer.append(activitiesContainer));
        
    }
    

}
                    