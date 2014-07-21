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
		<div class="container">
			<h1><span class="toTranslate">statistics.h1</span></h1>
            
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<p id="generalInstructions">statistics.instructions</p>
			
			<div id="statistics">
			
                <?php
					/*
					Structure : use empty.xml to know the structure of the profile. Then array 'profileElementId' => list_of_all_values.
					Then used to make statistics, displaying all the statistic elements in the right order of the profile (if possible in the form of a tree, like for values modification)
					*/
					
					$pathToProfiles = 'data/learners';
					$learnersFiles = scandir($pathToProfiles);
					$nbOfLearners = count($learnersFiles) - 2;
					$data = array();
					$emptyProfilePath = 'data/teacher/profiles/empty.xml';					
					$emptyProfile= new DOMDocument();
					$emptyProfile->load($emptyProfilePath);
					
					$allElements = $emptyProfile->getElementsByTagName('*');
					foreach($allElements as $element){
						if($element->childNodes->length === 0){
							if($element->tagName != 'id'){// TODO : use array of untreated arguments
								if($element->getAttribute('fixed') != 'true'){
									$elementId = $element->getAttribute('id');
									$data[$elementId] = array();
								}
							}
						}
					}
					
					foreach($learnersFiles as $learnerFile){
						if($learnerFile != '.' && $learnerFile != '..'){
							$profile= new DOMDocument();
							$fullPath = $pathToProfiles.'/'.$learnerFile.'/profile.xml';
							$profile->load($fullPath);
							$xpathProfile = new DOMXPath($profile);
							$query = "//*[@id='LP5']";
							$t2 = $xpathProfile->query($query);
							var_dump($t2->item(0)->nodeValue);
							
							/* foreach($data as $profileId => $arr){
							var_dump($profileId);
								//$learnerValue = $profile->getElementById($profileId);
								$learnerValue = $profile->getElementById('LP5');
								var_dump($learnerValue);
								//echo $learnerValue->item(0)->nodeValue;
							
							} */
							
							
							
						}
					}
					
					var_dump($data);
				
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