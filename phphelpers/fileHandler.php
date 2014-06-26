<?php
session_start();
//This file treats demands of duplication, creation and deletion of files.

var_dump($_POST);

$_SESSION['file'] = $_POST['file'];
$_SESSION['path'] = $_POST['path'];
$_SESSION['section'] = $_POST['section'];
$_SESSION['schema'] = $_POST['schema'];
$_SESSION['scales'] = $_POST['scales'];



if(isset($_POST['fileCreator'])){//create file with minimal structure (actually just duplicate 'empty.xml')
    $newFileName = 'RENAMEFILE.xml';
    copy('../'.$_POST['path'].'empty.xml', '../'.$_POST['path'].$newFileName);
    $_SESSION['file'] = $newFileName;
    header('Location: ../'.$_POST['action']);
}


else if(isset($_POST['fileDuplicator'])){//duplicate file
    $newFileName = 'RENAMEFILE.xml';
    copy('../'.$_POST['path'].$_POST['file'], '../'.$_POST['path'].$newFileName);
    $_SESSION['file'] = $newFileName;
    header('Location: ../'.$_POST['action']);


}


else {//remove file
    unlink('../'.$_POST['path'].$_POST['file']);
    $_SESSION['fileRemoved'] = true;
    header('Location: ../');
}




//going to the edition page of the newly created resource
//header('Location: ../'.$_POST['action']);






?>