<!DOCTYPE HTML>
<!-- This file uses XMLManipulator in order to enable the user to change the values of the XML file he is using. Name and path of the file are sent by a POST form  -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/XMLDisplay.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
		<div class="container">
			<h1>Modification page:&nbsp;<?php echo $_POST['section']; ?> <small>Currently editing&nbsp;<?php echo $_POST['file']; ?></small></h1>
			<p>You can change the value by clicking on them, giving the wished value in the input and then validating by pressing "enter"</p>
			<p><a href="index.php">Back to main menu</a></p>
			<div id="XMLcontainer"></div>
        </div>
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/xmlManipulator.js"></script>
        <script type="text/javascript">
		
			var file = <?php echo "'".$_POST['path']."/".$_POST['file']."'"; ?>;
		
            manipulateXML(file,'#XMLcontainer', 'modify');
        </script>
    </body>
    
</html>