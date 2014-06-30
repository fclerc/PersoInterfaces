<?php session_start();
    require_once 'phphelpers/langFinder.php';
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
<!DOCTYPE HTML>

<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
        <link href="css/XMLManipulator.css" type="text/css" rel="stylesheet"/>
        <link href="css/RulesInterface.css" type="text/css" rel="stylesheet"/>
        
        <style>
        
        </style>
    </head>
    
    <body>
		<h1><span id="strategyPageTitle">strategy.h1</span><small><span id="currentFile">strategy.currentFileIntro</span><span class="currentFileName"><?php echo $file; ?></span></small></h1>
		<p><a href="index.php" id="mainLink">common.back</a></p>
			
        <div id="ProfileAndContext" class="mains">
            <h2>strategy.profiles.h2</h2>
            <p>strategy.profiles.explanation</p>
            <ul class="nav nav-tabs">
                <li class="active" id="profileTabLi"><a href="#Profile" data-toggle="tab">Profile</a></li>
                <li id="contextTabLi"><a href="#Context" data-toggle="tab">Context</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="Profile"></div>
                <div class="tab-pane" id="Context"></div>
            </div>
        
        </div>
	
		<div id="Rules" class="mains">
			<h2>strategy.rules.h2</h2>
            <div class="filenameForm"><label for="filenameInput">Name: </label><input type="text" name="filenameInput" id="strategyFilenameInput" value="<?php echo $file; ?>" /></div>
			<button id="ruleAdder" class = "btn btn-info"><span class="glyphicon glyphicon-plus"></span> <span>strategy.rules.add</span></button>
			<button id="strategySaver" class = "btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span><span>strategy.rules.save</span></button>
            <hr/>
			<div id="newRuleInstruction">strategy.rules.instructions</div>
			<div id="newRuleContainer"></div>
			<div id="newRuleForm"></div>
            <div id="newRuleButtons"></div>
            <hr/>
            <h3>strategy.rules.defined.h3</h3>
            
            <div id="uselessDivToInsertRulesAbove"></div>
		</div>
		<div id="Activities" class="mains">
			<h2>strategy.activities.h2</h2>
		</div>
        <div id="scaleDisplayer"></div>
		
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        
        <script type="text/javascript" src="js/scaleDisplayers.js"></script>
        <script type="text/javascript" src="js/XMLManipulator.js"></script>
        <script type="text/javascript" src="translation/translate.js"></script>
        <script type="text/javascript" src="translation/icu.js"></script>
        <script type="text/javascript">
            
        
        
        $(function(){
			var strategyPath = <?php echo "'".$path."'"; ?>;
            var strategyFilename = <?php echo "'".$file."'"; ?>;
        $.ajaxSetup({ cache: false });//TODO : remove it and only cache: false for wise files; not always necessary for translations, documentations,...
            var translationFile = 'translation/'+<?php echo "'".$lang."'"; ?>+'.json';
        $.ajax({//loading translation
            type: "GET",
            url: translationFile,
            success: function(data){
                _.setTranslation(data);
			//translating the already displayed content
            $('#ProfileAndContext h2, #ProfileAndContext p, #ProfileAndContext ul li a, #Rules h2, #Rules h3, #Rules button span, #Activities h2, #mainLink, #newRuleInstruction, #strategyPageTitle, #currentFile, #sectionName').each(function(){
                $(this).text(_($(this).text()));
            });
        
        $.ajax({//loading the strategy file, which contains all the required informations (including other files names)
            type: "GET",
            url: strategyPath + strategyFilename,
            success: function(data){//get the xml document
				var strategy = $(data);//load xml tree
                var profileFilename = $($(strategy).find('exploitedProfile')[0]).text();
                var contextFilename = $($(strategy).find('exploitedContext')[0]).text();
        
        $.getJSON('data/schemas/profileScales.json', function(profileScales){
        $.getJSON('data/schemas/contextScales.json', function(contextScales){
        $.getJSON('data/schemas/resourcesData.json', function(resourcesData){
        
		
		
		$.ajax({
            type: "GET",
            url: profileFilename,
            success: function(profile){
        $.ajax({
            type: "GET",
            url: contextFilename,
            success: function(context){


                
				
                //loading the content of the other xml docs
                var pedagogicalPropertiesFilename = $($(strategy).find('pedagogicalProperties')[0]).text();
                var t1 = manipulateXML(profileFilename,'#Profile', 'selectWithValues', "#Rules", profileScales, '#scaleDisplayer');
                var t2 = manipulateXML(contextFilename,'#Context', 'selectWithValues', '#Rules', contextScales, '#scaleDisplayer');
                
                $.when(t1, t2).done(function() {//when profile and context are displayed
                    
                    displayActivitiesAndRules(pedagogicalPropertiesFilename, strategy);
                    
                    
                    
                    //this object will be used as a dictionnary to make a list of parameters corresponding to their IDs, like 'P012' : 'Length' .
                    var parametersDictionnary= [];
                    //this object will be used as a dictionnary to make a list of activities corresponding to their IDs, like 'A001' : 'Learning' .
                    var activitiesDictionnary= [];
                    //when creating new rule, we need to give it an id, like R12. This variable contains the highest id a rule currently has.
                    var rulesMaxId = 0;
                    
                    //the element which triggers events to treat them when an element is clicked.
                    var reader = $('#Rules');
                    
                    function displayActivitiesAndRules(pedagogicalPropertiesFilename, strategy){
                        
                    
                        
                        $.ajax({
                            type: "GET",
                            url: pedagogicalPropertiesFilename,
                            success: function(data){
                                var xml = {};
                                xml['#Activities']=$(data);//load xml tree
                                
                                
                                
                                //the element that will contain all the activities
                                var activitiesContainer = $('<ul>').addClass('activities');
                                
                                //going through the activities a first time, to find the 'All' one and expand all the other parameters list with its parameters. At the same time, we fill the activitiesDictionnary.
                                var allActivityParameters = [];
                                $(xml['#Activities']).find('TypeOfActivity').each(function(){
                                    var activity = this;
                                    var activityName = _($($(activity).children('Name')[0]).text());
                                    //filling the activitiesDictionnary                                    
                                    var activityId = $(activity).attr('ID');
                                    if(!activitiesDictionnary.hasOwnProperty(activityId)){
                                        activitiesDictionnary[activityId] = activityName;
                                    }
                                    
                                    if(activityName == 'All'){
                                        $(activity).find('Parameter').each(function(){
                                            allActivityParameters.push(this);
                                        });
                                    }
                                });
                                
                                
                                //going through activities to display them and their parameters as lists.
                                $(xml['#Activities']).find('TypeOfActivity').each(function(){
                                    $(activitiesContainer).append(displayActivity(this, allActivityParameters));
                                });
                                
                                
                                $('#Activities').append(activitiesContainer);
                                
                                //setting events when clicking on activities and parameters
                                
                                //event when clicking on activity names. Msg is 'activity', id, '#Activities' 
                                $('#Activities .activity > span').click(function(event){
                                
                                    var activityId = $(event.target).attr('id');
                                    $(reader).trigger("leafValueReading",  ['activity', activityId, '#Activities']);
                                });
                                //event when clicking on parameters names. Msg is 'parameter', id, '#Activities' 
                                $('#Activities .parameters .activityParameter ').click(function(event){
                                    var parameterId = $(event.target).attr('id');
                                    $(reader).trigger("leafValueReading",  ['parameter', parameterId, '#Activities']);
                                });
                                
                                
                                //displaying the rules in the center part of the page.
                                //placed here in the code : the parametersDictionnary is defined just above and ready to be used.
                                $(strategy).find("rule").each(function(){
                                    displayRule(this, '#uselessDivToInsertRulesAbove', false, false);
                                });
                                
                                
                            },
                            cache: false
                        });
                    
                    }	
                    
                    
                    
                    /*
                    Takes an xml element containing the activity, and the list of parameters contained in the 'All' activity.
                    Returns the html.
                    
                    
                    */
                    function displayActivity(activity, allActivityParameters){
                        var activityName = _($($(activity).children('Name')[0]).text());
                        var activityId = $(activity).attr('ID');
                        if(activityName != 'All'){//the 'All' activity is only used to factorize parameters that apply to all activities, and is therefore not displayed
                            //displaying the parameters of the activity, including the parameters contained in the 'All' activity
                            var activityContainer=$('<li>').addClass('activity').append($('<span>').append(activityName).attr('id', activityId));
                            
                            var parametersContainer =$('<ul>').addClass('parameters');
                            
                            $(allActivityParameters).each(function(){
                                addParameterToList(this, parametersContainer);								
                            });
                            $(activity).find('Parameter').each(function(){
                                addParameterToList(this, parametersContainer);
                            });
                            
                            $(activityContainer).append(parametersContainer);
                            return activityContainer;
                        }
                        
                        else{								
                            return '';
                        }
                    }
                    
                    /*
                    Takes a parameter xml element, with the div which will have to contain it.
                    Appends the container with html.
                    
                    */
                    function addParameterToList(parameter, container){
                        var untranslatedParameterName = $($(parameter).children('Name')[0]).text();
                        var parameterName = _(untranslatedParameterName);
                        var parameterId = $(parameter).attr('ID');
                        var parameterComment = $($(parameter).children('Comment')[0]).text();
                        var parameterScale = $(parameter).children().last();//the scale is always the last element in the parameter element
                        var parameterContainer = $('<span>').append(parameterName).addClass('activityParameter').attr('id', parameterId);
                        $(container).append($('<li>').append(parameterContainer));
                        
                        
                        //displaying an information popover with the comment : doesn't work because of size of the containing window
                        //var commentPopover = $('<a>').attr('id', 'popParameterComment').attr('href', '#').attr('data-toggle', 'popover').attr('data-content', parameterComment).append($('<span>').addClass('glyphicon glyphicon-info-sign commentPopover'));
                        
                        //displaying comment in alert
                        if(parameterComment != ''){
                            var commentPopover = $('<span>').addClass('glyphicon glyphicon-info-sign commentPopover').attr('title', _('Click for more information'));
                            $(commentPopover).click(function(){
                                alert(parameterComment);
                            });
                            
                            $(commentPopover).hover(function(){
                                $('#scaleDisplayer').empty();
                                displayParameterScale((parametersDictionnary[parameterId]).scale, resourcesData[untranslatedParameterName.toLowerCase()], '#scaleDisplayer', false);
                                $('#scaleDisplayer').show();
                            },
                            function(){
                                $('#scaleDisplayer').hide();
                            
                            });
                            
                            $($(container).find('li').last()).append(commentPopover);
                        }
                        
                        if(!parametersDictionnary.hasOwnProperty(parameterId)){//if this parameter is not yet in the Dictionnary, add it.
                            parametersDictionnary[parameterId] = {name: untranslatedParameterName, comment: parameterComment, scale: parameterScale};
                        }
                    }
                    
                    
                    
                    function getParameterId(parameterName){
                        $.each(parametersDictionnary, function(id, value){
                            if(value.name==parameterName){
                                return id;
                            }
                        });
                    }
                    
                    
                    function switchProfileContext(){//switches from profile to context and vice-versa in left part.
						if($('#Profile').hasClass('active')){
							$('#Context').addClass('active');
							$('#contextTabLi').addClass('active');
							$('#Profile').removeClass('active');
							$('#profileTabLi').removeClass('active');
						}
						else{
							$('#Context').removeClass('active');
							$('#contextTabLi').removeClass('active');
							$('#Profile').addClass('active');
							$('#profileTabLi').addClass('active');
						}
					}
                    
                    /*
                    
                    
                    
                    
                    Rules Section
                    
                    */
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    //responding to user definition of rules
                    /*
                    1: Selection of indicator
                    2: Selection of operator
                    3: referenceValue is given
                    4: Selection of activity for then
                    5: Selection of parameter for then
                    6: value of parameter is given for then
                    7: Selection of activity for else
                    8: Selection of parameter for else
                    9: value of parameter is given for else
                    10: Priority for the rule is given
                    11: Rule is ready to be edited
                    12: Rule is saved
                    13: chosing 'AND' or 'OR' (should be another number, but I only added it in the end)
                    
                    
                    At each time : change the xml tree, and make a new call on the displayer.
                    
                    */
                    var currentOperation = 0;
                    var instructions = ['Click on new rule', 'Choose the indicator you want to base your rule on from the profile or the context', 'Choose the operator you want to use in this rule in the form below', 'Give the value used for the comparison', 'Select the activity for the then part of the rule', 'Select a parameter you want to set for this activity', 'Give the value for the parameter', 'Select the activity for the else part of the rule', 'Select a parameter you want to set for this activity', 'Give the value for the parameter', 'Give the priority for this rule', 'Save your new rule', 'Success, your rule has been saved! You can create/edit another one if you want'];
                    
                    //used in last operations, to know whether we are in the definition of <then> or <else>
                    var operationToConsequence = ['new','if', 'if', 'if', 'then','then','then','else','else','else', 'priority', 'save', 'save'];
                    
                    var builtRule = {};
                    
                    var currentIndicatorId;//used to display the scale in todoBefore[3]
                    
                    var currentParameter;//used in stages when asking parameter value to the user, to add this parameter the <value>
                    var currentActivity;//used in stages when asking parameter to the user, to add the parameter to this activity
                    var currentCondition;//used in the case the user wants to define complex conditions, using AND and OR. It stores the constraint which is going to be combined with the next defined constraint. Also used to know which is the target constraint chosing operator and value in stages 2 and 3, or when a constraint is edited
                    var editingCondition = false;//true iff we are editing a constraint (to make the difference with the case when adding a new constraint)
                    
                    
                    var resetAllCurrent = false; //in case the rule has just been saved, reset all the 'current' variables to avoid any problem if user clicks on buttons without paying attention
                    
                    
                    var formToDisplay = '' //used when displaying a new rule, to know whether a form has to be displayed in it.
                    //formToDisplay  = string. 'comparisonOperator' = form to select an operator for currentCondition; 'refValueConstraint' = form for the value of currentParameter; 'refValueParameter' = form for the value of currentParameter; 'priority' = you know what.
                    //'indicator' if the user has to chose an indicator in the list (either to edit an indicator, or to chose the indicator of a new constraint)
                    //'parameter' if the user has to chose a new parameter in the list
                    //'thenActivity' if the user has to chose an activity in the list for the then part
                    //'elseActivity' if the user has to chose an activity in the list for the else part
                    //'conditionType' if the user has to chose between 'and' and 'or'
                    //'conditionTypeEdition' if the user has to chose between 'and' and 'or' to MODIFY a condition operator
                    
                    
                    $("#Rules").on("leafValueReading", function(event, value, id, container){
                        
                        
                        if(currentOperation ==1){//user gives an indicator, other things are just ignored
                            if(container == "#Profile" || container == "#Context"){
                                var newConstraint = $('<constraint>').append($('<indicator>').text(id));
                                currentIndicatorId = id;
                                if($(builtRule).find('if').length === 0){//there's no 'if' defined.                                    
                                    $(builtRule).append($('<if>').append(newConstraint));
                                    currentCondition = newConstraint[0];
                                }
                                
                                
                                else if(typeof currentCondition != 'undefined' && currentCondition !== null){//we are defining a constraint contained in 'and' or 'or'; or editing a constraint
                                    if(editingCondition){
                                        $($(currentCondition).find('indicator')[0]).text(id);
                                        editingCondition = false;
                                    }
                                    else{
                                        $(currentCondition).parent().append(newConstraint);
                                        currentCondition = newConstraint[0];
                                    }
                                }
                                
                                
                                else{//else : replace the first indicator (or create it, in the case the rule only contains <if></if> TODO : maybe forget it, and don't display 'edit if' button once <if> is not void.
                                    if($(builtRule).find('constraint').length==0){//create the first constraint
                                        $($(builtRule).find('if')[0]).append(newConstraint);
                                    }
                                    else{//replace the first constraint
                                        $($(builtRule).find('constraint')[0]).replaceWith(newConstraint);
                                    }
                                    currentCondition = newConstraint[0];
                                }
                                goFromTo(1,2);
                            }
                        }
                        
                        else if(currentOperation ==2){//received event contains the operator
                            if(container=='#newRuleContainer'){
                                var op = $('<operator>').text(value);
                                if($(currentCondition).find('operator').length == 1){//constraint already has an operator
                                    $($(currentCondition).find('operator')[0]).text(value);
                                }
                                else{//constraint doesn't have any operator, create it
                                    op.insertAfter($($(currentCondition).find('indicator')[0]));
                                }
                                goFromTo(2,3);
                                
                            }
                        }
                        
                        else if(currentOperation == 3){//event contains the value to compare the indicator with
                            if(container=='#newRuleContainer'){
                                var refVal = $('<referencevalue>').text(value);
                                if($(currentCondition).find('referencevalue').length == 1){//constraint already has an operator
                                    $($(currentCondition).find('referencevalue')[0]).text(value);
                                }
                                else{//constraint doesn't have any operator, create it
                                    refVal.insertAfter($($(currentCondition).find('operator')[0]));  
                                }
                                goFromTo(3,4);
                            }
                        
                        }
                        
                        else if(currentOperation == 4 || currentOperation == 7){//an activity has been clicked on (either for then or else parts), or user has clicked on the button 'noelse'
                            if(container=='#Activities' && value == 'activity'){
                                var activity = $('<activity>').append($('<typeofactivity>').append(id)).append($('<parameters>'));
                                currentActivity = activity[0];
                                
                                var consequenceContainer = {};
                                if($(builtRule).find(operationToConsequence[currentOperation]).length === 0){
                                    consequenceContainer = $('<'+operationToConsequence[currentOperation]+'>').append($('<activities>')).append(activity);
                                    $(builtRule).append(consequenceContainer);
                                }
                                else{
                                    consequenceContainer = $(builtRule).find(operationToConsequence[currentOperation])[0];
                                    $(consequenceContainer).append(activity);
                                }
                                
                                goFromTo(currentOperation, currentOperation+1);
                            }
                            
                        }
                        
                        else if(currentOperation == 5 ||currentOperation==8){//a parameter has been clicked on
                            if(container=='#Activities' && value == 'parameter'){
                                //var activity = $($(builtRule).find(operationToConsequence[currentOperation])[0]).find('activity').last();
                                
                                var parameter = $('<parameter>').append($('<id>').text(id));
                                $($(currentActivity).find('parameters')[0]).append(parameter);
                                currentParameter = parameter[0];
                                goFromTo(currentOperation, currentOperation+1);
                            }
                        }
                        
                        else if(currentOperation == 6 || currentOperation == 9){//value of the parameter has  been given
                            
                            if(container == '#newRuleContainer'){
                                if($(currentParameter).find('value').length === 0){
                                    $(currentParameter).append($('<value>').append(value));
                                }
                                else{
                                    $($(currentParameter).find('value')[0]).text(value);
                                }
                                goFromTo(currentOperation, currentOperation-1);
                            }
                        }
                        
                        else if(currentOperation==10){
                            if(container == '#newRuleContainer'){
                                $(builtRule).prepend($('<priority>').append(value));
                                goFromTo(10,11);
                            }
                        }
                        
                        else if(currentOperation == 13){
                            if((currentCondition.nodeName).toLowerCase() == 'constraint' || formToDisplay == 'conditionType'){//we are adding a new constraint
                                $(currentCondition).wrap($('<'+ value +'>'));
                            }
                            else{//we are changing the 'and' or 'or' of the current condition
                                
                                var content = $(currentCondition).html();
                                $(currentCondition).replaceWith($('<'+ value +'>').html(content));
                            }
                            goFromTo(13, 1);
                        }
                        
                    });
                    
                    /*
                    Function used to change the interface and text dispalyed when going from any 'currentOperation' origin to any other 'currentOperation' target.
                    
                    */
                    function goFromTo(origin, target){
                        currentOperation = target;
                        todoAfter[origin]();
                        todoBefore[target]();
                        
                        if(target!==0 && target !=12){
                            displayNewRule();
                        }
                        //instruct();
                    }
                    
                    var todoBefore = [//contains what has to be done before each operation
                        function(){//0
                            removeAllButtons();
                            $('#newRuleContainer').empty();
                        },
                        function(){//1
                            //addEditButtons();
                            formToDisplay = 'indicator';
                        },
                        function(){//2
                            formToDisplay = 'comparisonOperator';
                        
                        },
                        function(){//3
                            formToDisplay = 'refValueConstraint';
                            var indicatorName;
                            if($(profile).find('#'+currentIndicatorId)[0]){
                                indicatorName = $(profile).find('#'+currentIndicatorId)[0].nodeName;
                                displayIndicatorScale(indicatorName, '#newRuleForm', currentIndicatorId, profileScales, true);
                            }
                            else{
                                indicatorName = $(context).find('#'+currentIndicatorId)[0].nodeName;
                                displayIndicatorScale(indicatorName, '#newRuleForm', currentIndicatorId, contextScales, true);
                            }
                        },
                        function(){//4
                            formToDisplay = 'thenActivity';
                        
                        },
                        function(){//5
                            formToDisplay = 'parameter';
                        
                        },
                        function(){//6
                            formToDisplay = 'refValueParameter';
                            //displaying information about the scale just above the rule
                            var currentParameterId = $($(currentParameter).find('id')[0]).text();
                            var currentParameterName = (parametersDictionnary[currentParameterId]).name.toLowerCase();
                            if(parametersDictionnary[currentParameterId]){
                                displayParameterScale((parametersDictionnary[currentParameterId]).scale,resourcesData[currentParameterName], '#newRuleForm', true);
                            }
                        },
                        function(){//7
                            formToDisplay = 'elseActivity';
                        
                        },
                        function(){//8
                            formToDisplay = 'parameter';
                        
                        },
                        function(){//9 TODO : eliminate it, same as 6
                            formToDisplay = 'refValueParameter';
                            //displaying information about the scale just after the form
                            var currentParameterId = $($(currentParameter).find('id')[0]).text();
                            var currentParameterName = (parametersDictionnary[currentParameterId]).name.toLowerCase();
                            if(parametersDictionnary[currentParameterId]){
                                displayParameterScale((parametersDictionnary[currentParameterId]).scale,resourcesData[currentParameterName], '#newRuleForm', true);
                            }
                        
                        },
                        function(){//10
                            formToDisplay = 'priority'
                            //displayValueInput();
                        
                        },
                        function(){//11
                            //addEditButtons();
                        },
                        function(){//12
                            removeAllButtons();
                            $('#newRuleContainer').empty();
                        },
                        function(){//13
                            if(formToDisplay != 'conditionTypeEdition'){
                                formToDisplay = 'conditionType';
                            }
                        }
                    ];
                    
                    
                    
                    
                    
                    var todoAfter = [//contains what has to be done after each operation
                        function(){//0
                        
                        
                        },
                        function(){//1
                        
                        
                        },
                        function(){//2
                            removeForms();
                            formToDisplay = '';
                        },
                        function(){//3
                            formToDisplay = '';
                            removeForms();
                        
                        },
                        function(){//4
                            formToDisplay = '';
                        
                        },
                        function(){//5
                            formToDisplay = '';
                        
                        },
                        function(){//6
                            removeForms();
                            formToDisplay = '';
                        },
                        function(){//7
                            formToDisplay = '';
                        
                        },
                        function(){//8
                            formToDisplay = '';
                        
                        },
                        function(){//9
                            removeForms();
                        
                        },
                        function(){//10
                            removeForms();
                            formToDisplay = '';
                        },
                        function(){//11
                            removeForms();
                            if(resetAllCurrent){//in case the rule has just been saved, reset all the 'current' variables to avoid any problem if user clicks on buttons without paying attention
                                currentActivity = null;
                                currentCondition = null;
                                currentParameter = null;
                            }
                            resetAllCurrent = false;
                        },
                        function(){//12
                        
                        },
                        function(){//13
                            removeForms();
                            formToDisplay = '';
                        }
                    ];
                    
                    
                    
                    
                    function saveRule(){
                        var ruleInXMLFile = $(strategy).find('rule[id="' + $(builtRule).attr('id') + '"]');
                        if(ruleInXMLFile.length === 0){//this is a new rule, add it to the end of the file
                            $($(strategy).find('strategyRules')[0]).append(builtRule);
                           rulesMaxId++;//the rule is now stored, let's go the next id for future rules
                           displayRule(builtRule, '#uselessDivToInsertRulesAbove', true, false);
                        }
                        else{//the rule already exists, replace it in the file
                            $(ruleInXMLFile[0]).replaceWith(builtRule);
                            updateRule(builtRule, $(builtRule).attr('id'));
                        }
                       
                        goFromTo(11,12);
                        
                    }
                    
                    $('#ruleAdder').click(function(){
                        if(currentOperation === 0 || currentOperation == 12){
                            var nextRuleId = rulesMaxId+1;
                            builtRule = $('<rule>').attr('id', 'R'+(nextRuleId));
                            goFromTo(0,1);
                        }
                    });
                    //obsolete
                    function addEditButtons(){
                        addButton(_("Edit 'IF'"), 'ifEditorButton', 1);
                        addButton(_("Add 'THEN' Activity"), 'thenEditorButton', 4);
                        addButton(_("Add 'ELSE' Activity"), 'elseEditorButton', 7);
                        addButton(_('Save rule'), 'ruleSaver', 11);
                    }
                    
                    $('#strategySaver').click(function(){
                        var xmlS = (new XMLSerializer()).serializeToString(strategy[0]);
                        $.post('phphelpers/saveXMLDocument.php', { 
                            file: '../'+strategyPath + $('#strategyFilenameInput').val(),
                            data: xmlS,
                            formerFile: '../'+strategyPath + strategyFilename}, 
                            function(data, txt, jqXHR){
                                if(txt=="success"){
                                    if(data.message == 'RENAMEERROR'){
                                        alert('File not saved: a file with this name already exists');
                                    }
                                    else{
                                        alert(_('strategy.save.success'));
                                        strategyFilename = $('#strategyFilenameInput').val();
                                        $('.currentFileName').text(strategyFilename);
                                    }
                                }
                            }
                        );
                    
                    });
                    
                    function displayNewRule(){//when creating a new rule, displays the rule in the #newRuleContainer and displays instruction
                        $('#newRuleContainer').text('');//empty the container
                        $('#newRuleContainer').append($('<div>').attr('id', 'emptyDiv'));//adding empty div to pass it as insBeofre argument in displayRule function
                        (displayRule(builtRule[0], '#emptyDiv', true, true));//displaying the in-process rule
                        
                    }
                    
                    function instruct(){//giving next instruction to the user
                        $('#newRuleInstruction').text(_(instructions[currentOperation]));
                        if(currentOperation !== 0 && currentOperation != 12){
                            //$('#newRuleInstruction').append(_(', or click on a button below'));
                        }
                    }
                    
                    
                    //displays the value input together with a button to click on
                    function displayValueInput(){							
                        var valueInput = $('<input>').attr('type', 'text');
                        
                        $('#newRuleForm').append(valueInput);
                        $('#newRuleForm').append($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                        
                        $('#newRuleForm .glyphicon-ok-sign').unbind('click').click(function(){
                            var val = $('#newRuleForm input').val();
                            $(reader).trigger("leafValueReading",  [val, 0, '#newRuleForm']);									
                        });
                    }
                    
                    //removes all forms in the rules part.
                    function removeForms(){
                        $('#newRuleForm').empty();
                    }
                    //obsolete
                    function addButton(value, id, operationIfClicked){//adding a button to chose next interaction with the rule; and triggering event to the reader
                        if($('#newRuleButtons').find('#'+id).length===0){                            
                            var button = $('<button>').append(value).addClass('operationButton btn btn-primary').attr('id', id);
                            $(button).click(function(){
                                
                                
                                goFromTo(currentOperation, operationIfClicked);
                                if(id=="ruleSaver"){
                                    resetAllCurrent = true;
                                    saveRule();
                                }
                                else if(id=='ifEditorButton'){
                                    currentCondition = null;
                                }
                            });
                            $('#newRuleButtons').append(button);
                        }
                    }
                    
                    function removeAllButtons(){
                        $('#newRuleButtons').empty();
                    }
                    
                    
                    
                    
                    
                    function updateRule(ruleXML, id){//updates a rule which is displayed : removes the rule and displays the new one at the same place/
                        formerRuleContainer = $('#Rules').find('.ruleContainer[id="' + id + '"]');
                        insBefore = $(formerRuleContainer).next();
                        $(formerRuleContainer).remove();
                        displayRule(ruleXML, insBefore, false, false);                        
                    }
                    
                    function duplicateAndEditRule(rule){
                        if($('#newRuleContainer').text().length===0){//no rule is currently being edited
                                currentOperation = 11;
                                builtRule = $(rule).clone();
                                var nextRuleId = rulesMaxId+1;
                                $(builtRule).attr('id', 'R'+(nextRuleId));
                                //addEditButtons();
                                displayNewRule();
                                $('#newRuleInstruction')[0].scrollIntoView(true);
                        }
                    }
                    
                    /*
                        Argument is a xml node containing a rule .
                        This function displays the rule just before the second argument insBefore
                        createdRule is a boolean, true iff the rule is has been built by the user during current use of the page (thus don't go in the part that tries to find the highest id of rules)
                        editMode is a boolean, true iff rule is currently being enabled (this allow click on elements to edit them directly)
                        
                    */
                    function displayRule(rule, insBefore, createdRule, editMode){
                        
                        //top-right sign to remove the rule
                        var ruleRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle ruleRemover pull-right').attr('title', _('Delete rule'));
                        var ruleEditor = $('<span>').addClass('glyphicon glyphicon-edit ruleEditor pull-right').attr('title', _('Edit rule'));
                        var ruleDuplicator = $('<span>').addClass('glyphicon glyphicon-share ruleDuplicator pull-right').attr('title', _('Duplicate rule'));
                        
                        
                        
                        
                        
                        var priority = $($(rule).find("priority")[0]).text();
                        var priorityContainer = $('<div>').addClass('priority').append(_('Priority: '));
                        if(editMode && formToDisplay == 'priority'){//we are editing the priority
                            var valueInput = $('<input>').attr('type', 'text').attr('value', priority);
                            $(priorityContainer).append(valueInput);
                            var formValidator = ($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                            $(priorityContainer).append(formValidator);
                            
                            $(formValidator).unbind('click').click(function(){
                                var val = $(valueInput).val();
                                $(reader).trigger("leafValueReading",  [val, 0, '#newRuleContainer']);									
                            });
                           
                        }
                        else if(editMode){//just display the value, and an icon to modify it
                            $(priorityContainer).append(priority);
                            
                            var priorityEditor = $('<span>').addClass('glyphicon glyphicon-edit priorityEditor').attr('title', _('Edit priority'));
                            
                            $(priorityEditor).click(function(){
                                goFromTo(currentOperation, 10);				
                            });
                            
                            $(priorityContainer).append(priorityEditor);
                        }
                        else{
                            $(priorityContainer).append(priority);
                        }
                        
                        
                        var ruleContainer = $('<div>').attr('id', $(rule).attr('id')).addClass('ruleContainer').append($('<h4>').append(_('Rule') + $(rule).attr('id'))).append(ruleRemover).append(ruleEditor).append(ruleDuplicator).append(priorityContainer);
                        
                        if(editMode){
                            var ruleSaver = $('<span>').addClass('glyphicon glyphicon-floppy-disk ruleSaver pull-right').attr('title', _('Save rule'));
                            $(ruleSaver).click(function(){
                                goFromTo(currentOperation, 11);
                                resetAllCurrent = true;
                                saveRule();
                            });
                            
                            $(ruleContainer).append(ruleSaver);
                        }
                        
                        
                        if(!createdRule){
                            var ruleIdNumber = parseInt($(rule).attr('id').split('R')[1]);
                        
                            if(rulesMaxId <= ruleIdNumber){
                                rulesMaxId = ruleIdNumber;
                            }
                        }
                        
                        //Action to delete the rule
                        $(ruleRemover).click(function(){
                            if($('#newRuleContainer #' +$(rule).attr('id') ).length===0){//this is not the currently created rule : delete it in the xml tree
                                if(confirm(_('strategy.rules.delete'))){
                                    $(rule).remove();
                                    $(ruleContainer).remove();
                                }
                            }
                            
                            else{//this is the currently created rule
                                if(confirm(_('strategy.rules.newrule.delete'))){
                                    builtRule = {};
                                    goFromTo(currentOperation, 0);
                                }
                                
                            }
                        });
                        
                        $(ruleEditor).click(function(){
                            if($('#newRuleContainer').text().length===0){//no rule is currently being edited
                                builtRule = $(rule);
                                goFromTo(currentOperation, 11);
                                $('#newRuleInstruction')[0].scrollIntoView(true);
                            }
                            else{
                                alert(_('strategy.rules.cantedit'));
                            }
                        });
                        
                        $(ruleDuplicator).click(function(){
                            duplicateAndEditRule(rule);
                        });
                        
                        
                        
                        
                        
                        
                        
                        var ifContainer = $('<div>').addClass('ifContainer').append(_('IF'));

                        var ifElement = $(rule).find("if")[0];
                        if($(ifElement).children().length > 0){//there are constraints, display it
                            $(ifElement).children().each(function(){
                                $(ifContainer).append(getConditionElementContainer(this));
                            });
                        }
                        else if(editMode && formToDisplay == 'indicator'){//no constraint and we are in edit mode, wanting to modify indicator : tell the user he has to select an indicator
                            $(ifContainer).append($('<span>').addClass('instruction').append(_('strategy.rules.newrule.chose.indicator')));
                        }
                        else if(editMode && formToDisplay !='indicator'){//editmode, with 'if' void and which is not currently edited : display a '+' icon to enable to add a constraint
                            var constraintAdder = $('<span>').addClass('glyphicon glyphicon-plus constraintAdder').attr('title', _('Add constraint'));
                            $(constraintAdder).click(function(){
                                goFromTo(currentOperation, 1);
                                currentCondition = null;
                            });
                            $(ifContainer).append(constraintAdder);
                        }
                        
                        
                        
                        todoWhenConditionReady();
                        
                        function getConditionElementContainer(element){
                            var toReturn;
                            if((element.nodeName).toLowerCase() == 'constraint'){
                                toReturn = getConstraintContainer(element);
                            }
                            else{//AND or OR, with 2 conditionElement children
                                var c1, c2;
                                if($(element).children().length >= 1){
                                    c1 = getConditionElementContainer($(element).children()[0]);
                                        if($(element).children().length >= 2){
                                            c2 = getConditionElementContainer($(element).children()[1]);
                                        }
                                        else if(editMode){
                                            var constraintRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle constraintRemover').attr('title', _('Remove complex condition'));
                                            $(constraintRemover).click(function(){
                                                var constraintToKeep = $(element).children()[0]
                                                $(element).replaceWith(constraintToKeep);
                                                goFromTo(currentOperation, 11);
                                            });
                                            c2 =$('<span>').append(constraintRemover);
                                            
                                            if(formToDisplay == 'indicator'){//tell the user he has to select an indicator
                                                $(c2).append($('<span>').addClass('instruction').append(_(' Select indicator')));
                                            }
                                        }
                                        
                                }
                                
                                
                                toReturn = $('<span>').addClass('conditionElement');
                                
                                var conditionTypeContainer = $('<span>').addClass('conditionType');
                                
                                if(editMode){
                                    if(formToDisplay == 'conditionTypeEdition' && currentCondition == element){
                                        var conditionSelect = $('<select>');
                                        var conditions = ['AND', 'OR'];
                                        $(conditions).each(function(id, op){
                                            $(conditionSelect).append($('<option>').append(op));
                                        });
                                        var formValidator = ($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                                        $(conditionTypeContainer).append(conditionSelect).append(formValidator);
                                        
                                        $(formValidator).click(function(){
                                            var cond = $(conditionSelect).val();
                                            $(reader).trigger("leafValueReading",  [cond, 0, '#newRuleForm']);	
                                        });
                                    
                                    
                                    }
                                    
                                    else{//displaying the operator name, and buttons next to the operator name, to edit and add new constraint
                                        $(conditionTypeContainer).append(' '+_(element.nodeName.toUpperCase()));
                                    
                                        var conditionTypeEditor = ($('<span>').addClass('glyphicon glyphicon-edit conditionTypeEditor').attr('title', _('Edit operator')));
                                        
                                        var otherConstraintAdder = $('<span>').addClass('glyphicon glyphicon-plus otherConstraintAdder').attr('title', _('Combine with another constraint'));
                                            
                                        $(otherConstraintAdder).click(function(){
                                            currentCondition = element;
                                            goFromTo(currentOperation, 13);
                                            editingCondition = false;
                                            currentCondition = element;//doing it once again if changed by goFromTo...
                                        });
                                        
                                        $(conditionTypeEditor).click(function(){
                                            currentCondition = element;
                                            formToDisplay = 'conditionTypeEdition';
                                            goFromTo(currentOperation, 13);
                                            editingCondition = false;
                                            currentCondition = element;//doing it once again if changed by goFromTo...
                                        });
                                        
                                        $(conditionTypeContainer).append(conditionTypeEditor).append(otherConstraintAdder);
                                    }
                                }
                                
                                else{
                                    $(conditionTypeContainer).append(' '+_(element.nodeName.toUpperCase()));
                                }
                                
                                $(toReturn).append(' (').append(c1).append(conditionTypeContainer).append(c2).append(' )');
                            }
                            
                            
                            if(editMode && currentCondition == element && formToDisplay == 'conditionType'){//display the form to chose 'and' or 'or' (which will be then combined with the currentCondition)
                                var conditionSelect = $('<select>');
                                var conditions = ['AND', 'OR'];
                                $(conditions).each(function(id, op){
                                    $(conditionSelect).append($('<option>').append(op));
                                });
                                var formValidator = ($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                                $(toReturn).append(conditionSelect).append(formValidator);
                                
                                $(formValidator).click(function(){
                                    var cond = $(conditionSelect).val();
                                    $(reader).trigger("leafValueReading",  [cond, 0, '#newRuleForm']);	
                                });
                            }
                            
                            return toReturn;
                        }
                        
                        function getConstraintContainer(constraint){
                            var indicatorId = $($(constraint).find("indicator")[0]).text();
                            //finding the corresponding element in the left part of the page, to display the name of the indicator, and indicate it when hovering the indicator in the page.
                            var indicatorName='';
                            var indicatorSelectionContainer;
                            getIndicatorInfos();
                            return todoWhenIndicatorReady();
                            
                            //Next part was used in the past to avoid synchronism problem (in unelegant manner), now unnecessary
                             //using a timeout to regularly check if left part of the page is displayed...not very elegant but works. This check doesn't have to be made when this is a newly created rule : everything is already loaded.
                            /*if(indicatorName == ''){//didn't manage to get the name, left part of the page is not yet loaded
                                var indicatorChecker = setInterval(function(){
                                    getIndicatorInfos();
                                    if(indicatorName != ''){//if we managed to find the indicator name, then we can go to the next part.
                                        clearInterval(indicatorChecker);
                                        return todoWhenIndicatorReady();
                                    }
                                }, 200);
                            }
                            else{
                                return todoWhenIndicatorReady();
                            }*/
                            
                            function todoWhenIndicatorReady(){
                                var operator = $($(constraint).find("operator")[0]).text();
                                var referenceValue = $($(constraint).find("referencevalue")[0]).text();
                                var indicatorContainer = $('<span>').addClass('indicator').append(' '+indicatorName);
                                
                                if(editMode && formToDisplay == 'indicator' && currentCondition == constraint && editingCondition){//if we are currently editing this indicator, show it clearly
                                    $(indicatorContainer).addClass('editedIndicator');
                                }
                                
                                var operatorContainer = $('<span>').addClass('operator').append(' ');
                                var referenceValueContainer = $('<span>').addClass('referenceValue').append(' ');
                                
                                var allowComplex = true;
                                if(editMode && formToDisplay == 'comparisonOperator' && constraint == currentCondition){
                                    allowComplex = false;
                                    //displaying the form to select operator
                                    var operatorSelect = $('<select>');
                                    var operators = ['=', '>', '<', '!='];
                                    $(operators).each(function(id, op){
                                        $(operatorSelect).append($('<option>').append(op));
                                    });
                                    
                                    var formValidator = ($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                                    $(operatorContainer).append(operatorSelect).append(formValidator);
                                    
                                    $(formValidator).unbind('click').click(function(){
                                        var val = $(operatorSelect).val();
                                        $(reader).trigger("leafValueReading",  [val, 0, '#newRuleContainer']);			
                                    });
                                }
                                
                                
                                
                                else{
                                    $(operatorContainer).append(operator);
                                }
                                
                                if(editMode && formToDisplay == 'refValueConstraint' && constraint == currentCondition){
                                    var valueInput = $('<input>').attr('type', 'text').attr('value', referenceValue);
                                    $(referenceValueContainer).append(valueInput);
                                    var formValidator = ($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                                    $(referenceValueContainer).append(formValidator);
                                    
                                    $(formValidator).unbind('click').click(function(){
                                        var val = $(valueInput).val();
                                        $(reader).trigger("leafValueReading",  [val, 0, '#newRuleContainer']);			
                                    });
                                }
                                
                                
                                else{
                                    $(referenceValueContainer).append(referenceValue);
                                }
                                
                                
                                
                                
                                
                                
                                var constraintContainer = $('<span>').addClass('constraint');
                                $(constraintContainer).append(indicatorContainer);
                                $(constraintContainer).append(operatorContainer);
                                $(constraintContainer).append(referenceValueContainer);
                                
                                if(editMode){
                                    var otherConstraintAdder;
                                    if(allowComplex){//enable conditions combination only if you aren't currently selecting the operator
                                        var otherConstraintAdder = $('<span>').addClass('glyphicon glyphicon-plus otherConstraintAdder').attr('title', _('Combine with another constraint'));
                                        
                                        $(otherConstraintAdder).click(function(){
                                            currentCondition = constraint;
                                            goFromTo(currentOperation, 13);
                                            editingCondition = false;
                                            currentCondition = constraint;//doing it once again if changed by goFromTo...
                                        });
                                        
                                        $(operatorContainer).append(otherConstraintAdder);
                                    }
                                    
                                    var constraintRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle constraintRemover').attr('title', _('Remove constraint'));
                                    var constraintEditor = $('<span>').addClass('glyphicon glyphicon-edit constraintEditor').attr('title', _('Edit constraint'));
                                    
                                    $(constraintRemover).click(function(){
                                        var parent = $(constraint).parent()[0];//if it is and or or : only keep the other constraint
                                        if(['and', 'or'].indexOf((parent.nodeName).toLowerCase()) != -1){
                                            constraint.remove();
                                            var theSecondConstraint = $(parent).children()[0];
                                            $(parent).replaceWith(theSecondConstraint);
                                        }
                                        else{
                                            constraint.remove();
                                        }
                                        
                                       
                                        goFromTo(currentOperation, 11);
                                    });
                                    $(constraintEditor).click(function(){
                                        currentCondition = constraint;
                                        editingCondition = true;
                                        goFromTo(currentOperation, 1);
                                    });
                                    
                                    $(referenceValueContainer).after(constraintEditor).after(constraintRemover);
                                }
                                
                                
                                
                                
                                
                                
                                //displaying the indicator in color when hovering
                                var indicatorSelectionContainerColor = $(indicatorSelectionContainer).css('background-color');
                                $(indicatorContainer).hover(function(event){
                                    $(indicatorSelectionContainer).css('background-color', '#FF7F24');
                                },
                                function(event){
                                    $(indicatorSelectionContainer).css('background-color', indicatorSelectionContainerColor);
                                
                                });
                                
                                //scrolling to the indicator in left part when clicking on it in rule part (and swithing if necessary to context or profile).
                                $(indicatorContainer).click(function(event){
                                
                                    if($('#Profile' + ' #' +indicatorId).length > 0){
                                        if(!$('#Profile').hasClass('active')){
                                            switchProfileContext();
                                        }
                                        indicatorSelectionContainer[0].scrollIntoView(true);
                                    }
                                    else if($('#Context' + ' #' +indicatorId).length > 0){
                                        if(!$('#Context').hasClass('active')){
                                            switchProfileContext();
                                        }
                                        
                                    }
                                    //expanding all the parents, to see the indicator if hidden
                                    var elementToExpand = indicatorSelectionContainer[0];
                                    while(typeof $(elementToExpand).parent()[0] != 'undefined'){//for each of the ancestors, seee if it has a child with 'glyphicon-plus'. If yes, expand
                                        if($(elementToExpand).children('.glyphicon-plus').length > 0){
                                            $(elementToExpand).children('.glyphicon-plus').each(function(){
                                                $(this).trigger('click');
                                            });
                                        }
                                        
                                        elementToExpand = $(elementToExpand).parent()[0]
                                    }
                                   
                                    indicatorSelectionContainer[0].scrollIntoView(true); 
                                        
                                        
                                });
                                
                                
                                
                                return constraintContainer;
                            }
                            
                            function getIndicatorInfos(){
                                indicatorSelectionContainer = $('#ProfileAndContext').find('#'+indicatorId);
                                indicatorName = $($(indicatorSelectionContainer).find('.elementName')[0]).text();
                            }
                        }
                       
                        
                        
                        
                        
                        
                        
                        
                        function todoWhenConditionReady(){
                            
                            var thenContainer = getConsequencesContainer($(rule).find('then')[0], 'then');
                            var elseContainer = getConsequencesContainer($(rule).find('else')[0], 'else');
                            
                            $(ruleContainer).append(ifContainer);
                            $(ruleContainer).append(thenContainer);
                            $(ruleContainer).append(elseContainer);
                            $(ruleContainer).insertBefore($(insBefore));
                        
                        }
                        
                        //from one of the 2 tags containing the activities (<then> or <else>), returns the activities contained in it, displayed with their parameters
                        //containerName = 'then' or 'else'
                        function getConsequencesContainer(consequencesElement, containerName){
                        
                            var consequenceContainer = $('<div>').addClass(containerName).append($('<span>').append(_(containerName.toUpperCase())+_('strategy.rules.learnerActivity')));
                            
                            if(editMode){//displaying '+' icon to add a new activity
                                var activityAdder = $('<span>').addClass('glyphicon glyphicon-plus activityAdder').attr('title', _('Add activity'));
                                $(activityAdder).click(function(){
                                    var operationIfClicked;
                                    if(containerName == 'then'){
                                        operationIfClicked = 4;
                                    }
                                    else{
                                        operationIfClicked = 7;
                                    }
                                    goFromTo(currentOperation, operationIfClicked);
                                });
                                $(consequenceContainer).append(activityAdder);
                            }
                            
                            
                            var activitiesContainer = $('<ul>');
                            $(consequencesElement).find('activity').each(function(){
                                var activity = this;
                                var activityContainer = $('<li>').addClass('activityContainer');
                                
                                
                                
                                
                                var typeOfActivityId = $($(this).find("typeofactivity")[0]).text();
                                var typeOfActivityContainer = $('<span>').addClass('typeOfActivity').append(' '+activitiesDictionnary[typeOfActivityId] + _('strategy.rules.parametersListIntro'));
                                
                                
                                
                                //link between activity in the rule and activity in the right part when hovering and clicking
                                var typeOfActivitySelectionContainer = $('#Activities #'+typeOfActivityId);
                                //var typeOfActivitySelectionContainerColor = $(typeOfActivitySelectionContainer).css('background-color');
                                
                                $(typeOfActivityContainer).hover(function(){
                                    $(typeOfActivitySelectionContainer).css('background-color', '#FF7F24');
                                },
                                function(){
                                    $(typeOfActivitySelectionContainer).css('background-color', 'transparent');
                                });
                                
                                $(typeOfActivityContainer).click(function(){
                                    typeOfActivitySelectionContainer[0].scrollIntoView(true); 
                                
                                
                                });
                                
                                if(editMode){
                                    var activityRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle activityRemover').attr('title', _('Remove activity'));
                                    $(typeOfActivityContainer).append(activityRemover);
                                    $(activityRemover).click(function(){
                                        if(confirm(_('strategy.rules.newrule.deleteActivity'))){										
                                            activity.remove();
                                            goFromTo(currentOperation, 11);
                                        }
                                    });
                                    
                                    var activityUpper = $('<span>').addClass('glyphicon glyphicon glyphicon-arrow-up activityUpper').attr('title', _('Place above'));
                                    $(typeOfActivityContainer).append(activityUpper);
                                    $(activityUpper).click(function(){							
                                        $(activity).insertBefore($(activity).prev());
                                        goFromTo(currentOperation, 11);
                                    });
                                }
                                
                                var parametersContainer =$('<ul>').addClass('parameters');
                                $(this).find('parameter').each(function(){
                                    var parameter = this;
                                    var paramId = $($(this).find('id')[0]).text();
                                    var paramValue = $($(this).find('value')[0]).text();
                                    var paramValueContainer = $('<span>').append(paramValue);
                                    var parameterContainer = $('<li>').append((parametersDictionnary[paramId]).name+_(': ')).append(paramValueContainer);
                                    
                                    if(editMode && formToDisplay == 'refValueParameter' && parameter == currentParameter){//currently edited parameter, and we want to change its value.
                                        var valueInput = $('<input>').attr('type', 'text').attr('value', paramValue);
                                        $(paramValueContainer).append(valueInput);
                                        var formValidator = ($('<span>').addClass('glyphicon glyphicon-ok-sign').attr('title', _('Validate')));
                                        $(paramValueContainer).append(formValidator);
                                        
                                        $(formValidator).unbind('click').click(function(){
                                            var val = $(valueInput).val();
                                            $(reader).trigger("leafValueReading",  [val, 0, '#newRuleContainer']);			
                                        });
                                    }
                                    else{
                                        paramValueContainer = paramValue;
                                        
                                    }
                                        //link between parameter in the rule and parameter in the right part when hovering and clicking
                                        var parameterSelectionContainer = $('#Activities #'+typeOfActivityId +' +ul li #'+paramId);
                                        //var parameterSelectionContainerColor = $(parameterSelectionContainer).css('background-color');
                                        
                                        $(parameterContainer).hover(function(){
                                            $(parameterSelectionContainer).css('background-color', '#FF7F24');
                                        },
                                        function(){
                                            $(parameterSelectionContainer).css('background-color', 'transparent');
                                        });
                                        
                                        $(parameterContainer).click(function(){
                                            parameterSelectionContainer[0].scrollIntoView(true);
                                        });
                                    
                                    
                                    
                                    
                                    if(editMode){
                                        var parameterValueEditor = $('<span>').addClass('glyphicon glyphicon-edit parameterValueEditor').attr('title', _('Edit value'));
                                        $(parameterContainer).append(parameterValueEditor);
                                        $(parameterValueEditor).click(function(){
                                            if($('#newRuleContainer').find('input').length === 0){//no other form is displayed currently (you can't edit more than one parameter at once)
                                                currentParameter = parameter;
                                                goFromTo(currentOperation, 6);
                                            }
                                        });
                                    
                                        var parameterRemover = $('<span>').addClass('glyphicon glyphicon-remove-circle parameterRemover').attr('title', _('Remove parameter'));
                                        $(parameterContainer).append(parameterRemover);
                                        $(parameterRemover).click(function(){
                                            parameter.remove();
                                            goFromTo(currentOperation, 11);
                                        });
                                    
                                    }
                                    
                                    $(parametersContainer).append(parameterContainer);
                                    
                                });
                                
                                if(editMode){//enabling to add a parameter
                                    if(formToDisplay == 'parameter' && currentActivity == activity){//highlight this zone where the user is chosing a new parameter
                                        $(parametersContainer).append($('<li>').addClass('newParameterToAdd').addClass('instruction').append(_('strategy.rules.newrule.chose.parameter')));
                                    }
                                    else{//display add parameter button
                                    
                                        var parameterAdder = $('<span>').addClass('glyphicon glyphicon-plus parameterAdder').attr('title', _('Add parameter'));
                                        parametersContainer.append($('<li>').append(parameterAdder));
                                        $(parameterAdder).click(function(){
                                            currentActivity = activity;
                                            goFromTo(currentOperation, 5);
                                        });
                                    }
                                }
                                
                                $(activityContainer).append(typeOfActivityContainer);
                                $(activityContainer).append(parametersContainer);
                                $(activitiesContainer).append(activityContainer);
                                
                                
                                
                            });
                            if(editMode){
                                if(formToDisplay == 'thenActivity' && containerName == 'then'){
                                    $(activitiesContainer).append($('<div>').addClass('instruction').text(_('strategy.rules.newrule.chose.activity')));
                                }
                                else if(formToDisplay == 'elseActivity' && containerName == 'else'){
                                    $(activitiesContainer).append($('<div>').addClass('instruction').text(_('strategy.rules.newrule.chose.activity')));
                                }
                            
                            }
                            
                            
                            
                            return $(consequenceContainer.append(activitiesContainer));
                            
                        }
                        
                    
                    }
                    
                    
                });
                
                    
                    
                  
            },
            cache: false
        });//get live context
    
    
        }});// get profile
        });//get json resourcesData
        });//get json context
        });//get json profile
        }});//strategy file
        }});//translation file
        });//jquery entry
        
            
        </script>
    </body>
</html>
