<?php
  require_once 'inc/database.php';
  require_once 'inc/function.php';

  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){

    if(isset($_GET['type']) && $_GET['type'] == 'folder'){
      $id = $pdo->quote($_GET['id']);
      $req = $pdo->query("SELECT * FROM files WHERE id = $id");
      $fileDetect = false;
      while($data = $req->fetch()){
        $fileName = $data->name;
        $dirNumber = $data->id_directory;
        $fileDetect = true;
      }
      if($fileDetect == true){
        $req = $pdo->query("SELECT * FROM settings");
        $path = $req->fetch();
        $directory = "directory/".$dirNumber."/".$fileName;
        $location = $path->path.$directory;

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-disposition: attachment; filename="' . $fileName . '"');
        readfile($location);
        exit();
      }
    }elseif(isset($_GET['type']) && $_GET['type'] == 'link'){

      $id = $pdo->quote($_GET['id']);
      $req = $pdo->query("SELECT * FROM internal WHERE id = $id");
      while($data = $req->fetch()){
        $location = $data->location;
      }

      if(isset($_GET['file']) && $_GET['file'] && preg_match("/^[0-9]+$/i",$_GET['file'])){

        if(isset($_GET['dir'])){
          // Si dans un dossier
          $number = 1;
          if($dh = opendir($location.$_GET['dir'])){
            while(($entry = readdir($dh)) !== false){
              if(!is_dir($entry)){
                if($number == $_GET['file']){
                  $location_file = $location.$_GET['dir']."/".$entry;
                  $filename = $entry;
                }
                $number++;
              }

            }
            closedir($dh);
          }

          if(file_exists($location_file)){

            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Content-disposition: attachment; filename="' . basename($location_file) . '"');
            readfile($location_file);
            exit();

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

            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Content-disposition: attachment; filename="' . basename($location_file) . '"');
            readfile($location_file);
            exit();

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
