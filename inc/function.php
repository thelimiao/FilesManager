<?php

// Initialisation du nom du dossier racine de l'application
$app = "fileslister";
$url = explode($app, $_SERVER['REQUEST_URI']);
if(count($url) == 1){
    define('WEBROOT', '/');
}else{
    define('WEBROOT', $url[0] . $app . '/');
}

// Simple fonction pour mieux debug
function debug($variable){
    echo '<pre>' . print_r($variable, true) . '</pre>';
}

// Démarre la session si il y en as pas.
function is_session(){
  if(session_status() == PHP_SESSION_NONE){
      session_start();
  }
}

// Fonction qui vérifie si il y a une authentification en cours
function is_authenticated(){
    if(!isset($_SESSION['auth'])){
        $_SESSION['flash']['danger'] = 'Vous devez vous connectez';
        header('location: '.WEBROOT.'login.php');
        exit();
    }
}

// Fonction qui vérifie si la personne authentifié est dans le groupe admin
function is_admin(){
    is_authenticated();

    $rank_id = $_SESSION['auth']->id_rank;
    $req = $pdo->prepare('SELECT name FROM ranks WHERE id = ?');
    $req->execute([$rank_id]);
    $rank_name = $req->fetch();

    if(!$rank_name->name == "admin"){
      $_SESSION['flash']['danger'] = "Vous n'avez pas les permission pour accéder à cette page";
      header('location: '.WEBROOT.'index.php');
      exit();
    }

}

?>
