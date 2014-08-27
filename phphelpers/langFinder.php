<?php
//Sets $lang to the right value wished by the user

//the user clicked on one of the flags
if(isset($_GET['lang'])){
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $_GET['lang'];
}
//else the lang is stored in the session variables
else if(isset($_SESSION['lang'])){
    $lang = $_SESSION['lang'];
}
//default
else{
    $lang = 'fr';
}
?>