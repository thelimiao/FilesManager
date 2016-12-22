<?php
  require_once 'inc/database.php';
  require_once 'inc/function.php';

  is_session();
  is_authenticated();
  if(($directory = check_directory($_SESSION['auth']->id)) == false){
    $_SESSION['flash']['danger'] = 'Vous ne disposez pas de dossier personnel pour un transfert';
    header('location: index.php');
    exit();
  }


  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
    if(isset($_GET['type']) && $_GET['type'] == 'link'){

      $id = $pdo->quote($_GET['id']);
      $req = $pdo->query("SELECT * FROM internal WHERE id = $id");
      while($data = $req->fetch()){
        $location = $data->location;
        $id_url = $data->id;
      }

      if(isset($_GET['file']) && $_GET['file'] && preg_match("/^[0-9]+$/i",$_GET['file'])){

        if(isset($_GET['dir'])){
          // Si dans un dossier
          $number = 1;
          $trans = array("@" => "+");
          $the_dir = strtr($_GET['dir'], $trans);

          if($dh = opendir($location.$the_dir)){
            while(($entry = readdir($dh)) !== false){
              if(!is_dir($entry)){
                if($number == $_GET['file']){
                  $location_file = $location.$the_dir."/".$entry;
                  $filename = $entry;
                }
                $number++;
              }

            }
            closedir($dh);
          }

          if(file_exists($location_file)){

            if(!file_exists($directory."/".basename($location_file))){
              if(!copy($location_file, "directory/".$directory."/".basename($location_file))){
                $_SESSION['flash']['danger'] = 'Impossible de copier le fichier, vérifier les droits d\'accès aux dossiers';
                header('location: explorer.php?id='.$id_url.'&type=link');
                exit();
              }else{
                $name = $pdo->quote(basename($location_file));
                $req = $pdo->query("INSERT INTO files SET name = $name, id_directory = $directory");

                $_SESSION['flash']['success'] = 'Le fichier à bien été transféré';
                header('location: explorer.php?id='.$id_url.'&type=link');
                exit();

              }
            }else{
              $_SESSION['flash']['warning'] = 'Vous disposez déjà d\'un fichier portant ce nom dans votre dossier personnel';
              header('location: explorer.php?id='.$id_url.'&type=link');
              exit();
            }

          }

        }else{
          // Si dans la racine du répertoire
          $number = 1;
          if($dh = opendir($location)){
            while(($entry = readdir($dh)) !== false){
              if(!is_dir($entry)){
                if($number == $_GET['file']){
                  $location_file = $location."".$entry;
                  $filename = $entry;
                }
                $number++;
              }
            }
            closedir($dh);
          }
          if(file_exists($location_file)){

            if(!file_exists($directory."/".basename($location_file))){
              if(!copy($location_file, "directory/".$directory."/".basename($location_file))){
                $_SESSION['flash']['danger'] = 'Impossible de copier le fichier, vérifier les droits d\'accès aux dossiers';
                header('location: explorer.php?id='.$id_url.'&type=link');
                exit();
              }else{
                $name = $pdo->quote(basename($location_file));
                $req = $pdo->query("INSERT INTO files SET name = $name, id_directory = $directory");

                $_SESSION['flash']['success'] = 'Le fichier à bien été transféré';
                header('location: explorer.php?id='.$id_url.'&type=link');
                exit();

              }
            }else{
              $_SESSION['flash']['warning'] = 'Vous disposez déjà d\'un fichier portant ce nom dans votre dossier personnel';
              header('location: explorer.php?id='.$id_url.'&type=link');
              exit();
            }

          }

        }
      }

    }
  }else{
    $_SESSION['flash']['danger'] = 'Aucun fichier sélectionné';
    header('location: index.php');
    exit();
  }

?>
