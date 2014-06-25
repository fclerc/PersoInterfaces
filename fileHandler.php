<!DOCTYPE HTML>
<!-- This file is simply a demo of how to use the XMLManipulator
You can change the call to manipulateXML to make some tests. See xmlManipulator.js for details about the way to call it. -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        
        
    </head>
    
    <body>
		<div class="container">
			<?php
				include 'phphelpers/sectionsDictionnaries.php';
				//Possibilities are 'profile', 'liveContext', 'strategy', 'sequenceContext'.
				$section = 'profile';
				$files = scandir($paths[$section]);
			?>
			
			<h1><?php echo $h1s[$section]; ?></h1>
			<p><?php echo $instructions[$section]; ?></p>
				
			
		
			<div id="fileFormContainer"></div>
		
		
		</div>
		
		<script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
		<script type="text/javascript">
            $(function(){
				var files = <?php echo json_encode($files); ?>;
				var action =  <?php echo "'".$interfaces[$section]."'"; ?>;
				var form = $('<form>').attr('action', action);
				
				var fileSelect = $('<select>').addClass('form-control').attr('name', 'file');
				$(files).each(function(id, file){
					if(file != '.' && file !='..'){
						$(fileSelect).append($('<option>').append(file));
					}
				});
				
				var formSubmiter = $('<input>').attr('type', 'submit').attr('Value', 'Open');

				console.log(formSubmiter);
				$(form).append(fileSelect);
				$(form).append(formSubmiter);
				
				$('#fileFormContainer').append(form);
			});
        </script>
    </body>
    
</html>





<?php








?>