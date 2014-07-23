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
        <link href="css/d3.css" type="text/css" rel="stylesheet"/>
        
    </head>
    
    <body>
		<div class="container">
			<h1><span class="toTranslate">statistics.h1</span></h1>
            
			<p><a href="index.php" id="mainLink">common.back</a></p>
			<p id="generalInstructions">statistics.instructions</p>
			<div class="pie"></div>
			<div id="statistics">
			
                <?php
					/*
					Structure : use empty.xml to know the structure of the profile. Then array 'profileElementId' => list_of_all_values.
					Then used to make statistics, displaying all the statistic elements in the right order of the profile (if possible in the form of a tree, like for values modification)
					*/
					
					$pathToProfiles = 'data/learners';
					$learnersFiles = scandir($pathToProfiles);
					$nbOfLearners = count($learnersFiles) - 2;
                    $idToIndicatorName = array();
					$initialData = array();
					$emptyProfilePath = 'data/teacher/profiles/empty.xml';					
					$emptyProfile= new DOMDocument();
					$emptyProfile->load($emptyProfilePath);
					
                    $indicatorsToIgnore = array('id', 'email', 'birthDate', 'registrationDate');
                    
					$allElements = $emptyProfile->getElementsByTagName('*');
					foreach($allElements as $element){
						if($element->childNodes->length === 0){
							if(!in_array($element->tagName, $indicatorsToIgnore)){
								if($element->getAttribute('fixed') != 'true'){
									$elementId = $element->getAttribute('id');
									$initialData[$elementId] = array();
                                    $idToIndicatorName[$elementId] = $element->tagName;
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
							
							foreach($initialData as $profileId => $arr){
								$query = "//*[@id='".$profileId."']";
								$value = $xpathProfile->query($query)->item(0)->nodeValue;
								$initialData[$profileId][] = $value;
							}
						}
					}
                    
                    
                    $scalesFile = file_get_contents('data/schemas/profileScales.json');
                    $scales = json_decode($scalesFile);
                    
                    $data = array();
                    
                    foreach($initialData as $indicatorId=>$values){
                        $data[$indicatorId] = array();
                        $indicatorScale = $scales->$idToIndicatorName[$indicatorId];
                        
                        $pieTypes = array('xs:string', 'xs:integer', 'xs:NCNAME', 'xs:boolean');
                        if(isset($indicatorScale->nature) && (($indicatorScale->nature == 'restriction' && isset($indicatorScale->baseTypeName) && in_array($indicatorScale->baseTypeName, $pieTypes))  ||  $indicatorScale->nature == 'predefined' && ($indicatorScale->typeName == 'xs:boolean' || $indicatorScale->typeName == 'xs:string' ))){
                            $data[$indicatorId]['chart'] = 'pie';
                            
                            //data will contain pairs 'value' => nbOfLearnersHavingThisValue
                            $data[$indicatorId]['data'] = array();
                            foreach($values as $value){
                                if(isset($data[$indicatorId]['data'][$value])){
                                 $data[$indicatorId]['data'][$value]++;
                                }
                                else{
                                    $data[$indicatorId]['data'][$value] = 1;
                                }
                            }
                        }
                        
                        else{
                            $data[$indicatorId]['chart'] = 'boxplot';
                            $data[$indicatorId]['data'] = $values;
                        }
                    
                    }
                    
                    echo '<p>There are currently '.$nbOfLearners.' students in the MOOC';
                    
				
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
        
        <script src="js/d3.min.js" charset="utf-8"></script>
        <script>
//data = [{'name': 'M' , 'value' : 1200}, {'name': 14 , 'value' : 120}, {'name': 16 , 'value' : 120}];
//displayPie(data, '.pie');
function displayPie(data, target){
    var width = 500,
        height = 350,
        radius = Math.min(width, height) / 2;

    var color = d3.scale.ordinal()
        .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

    var arc = d3.svg.arc()
        .outerRadius(radius - 10)
        .innerRadius(0);

    var pie = d3.layout.pie()
        .sort(null)
        .value(function(d) { return d.value; });

    var svg = d3.select(target).append("svg")
        .attr("width", width)
        .attr("height", height)
      .append("g")
        .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

      data.forEach(function(d) {
        d.value = +d.value;
      });

      var g = svg.selectAll(".arc")
          .data(pie(data))
        .enter().append("g")
          .attr("class", "arc");

      g.append("path")
          .attr("d", arc)
          .style("fill", function(d) { return color(d.data.name); });

      g.append("text")
          .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
          .attr("dy", ".35em")
          .style("text-anchor", "middle")
          .text(function(d) { return d.data.name; });
}






(function() {

// Inspired by http://informationandvisualization.de/blog/box-plot
d3.box = function() {
  var width = 1,
      height = 1,
      duration = 0,
      domain = null,
      value = Number,
      whiskers = boxWhiskers,
      quartiles = boxQuartiles,
	  showLabels = true, // whether or not to show text labels
	  numBars = 4,
	  curBar = 1,
      tickFormat = null;

  // For each small multiple…
  function box(g) {
    g.each(function(data, i) {
      //d = d.map(value).sort(d3.ascending);
	  //var boxIndex = data[0];
	  //var boxIndex = 1;
	  var d = data[1].sort(d3.ascending);
	  
	 // console.log(boxIndex); 
	  //console.log(d); 
	  
      var g = d3.select(this),
          n = d.length,
          min = d[0],
          max = d[n - 1];

      // Compute quartiles. Must return exactly 3 elements.
      var quartileData = d.quartiles = quartiles(d);

      // Compute whiskers. Must return exactly 2 elements, or null.
      var whiskerIndices = whiskers && whiskers.call(this, d, i),
          whiskerData = whiskerIndices && whiskerIndices.map(function(i) { return d[i]; });

      // Compute outliers. If no whiskers are specified, all data are "outliers".
      // We compute the outliers as indices, so that we can join across transitions!
      var outlierIndices = whiskerIndices
          ? d3.range(0, whiskerIndices[0]).concat(d3.range(whiskerIndices[1] + 1, n))
          : d3.range(n);

      // Compute the new x-scale.
      var x1 = d3.scale.linear()
          .domain(domain && domain.call(this, d, i) || [min, max])
          .range([height, 0]);

      // Retrieve the old x-scale, if this is an update.
      var x0 = this.__chart__ || d3.scale.linear()
          .domain([0, Infinity])
		 // .domain([0, max])
          .range(x1.range());

      // Stash the new scale.
      this.__chart__ = x1;

      // Note: the box, median, and box tick elements are fixed in number,
      // so we only have to handle enter and update. In contrast, the outliers
      // and other elements are variable, so we need to exit them! Variable
      // elements also fade in and out.

      // Update center line: the vertical line spanning the whiskers.
      var center = g.selectAll("line.center")
          .data(whiskerData ? [whiskerData] : []);

	 //vertical line
      center.enter().insert("line", "rect")
          .attr("class", "center")
          .attr("x1", width / 2)
          .attr("y1", function(d) { return x0(d[0]); })
          .attr("x2", width / 2)
          .attr("y2", function(d) { return x0(d[1]); })
          .style("opacity", 1e-6)
        .transition()
          .duration(duration)
          .style("opacity", 1)
          .attr("y1", function(d) { return x1(d[0]); })
          .attr("y2", function(d) { return x1(d[1]); });

      center.transition()
          .duration(duration)
          .style("opacity", 1)
          .attr("y1", function(d) { return x1(d[0]); })
          .attr("y2", function(d) { return x1(d[1]); });

      center.exit().transition()
          .duration(duration)
          .style("opacity", 1e-6)
          .attr("y1", function(d) { return x1(d[0]); })
          .attr("y2", function(d) { return x1(d[1]); })
          .remove();

      // Update innerquartile box.
      var box = g.selectAll("rect.box")
          .data([quartileData]);

      box.enter().append("rect")
          .attr("class", "box")
          .attr("x", 0)
          .attr("y", function(d) { return x0(d[2]); })
          .attr("width", width)
          .attr("height", function(d) { return x0(d[0]) - x0(d[2]); })
        .transition()
          .duration(duration)
          .attr("y", function(d) { return x1(d[2]); })
          .attr("height", function(d) { return x1(d[0]) - x1(d[2]); });

      box.transition()
          .duration(duration)
          .attr("y", function(d) { return x1(d[2]); })
          .attr("height", function(d) { return x1(d[0]) - x1(d[2]); });

      // Update median line.
      var medianLine = g.selectAll("line.median")
          .data([quartileData[1]]);

      medianLine.enter().append("line")
          .attr("class", "median")
          .attr("x1", 0)
          .attr("y1", x0)
          .attr("x2", width)
          .attr("y2", x0)
        .transition()
          .duration(duration)
          .attr("y1", x1)
          .attr("y2", x1);

      medianLine.transition()
          .duration(duration)
          .attr("y1", x1)
          .attr("y2", x1);

      // Update whiskers.
      var whisker = g.selectAll("line.whisker")
          .data(whiskerData || []);

      whisker.enter().insert("line", "circle, text")
          .attr("class", "whisker")
          .attr("x1", 0)
          .attr("y1", x0)
          .attr("x2", 0 + width)
          .attr("y2", x0)
          .style("opacity", 1e-6)
        .transition()
          .duration(duration)
          .attr("y1", x1)
          .attr("y2", x1)
          .style("opacity", 1);

      whisker.transition()
          .duration(duration)
          .attr("y1", x1)
          .attr("y2", x1)
          .style("opacity", 1);

      whisker.exit().transition()
          .duration(duration)
          .attr("y1", x1)
          .attr("y2", x1)
          .style("opacity", 1e-6)
          .remove();

      // Update outliers.
      var outlier = g.selectAll("circle.outlier")
          .data(outlierIndices, Number);

      outlier.enter().insert("circle", "text")
          .attr("class", "outlier")
          .attr("r", 5)
          .attr("cx", width / 2)
          .attr("cy", function(i) { return x0(d[i]); })
          .style("opacity", 1e-6)
        .transition()
          .duration(duration)
          .attr("cy", function(i) { return x1(d[i]); })
          .style("opacity", 1);

      outlier.transition()
          .duration(duration)
          .attr("cy", function(i) { return x1(d[i]); })
          .style("opacity", 1);

      outlier.exit().transition()
          .duration(duration)
          .attr("cy", function(i) { return x1(d[i]); })
          .style("opacity", 1e-6)
          .remove();

      // Compute the tick format.
      var format = tickFormat || x1.tickFormat(8);

      // Update box ticks.
      var boxTick = g.selectAll("text.box")
          .data(quartileData);
	 if(showLabels == true) {
      boxTick.enter().append("text")
          .attr("class", "box")
          .attr("dy", ".3em")
          .attr("dx", function(d, i) { return i & 1 ? 6 : -6 })
          .attr("x", function(d, i) { return i & 1 ?  + width : 0 })
          .attr("y", x0)
          .attr("text-anchor", function(d, i) { return i & 1 ? "start" : "end"; })
          .text(format)
        .transition()
          .duration(duration)
          .attr("y", x1);
	}
		 
      boxTick.transition()
          .duration(duration)
          .text(format)
          .attr("y", x1);

      // Update whisker ticks. These are handled separately from the box
      // ticks because they may or may not exist, and we want don't want
      // to join box ticks pre-transition with whisker ticks post-.
      var whiskerTick = g.selectAll("text.whisker")
          .data(whiskerData || []);
	if(showLabels == true) {
      whiskerTick.enter().append("text")
          .attr("class", "whisker")
          .attr("dy", ".3em")
          .attr("dx", 6)
          .attr("x", width)
          .attr("y", x0)
          .text(format)
          .style("opacity", 1e-6)
        .transition()
          .duration(duration)
          .attr("y", x1)
          .style("opacity", 1);
	}
      whiskerTick.transition()
          .duration(duration)
          .text(format)
          .attr("y", x1)
          .style("opacity", 1);

      whiskerTick.exit().transition()
          .duration(duration)
          .attr("y", x1)
          .style("opacity", 1e-6)
          .remove();
    });
    d3.timer.flush();
  }

  box.width = function(x) {
    if (!arguments.length) return width;
    width = x;
    return box;
  };

  box.height = function(x) {
    if (!arguments.length) return height;
    height = x;
    return box;
  };

  box.tickFormat = function(x) {
    if (!arguments.length) return tickFormat;
    tickFormat = x;
    return box;
  };

  box.duration = function(x) {
    if (!arguments.length) return duration;
    duration = x;
    return box;
  };

  box.domain = function(x) {
    if (!arguments.length) return domain;
    domain = x == null ? x : d3.functor(x);
    return box;
  };

  box.value = function(x) {
    if (!arguments.length) return value;
    value = x;
    return box;
  };

  box.whiskers = function(x) {
    if (!arguments.length) return whiskers;
    whiskers = x;
    return box;
  };
  
  box.showLabels = function(x) {
    if (!arguments.length) return showLabels;
    showLabels = x;
    return box;
  };

  box.quartiles = function(x) {
    if (!arguments.length) return quartiles;
    quartiles = x;
    return box;
  };

  return box;
};

function boxWhiskers(d) {
  return [0, d.length - 1];
}

function boxQuartiles(d) {
  return [
    d3.quantile(d, .25),
    d3.quantile(d, .5),
    d3.quantile(d, .75)
  ];
}

})();







var input = [1,2,4];
//displayBoxPlot(input, ".boxplot", 'Students');

function displayBoxPlot(input, target, title){
    for(id in input){
        input[id] = parseInt(input[id]);
    }

    var labels = true; // show the text labels beside individual boxplots?

    var margin = {top: 30, right: 50, bottom: 70, left: 50};
    var  width = 800 - margin.left - margin.right;
    var height = 400 - margin.top - margin.bottom;
        
    var min = Infinity,
        max = -Infinity;
        
    // parse in the data	

        // using an array of arrays with
        // data[n][2] 
        // where n = number of columns in the csv file 
        // data[i][0] = name of the ith column
        // data[i][1] = array of values of ith column

        var data = [];
        data[0] = [];
        // add more rows if your csv file has more columns

        // add here the header of the csv file
        data[0][0] = "Q1";
        // add more rows if your csv file has more columns

        data[0][1] = [];
      
        input.forEach(function(x) {
            var v1 = Math.floor(x)
                // add more variables if your csv file has more columns
                
            var rowMax = Math.max(v1);
            var rowMin = Math.min(v1);

            data[0][1].push(v1);
             // add more rows if your csv file has more columns
             
            if (rowMax > max) max = rowMax;
            if (rowMin < min) min = rowMin;	
        });
      
        var chart = d3.box()
            .whiskers(iqr(1.5))
            .height(height)	
            .domain([min, max])
            .showLabels(labels);

        var svg = d3.select(target).append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .attr("class", "box")    
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
        
        // the x-axis
        var x = d3.scale.ordinal()	   
            .domain( data.map(function(d) { return d[0] } ) )	    
            .rangeRoundBands([0 , width], 0.7, 0.3); 		

        var xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom");

        // the y-axis
        var y = d3.scale.linear()
            .domain([min, max])
            .range([height + margin.top, 0 + margin.top]);
        
        var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left");

        // draw the boxplots	
        svg.selectAll(".box")	   
          .data(data)
          .enter().append("g")
            .attr("transform", function(d) { return "translate(" +  x(d[0])  + "," + margin.top + ")"; } )
          .call(chart.width(x.rangeBand())); 
        
              
        // add a title
        /* svg.append("text")
            .attr("x", (width / 2))             
            .attr("y", 0 + (margin.top / 2))
            .attr("text-anchor", "middle")  
            .style("font-size", "18px") 
            //.style("text-decoration", "underline")  
            .text(title); */
     
         // draw y axis
        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis);
            // .append("text") // and text1
              // .attr("transform", "rotate(-90)")
              // .attr("y", 6)
              // .attr("dy", ".71em")
              // .style("text-anchor", "end")
              // .style("font-size", "16px") 
              // .text("Revenue in €");		
        
        // draw x axis	
        svg.append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + (height  + margin.top + 10) + ")")
          .call(xAxis)
         /*  .append("text")             // text label for the x axis
            .attr("x", (width / 2) )
            .attr("y",  10 )
            .attr("dy", ".71em")
            .style("text-anchor", "middle")
            .style("font-size", "16px") 
            .text("Quarter"); */

    // Returns a function to compute the interquartile range.
    function iqr(k) {
      return function(d, i) {
        var q1 = d.quartiles[0],
            q3 = d.quartiles[2],
            iqr = (q3 - q1) * k,
            i = -1,
            j = d.length;
        while (d[++i] < q1 - iqr);
        while (d[--j] > q3 + iqr);
        return [i, j];
      };
    }
}

        

var data = <?php echo json_encode($data); ?>;

displayChartsTree(data, 'data/teacher/profiles/empty.xml', '#statistics');
function displayChartsTree(data, structureFile, container){
    $.ajax({
    type: "GET",
    url: structureFile,
    success: function(structure){
        structure = $(structure)
        
        //going recursively through structure, to display tag names or charts
        $(container).append($('<div>').addClass('container').append(displayAndChildren($(structure).children().first()[0], data) ));
		displayCharts(data);
        //for elements having list below them : toggle visibility of this list when clicking on the element
        $(container +' .reducer').click(function(event){
            var toToggle = $(event.target).next().next();
            if(toToggle[0].nodeName != 'ul' && toToggle[0].nodeName != 'UL'){//in case there is the information icon, go one step further to find the list to hide.
                toToggle = $(toToggle).next()
            }
            if(toToggle[0].nodeName != 'ul' && toToggle[0].nodeName != 'UL'){//in case there is a value, go one step further to find the list to hide.
                toToggle = $(toToggle).next()
            }
                $(toToggle).toggle(300);
                
                //just changing the glyphicon
                if($(event.target).hasClass('glyphicon-plus')){
                    $(event.target).addClass('glyphicon-minus');
                    $(event.target).removeClass('glyphicon-plus');
                }
                else{
                    $(event.target).addClass('glyphicon-plus');
                    $(event.target).removeClass('glyphicon-minus');
                }
            
            return false;
        });
    }});
}

function displayAndChildren(xmlNode, data){
    var nodeName = xmlNode.nodeName;
    var elementNameContainer = $('<span>').append(nodeName).addClass('elementName');
    var result = $('<li>').attr('id', $(xmlNode).attr('id')).append(elementNameContainer); 
    
    if($(xmlNode).children().length>0 ){
        $(result).addClass('hasChild');
        $(result).prepend($('<span>').addClass('glyphicon glyphicon-minus').addClass('reducer'));
        var chs = $('<ul>');
        $(xmlNode).children().each(function(){
            $(chs).append(displayAndChildren(this, data));
        });
        
        result.append(chs);
    
    }
    else if(typeof data[$(xmlNode).attr('id')] == 'undefined'){
        result.append($(xmlNode).html());
    }
    return result;
}

function displayCharts(data){
    for(id in data){
        if(data[id]['chart'] == 'pie'){
            $('#'+id).addClass('pie');
            dataForPie = convertForPie(data[id]['data']);
            displayPie(dataForPie, '#'+id);
        }
        else if(data[id]['chart'] == 'boxplot'){
            $('#'+id).addClass('boxplot');
            //console.log(typeof data[id]['data'][0]);
            displayBoxPlot(data[id]['data'], '#'+id, '')
        }
    }
}

function convertForPie(data){
    var result = [];
    for(elem in data){
        result.push({'name': elem, 'value': data[elem]});
    }
    return result;

}     
        
        </script>
        
    </body>
    
</html>