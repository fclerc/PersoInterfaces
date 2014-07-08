/*
Script used to display and modify the parameters and the whole tree of resources



*/



//cloned when addding a new resource
var emptyResource = '<resource URI=""><name></name><type></type><status></status><order></order><difficulty></difficulty><sequence></sequence><grade></grade><length></length><categories></categories><grade></grade><order></order><description></description></resource>';




//when editing parameters of a resource, 
var currentResource;
var currentResourceContainer;

//container : the id of the container of the displayed XML, for example : '#MyXMLContainer'. Used as a sort of namespace for data manipulation in case of using several times this function in the same page (see xml[container] or selectors to define events).
//scales : json with information about the indicators. WARNING : scalesDisplayers have to be loaded before.
//scaleContainer : the html element you want the scale to be displayed
//filenameContainer : if elements in your page display the name of the file, give their selector in order to have name changed if the user renames his file.
function manipulateResourcesXML(filepath, container, filenameContainer = ''){
    
    return $.ajax({
		type: "GET",
		url: filepath,
		success: function(data){//get the xml document
			var xml = [];
			xml[container]=$(data);//load xml tree
			
			//going recursively through the resources xml, and displaying its content
			$(container).append($('<div>').addClass('resourcesContainer').addClass(filepath.split('.').join("")).append(displayAndChildren($($(xml[container]).children('resourcesStructure')).children('resource').first()[0]) ));
			
			//for elements having list below them : toggle visibility of this list when clicking on the element
			$(container +' .reducer').click(function(event){
				var toToggle = $(event.target).next().next();
                if(toToggle[0].nodeName != 'ul' && toToggle[0].nodeName != 'UL'){//in case there is the information icon, go one step further to find the list to hide.
                    toToggle = $(toToggle).next()
                }
                if(toToggle[0].nodeName != 'ul' && toToggle[0].nodeName != 'UL'){//in case there is a value, go one step further to find the list to hide.
                    toToggle = $(toToggle).next()
                }
					$(toToggle).toggle(300);
					
					//just changing the glyphicon
					if($(event.target).hasClass('glyphicon-plus')){
						$(event.target).addClass('glyphicon-minus');
						$(event.target).removeClass('glyphicon-plus');
					}
					else{
						$(event.target).addClass('glyphicon-plus');
						$(event.target).removeClass('glyphicon-minus');
					}
				
				return false;
			});                 

            
            //lines to add the 'save' button and send data with XHR
            var filename = filepath.replace(/^.*(\\|\/|\:)/, '');//just the name of the file
            var repo = filepath.replace('/'+filename, '');//the name of the dossier where the file is situated
            
            var filenameInputContainer = $('<span>').append(_('Name: ')).addClass('filenameInput');
            var filenameInput = $('<input>').attr('type', 'text').attr('value', filename);//input to enable user to change the name of file
            $(filenameInputContainer).append(filenameInput);
            $(container).prepend(filenameInputContainer);
            $(container).prepend($('<button>').addClass('btn btn-info').attr('id', "XMLSaveButton").append($('<span>').addClass('glyphicon glyphicon-floppy-disk')).append(_("Save modifications")));
        
            $(container +' #XMLSaveButton').click(function(){//using ajax to store the xml on the server.
                var xmlS = (new XMLSerializer()).serializeToString(xml[container][0]);
                $.post('phphelpers/saveXMLDocument.php', { file: '../'+repo+$(filenameInput).val() , data: xmlS, formerFile: '../'+repo+filename}, 
                    function(data, txt, jqXHR){
                        if(txt=="success"){
                            if(data.message == 'RENAMEERROR'){
                                alert('File not saved: a file with this name already exists');
                            }
                           else{                            
                                alert('Your data have been successfully saved');
                                filename = $(filenameInput).val();//updating the filename, in case the user renames it one more time
                                $(filenameContainer).text(filename);
                            }
                        }
                    }
                );
            });
            
		},
		cache: false
	});
    
    
}        
        
//this function takes a resource node as argument, displays its name and URI + form to edit its parameters + button to add children + button to remove it
function displayAndChildren(xmlNode){
    var result = displayThis(xmlNode);
    
    
    //if this is a group : recurse
    if($($(xmlNode).children('type')).text() == 'group'){
    //reducer class enables to toggle visibility of children (particular case is: node has an attribute, this attribute is 'fixed', which is not displayed
    //other classes are used for style
        $(result).addClass('hasChild');
        $(result).prepend($('<span>').addClass('glyphicon glyphicon-minus').addClass('reducer'));
        
        //variable containing the texts returned by the call of the function on the children (in a html list)
        var chs = $('<ul>');
        $(xmlNode).children('resource').each(function(){
            $(chs).append(displayAndChildren(this));
        });
        
        result.append(chs);
    
    }
    return result;
}




function displayThis(xmlNode){

    var resourceName = $($(xmlNode).children('name')).text();
    var resourceURI = $(xmlNode).attr('URI');
    var resourceNameContainer = $('<span>').append(resourceName).addClass('resourceName');
    var resourceURIContainer = $('<span>').append(resourceURI).addClass('resourceURI');
    
    
    var resourceEditor = $('<span>').addClass('glyphicon glyphicon-edit resourceEditor').attr('title', _('Edit properties'));
    var resourceRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle resourceRemover').attr('title', _('Remove resource'));
    var resourceAdder = $('<span>').addClass('glyphicon glyphicon-plus resourceAdder').attr('title', _('Add resource'));
    
    var result = $('<li>').attr('id', $(xmlNode).attr('id')).append(resourceNameContainer).append(resourceURIContainer).append(resourceEditor).append(resourceAdder).append(resourceRemover);
    
    $(resourceEditor).click(function(){
        currentResource = xmlNode;
        currentResourceContainer = result;
        
        emptyForm();
        
        //fill the form with available parameters
        $(currentResource).children().each(function(){
            var parameterName = this.nodeName.toLowerCase();
            if(parameterName != 'resource'){
                $('#paramForm #'+parameterName).val($(this).text());
            }
            
            if(parameterName == 'grade' && $(this).text() == 'true'){
                $('#paramForm #'+parameterName).prop('checked','true');
                if(typeof $(this).attr('maxPoints') == 'undefined'){//lowercase issue...
                    $('#paramForm #maxPoints').val($(this).attr('maxpoints'));
                }
                else{
                    $('#paramForm #maxPoints').val($(this).attr('maxPoints'));   
                }
                $('#maxPointsForm').show();
            
            }
        })
        $('#paramForm #URI').val($(currentResource).attr('URI'));
        
        
        //treating orders
        //void form element to clone and use to display orders
        var orderForm = '<div class="orderForm"><span class="glyphicon glyphicon-minus orderRemover" title="Remove order"></span><label for="context">Context: </label><input type="text" name="context"></input><label for="position">Position: </label><input type="number" step="1" name="position"></input></div>';
        
        //if  resource has order tag 
        if($(currentResource).children('order').length > 0){
            $(currentResource).children('order').first().children('context').each(function(){
                var form = $(orderForm).clone()
                $(form).children('input').first().val($(this).text());
                $(form).children('input').last().val($(this).next().text());
                console.log(form);
                $('#orderForms').append(form);
                
                //TODO : remove duplication, same code in resourcesModification.php
                $('.orderRemover').unbind().click(function(){
                    $(this).next().remove();
                    $(this).next().remove();
                    $(this).next().remove();
                    $(this).next().remove();
                    $(this).remove();
                });
            });
        
        }
        
        
        
        
        
        
        
        
        
        
        
        $('#paramModal').modal('show');
    });
    
    $(resourceRemover).click(function(){
        if(confirm('Are you sure ? Any deletion is definitive')){
            $(xmlNode).remove();
            $(result).remove();
        }
    });
    
    $(resourceAdder).click(function(){
        var newResource = $(emptyResource).clone();
        $(xmlNode).append($(newResource));
        
        currentResource = newResource;
        var newContent = displayAndChildren(xmlNode)
        
        $(result).children().last().replaceWith(newContent.children().last());
        currentResourceContainer = $(result).children('ul').children().last();
        
        emptyForm();
        $('#paramModal').modal('show');
        
        
    });
    
    return result;
}

$('#paramModalSaver').click(function(){
    //fill the resource with non-void inputs
    $('#paramForm').children().each(function(){
        if($(this).attr('name') != '' && $(this).val()!=''){
            var input = this;
            var parameterName = $(input).attr('name').toLowerCase();
            if(parameterName != 'uri'){
                if($(currentResource).children(parameterName).length != 0){
                    $(currentResource).children(parameterName).each(function(){
                        $(this).text($(input).val());
                    });
                }
                else{
                    var parameterElement = $('<'+parameterName+'>').text($(input).val());
                    $(currentResource).append(parameterElement);
                }
            }
        }
    })
    $(currentResource).attr('URI', $('#paramForm #URI').val());
    
    if($('#paramModal #grade').prop('checked')){
        if($(currentResource).children('grade').length > 0){//grade tag already here
            $(currentResource).children('grade').first().text('true');
            $(currentResource).children('grade').first().attr('maxPoints', $('#paramForm #maxPoints').val());
        }
        else{//create the tag
            var gradeElement = $('<grade>').text('true').attr('maxPoints', $('#paramForm #maxPoints').val());
            
            $(currentResource).append(gradeElement);
        }
    }
    else{
        $(currentResource).children('grade').each(function(){
            $(this).text('false');
        });
    }
    
    
    
    //updating the name and URI displayed
    $(currentResourceContainer).children().each(function(){
        if($(this).hasClass('resourceName')){
            $(this).text($('#paramForm #name').val())
        }
        
        else if($(this).hasClass('resourceURI')){
            $(this).text($('#paramForm #URI').val())
        }
    });
    
    $('#paramModal').modal('hide');
    
});

function emptyForm(){
    $('#paramModal form').children().each(function(){
        $(this).val('');        
    });
    $('#paramModal #maxPoints').val('');
    $('#maxPointsForm').hide();
    $('#paramModal #grade').prop('checked', false);
    
}