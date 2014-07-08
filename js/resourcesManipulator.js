/*
Script used to display and modify the parameters and the whole tree of resources



*/



//cloned when addding a new resource
var emptyResource = '<resource URI=""><name></name><type></type><status></status><order></order><difficulty></difficulty><sequence></sequence><grade></grade><length></length><categories></categories><description></description></resource>';

//when editing parameters of a resource, 
var currentResource;

//container : the id of the container of the displayed XML, for example : '#MyXMLContainer'. Used as a sort of namespace for data manipulation in case of using several times this function in the same page (see xml[container] or selectors to define events).
//scales : json with information about the indicators. WARNING : scalesDisplayers have to be loaded before.
//scaleContainer : the html element you want the scale to be displayed
//filenameContainer : if elements in your page display the name of the file, give their selector in order to have name changed if the user renames his file.
function manipulateResourcesXML(filepath, container, scales = '', scaleContainer = '', filenameContainer = ''){
    return $.ajax({
		type: "GET",
		url: filepath,
		success: function(data){//get the xml document
			var xml = [];
			xml[container]=$(data);//load xml tree
			
			//going recursively through the resources xml, and displaying its content
			$(container).append($('<div>').addClass('resourcesContainer').addClass(filepath.split('.').join("")).append(displayAndChildren($($(xml[container]).children('resourcesStructure')).children('resource').first()[0], scales, scaleContainer) ));
			
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
			
			
			//If element can be modified :
			//-on click : replace it by input containing the value
			//-when enter on the input : replace input by simple text with the new value
			$(container +' .value').click(function(event){
				var elem = event.target;
				var value = $(elem).html();
				
				/* if(mode == 'modify'){
				//the target can be 2 things : the span (thus check if it doesn't already contain an input), or the input (thus don't try to add a new input into this)
					if($(elem).children('input').length === 0 && $(elem).prop('tagName')!='INPUT'){//we have to add an input
						input=$('<input>').attr("type", "text").attr('value', value);
						if(value == '&nbsp;'){//if the value is just the space we use to always hae span with a min-width : don't display the entity of the space
							$(input).attr('value', '');
						}
						$(elem).html(input);
						$(input).select();
						
						$(input).keyup(function (event) {//event when submiting content of input : replace by plain text and modify xml tree, based on id.
							if (event.keyCode == 13) {
								//replacing input by plain text
								var input = event.target;
								var value = $(input).prop('value');
								var id;
								if($(input).parent().hasClass('attribute')){
									//finding the right element having this attribute
									var data = $(input).parent().parent().attr('id').split('--');
									id = data[0];
									var attribute = data[1];
									var valueToDisplay;
									if(value === ''){//never let a span empty, otherwise it won't be possible to click on it
										valueToDisplay = '&nbsp';
									}
									else valueToDisplay = value;
									$(input).parent().html(valueToDisplay);
									$(xml[container]).find('[id="' + id + '"]').attr(attribute, value);
								}
								
								else{//replace the value of an element
									id = $(input).parent().parent().attr("id");//corresponding id in the xml tree
									
									var valueToDisplay;
									if(value === ''){//never let a span empty, otherwise it won't be possible to click on it
										valueToDisplay = '&nbsp';
									}
									else valueToDisplay = value;
									
									$(input).parent().html(valueToDisplay);
									
									//modifying value in XML tree
									$(xml[container]).find('[id="' + id + '"]').text(value);
								}
							}
						});
					}
				} */
				
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
function displayAndChildren(xmlNode, scales, scaleContainer){
    var result = displayThis(xmlNode, scales, scaleContainer);
    
    
    //if this is a group : recurse
    if($($(xmlNode).children('type')).text() == 'group'){
    //reducer class enables to toggle visibility of children (particular case is: node has an attribute, this attribute is 'fixed', which is not displayed
    //other classes are used for style
        $(result).addClass('hasChild');
        $(result).prepend($('<span>').addClass('glyphicon glyphicon-minus').addClass('reducer'));
        
        //variable containing the texts returned by the call of the function on the children (in a html list)
        var chs = $('<ul>');
        $(xmlNode).children('resource').each(function(){
            $(chs).append(displayAndChildren(this, scales, scaleContainer));
        });
        
        result.append(chs);
    
    }
    return result;
}




function displayThis(xmlNode, scales, scaleContainer){

    var resourceName = $($(xmlNode).children('name')).text();
    var resourceURI = $(xmlNode).attr('URI');
    var resourceNameContainer = $('<span>').append(resourceName).addClass('resourceName');
    var resourceURIContainer = $('<span>').append(resourceURI).addClass('resourceURI');
    
    var resourceEditor = $('<span>').addClass('glyphicon glyphicon-edit resourceEditor').attr('title', _('Edit properties'));
    $(resourceEditor).click(function(){
        currentResource = xmlNode;
        
        //fill the input with available parameters
        $(currentResource).children().each(function(){
            var parameterName = this.nodeName.toLowerCase();
            if(parameterName != 'resource'){
                $('#paramForm #'+parameterName).val($(this).text());
            }
        })
        $('#paramForm #URI').val($(currentResource).attr('URI'));
        
        $('#paramModal').modal('show');
    });
    
    var resourceRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle resourceRemover').attr('title', _('Remove resource'));
    $(resourceRemover).click(function(){
        if(confirm('Are you sure ? Any deletion is definitive')){
            $(xmlNode).remove();
            $(result).remove();
        }
    });
    
    var resourceAdder = $('<span>').addClass('glyphicon glyphicon-plus resourceAdder').attr('title', _('Add resource'));
    $(resourceAdder).click(function(){
        //TODO later
    });
    
    
    
    var result = $('<li>').attr('id', $(xmlNode).attr('id')).append(resourceNameContainer).append(resourceURIContainer).append(resourceEditor).append(resourceAdder).append(resourceRemover);
    
    /* if(scales !== ''){//if we want to display the scales TODO : use mode (also for attributes display)
        var popoverTitleInfo = 'Click for more information'
        if(typeof window._ != "undefined"){//if translation object is set, translate the nodeName
            popoverTitleInfo = _(popoverTitleInfo);
        }
        var commentPopover = $('<span>').addClass('glyphicon glyphicon-info-sign commentPopover').attr('title', popoverTitleInfo);
        $(commentPopover).hover(function(){
            $(scaleContainer).empty();
            displayIndicatorScale(untranslatedNodeName, scaleContainer, $(xmlNode).attr('id'), scales, false);
            $(scaleContainer).show();
        },
        function(){
            $(scaleContainer).hide();
        });
        if(scales[untranslatedNodeName]){
            if(scales[untranslatedNodeName].documentation){
                $(commentPopover).click(function(){
                    alert(scales[untranslatedNodeName].documentation);
                });
            }
            $(result).append(commentPopover);
        }
    } */
    return result;
}

$('#paramModalSaver').click(function(){
    
    //fill the resource with non-void inputs
    $('#paramForm').children().each(function(){
    console.log('o1');
        if($(this).attr('name') != '' && $(this).val()!=''){console.log('og');
            var input = this;
            var parameterName = $(input).attr('name').toLowerCase();
            if(parameterName != 'resource'){
                $(currentResource).children(parameterName).each(function(){
                    console.log(this);
                    $(this).text($(input).val());
                });
            }
        }
    })
    $(currentResource).attr('URI', $('#paramForm #URI').val());
});