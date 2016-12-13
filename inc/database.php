<?php

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "filesmanager";

// On essaye de se connecter à la base de données
try{
  // Les informations de la base de données
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

  // Attribut pour définir les type de messages d'erreurs
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

  // Encodage des données récupérées
  $pdo->exec('SET NAMES utf8');

// En cas d'échec, renvoyer l'erreur
}catch(PDOException $e){
  print "Erreur : " . $e->getMessage() . "<br/>";
  die();
}

?>
