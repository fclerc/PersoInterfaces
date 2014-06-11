<?php
    include 'XMLIdAdder.class.php';
    $x = new XMLIdAdder();
    
    $x->addIdsToXML('foveaContext.xml', 'CTX', '', 'all');
?>