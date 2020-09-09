<?php
session_start();
require_once "mysql.php";
if(!isset($_SESSION['username'])){
  header('Location: login.php');
  return;
}
else{
  header('Location: profile.php');
  return;
}

 ?>
