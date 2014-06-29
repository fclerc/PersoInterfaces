/*
Takes a scale element, and displays an information about allowed values next to the value form
clickable : boolean to know if the user has the possibility to click on the values (currently only used in order not to display the 'click on the value you want')
reesourcesData : list of the values the parameter takes in the resources definition
*/
function displayParameterScale(scaleElement, resourcesData, container, clickable){//TODO : add elements
    var informationToDisplay = $('<span>').addClass('scaleInformation');
    var scaleType = scaleElement[0].nodeName;
    
    if(scaleType == 'ScaleBoolean'){
        $(informationToDisplay).append(_('true or false'));
    }
    else if(scaleType == 'ScaleList'){//display the  possibilities
        
        if($(scaleElement).find('Name').length > 0){
            $(informationToDisplay).append(_('scales.enumeration.intro'));
            //this variable will contain the list of possibilities, the values are clickable to fill the input, and display the translation if available
            var enumeration = $('<span>').addClass('enumeration');
            //TODO : if enumeration is void, then go in the json file
            $(scaleElement).find('Name').each(function(){
                var value = $(this).text();
                var valueContainer = $('<span>').addClass('inputFiller').text(value);
                $(enumeration).append(valueContainer);
                if(!(value == _(value))){//translate between brackets if necessary
                    $(enumeration).append(' ('+_(value)+')');
                }
                $(enumeration).append(', ');
                
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
        if($(scaleElement).find('Step').length === 0){
            $(informationToDisplay).append(_(' number'));
        }
        else{
            var step = $($(scaleElement).find('Step')[0]).text();
            if(step=='1'){
                $(informationToDisplay).append(_('n integer'));
            }
            else{
                $(informationToDisplay).append( (_(' number, with step ')+step));
            }
        
        }
    }
    
    else{//the parameter is not in the dictionnary
        $(informationToDisplay).append(_('scales.noscale'));
    }
    
    if(resourcesData){//if not undefined
        if(resourcesData.length > 0){//if the json file contains the list  of values used in the resources file for this parameter : display the values
            $(informationToDisplay).append('<br>').append('In resources file you used the values: ');
            getEnumerationStringFromArray(informationToDisplay, resourcesData, clickable);
        }
    }
    $(container).append(informationToDisplay);
}


/*
Takes the name of an indicator, and displays the corresponding scale.
function displayIndicatorScale(indicatorName){//TODO : merge with precedent one, with argument profile or activity
*/
function displayIndicatorScale(indicatorName, container, currentIndicatorId, scales, clickable){//TODO : merge with precedent one, with argument profile or activity
    
    var informationToDisplay = $('<span>').addClass('scaleInformation');
    if(scales[indicatorName]){//if a constraint concerning this indicator is present
        var scaleElement = scales[indicatorName];
        if(scaleElement.nature == 'predefined'){
            $(informationToDisplay).append(_('scales.value.intro'));
            $(informationToDisplay).append(_(scaleElement.typeName));
        
        }
        else if(scaleElement.nature == 'restriction'){//restriction
            if(scaleElement.baseTypeName == 'xs:float' || scaleElement.baseTypeName == 'xs:integer'){//if number
                $(informationToDisplay).append((_('scales.value.intro') + _(scaleElement.baseTypeName)));
                if(scaleElement.min && scaleElement.max){//if min and max are set
                    $(informationToDisplay).append((_(' between ') + scaleElement.min + _(' and ') + scaleElement.max));
                }
            }
            else if(scaleElement.baseTypeName == 'xs:string' && scaleElement.enumeration){//there's an enumeration
                if(scaleElement.enumeration.length > 0){
                    $(informationToDisplay).append(_('scales.enumeration.intro'));
                    getEnumerationStringFromArray(informationToDisplay, scaleElement.enumeration, clickable)
                }
            }
            
        }
        else{
            $(informationToDisplay).append(_('scales.noscale'));
        }
        
        var commentPopover;
        if(scaleElement.documentation && clickable){
            commentPopover = $('<span>').addClass('glyphicon glyphicon-info-sign commentPopover').attr('title', _('More information'));
            $(commentPopover).click(function(){
                alert(scaleElement.documentation);
            });
        }
        
        $(container).append(informationToDisplay).append(commentPopover);
                                                     
    }
    
    
}

/*
Appends container with the list of values (clickable)
*/
function getEnumerationStringFromArray(container, valueArray, clickable){
    var enumeration = $('<span>').addClass('enumeration');
    $(valueArray).each(function(){
        var value = this;
        var valueContainer = $('<span>').addClass('inputFiller').text(value);
        $(enumeration).append(valueContainer);
        if(!(value == _(value))){//translate between brackets if necessary
             $(enumeration).append(' ('+_(value)+')');
        }
        $(enumeration).append(', ');
        $(valueContainer).click(function(){//enable user to click on the container, and fill the input with the value TODO : add as parameter
            $($('#newRuleContainer').find('input')[0]).attr('value', value);
        });
    });
    //container = container.slice(0, -2);
    $(container).append(enumeration);
    if(clickable){
        $(enumeration).append(_('scales.enumeration.conclu'));
    }



}