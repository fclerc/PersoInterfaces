<?php 

/*
Main page of the application
Choice is given to the user to go to the other interfaces and select the files used.


*/

session_start();
require_once 'phphelpers/langFinder.php';
    
?>
<!DOCTYPE HTML>
<html>
	<?php
        //the data used to display the different sections of the main page (strategy, sequenceContext,...).
        /*
        For each section it contains : 
            h2 : title of the section
            instruction : text under the title
            path : the path to the folder containing the files corresponding to this section
            interface : the name of the interface to use for this section
            schema : if relevant, the schema corresponding to the data in this section (for the profile section : path to the profile.xsd)
            scales : path to the file used to display more informations to the user in the interface
        */
		$data = json_decode(file_get_contents('resources/filePageData.json'));
        
        //adding for each section the list of files available (for example the list of strategy files in data/teacher/strategies)
		$sections = $data->sections;
		$files = array();
		foreach($sections as $section){
			$path = $data->path->$section;
			$sectionFiles = scandir($path);
			$files[$section] = $sectionFiles;
		}
		$data->files = $files;
	
	?>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="css/main.css" type="text/css" rel="stylesheet"/>
	</head>
    
    <body>
        <div class="container">
            <h1>fileChoice.h1</h1>
            <p>fileChoice.explanations</p>
            
            <hr/>
            <a href="statistics.php" class="toTranslate btn btn-primary" id="statsLink">fileChoice.statistics.link</a><br/>
            <a href="sequence_association.php" class="toTranslate btn btn-primary" id="associationsLink">fileChoice.association.link</a>
            
            
            <div id="languageChoice"><span>languageChoice</span><a href="index.php?lang=fr"><img src="img/fr.png"/></a><a href="index.php?lang=en"><img src="img/gb.png"/></a></div>
            <?php 
                //if a file has been removed by the user just before, display a message
                if(isset($_SESSION['fileRemoved'])){
                    if($_SESSION['fileRemoved']){
                        echo '<p class="alert alert-info" id="fileRemovedSuccess">File successfully removed</p>';
                        $_SESSION['fileRemoved'] = false;
                    }
                }
            ?>
            
            <div id="sectionsContainer"></div>
            
        </div>
	</body>
    
	<script type="text/javascript" src="js/jquery-2.1.1.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="translation/translate.js"></script>
    <script type="text/javascript" src="translation/icu.js"></script>
	
	<script type="text/javascript">
		$(function(){
        var translationFile = 'translation/'+<?php echo "'".$lang."'"; ?>+'.json';
        $.ajax({//loading translation
            type: "GET",
            url: translationFile,
            success: function(translationData){
                _.setTranslation(translationData);
			//translating the already displayed content
            $('#fileRemovedSuccess, .container h1, #languageChoice span, p, .toTranslate').each(function(){
                $(this).text(_($(this).text()));
            });
        
        
            //passing the data to js
			var data = <?php echo json_encode($data); ?>;
			var sectionsContainer =$('#sectionsContainer');
            
            //for each section, display informations and form to select file
			$(data['sections']).each(function(i, section){
				var sectionContainer = $('<div>').addClass('section');
                
                //title and instruction
				$(sectionContainer).append($('<h2>').append(_(data['h2'][section])));
				$(sectionContainer).append($('<p>').append(_(data['instruction'][section])));
				
                //form to select file
				var form = $('<form>').attr('action', data['interface'][section]).attr('method', 'POST');
                //creating the select input, and filling with list of files
				var fileSelect = $('<select>').addClass('form-control').attr('name', 'file');
				$(data['files'][section]).each(function(id, file){
					if(file != '.' && file !='..' && file!='empty.xml'){
						$(fileSelect).append($('<option>').append(file));
					}
				});
                
                //transmission of the data to the interface page through hidden inputs
				var pathForm = $('<input>').attr('type', 'hidden').attr('name', 'path').attr('value', data['path'][section]);
				var sectionForm = $('<input>').attr('type', 'hidden').attr('name', 'section').attr('value', section);
				var scalesForm = $('<input>').attr('type', 'hidden').attr('name', 'scales').attr('value', data['scales'][section]);
				var schemaForm = $('<input>').attr('type', 'hidden').attr('name', 'schema').attr('value', data['schema'][section]);
				var actionForm = $('<input>').attr('type', 'hidden').attr('name', 'action').attr('value', data['interface'][section]);
                
                //buttons to open, create, duplicate and delete file
				var fileOpener = $('<input>').attr('type', 'submit').attr('Value', _('Open file')).addClass('btn btn-success').attr('name', 'fileOpener');
				var fileCreator = $('<input>').attr('type', 'submit').attr('Value', _('Create new file')).addClass('btn btn-info').attr('name', 'fileCreator');
				var fileDuplicator = $('<input>').attr('type', 'submit').attr('Value', _('Duplicate file')).addClass('btn btn-primary').attr('name', 'fileDuplicator');
				var fileDeleter = $('<input>').attr('type', 'submit').attr('Value', _('Delete file')).addClass('btn btn-danger').attr('name', 'fileDeleter');
                
                //changing the action page if click on 'create' or 'delete' button : treatment by the fileHandler is required
                $(fileCreator).add(fileDuplicator).click(function(){
                    $(this).closest('form').attr('action', 'phphelpers/fileHandler.php');
                });
                $(fileDeleter).click(function(event){
                    //asking confirmation to the user for deletion
                    if(confirm(_('fileChoice.delete.confirm'))){
                        $(this).closest('form').attr('action', 'phphelpers/fileHandler.php');
                    }
                    else{
                        event.preventDefault();
                    }
                });
                
                //if strategy section test : this is not the only file select to display, thus display a label to explain what the first select is
                if(section == 'strategyTest'){
                    $(form).append($('<label>').append(_('Chose your strategy')).attr('for', 'file'));
                }
                
                //adding all elements to the form
				$(form).append(fileSelect).append(pathForm).append(sectionForm).append(schemaForm).append(scalesForm).append(actionForm);
                
                //if strategy section : display the other select file, to chose a profile, a liveContext and sequenceContext
                if(section == 'strategyTest'){
                    var otherFiles = ['profile', 'liveContext', 'sequenceContext'];
                    //for each type of file, display the select
                    $(otherFiles).each(function(id, name){
                        var label = $('<label>').append(_(name)).attr('for', name+'file')
                        var fileSelect = $('<select>').addClass('form-control').attr('name', name+'file');
                        $(data['files'][name]).each(function(id, file){
                            if(file != '.' && file !='..' && file!='empty.xml'){
                                $(fileSelect).append($('<option>').append(file));
                            }
                        });
                        //passing to the interface the path to the folder
                        var pathForm = $('<input>').attr('type', 'hidden').attr('name', name+'path').attr('value', data['path'][name]);
                        
                        $(form).append(label).append(fileSelect).append(pathForm);
                    });
                }
                
                //adding buttons (in case of strategy test, buttons other than open make no sense)
				$(form).append(fileOpener)
                if(section != 'strategyTest'){
                    $(form).append(fileCreator).append(fileDuplicator).append(fileDeleter);
				}
				$(sectionContainer).append(form);
				
				
				//Adding generated html to the sectionsContainer
				if(i%2 == 0){//new row
					$(sectionsContainer).append($('<div>').addClass('row').append(sectionContainer));
				}
				else{//append last row
					$(sectionsContainer).children('div').last().append(sectionContainer);
				}
			});
		
        }});//translation file
		});//jQuery
	
	</script>
	
</html>