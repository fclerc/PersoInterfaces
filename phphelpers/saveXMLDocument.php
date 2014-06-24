<?php
//called in AJAX by POST method to save an xml document.
    $doc = new DOMDocument('1.0');
    $doc->loadXML($_POST['data']);
    $doc->save($_POST['file']);

?>