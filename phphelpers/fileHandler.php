<?php
session_start();
//This file treats demands of duplication, creation and deletion of files.

//storing the informations from the form in the session array
$_SESSION['file'] = $_POST['file'];
$_SESSION['path'] = $_POST['path'];
$_SESSION['section'] = $_POST['section'];
$_SESSION['schema'] = $_POST['schema'];
$_SESSION['scales'] = $_POST['scales'];


//if user wants a new file : create file by doing a copy of the corresponding 'empty.xml'
if(isset($_POST['fileCreator'])){
    $newFileName = 'RENAMEFILE.xml';
    copy('../'.$_POST['path'].'empty.xml', '../'.$_POST['path'].$newFileName);
    $_SESSION['file'] = $newFileName;
    //go to the corresponding interface
    header('Location: ../'.$_POST['action']);
}

//if user wants to duplicate the file...duplicate the file
else if(isset($_POST['fileDuplicator'])){
    $newFileName = 'RENAMEFILE.xml';
    copy('../'.$_POST['path'].$_POST['file'], '../'.$_POST['path'].$newFileName);
    $_SESSION['file'] = $newFileName;
    //go to the corresponding interface
    header('Location: ../'.$_POST['action']);


}

//remove file
else {
    unlink('../'.$_POST['path'].$_POST['file']);
    $_SESSION['fileRemoved'] = true;
    header('Location: ../');
}
?>