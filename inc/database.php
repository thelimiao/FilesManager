<?php

$user = "root";
$pass = "root";

try{
  $pdo = new PDO('mysql:host=localhost;dbname=filesmanager', $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
  $pdo->exec('SET NAMES utf8');

}catch(PDOException $e){
  print "Erreur : " . $e->getMessage() . "<br/>";
  die();
}


?>
