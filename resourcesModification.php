<?php session_start(); 
    require_once 'phphelpers/langFinder.php';
?>
<!DOCTYPE HTML>
<!-- This file uses XMLManipulator in order to enable the user to change the values of the XML file he is using. Name and path of the file are sent by a POST form  -->
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
              <div class="modal-body toTranslate">
                Nothing
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default toTranslate" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary toTranslate" id="paramModalSaver">Save changes</button>
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
            
            var translationFile = 'translation/'+<?php echo "'".$lang."'"; ?>+'.json';
            $.ajax({//loading translation
                type: "GET",
                url: translationFile,
                success: function(data){
                    _.setTranslation(data);
                    
                    $('.toTranslate, #currentFile, #generalInstructions, #mainLink, #sectionName').each(function(){
                        $(this).text(_($(this).text()));
                    });
                    
                    
                    
                    var file = <?php echo "'".$path."/".$file."'"; ?>;
                    var scales;
                    //var scales = <?php //if($scales!=''){echo file_get_contents($scales);}else{echo '""';} ?>;
                    manipulateResourcesXML(file,'#XMLcontainer', scales , '#scalesContainer', "#currentFileName");   
                }
            });
        });
        </script>
    </body>
    
</html>