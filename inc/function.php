<?php

// Fonction qui gère la répartition des redirections
// $page est le paramètre du nom du fichier que nous souhaite avoir en redirection
// $meaning est le paramètre qui défini l'emplacement du fichier que nous souhaitons être rediriger
// (admin) pour dir que le fichier est dans "/admin/" ou (racine) pour dire qu'il est dans la racine de l'application
// $meaning vide = "racine" par défaut exemple redirection_link('index');
function redirection_link($page, $meaning = "racine"){
  if($meaning == 'admin'){
    if(basename(__DIR__) == 'admin'){
      header('location: '.$page.'.php');
    }else{
      header('location: /admin/'.$page.'.php');
    }
  }elseif($meaning == 'racine'){
    if(basename(__DIR__) == 'admin'){
      header('location: ../'.$page.'.php');
    }else{
      header('location: '.$page.'.php');
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
    is_authenticated();

    $rank_id = $pdo->quote($_SESSION['auth']->id_rank);
    $req = $pdo->query("SELECT name FROM ranks WHERE id = $rank_id");
    $rank = $req->fetch();

    if(isset($rank) && $rank->name != "admin"){
      $_SESSION['flash']['danger'] = "Vous n'avez pas les permissions pour accéder à cette page";
      redirection_link('index');
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
  if(!file_exists('directory/'.$name)){
    if(!mkdir('directory/'.$name, 0775, true)){
      $_SESSION['flash']['danger'] = "Impossible de créer le dossier, vérifier les permissions du serveur web sur l'application";
      redirection_link('directory','admin');
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
    redirection_link('directory','admin');
    die();
  }
}

// Fonction qui permet de supprimer un répertoire
function remove_directory($name){
  $id = trim($name, "'");
  if(file_exists('directory/'.$id)){
    if(!rmdir('directory/'.$id)){
      $_SESSION['flash']['danger'] = "Impossible de supprimer le dossier, vérifier les permissions du serveur web sur l'application";
      redirection_link('directory','admin');
      die();
    }
  }
}

?>
