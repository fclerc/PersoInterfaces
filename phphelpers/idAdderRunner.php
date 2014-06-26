<?php
    include 'XMLIdAdder.class.php';
    $x = new XMLIdAdder();
    
    $x->addIdsToXML('../data/resources/foveaResources.xml', 'R', '', 'all');
?>