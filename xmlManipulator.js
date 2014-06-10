/*
Script used to display in web browser the values contained in an XML file. Modification is possible in mode = "modify"

In mode 'select', "leafValueReading" events will be triggered, containing the id of the xml leaf concerned, and the value it contains.



*/






//mode : 'modify', 'select'
//container : the id of the container of the displayed XML, for example : '#MyXMLContainer'. Used as a sort of namespace for data manipulation in case of using several times this function in the same page (see xml[container] or selectors to define events).
//reader : "leafValueReading" events will be triggered, and reader will be your own element (on your web page) that will trigger these events, and then treat them (to display the content on which the user clicked).
function manipulateXML(filename, container, mode, reader){
    id = 0; //used to add ids to ul and li tags...not yet used

    
    $.get(filename, function(data){//get the xml document
        var xml = new Array;
        xml[container]=$(data);//load xml tree
        
        
        //going recursively through the xml, and displaying its content
        $(container).append($('<div>').addClass('XMLContainer').addClass(filename.split('.').join("")).append(displayAndChildren($(xml[container]).children().first()[0])));
    
        
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
            //the target can be 2 things : the span (thus check if it doesn't already contain an input), or the input (this don't try to add a new input into this)
                if($(elem).children('input').length == 0 && $(elem).prop('tagName')!='INPUT'){
                    input=$('<input>').attr("type", "text").attr('value', value);
                    $(elem).html(input);
                    $(input).select();
                    
                    $(input).keyup(function (event) {//event when submiting content of input : replace by plain text and modify xml tree, based on id.
                        if (event.keyCode == 13) {
                            //replacing input by plain text
                            var input = event.target;
                            var value = $(input).prop('value');
                            var id = $(input).parent().attr("id");//corresponding id in the xml tree
                            $(input).parent().html(value);
                            
                            //modifying value in XML tree
                            $(xml[container]).find('[id="' + id + '"]').text(value);
                        }
                    });
                }
            }
            
            
            else{
                //alert($(event.target).html());
                var value = $(event.target).html();
                var id = $(event.target).attr('id');
                $(reader).trigger("leafValueReading",  [value, id, container]);
            }
            
            return false;
        });                   

        if(mode=='modify'){
            $(container).append($('<button>').addClass('btn btn-info').attr('id', "XMLSaveButton").html("Save modifications"));
            //<button type="button" class="btn btn-info" id="XMLSaveButton">Save modifications</button>
        
        }
        
        
        $(container +' #XMLSaveButton').click(function(){//using ajax to store the xml on the server.
            xmlS = (new XMLSerializer()).serializeToString(xml[container][0]);console.log(xmlS);
            $.post('saveXMLDocument.php', { file: filename , data: xmlS}, 
                function(data, txt, jqXHR){
                    if(txt=="success"){
                        alert('Your data have been successfully saved');
                    }
                }
            );
        });
    });
}        
        
    //this function takes a XML node as argument, and returns an element <li> containing his Name, and:
    //-if it has children: the list of its children
    //-if it has no child: display  its value
function displayAndChildren(xmlNode, mode){
    //for each node : add it as an item to the list of its parent's elements (except for first element)
    var result = $('<li>').append(xmlNode.nodeName).attr('id', 'li'+id);
    
    if($(xmlNode).children().length>0){
    //if the node has children : display the list of these children.
    //reducer class enables to toggle visibility of children
    //other classes are used for style
        $(result).addClass('hasChild');
        $(result).wrapInner($('<span>'));
        $(result).prepend($('<span>').addClass('glyphicon glyphicon-minus').addClass('reducer'));
        //variable containing the texts returned by the call of the function on the children (in a html list)
        var chs = $('<ul>').attr('id', 'ul'+id);                        
        $(xmlNode).children().each(function(){
            $(chs).append(displayAndChildren(this));
        });
        result.append(chs);
    
    }
    
    else{
    //if no child: display the node value, with class indicating you can modify it
        result.append(': ').append($('<span>').append($(xmlNode).html()).attr("id", $(xmlNode).attr("id")).addClass("value"));
        
    }
    id++;
    return result;
}