<?php
//Sets $lang to the right value for the user
if(isset($_GET['lang'])){
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $_GET['lang'];
}
else if(isset($_SESSION['lang'])){
    $lang = $_SESSION['lang'];
}
else{
    $lang = 'fr';
}
?>