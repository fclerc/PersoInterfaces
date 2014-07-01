<?php
    include 'XMLIdAdder.class.php';
    $x = new XMLIdAdder();
    
    $x->addIdsToXML('../data/teacher/liveContexts/liveContext1.xml', 'CTX', '','restart', 'all');
?>