<?php session_start(); 
?>
<!DOCTYPE HTML>
<!-- 
Use this file in order to generate lists of activities for a particular student,and have this list stored in his own folder.
Youcan also use it with a loop, going through all the learners to generate the lists.
-->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
		<div class="container">
			<h1>Lists of activities Generation</h1>
            
			<?php
                include 'ConsequenceGenerator.class.php';
                include 'ConditionChecker.class.php';
                include 'ActivitiesGenerator.class.php';
                    
                    
                    
                    
                $sequence = 'Sequence1';
                $learners = ['fclerc', 'mlefevre'];
                
                generateListForLearners($sequence, $learners);
                    
                //$sequence : the id of the sequence, as used in sequence_association.json
                //$learners : array of strings, each string being the id of a learner
                function generateListForLearners($sequence, $learners){
                
                    //getting the paths to strategy and context for this sequence
                    $associationsPath = '../data/teacher/sequence_association.json';
                    $associations = json_decode(file_get_contents($associationsPath));
                    $strategyPath = '../data/teacher/strategies/'.$associations->$sequence->strategy;
                    $sequenceContextPath = '../data/teacher/sequenceContexts/'.$associations->$sequence->context;
                    
                    $generator = new ActivitiesGenerator($strategyPath);
                    
                    
                    foreach($learners as $learnerId){
                    
                        //HERE you can add your code to generate the liveContext file
                        $liveContextPath = '../data/teacher/liveContexts/empty.xml';
                        generateListFor1Learner($generator, $learnerId, $liveContextPath, $sequenceContextPath);
                    }
                }
                    
                    
                //generator : insrance of ActivitiesGenerator, with the strategy you want
                //learnerId : id of the learner on the platform
                //liveContextPath  : path to the liveContext to use for the generation
                function generateListFor1Learner($generator, $learnerId, $liveContextPath, $sequenceContextPath){
                    $profilePath = '../data/learners/'.$learnerId.'/profile.xml';
                    
                    $html = $generator->generate($profilePath, $sequenceContextPath, $liveContextPath);
                    
                    file_put_contents('../data/learners/'.$learnerId.'/boussole.html', $html);
                    
                    echo 'Generated for '.$learnerId.'<br/>';
                }
                
				
				
			
			
			?>
			
		</div>
			
		
    </body>
    
</html>