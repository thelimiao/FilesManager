<?php

$user = "root";
$pass = "root";

try{
  $pdo = new PDO('mysql:host=localhost;dbname=fileslister', $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

}catch(PDOException $e){
  print "Erreur lors de la connexion à la base de données : " . $e->getMessage() . "<br/>";
  die();
}


?>
