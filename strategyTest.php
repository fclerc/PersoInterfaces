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
			<h1><span class="toTranslate">strategyTest.h1</span><span id="sectionName"><?php echo $section; ?></span><small><span id="currentFile">strategyTest.currentFileIntro</span><span id="currentFileName"><?php echo $file; ?></span></small></h1>
			<p id="generalInstructions">strategyTest.instructions</p>
			<p><a href="index.php" id="mainLink">common.back</a></p>
			
			
			
			<?php
			
				//loading strategy file
				$strategy = new DOMDocument();
				$strategy->load($path.$file);
				//loading other files used for test
				$exploitedProfileFile = $strategy->getElementsByTagName('exploitedProfile')->item(0)->nodeValue;
				$exploitedContextFile = $strategy->getElementsByTagName('exploitedContext')->item(0)->nodeValue;
				$pedagogicalPropertiesFile = $strategy->getElementsByTagName('pedagogicalProperties')->item(0)->nodeValue;
				
				$profile = new DOMDocument();
				$profile->load($exploitedProfileFile);
				
				$liveContext = new DOMDocument();
				$liveContext->load($exploitedContextFile);
			
				$pedaProp = new DOMDocument();
				$pedaProp->load($pedagogicalPropertiesFile);
			
			
			//TODO ; use $_POST
				$seqContext = new DOMDocument();
				$seqContext->load('data/teacher/sequenceContexts/Sequence1.xml');
			
				
				$rules = $strategy->getElementsByTagName('rule');
				
				//this array will contain the rules that apply to the learner.
				//elements have the form 'then' or 'else' => rule    TODO change if changed
				$rulesToApply = array();
				foreach ($rules as $rule){
					$ifElement = $rule->getElementsByTagName('if')->item(0);
					$condition = $ifElement->childNodes->item(0);
					var_dump(checkCondition($condition, $profile, $liveContext));
					
				
				}
			
				
				
				function checkCondition($condition, $profile, $liveContext){
					$xpathProfile = new DOMXPath($profile);
					
					if(strToLower($condition->tagName) == 'constraint'){
						$indicatorId = $condition->getElementsByTagName('indicator')->item(0)->nodeValue;
						$indicator = $xpathProfile->query("//*[@id='$indicatorId']")->item(0);
						$referenceValue;
						if($condition->getElementsByTagName('referenceValue')->item(0) != null){
							$referenceValue = $condition->getElementsByTagName('referenceValue')->item(0)->nodeValue;
						}
						else{//because of jQUery, case is not always respected
							$referenceValue = $condition->getElementsByTagName('referencevalue')->item(0)->nodeValue;
						}
						
						$operator  = $condition->getElementsByTagName('operator')->item(0)->nodeValue;
						if($operator == '='){
							return ($referenceValue == $indicator->nodeValue);
						}
						elseif($operator == '!='){
							return $referenceValue != $indicator->nodeValue;
						}
						else if($operator == '>'){//todo : scale and jpin with next
							return $referenceValue > $indicator->nodeValue;
						}
						else if($operator == '<'){//todo : scale
							return $referenceValue < $indicator->nodeValue;
						}
						
						
					}
				}
				
				
			?>
			
			
			
			
			
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