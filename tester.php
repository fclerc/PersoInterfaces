<?php
    include 'XMLIdAdder.class.php';
    $x = new XMLIdAdder();
    
    $x->addIdsToXML('foveaResources.xml', 'R', '', 'all');
?>