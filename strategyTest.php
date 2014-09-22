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
        <link href="css/XMLManipulator.css" type="text/css" rel="stylesheet"/>
        <link href="css/boussoleDisplayer.css" type="text/css" rel="stylesheet"/>
        
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
            
            <!-- Form to enable user to make a new test  -->
            <div id="newTestForm">
            <?php
                $data = json_decode(file_get_contents('resources/filePageData.json'));
            
                //adding for each section the list of files available (for example the list of strategy files in data/teacher/strategies)
                $sections = $data->sections;
                $files = array();
                foreach($sections as $section){
                    $path = $data->path->$section;
                    $sectionFiles = scandir($path);
                    $files[$section] = $sectionFiles;
                }
                $data->files = $files;
            
            ?>
            </div>
            
            
            
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<p id="generalInstructions">strategyTest.instructions</p>
			
            <div><button id="explanationToggler" class = "btn btn-info"><span class="glyphicon glyphicon-plus"></span> <span>Explain me why</span></button></div>
            <div id="ProfileAndContext" class="mains" style="display:none;">
                <ul class="nav nav-tabs">
                    <li class="active" id="profileTabLi"><a href="#Profile" data-toggle="tab">Profile</a></li>
                    <li id="contextTabLi"><a href="#Context" data-toggle="tab">Context</a></li>
                </ul>
                <div class="tab-content">
                <div class="tab-pane active" id="Profile"></div>
                <div class="tab-pane" id="Context"></div>
            </div>
        
            </div>
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
		
		
       
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/XMLManipulator.js"></script>
       
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
                    
                    /*This section displays the form enabling the user to regenerate a list of activities (without going back to the homepage to select the files)
                    
                    
                    
                    */
                    
                    //passing the data to js
                    var data = <?php echo json_encode($data); ?>;
                    var strategyfile = <?php echo "'".$file."'" ; ?> ;
                    var profilefile = <?php echo "'".$profilefile."'" ; ?> ;
                    var sequenceContextfile = <?php echo "'".$sequenceContextfile."'" ; ?> ;
                    var liveContextfile = <?php echo "'".$liveContextfile."'" ; ?> ;
                    
                    
                    
                    var form = $('<form>').attr('action', data['interface']['strategyTest']).attr('method', 'POST');
                    
                    
                    var fileSelect = $('<select>').addClass('form-control').attr('name', 'file');
                    $(data['files']['strategyTest']).each(function(id, file){
                        if(file != '.' && file !='..' && file!='empty.xml'){
                            var option = $('<option>').append(file);
                            if(strategyfile == file){
                                $(option).attr('selected', 'selected');
                            }
                            $(fileSelect).append(option);
                        }
                    });
                    
                    
                    var pathForm = $('<input>').attr('type', 'hidden').attr('name', 'path').attr('value', data['path']['strategyTest']);
                    var sectionForm = $('<input>').attr('type', 'hidden').attr('name', 'section').attr('value', 'strategyTest');
                    var scalesForm = $('<input>').attr('type', 'hidden').attr('name', 'scales').attr('value', data['scales']['strategyTest']);
                    var schemaForm = $('<input>').attr('type', 'hidden').attr('name', 'schema').attr('value', data['schema']['strategyTest']);
                    var actionForm = $('<input>').attr('type', 'hidden').attr('name', 'action').attr('value', data['interface']['strategyTest']);
                    
                    
                    var newTestButton = $('<input>').attr('type', 'submit').attr('Value', _('New test')).addClass('btn btn-success').attr('name', 'newTestButton');
                    
                    //$(form).append($('<label>').append(_('Chose your strategy')).attr('for', 'file'));
                    $(form).append(fileSelect).append(pathForm).append(sectionForm).append(schemaForm).append(scalesForm).append(actionForm);
                    
                    
                    
                    //displaying the other files select for the test
                    var otherFiles = ['profile', 'liveContext', 'sequenceContext'];
                    //for each type of file, display the select
                    $(otherFiles).each(function(id, name){
                        //var label = $('<label>').append(_(name)).attr('for', name+'file');
                        var fileSelect = $('<select>').addClass('form-control').attr('name', name+'file');
                        $(data['files'][name]).each(function(id, file){
                            if(file != '.' && file !='..' && file!='empty.xml'){
                                //$(fileSelect).append($('<option>').append(file));
                                
                                var option = $('<option>').append(file);
                                if((name == 'profile' && profilefile == file) || (name == 'liveContext' && liveContextfile == file) || (name == 'sequenceContext' && sequenceContextfile == file)){
                                    $(option).attr('selected', 'selected');
                                }
                                $(fileSelect).append(option);
                                
                                
                                
                                
                            }
                        });
                        //passing to the interface the path to the folder
                        var pathForm = $('<input>').attr('type', 'hidden').attr('name', name+'path').attr('value', data['path'][name]);
                        
                        //$(form).append(label).append(fileSelect).append(pathForm);
                        $(form).append(fileSelect).append(pathForm);
                    });
                    
                    $(form).append(newTestButton);
                    
                    $('#newTestForm').append(form);
                    
                    
                    
                    
                    
                    
                    var profilePath = <?php echo "'".$profilePath."'"; ?>;
                    var liveContextPath = <?php echo "'".$liveContextPath."'"; ?>;
                    var t1 = manipulateXML(profilePath,'#Profile', 'selectWithValues', "#Rules", '', '#scaleDisplayer');
                    var t2 = manipulateXML(liveContextPath,'#Context', 'selectWithValues', '#Rules', '', '#scaleDisplayer');
                    $.when(t1, t2).done(function() {//when profile and context are displayed
                    
                        
                        
                        
                        
                        
                        
                        var explDisplayed = false;
                        $('#explanationToggler').click(function(){
                            $('.explanationRule').toggle(300);
                            
                            if(!explDisplayed){
                                $('#boussole').css('width', '65%');
                                explDisplayed = true;
                            }
                            else{
                                $('#boussole').css('width', '100%');
                                explDisplayed = false;
                            }
                            $('#ProfileAndContext').toggle(300);
                        });
                        
                        
                        
                        var strategyPath = <?php echo "'".$strategyPath."'"; ?>;
                        
                        $.ajax({//loading the strategy file, which contains all the required informations (including other files names)
                            type: "GET",
                            url: strategyPath,
                            success: function(data){//get the xml document
                                var strategy = $(data);//load xml tree
                                
                                $('.displayRuleThere').each(function(){
                                    var div = this;
                                    $(strategy).find("rule").each(function(){
                                        if($(this).attr('id') == $(div).attr('id')){
                                            $(div).html(this);
                                        }
                                        
                                    });
                                });
                            }
                        });
                    });
                }
            });
        });
        </script>
    </body>
    
</html>