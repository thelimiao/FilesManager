<?php

// Fonction qui gère la répartition des redirections
// $page est le paramètre du nom du fichier que nous souhaite avoir en redirection
// $meaning est le paramètre qui défini l'emplacement du fichier que nous souhaitons être rediriger
// (admin) pour dir que le fichier est dans "/admin/" ou (racine) pour dire qu'il est dans la racine de l'application
// $meaning vide = "racine" par défaut exemple redirection_link('index');
function redirection_link($page, $meaning = "racine"){
  if($meaning == 'admin'){
    if(!file_exists('admin')){
      header('location: '.$page.'.php');
    }else{
      header('location: /admin/'.$page.'.php');
    }
  }elseif($meaning == 'racine'){
    if(file_exists('admin')){
      header('location: '.$page.'.php');
    }else{
      header('location: ../'.$page.'.php');
    }
  }
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
        redirection_link('login');
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
  $id = $pdo->quote($id_rank);
  $req = $pdo->query("SELECT name FROM ranks WHERE id = $id");
  $result = $req->fetch();
  if(!empty($result)){
    return $result->name;
  }else{
    return false;
  }

}

// Fonction qui retourne l'id du dossier qui appartient à l'utilisateur via son id
function check_directory($id_user){
  if(!isset($pdo)){
    global $pdo;
  }
  $id = $pdo->quote($id_user);
  $req = $pdo->query("SELECT id FROM directory WHERE id_user = $id");
  $result = $req->fetch();
  if(!empty($result)){
    return $result->id;
  }else{
    return false;
  }
}

// Fonction qui vérifie et authorise l'accès si la personne authentifié est dans le groupe admin
function is_admin(){
  if(!isset($pdo)){
      global $pdo;
  }

  if(!isset($_SESSION['auth'])){
      $_SESSION['flash']['danger'] = 'Vous devez vous connectez';
      redirection_link('login');
      exit();
  }else{
    // Création de la clef csrf en variable de session
    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = md5(time() + rand());
    }
  }

  $rank_id = $pdo->quote($_SESSION['auth']->id_rank);
  $req = $pdo->query("SELECT name FROM ranks WHERE id = $rank_id");
  $rank = $req->fetch();

  if(isset($rank) && $rank->name != "admin"){
    $_SESSION['flash']['danger'] = "Vous n'avez pas les permissions pour accéder à cette page";
    redirection_link('index');
    exit();
  }

}

// Générateur de clé
function str_random($length){
    $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
}

// Cookie de reconnexion de session
function reconnect_cookie(){
  if(!isset($pdo)){
    global $pdo;
  }
  if(isset($_COOKIE['remember']) && (!isset($_SESSION['auth']) || empty($_SESSION['auth']))){

    $remember_token = $_COOKIE['remember'];
    $parts = explode('==', $remember_token);
    $user_id = $parts[0];
    $req = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $req->execute([$user_id]);
    $user = $req->fetch();
    if($user){
        $expected = $user_id . '==' . $user->remember_token . sha1($user_id . 'adtr');
        if($expected == $remember_token){
            $_SESSION['auth'] = $user;
            setcookie('remember', $remember_token, time() + 60 * 60 * 24 * 7);
        }
    }else{
        setcookie('remember', NULL, -1);
    }
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
      redirection_link('index');
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
  if(!file_exists('../directory/'.$name)){
    if(!mkdir('../directory/'.$name, 0775, true)){
      $_SESSION['flash']['danger'] = "Impossible de créer le dossier, vérifier les permissions du serveur web sur l'application";
      redirection_link('directory','admin');
      die();
    }
  }
}

// Fonction qui permet de supprimer le contenu d'un répertoire
function clear_directory($name){
  $id = trim($name, "'");
  if(file_exists('../directory/'.$id) && is_dir('../directory/'.$id)){
    if($handle = opendir('../directory/'.$id)){
      while(false !== ($entry = readdir($handle))){
        if($entry != "." && $entry != ".."){
          if(isset($entry)){
            unlink('../directory/'.$id.'/'.$entry);
          }
        }
      }
    }
    closedir($handle);
  }else{
    $_SESSION['flash']['danger'] = "Impossible de supprimer le contenu du dossier, le dossier n'existe pas";
    redirection_link('directory','admin');
    die();
  }
}

// Fonction qui permet de supprimer un répertoire
function remove_directory($name){
  $id = trim($name, "'");
  if(file_exists('../directory/'.$id)){
    if(!rmdir('../directory/'.$id)){
      $_SESSION['flash']['danger'] = "Impossible de supprimer le dossier, vérifier les permissions du serveur web sur l'application";
      redirection_link('directory','admin');
      die();
    }
  }
}

// Fonction qui permet de supprimer un fichier (côté utilisateur)
function remove_file($idFile){
  if(!isset($pdo)){
      global $pdo;
  }

  $id = $pdo->quote($idFile);
  $req = $pdo->query("SELECT * FROM files WHERE id = $id");
  while($data = $req->fetch()){
    $fileName = $data->name;
    $dirNumber = $data->id_directory;
  }

  if(file_exists('directory/'.$dirNumber.'/'.$fileName)){
    if(!unlink('directory/'.$dirNumber.'/'.$fileName)){
      $_SESSION['flash']['danger'] = "Impossible de supprimer le fichier, vérifier les permissions du serveur web sur l'application";
      redirection_link('index');
      die();
    }
  }
}

// Fonction qui converti une valeur MO en Octets
function moConvert($value){

  $result = $value * 1048576;
  return $result;
}

// Fonction qui converti une valeur Go en Octets
function goConvert($value){

  $result = $value * 1073741824;
  return $result;
}

// Fonction qui converti une valeur Octets en Mo
function octetConvertToMo($value){
  $result = $value / 1048576;
  return $result;
}

// Fonction qui retourne la valeur en octet la taille d'upload max présent en base de données
function maxUploadSize(){
  if(!isset($pdo)){
      global $pdo;
  }
  $req = $pdo->query("SELECT upload_size FROM settings");
  $data = $req->fetch();
  return intval($data->upload_size);
}

function fileSizeConvert($value){
  if($value >= 1073741824){
    $result = $value / 1073741824;
    return round($result, 3).' Go';
  }else{
    $result = $value / 1048576;
    return round($result, 3).' Mo';
  }
}

?>
