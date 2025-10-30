<?php 
session_start();
if(!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true){
    header('Location: index.php');
    exit;
}

session_unset();

if(session_destroy()){
    header('Location: index.php');
}