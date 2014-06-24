/*
Takes a scale element, and displays an information about allowed values next to the value form
clickable : boolean to know if the user has the possibility to click on the values (currently only used in order not to display the 'click on the value you want')
*/
function displayParameterScale(scaleElement, container, clickable){//TODO : add elements
    var informationToDisplay = $('<span>').addClass('scaleInformation');
    var scaleType = scaleElement[0].nodeName;
    
    if(scaleType == 'ScaleBoolean'){
        $(informationToDisplay).append(_('true of false'));
    }
    else if(scaleType == 'ScaleList'){//display the  possibilities
        $(informationToDisplay).append(_('You can chose between: '));
        //this variable will contain the list of possibilities, the values are clickable to fill the input, and display the translation if available
        var enumeration = $('<span>').addClass('enumeration');
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
            $(enumeration).append(_('click on the value you want'))
        }
        $(informationToDisplay).append(enumeration);
    
    }
    
    else if(scaleType == 'ScaleNumerical'){//display the type of value, integer or number, and the step.
        $(informationToDisplay).append(_('The value must be a'));
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
        $(informationToDisplay).append(_('There is no particular constraint on this parameter'));
    }
    
    $(container).append(informationToDisplay);
}


/*
Takes the name of an indicator, and displays the corresponding scale.
function displayIndicatorScale(indicatorName){//TODO : merge with precedent one, with argument profile or activity
*/
function displayIndicatorScale(indicatorName, container, currentIndicatorId, profileScales, contextScales, clickable){//TODO : merge with precedent one, with argument profile or activity
    var informationToDisplay = $('<span>').addClass('scaleInformation');
    var scales;//2 possible values, profileScales and contextScales, depending on where the indicator comes from.
    //TODO : I already have this information in todoBefore[3]
    if($('#Profile' + ' #' +currentIndicatorId).length > 0){//the indicator is in the profile
         scales = profileScales;
    }
    else{
        scales = contextScales;
    }
                
    
    if(scales[indicatorName]){//if a constraint concerning this indicator is present
        var scaleElement = scales[indicatorName];
    
        if(scaleElement.nature == 'predefined'){
            $(informationToDisplay).append(_('The value must be a'));
            $(informationToDisplay).append(_(scaleElement.typeName));
        
        }
        else if(scaleElement.nature == 'restriction'){//restriction
            if(scaleElement.baseTypeName == 'xs:float' || scaleElement.baseTypeName == 'xs:integer'){//if number
                $(informationToDisplay).append((_('The value must be a') + _(scaleElement.baseTypeName)));
                if(scaleElement.min && scaleElement.max){//if min and max are set
                    $(informationToDisplay).append((_(' between ') + scaleElement.min + _(' and ') + scaleElement.max));
                }
            }
            else if(scaleElement.baseTypeName == 'xs:string' && scaleElement.enumeration){//there's an enumeration
                $(informationToDisplay).append(_('You can chose between: '));
                var enumeration = $('<span>').addClass('enumeration');
                $(scaleElement.enumeration).each(function(){
                    var value = this;
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
                //informationToDisplay = informationToDisplay.slice(0, -2);
                $(informationToDisplay).append(enumeration);
                if(clickable){
                    $(enumeration).append(_('click on the value you want'))
                }
            }
            
        }
                                                         
    }
    else{
        $(informationToDisplay).append(_('There is no particular constraint on this parameter'));
    }
    var commentPopover;
    if(scaleElement.documentation){
        var commentPopover = $('<span>').addClass('glyphicon glyphicon-info-sign commentPopover').attr('title', _('More information'));
        $(commentPopover).click(function(){
            alert(scaleElement.documentation);
        });
    }
    
    $(container).append(informationToDisplay).append(commentPopover);

}