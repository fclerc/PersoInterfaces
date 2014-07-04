<?php session_start();
    require_once 'phphelpers/langFinder.php';
    
?>
<!DOCTYPE HTML>
<html>
	<?php
		$data = json_decode(file_get_contents('resources/filePageData.json'));
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
		<p>fileChoice.links</p>
        <ul>
            <li><a class='toTranslate' href="presentation.php">fileChoice.modelLink</a></li>
            <li><a class='toTranslate' href="http://liris.cnrs.fr/coatcnrs/wiki/doku.php">fileChoice.coatLink</a></li>
            <li><a class='toTranslate' href="http://liris.cnrs.fr/coatcnrs/wiki/doku.php?id=florian_clerc_stage_master_recherche_et_ingenieur_31_mars-_26_septembre#dokuwiki__top">fileChoice.internshipLink</a></li>
            <li><a href="http://youtu.be/8UqVxA7Cl7E">Vidéo de démonstration</a></li>
        </ul>
        <hr/>
        <p>fileChoice.readPresentation</p>
        <div id="languageChoice"><span>languageChoice</span><a href="index.php?lang=fr"><img src="img/fr.png"/></a><a href="index.php?lang=en"><img src="img/gb.png"/></a></div>
        <?php 
            if(isset($_SESSION['fileRemoved'])){//if a file has bee removed, display a message
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
            success: function(data){
                _.setTranslation(data);
			//translating the already displayed content
            $('#fileRemovedSuccess, .container h1, #languageChoice span, p, .toTranslate').each(function(){
                $(this).text(_($(this).text()));
            });
        
        
			var data = <?php echo json_encode($data); ?>;
			var sectionsContainer =$('<div>');
			$(data['sections']).each(function(i, section){
				var sectionContainer = $('<div>').addClass('section');
				$(sectionContainer).append($('<h2>').append(_(data['h2'][section])));
				$(sectionContainer).append($('<p>').append(_(data['instruction'][section])));
				
				var form = $('<form>').attr('action', data['interface'][section]).attr('method', 'POST');
				var fileSelect = $('<select>').addClass('form-control').attr('name', 'file');
				$(data['files'][section]).each(function(id, file){
					if(file != '.' && file !='..' && file!='empty.xml'){
						$(fileSelect).append($('<option>').append(file));
					}
				});
				var pathForm = $('<input>').attr('type', 'hidden').attr('name', 'path').attr('value', data['path'][section]);
				var sectionForm = $('<input>').attr('type', 'hidden').attr('name', 'section').attr('value', section);
				var scalesForm = $('<input>').attr('type', 'hidden').attr('name', 'scales').attr('value', data['scales'][section]);
				var schemaForm = $('<input>').attr('type', 'hidden').attr('name', 'schema').attr('value', data['schema'][section]);
				var actionForm = $('<input>').attr('type', 'hidden').attr('name', 'action').attr('value', data['interface'][section]);
				var fileOpener = $('<input>').attr('type', 'submit').attr('Value', _('Open file')).addClass('btn btn-success').attr('name', 'fileOpener');
				var fileCreator = $('<input>').attr('type', 'submit').attr('Value', _('Create new file')).addClass('btn btn-info').attr('name', 'fileCreator');
				var fileDuplicator = $('<input>').attr('type', 'submit').attr('Value', _('Duplicate file')).addClass('btn btn-primary').attr('name', 'fileDuplicator');
				var fileDeleter = $('<input>').attr('type', 'submit').attr('Value', _('Delete file')).addClass('btn btn-danger').attr('name', 'fileDeleter');
                //changing the action page if need to create / delete files (and not only open it)
                $(fileCreator).add(fileDuplicator).click(function(){
                    $(this).closest('form').attr('action', 'phphelpers/fileHandler.php');
                });
                $(fileDeleter).click(function(event){
                    if(confirm(_('fileChoice.delete.confirm'))){
                        $(this).closest('form').attr('action', 'phphelpers/fileHandler.php');
                    }
                    else{
                        event.preventDefault();
                    }
                });
                
				$(form).append(fileSelect).append(pathForm).append(sectionForm).append(schemaForm).append(scalesForm).append(actionForm);
				$(form).append(fileOpener).append(fileCreator).append(fileDuplicator).append(fileDeleter);
				
				$(sectionContainer).append(form);
				
				
				//Adding content to the sectionsContainer
				if(i%2 == 0){//new row
					$(sectionsContainer).append($('<div>').addClass('row').append(sectionContainer));
				}
				else{//append last row
					$(sectionsContainer).children('div').last().append(sectionContainer);
				}
			});
		
            $('#sectionsContainer').append(sectionsContainer);
        }});//translation file
		});//jQuery
		
		
	
	</script>
	
	
    
</html>