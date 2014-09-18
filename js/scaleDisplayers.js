/*
In this file are the functions called in the interfaces to display more information about the elements manipulated by the user.

*/

/*
Takes a scale element, and displays an information about allowed values next to the value form
scaleelement : the part of the json containing scales data that concerns the element for which we need informations.
reesourcesData : list of the values the parameter takes in the resources definition
container : the html element in which the information has to be displayed
clickable : boolean to know if the user has the possibility to click on the values (currently only used in order not to display the 'click on the value you want in the rules interface when the user has to enter an indicator value in the input')
style : if 'list', display it as an html list (used when enumeration with lots of elements, for example forthe list of ressources)
*/
function displayParameterScale(scaleElement, resourcesData, container, clickable, style){//TODO : add elements
    var informationToDisplay = $('<span>').addClass('scaleInformation');
    var scaleType = scaleElement[0].nodeName;
    
    var formInformation = '' //information about what  kind of form has to be displayed in the rule edition - this is returned by the function
    
    if(scaleType == 'ScaleBoolean'){
        $(informationToDisplay).append(_('true or false'));
        
        formInformation = {'type' : 'select' , 'values' : ['true', 'false']};
    }
    else if(scaleType == 'ScaleList'){//display the  possibilities
        
        if($(scaleElement).find('Name').length > 0){
            $(informationToDisplay).append(_('scales.enumeration.intro'));
            //this variable will contain the list of possibilities, the values are clickable to fill the input, and display the translation if available
            var enumeration = $('<ul>').addClass('enumeration');
            //TODO : if enumeration is void, then go in the json file
            $(scaleElement).find('Name').each(function(){
                var value = $(this).text();
                var elementContainer = $('<span>');
                if(style == 'list'){
                    elementContainer = $('<li>');
                }
                var valueContainer = $('<span>').addClass('inputFiller').text(value);
                $(elementContainer).append(valueContainer);
                if(!(value == _(value))){//translate between brackets if necessary
                    $(elementContainer).append(' ('+_(value)+')');
                }
                $(elementContainer).append(', ');
                $(enumeration).append(elementContainer);
                
                $(valueContainer).click(function(){//enable user to click on the container, and fill the input with the value
                    $($('#newRuleContainer').find('input')[0]).attr('value', value);
                });
                
            });
            if(clickable){
                $(enumeration).append(_('scales.enumeration.conclu'));
            }
            $(informationToDisplay).append(enumeration);
        }
    
    }
    
    else if(scaleType == 'ScaleNumerical'){//display the type of value, integer or number, and the step.
        $(informationToDisplay).append(_('scales.value.intro'));
        if($(scaleElement).find('Step').length === 0){//no step : no precision about the number to display
            $(informationToDisplay).append(_(' number'));
        }
        else{
            var step = $($(scaleElement).find('Step')[0]).text();
            if(step=='1'){//step 1 : it's an integer
                $(informationToDisplay).append(_('n integer'));
            }
            else{//another value of step is provided
                $(informationToDisplay).append( (_(' number, with step ')+step));
            }
        
        }
    }
    
    else{//the parameter is not in the dictionnary
        $(informationToDisplay).append(_('scales.noscale'));
    }
    
    if(resourcesData){//if not undefined
        if(resourcesData.length > 0){//if the json file contains the list  of values used in the resources file for this parameter : display the values
            $(informationToDisplay).append('<br>').append(_('scales.usedValuesIntro'));
            getEnumerationStringFromArray(informationToDisplay, resourcesData, clickable, style);
        }
    }
    $(container).append(informationToDisplay);
    
    return formInformation;
    
}


/*
Takes the name of an indicator, and displays the corresponding scale.
See function above for explanations about the arguments
*/
function displayIndicatorScale(indicatorName, container, currentIndicatorId, scales, clickable, style){//TODO : merge with precedent one, with argument profile or activity
    
    var informationToDisplay = $('<span>').addClass('scaleInformation');
    
    var formInformation = '' //information about what  kind of form has to be displayed in the rule edition - this is returned by the function
    
    if(scales[indicatorName]){//if a constraint concerning this indicator is present
        var scaleElement = scales[indicatorName];
        if(scaleElement.nature == 'predefined'){
            $(informationToDisplay).append(_('scales.value.intro'));
            $(informationToDisplay).append(_(scaleElement.typeName));
            
            if(scaleElement.typeName == 'xs:boolean'){
                formInformation = {'type' : 'select' , 'values' : ['true', 'false']};
            }
        
        }
        else if(scaleElement.nature == 'restriction'){//restriction
            if(scaleElement.baseTypeName == 'xs:float' || scaleElement.baseTypeName == 'xs:integer'){//if number
                $(informationToDisplay).append((_('scales.value.intro') + _(scaleElement.baseTypeName)));
                if(scaleElement.min && scaleElement.max){//if min and max are set : geenrate a sentence telling the min and max
                    $(informationToDisplay).append((_(' between ') + scaleElement.min + _(' and ') + scaleElement.max));
                }
            }
            else if(scaleElement.baseTypeName == 'xs:string' && scaleElement.enumeration){//there's an enumeration : display the elements
                if(scaleElement.enumeration.length > 0){
                    $(informationToDisplay).append(_('scales.enumeration.intro'));
                    getEnumerationStringFromArray(informationToDisplay, scaleElement.enumeration, clickable, style)
                }
            }
            
        }
        else{//no information available
            $(informationToDisplay).append(_('scales.noscale'));
        }
        
        //if more information is available : and we want infos to be clickable : display an 'alert' containing the documentation 
        var commentPopover;
        if(scaleElement.documentation && clickable){
            commentPopover = $('<span>').addClass('glyphicon glyphicon-info-sign commentPopover').attr('title', _('More information'));
            $(commentPopover).click(function(){
                alert(scaleElement.documentation);
            });
        }
        
        $(container).append(informationToDisplay).append(commentPopover);
                                                     
    }
    
    return formInformation;
    
}

/*
Appends container with the list of values (clickable)
style : if 'list', display it as an html list (used when enumeration with lots of elements
*/
function getEnumerationStringFromArray(container, valueArray, clickable, style){
    var enumeration = $('<ul>').addClass('enumeration');
    $(valueArray).each(function(){
        var value = this;
        var elementContainer = $('<span>');
        if(style == 'list'){
            elementContainer = $('<li>');
        }
        var valueContainer = $('<span>').addClass('inputFiller').text(value);
        $(elementContainer).append(valueContainer);
        if(!(value == _(value))){//translate between brackets if necessary
            $(elementContainer).append(' ('+_(value)+')');
        }
        $(elementContainer).append(', ');
        $(enumeration).append(elementContainer);
        
        $(valueContainer).click(function(){//enable user to click on the container, and fill the input with the value TODO : add as parameter
            $($('#newRuleContainer').find('input')[0]).attr('value', value);
        });
    });
    $(container).append(enumeration);
    if(clickable){
        $(enumeration).append(_('scales.enumeration.conclu'));
    }



}