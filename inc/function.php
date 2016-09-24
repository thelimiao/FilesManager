<?php
// Active l'affichage des erreurs php
ini_set('display_errors', 1);

// Initialisation du nom du dossier racine de l'application pour les liens
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

// Fonction de récupération de l'adresse IP du visiteur
function get_ip(){
   if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   }elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
       $ip  = $_SERVER['HTTP_CLIENT_IP'];
   }else{
       $ip = $_SERVER['REMOTE_ADDR'];
   }
   return $ip;
}

// Fonction qui permet de créer un répertoire
function new_directory($name){
  if(!file_exists('directory/'.$name)){
    if(!mkdir('directory/'.$name, 0775, true)){
      $_SESSION['flash']['danger'] = "Impossible de créer le dossier, vérifier les permissions du serveur web sur l'application";
      header('Location:'.WEBROOT.'admin/directory.php');
      die();
    }
  }
}

// Fonction qui permet de supprimer le contenu d'un répertoire
function clear_directory($name){
  $id = trim($name, "'");
  if(file_exists('directory/'.$id) && is_dir('directory/'.$id)){
    if($handle = opendir('directory/'.$id)){
      while(false !== ($entry = readdir($handle))){
        if($entry != "." && $entry != ".."){
          if(!isset($entry)){
            unlink('directory'.$id.'/'.$entry);
          }
        }
      }
    }
    closedir($handle);
  }else{
    $_SESSION['flash']['danger'] = "Impossible de supprimer le contenu du dossier, le dossier n'existe pas";
    header('Location:'.WEBROOT.'admin/directory.php');
    die();
  }
}

// Fonction qui permet de supprimer un répertoire
function remove_directory($name){
  $id = trim($name, "'");
  if(file_exists('directory/'.$id)){
    if(!rmdir('directory/'.$id)){
      $_SESSION['flash']['danger'] = "Impossible de supprimer le dossier, vérifier les permissions du serveur web sur l'application";
      header('Location:'.WEBROOT.'admin/directory.php');
      die();
    }
  }
}

?>
