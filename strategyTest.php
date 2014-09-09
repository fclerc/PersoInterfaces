<?php session_start(); 
    require_once 'phphelpers/langFinder.php';
?>
<!DOCTYPE HTML>
<!-- This file enables the user test his strategies, by generating a list of activity, using a profile and contexts. PSOT variables are coming from the home page, section strategy test.  -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
    <?php 
    //I store these variables here to call them later...could be easier to use directly $_POST['path'],... in the rest of the script but idiot php tells me these indexes no longer exist in the array (well in fact sometimes there is no problem, but some other times, in EXACTLY the same conditions, it tells me they don't exist (even if it is possible to echo and display these array values in the html page, it doesn't let me use it in the other parts of the script)...
    if(!isset($_POST['path'])){//values are in the session variable
        $path = $_SESSION['path'];
        $profilepath = $_SESSION['profilepath'];
        $sequenceContextpath = $_SESSION['sequenceContextpath'];
        $liveContextpath = $_SESSION['liveContextpath'];
        $file = $_SESSION['file'];
        $profilefile = $_SESSION['profilefile'];
        $sequenceContextfile = $_SESSION['sequenceContextfile'];
        $liveContextfile = $_SESSION['liveContextfile'];
        $scales = $_SESSION['scales'];
        $section = $_SESSION['section'];
    }
    else{
        $path = $_POST['path'];
        $profilepath = $_POST['profilepath'];
        $sequenceContextpath = $_POST['sequenceContextpath'];
        $liveContextpath = $_POST['liveContextpath'];
        $file = $_POST['file'];
        $profilefile = $_POST['profilefile'];
        $sequenceContextfile = $_POST['sequenceContextfile'];
        $liveContextfile = $_POST['liveContextfile'];
        $scales = $_POST['scales'];
        $section = $_POST['section'];
    }
    ?>
		<div class="container">
			<h1><span class="toTranslate">strategyTest.h1</span><small><span id="currentFile">strategyTest.currentFileIntro</span><span id="currentFileName"><?php echo $file; ?></span></small></h1>
            <p><span class="toTranslate">You are using files: </span></br/>
                <?php
                    echo '<b><span class="toTranslate">Profile</span></b>: '.$profilefile.'<br/>';
                    echo '<b><span class="toTranslate">Live Context</span></b>: '.$liveContextfile.'<br/>';
                    echo '<b><span class="toTranslate">Sequence Context</span></b>: '.$sequenceContextfile.'<br/>';
                ?>
            </p>
            
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<p id="generalInstructions">strategyTest.instructions</p>
			
			<div id="boussole">
			
			<?php
                include 'phphelpers/ConsequenceGenerator.class.php';
                include 'phphelpers/ConditionChecker.class.php';
                include 'phphelpers/ActivitiesGenerator.class.php';
                
                //getting the file paths
				$strategyPath = $path.$file;
				$profilePath = $profilepath.$profilefile;
				$sequenceContextPath = $sequenceContextpath.$sequenceContextfile;
				$liveContextPath = $liveContextpath.$liveContextfile;
                //creating the generator from the  strategy
				$generator = new ActivitiesGenerator($strategyPath);
                //generating the list of activities from the different elements.
				echo $generator->generate($profilePath, $sequenceContextPath, $liveContextPath);
				
				
			
			
			?>
			
			</div>
			
        </div>
		
		
       
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
       
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
                   }
            });
        });
        </script>
    </body>
    
</html>