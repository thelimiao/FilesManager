<?php
// Active l'affichage des erreurs php
ini_set('display_errors', 1);

// Initialisation du nom du dossier racine de l'application
$app = "filesmanager";
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
    }else{
      // Création de la clef csrf en variable de session
      if(!isset($_SESSION['csrf'])){
          $_SESSION['csrf'] = md5(time() + rand());
      }
    }
}

// Fonction qui retourne le nom du grade via sont id
function check_rank($id_rank){
  if(!isset($pdo)){
      global $pdo;
  }
  $req = $pdo->query("SELECT name FROM ranks WHERE id = $id_rank");
  $result = $req->fetch();
  if(!empty($result)){
    return $result->name;
  }else{
    return false;
  }

}

// Fonction qui vérifie et authorise l'accès si la personne authentifié est dans le groupe admin
function is_admin(){
    if(!isset($pdo)){
        global $pdo;
    }
    is_authenticated();

    $rank_id = $_SESSION['auth']->id_rank;
    $req = $pdo->prepare('SELECT name FROM ranks WHERE id = ?');
    $req->execute([$rank_id]);
    $rank = $req->fetch();

    if(isset($rank) && $rank->name != "admin"){
      $_SESSION['flash']['danger'] = "Vous n'avez pas les permissions pour accéder à cette page";
      header('location: '.WEBROOT.'index.php');
      exit();
    }

}

// Fonction qui créer un champ pour l'url avec comme valeur la clef csrf
function csrf(){
    return 'csrf='.$_SESSION['csrf'];
}

// Fonction qui insert un input avec comme valeur la clef csrf
function csrfInput(){
    return '<input type="hidden" value="'.$_SESSION['csrf'].'" name="csrf">';
}

// Fonction qui vérifie la clef csrf et retourne true si elle est égale à celle de la variable de session
function checkCsrf(){
    if( (isset($_POST['csrf']) && $_POST['csrf'] == $_SESSION['csrf']) ||
        (isset($_GET['csrf']) && $_GET['csrf'] == $_SESSION['csrf']) ){

      return true;
    }else{
      $_SESSION['flash']['danger'] = "Action impossible, votre clef de session est incorrect ou inexistent.";
      header('Location:'.WEBROOT.'index.php');
      die();
    }

}

?>
