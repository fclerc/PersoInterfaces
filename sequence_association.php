<?php session_start(); 
    require_once 'phphelpers/langFinder.php';
?>
<!DOCTYPE HTML>
<!-- This file enables the user  to tell which strategy and context have to be used for each sequence of the MOOC. 
Same file is used to store the results once the form is submitted by the user.
sequence_association.json must be initialized with the right number of sequences
 -->
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/main.css" type="text/css" rel="stylesheet"/>
        <link href="css/d3.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
		<div class="container">
			<h1><span class="toTranslate">association.h1</span></h1>
            
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<p id="generalInstructions">association.instructions</p>
			<div class="pie"></div>
			<div id="association">
                <?php
                    //getting the file that contains the associations
                    $associationsPath = 'data/teacher/sequence_association.json';
                    $associations = json_decode(file_get_contents($associationsPath));
                    
                    
                    //if user has just submitted the form : store the data indicating the associations.
                    //example : {"Sequence1":{"strategy":"Atest.xml","context":"Sequence1.xml"},"Sequence2":{"strategy":"Sequence2.xml","context":"Sequence2.xml"}}
                    if(isset($_POST['formSent'])){//treat data sent by the form
                        echo '<p class="alert alert-info">Data successfully saved</p>';
                        
                        foreach($associations as $sequence => $data){
                            $newStrategy = $_POST[$sequence.'Strategy'];
                            $associations->$sequence->strategy = $newStrategy;
                        
                            $newContext = $_POST[$sequence.'Context'];
                            $associations->$sequence->context = $newContext;
                        }
                        
                        file_put_contents($associationsPath, json_encode($associations));
                    }
                
                
                    //getting the lists of files for contexts and strategies
                    $contextPath = 'data/teacher/sequenceContexts';
                    $strategyPath = 'data/teacher/strategies';
                    
                    $contextFiles = scandir($contextPath);
                    $strategyFiles = scandir($strategyPath);
                    
                    //the form used to define the associations
                    $form = '<form action="sequence_association.php" method="post">';
                    
                    //for each sequence, displaying the form with the 2 select
                    foreach($associations as $sequence => $data){
                        $form = $form.'<div><span><b>'.$sequence.' : </b></span>';
                        
                        $form = $form.'<label class="toTranslate">Strategy: </label>';
                        $form = $form.getStrategySelect($strategyFiles, $data->strategy, $sequence);
                        
                        $form = $form.'<label class="toTranslate">Context: </label>';
                        $form = $form.getContextSelect($contextFiles, $data->context, $sequence);
                        
                        $form = $form.'</div>';
                    }
                
                
                
                    $form = $form.'<input type="hidden" name="formSent" value="dontcare" />';
                    $form = $form.'<input class="btn btn-success" type="submit" value="Save" />';
                    $form = $form.'</form>';
                    
                    echo $form;
                    
                    //returns hmtl for a select with the context files, selecting the right one. sequence is used to give unique names to the select elements (like Sequence1Context
                    function getContextSelect($contextFiles, $selectedContext, $sequence){
                        $toReturn = '<select name="'.$sequence.'Context">';
                        foreach($contextFiles as $file){
                            if($file != '.' && $file !='..' && $file!= 'empty.xml'){
                                $selected = '';
                                if($file == $selectedContext){
                                    $selected = 'selected';
                                }
                                
                                $toReturn = $toReturn.'<option value ="'.$file.'" '.$selected.'>'.$file.'</option>';
                            }
                        }
                        $toReturn = $toReturn.'</select>';
                        return $toReturn;
                    }
                    
                    //returns hmtl for a select with the strategy files, selecting the right one. sequence is used to give unique names to the select elements (like Sequence1Strategy
                    function getStrategySelect($strategyFiles, $selectedStrategy, $sequence){
                        $toReturn = '<select name="'.$sequence.'Strategy">';
                        foreach($strategyFiles as $file){
                            if($file != '.' && $file !='..' && $file!= 'empty.xml'){
                                $selected = '';
                                if($file == $selectedStrategy){
                                    $selected = 'selected';
                                }
                                
                                $toReturn = $toReturn.'<option value ="'.$file.'" '.$selected.'>'.$file.'</option>';
                            }
                        }
                        $toReturn = $toReturn.'</select>';
                        return $toReturn;
                    }
                
                ?>
          
                
               
			</div>
			
			
			
        </div>
		
		
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
                    
                    $('.toTranslate, #currentFile, #generalInstructions, #mainLink, #sectionName').each(function(){
                        $(this).text(_($(this).text()));
                    });
                   }
            });
        });
        </script>

    </body>
    
</html>