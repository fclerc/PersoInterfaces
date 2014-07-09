<?php session_start(); 
    require_once 'phphelpers/langFinder.php';
?>
<!DOCTYPE HTML>
<!-- This file enables the user to modify the content of the resources file : adding resources and editing their parameters.  -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        <link href="css/resourcesManipulator.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
    <?php 
    //I store these variables here to call them later...could be easier to use directly $_POST['path'],... in the rest of the script but idiot php tells me these indexes no longer exist in the array (well in fact sometimes there is no problem, but some other times, in EXACTLY the same conditions, it tells me they don't exist (even if it is possible to echo and display these array values in the html page, it doesn't let me use it in the other parts of the script)...
    if(!isset($_POST['path'])){//values are in the session variable
        $path = $_SESSION['path'];
        $file = $_SESSION['file'];
        $scales = $_SESSION['scales'];
        $section = $_SESSION['section'];
    }
    else{
        $path = $_POST['path'];
        $file = $_POST['file'];
        $scales = $_POST['scales'];
        $section = $_POST['section'];
    }
    ?>
		<div class="container">
			<h1><span class="toTranslate">resourcesModification.h1</span><span id="sectionName"><?php echo $section; ?></span><small><span id="currentFile">resourcesModification.currentFileIntro</span><span id="currentFileName"><?php echo $file; ?></span></small></h1>
			<p id="generalInstructions">resourcesModification.instructions</p>
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<div id="XMLcontainer"></div>
        </div>
        
        <div id='scalesContainer'></div>
        
        
        
        <!-- Modal -->
        <div class="modal fade" id="paramModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title toTranslate">paramModal.h4</h4>
              </div>
			  <!-- The form which is displayed when user edits or create a new resource -->
              <div class="modal-body">
                <form id='paramForm'>
                    <label for="name">Name: </label><input class="form-control" type='text' name="name" id="name" /><br/>
                    <label for="URI">URI: </label><input class="form-control" type='text' name="URI" id="URI" /><br/>
                    
                    <label for="type">Type: </label>
                    <select class="form-control" name='type' id='type'>
                        <option></option>
                        <option>video</option>
                        <option>text</option>
                        <option>audio</option>
                        <option>quiz</option>
                        <option>assignment</option>
                        <option>trainingExercise</option>
                        <option>forum</option>
                        <option>wiki</option>
                        <option>group</option>
                        <option>other</option>
                    </select><br/>
                    
                    <label for='status'>Status: </label>
                    <select class="form-control" name='status' id='status'>
                        <option></option>
                        <option>Mandatory</option>
                        <option>Optional</option>
                        <option>Bonus</option>
                    </select><br/>
                    
                    <label for='difficulty'>Difficulty: </label>
                    <select class="form-control" name='difficulty' id='difficulty'>
                        <option></option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select><br/>
                    
                    <label for="sequence">Sequence: </label><input class="form-control" type='number' step='1' name="sequence" id="sequence" /><br/>
                    <label for="length">Length: </label><input class="form-control" type='number' step='1' name="length" id="length" /><br/>
                    <label for="categories">Categories: </label><input class="form-control" type='text' name="categories" id="categories" /><br/>
                    
                    <label for="grade">Grade?</label><input type="checkbox" name='grade' id='grade' />
                    <div id="maxPointsForm">
                        <label for="maxPoints">Maximum of points: </label><input class="form-control" type="number" step="1" name="maxPoints" id="maxPoints"/>
                    </div>
                    
                    <div id="orderForms">Orders
                        <span class="glyphicon glyphicon-plus orderAdder" title="Add order"></span>
                    </div>
                    
                    <label for="description">Description: </label> 
                    <textarea name="description" class="form-control" id="description"></textarea>
                    
                    
                    
                    
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default toTranslate" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary toTranslate" id="paramModalSaver">Validate</button>
              </div>
            </div>
          </div>
        </div>
        
        
        
        
        
        
        
        
        
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/resourcesManipulator.js"></script>
        <script type="text/javascript" src="js/scaleDisplayers.js"></script>
        <script type="text/javascript" src="translation/translate.js"></script>
        <script type="text/javascript" src="translation/icu.js"></script>
        <script type="text/javascript">
        $(function(){  
			//file used in order to display scales and documentation about the parameters.
            var pedagogicalPropertiesFilename = <?php if($scales!=''){echo "'".$scales."'";}else{echo '""';} ?>;
            var translationFile = 'translation/'+<?php echo "'".$lang."'"; ?>+'.json';
            $.ajax({//loading translation
                type: "GET",
                url: translationFile,
                success: function(data){
                    _.setTranslation(data);
                    
                    $('.toTranslate, #currentFile, #generalInstructions, #mainLink, #sectionName').each(function(){
                        $(this).text(_($(this).text()));
                    });
                    
                    $.ajax({//loading translation
                        type: "GET",
                        url: pedagogicalPropertiesFilename,
                        success: function(scalesData){
                            scales = $(scalesData);
                    
                    var file = <?php echo "'".$path."/".$file."'"; ?>;
                    manipulateResourcesXML(file,'#XMLcontainer', "#currentFileName");  
                    
                    //displaying documentation and scales in the form (next to each label of the inputs)
                    $('#paramForm').children().each(function(){
                        var label = this;
						//the 'for' attribute contains the name of the parameter, we search it in the pedagogical properties file, and (if found) add popovers for click and hover
                        if(typeof $(this).attr('for') != 'undefined'){ 
                            var parameterName = $(this).attr('for');
                            $(scales).children().find('Parameter').each(function(){
                                if($(this).children('Name').text().toLowerCase() == parameterName){
                                
                                    var parameterComment = $($(this).children('Comment')[0]).text();
                                    var parameterScale = $(this).children().last()
                                    
                                    if(parameterComment != ''){
                                        var commentPopover = $('<span>').addClass('glyphicon glyphicon-info-sign commentPopover').attr('title', _('Click for more information'));
                                        $(commentPopover).click(function(){
                                            alert(parameterComment);
                                        });
                                        
                                        $(commentPopover).hover(function(){
                                            $('#scalesContainer').empty();
                                            displayParameterScale(parameterScale, '', '#scalesContainer', false);
                                            $('#scalesContainer').show();
                                        },
                                        function(){
                                            $('#scalesContainer').hide();
                                        
                                        });
                                        
                                        $(label).after(commentPopover);
                                    }
                                    
                                    
                                    return false;
                                }
                            
                            });
                            
                        }
                    
                    
                    });
                    
					//displaying the maxPoints input only if 'grade' is checked
                    $('#grade').change(function(){
                        if($('#grade').is(':checked')){
                            $('#maxPointsForm').show('300');
                        }
                        else{
                            $('#maxPointsForm').hide('300');
                        }
                    
                    });
                    
					
					//next part enables to add and remove order inputs.
                    //the basic form of an order form (2 inputs)
                    var orderForm = '<div class="orderForm"><span class="glyphicon glyphicon-minus orderRemover" title="Remove order"></span><label for="context">Context: </label><input type="text" name="context"/><label for="position">Position: </label><input type="number" step="1" name="position"/></div>';
                    
                    $('.orderAdder').click(function(){
                        $('#orderForms').append($(orderForm).clone());
                    
                        $('.orderRemover').unbind().click(function(){
                            $(this).next().remove();
                            $(this).next().remove();
                            $(this).next().remove();
                            $(this).next().remove();
                            $(this).remove();
                        });
                    });
                    
                    }});
                }
            });
        });
        </script>
    </body>
    
</html>