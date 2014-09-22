<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>jQuery.parseXML demo</title>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
<p id="someElement"></p>
<p id="anotherElement"></p>
<script>
var xml = "<rss><channel><title>RSS Title</title></channel></rss>",
xmlDoc = $.parseXML( xml ),
$xml = $( xmlDoc ),
$title = $xml.find( "title" );
// Append "RSS Title" to #someElement
$( "#someElement" ).append( $title.text() );
// Change the title to "XML Title"
$title.text( "XML Title" );
// Append "XML Title" to #anotherElement
$( "#anotherElement" ).append( $title.text() );
</script>
</body>
</html>