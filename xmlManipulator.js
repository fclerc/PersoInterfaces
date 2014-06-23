/*
Script used to display in web browser the values contained in an XML file. Modification is possible in mode = "modify"

In mode 'select', "leafValueReading" events will be triggered, containing the id of the xml leaf concerned, and the value it contains.



*/






//mode : 'modify', 'select'
//container : the id of the container of the displayed XML, for example : '#MyXMLContainer'. Used as a sort of namespace for data manipulation in case of using several times this function in the same page (see xml[container] or selectors to define events).
//reader : "leafValueReading" events will be triggered, and reader will be your own element (on your web page) that will trigger these events, and then treat them (to display the content on which the user clicked).
function manipulateXML(filename, container, mode, reader){
    
    return $.ajax({
		type: "GET",
		url: filename,
		success: function(data){//get the xml document
			var xml = [];
			xml[container]=$(data);//load xml tree
			
			
			//going recursively through the xml, and displaying its content
			$(container).append($('<div>').addClass('XMLContainer').addClass(filename.split('.').join("")).append(displayAndChildren($(xml[container]).children().first()[0], mode) ));
			
			//for elements having list below them : toggle visibility of this list when clicking on the element
			$(container +' .reducer').click(function(event){
				var toToggle = $(event.target).next().next();
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
				
				if(mode == 'modify'){
				//the target can be 2 things : the span (thus check if it doesn't already contain an input), or the input (thus don't try to add a new input into this)
					if($(elem).children('input').length === 0 && $(elem).prop('tagName')!='INPUT'){//we have to add an input
						input=$('<input>').attr("type", "text").attr('value', value);
						
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
									$(input).parent().html(value);
									$(xml[container]).find('[id="' + id + '"]').attr(attribute, value);
								}
								
								else{//replace the value of an element
									id = $(input).parent().parent().attr("id");//corresponding id in the xml tree
									$(input).parent().html(value);
									
									//modifying value in XML tree
									$(xml[container]).find('[id="' + id + '"]').text(value);
								}
							}
						});
					}
				}
				
				return false;
			});                   

            $(container +' .elementName').click(function(event){
				var elem = event.target;
                var id = $(event.target).parent().attr('id');
                var value = $($(event.target).parent().find('.value')[0]).html();
                
                $(reader).trigger("leafValueReading",  [value, id, container]);
                
            });
            
            
			if(mode=='modify'){
				$(container).append($('<button>').addClass('btn btn-info').attr('id', "XMLSaveButton").append($('<span>').addClass('glyphicon glyphicon-floppy-disk')).append("Save modifications"));
			
			
			
			
                $(container +' #XMLSaveButton').click(function(){//using ajax to store the xml on the server.
                    var xmlS = (new XMLSerializer()).serializeToString(xml[container][0]);
                    $.post('saveXMLDocument.php', { file: filename , data: xmlS}, 
                        function(data, txt, jqXHR){
                            if(txt=="success"){
                                alert('Your data have been successfully saved');
                            }
                        }
                    );
                });
            }
		},
		cache: false
	});
    
    
}        
        
    //this function takes a XML node as argument, and returns an element <li> containing his Name, and:
    //-if it has children: the list of its children
    //-if it has no child: display  its value
function displayAndChildren(xmlNode, mode){
    //for each node : add it as an item to the list of its parent's elements (except for first element)
    var idText='';//uncomment next line to display the id
    //var idText=' (' + $(xmlNode).attr('id') + ') ';
    var nodeName = xmlNode.nodeName;
    if(typeof window._ != "undefined"){//if translation object is set, translate the nodeName
        nodeName = _(nodeName);
    }
    var result = $('<li>').attr('id', $(xmlNode).attr('id')).append($('<span>').append(nodeName + idText).addClass('elementName'));
    
    if($(xmlNode).children().length>0 || xmlNode.attributes.length > 1){
    //if the node has children : display the list of these children.
    //reducer class enables to toggle visibility of children
    //other classes are used for style
        $(result).addClass('hasChild');
        $(result).prepend($('<span>').addClass('glyphicon glyphicon-minus').addClass('reducer'));
        
        if($(xmlNode).children().length == 0){//if it has attributes, but no child : its text value has to be displayed
            result.append(': ').append($('<span>').append($(xmlNode).html()).addClass("value"));
        }
        
        //variable containing the texts returned by the call of the function on the children (in a html list)
        var chs = $('<ul>');
        
        $.each(xmlNode.attributes, function(i, attrib){//going through the attibutes
            var attributesToIgnore = ["id", "xmlns:xsi", "xsi:noNamespaceSchemaLocation"];
            if(attributesToIgnore.indexOf(attrib.name) == -1){
                
                var idText='';//uncomment next line to display the id
                //var idText=' (' + $(xmlNode).attr('id') +'--'+ attrib.name + '): ';
                var attribName = attrib.name;
                if(typeof window._ != "undefined"){//if translation object is set, translate the nodeName
                    attribName = _(attribName);
                }
                var txt = $('<li>').attr('id', $(xmlNode).attr('id') +'--'+ attrib.name ).append($('<span>').addClass('elementName').text(attribName + idText)).append(': ');
                $(txt).append($('<span>').append(attrib.value).addClass('attribute value'));
                $(chs).append(txt);
            }
        });
        
        $(xmlNode).children().each(function(){
            $(chs).append(displayAndChildren(this, mode));
        });
        
        result.append(chs);
    
    }
    
    else{
    //if no child: display the node value, with class indicating you can modify it (if mode = modify)
		var valueContainer = $('<span>').append($(xmlNode).html());
		if(mode == 'modify'){
			$(valueContainer).addClass("value");
		}
        result.append(': ').append(valueContainer);
        
    }
    return result;
}