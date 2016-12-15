<?php
  $page = 'file';
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);
  $directory = check_directory($_SESSION['auth']->id);

  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){

    if(isset($_GET['type'])){

      if($_GET['type'] == 'folder'){
        $id = $pdo->quote($_GET['id']);
        $req = $pdo->query("SELECT * FROM files WHERE id = $id");
        while($data = $req->fetch()){
          $fileName = $data->name;
          $directoryLocation = $data->id_directory;
        }
        if(isset($fileName)){
          $explode = explode(".", $fileName);
          $count = count($explode);
          $extension = $explode[$count-1];

          if($extension == 'mp4' || $extension == 'webm' || $extension == 'mkv'){
            $fileType = 'video';
          }elseif($extension == 'mp3' || $extension == 'wav' || $extension == 'ogg'){
            $fileType = 'sound';
          }elseif($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif' || $extension == 'bmp' || $extension == 'svg' || $extension == 'icon'){
            header('location: directory/'.$directoryLocation.'/'.$fileName);
            exit();
          }elseif($extension == 'pdf'){
            header('location: directory/'.$directoryLocation.'/'.$fileName);
            exit();
          }else{
            $_SESSION['flash']['danger'] = 'Format du fichier non pris en charge';
            header('location: index.php');
            exit();
          }

        }else{
          $_SESSION['flash']['danger'] = 'Aucun fichier ne correspond à cette url';
          header('location: index.php');
          exit();
        }

      }elseif($_GET['type'] == 'link'){
        $id = $pdo->quote($_GET['id']);
        $req = $pdo->query("SELECT * FROM internal WHERE id = $id");
        while($data = $req->fetch()){
          $location = $data->location;
        }

        if(isset($_GET['dir']) && $_GET['dir'] == 'yes'){
          // Si dans un dossier


        }else{
          // Si dans la racine du répertoire


        }
      }else{
        $_SESSION['flash']['danger'] = 'Aucun fichier ne correspond à cette url';
        header('location: index.php');
        exit();
      }
    }else{
      $_SESSION['flash']['danger'] = 'Aucun fichier ne correspond à cette url';
      header('location: index.php');
      exit();
    }
  }else{
    $_SESSION['flash']['warning'] = 'Aucun fichier sélectionné';
    header('location: index.php');
    exit();
  }

?>


  <div class="container">
    <br/>

    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-left">
          <li><a href="logout.php"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Mes dossiers</a></li>
          <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> &nbsp;Retourner à l'accueil</a></li>
        </ul>

        <ul class="nav nav-pills pull-right">
          <?php

          if($rank === "admin"){
            echo '<li><a href="admin/index.php"><span class="glyphicon glyphicon-user"></span> Panel admin</a></li>';
          }
          ?>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Se déconnecter</a></li>
        </ul>

      </nav>

    </div>

    <br/>

    <div class="jumbotron text-center">

      <?php

        if($fileType === 'video'){
          echo '<h2><strong>Vous regardez '.$fileName.'</strong></h2>';
          echo '<br/>';
          echo '<p><video width="720" controls>
                  <source src="directory/'.$directoryLocation.'/'.$fileName.'" type="video/'.$extension.'">
                  Votre navigateur ne supporte pas la lecture de vidéo avec HTML5.
                </video></p>';
        }elseif($fileType === 'sound'){
          echo '<h2><strong>Vous écoutez '.$fileName.'</strong></h2>';
          echo '<br/>';
          echo '<p><audio controls>
                  <source src="directory/'.$directoryLocation.'/'.$fileName.'" type="audio/'.$extension.'">
                otre navigateur ne supporte pas la lecture de son avec HTML5.
                </audio></p>';
        }

      ?>



    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
