<!DOCTYPE HTML>
<!-- This file uses XMLManipulator in order to enable the user to change the values of the XML file he is using. Name and path of the file are sent by a POST form  -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/XMLManipulator.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
    <?php 
    //I store these variables here to call them later...could be easier to use directly $_POST['path'],... in the rest of the script but idiot php tells me these indexes no longer exist in the array (well in fact sometimes there is no problem, but some other times, in EXACTLY the same conditions, it tells me they don't exist (even if it is possible to echo and display these array values in the html page, it doesn't let me use it in the other parts of the script)...
    $path = $_POST['path'];
    $file = $_POST['file'];
    $scales = $_POST['scales'];
    ?>
		<div class="container">
			<h1>Modification page:&nbsp;<?php echo $_POST['section']; ?> <small>Currently editing&nbsp;<?php echo $_POST['file']; ?></small></h1>
			<p>You can change the value by clicking on them, giving the wished value in the input and then validating by pressing "enter". Don't forget to save your file once you're finished.</p>
			<p><a href="index.php">Back to main menu</a></p>
			<div id="XMLcontainer"></div>
        </div>
        
        <div id='scalesContainer'></div>
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/scaleDisplayers.js"></script>
        <script type="text/javascript" src="js/XMLManipulator.js"></script>
        <script type="text/javascript" src="translation/translate.js"></script>
        <script type="text/javascript" src="translation/icu.js"></script>
        <script type="text/javascript">
        $(function(){    
            
            var translationFile = 'translation/fr.json';
            $.ajax({//loading translation
                type: "GET",
                url: translationFile,
                success: function(data){
                    _.setTranslation(data);
        
                    var file = <?php echo "'".$path."/".$file."'"; ?>;
                    var scales = <?php echo file_get_contents($scales); ?>;
                    manipulateXML(file,'#XMLcontainer', 'modify','', scales , '#scalesContainer');   
                }
            });
            
        });
        </script>
    </body>
    
</html>