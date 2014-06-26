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
		<h1>Main Menu</h1>
		<p>Quick explanations</p>
		<p>Other links, for example to a deeper explanation about the models</p>
		<div id="sectionsContainer"></div>
    </div>
	</body>
	<script type="text/javascript" src="js/jquery-2.1.1.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	
	
	<script type="text/javascript">
		$(function(){
			var data = <?php echo json_encode($data); ?>;
			var sectionsContainer =$('<div>');
			$(data['sections']).each(function(i, section){
				var sectionContainer = $('<div>').addClass('section');
				$(sectionContainer).append($('<h2>').append(data['h2'][section]));
				$(sectionContainer).append($('<p>').append(data['instruction'][section]));
				
				var form = $('<form>').attr('action', data['interface'][section]).attr('method', 'POST');
				var fileSelect = $('<select>').addClass('form-control').attr('name', 'file');
				$(data['files'][section]).each(function(id, file){
					if(file != '.' && file !='..'){
						$(fileSelect).append($('<option>').append(file));
					}
				});
				var pathForm = $('<input>').attr('type', 'hidden').attr('name', 'path').attr('value', data['path'][section]);
				var sectionForm = $('<input>').attr('type', 'hidden').attr('name', 'section').attr('value', section);
				var scalesForm = $('<input>').attr('type', 'hidden').attr('name', 'scales').attr('value', data['scales'][section]);
				var schemaForm = $('<input>').attr('type', 'hidden').attr('name', 'schema').attr('value', data['schema'][section]);
				var fileOpener = $('<input>').attr('type', 'submit').attr('Value', 'Open').addClass('btn btn-primary').attr('name', 'fileOpener');
				$(form).append(fileSelect).append(pathForm).append(sectionForm).append(schemaForm).append(scalesForm);
				$(form).append(fileOpener);
				
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
		});
		
		
	
	</script>
	
	
    
</html>