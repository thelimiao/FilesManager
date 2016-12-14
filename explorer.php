<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);

  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
    if(isset($_GET['type']) && $_GET['type'] == 'link'){

      $id = $pdo->quote($_GET['id']);
      $id_user = $pdo->quote($_SESSION['auth']->id);
      $req = $pdo->query("SELECT * FROM access WHERE id_directory = $id AND link_directory = 1 AND id_user = $id_user");
      $access = $req->fetch();
      if(empty($access)){
        $_SESSION['flash']['danger'] = 'Accès non autorisé ou répertoire introuvable';
        header('location: directory.php');
        exit();
      }else{
        $req = $pdo->query("SELECT * FROM internal WHERE id = $id");
        $internal = $req->fetch();
      }
    }elseif(isset($_GET['type']) && $_GET['type'] == 'folder'){
      $_SESSION['flash']['warning'] = 'Fonctionnalité pas encore disponible';
      header('location: directory.php');
      exit();
    }

  }else{
    $_SESSION['flash']['danger'] = 'Répertoire introuvable';
    header('location: directory.php');
    exit();
  }

?>

  <div class="container">
    <br/>
    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-left">
          <li><a href="index.php"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Mon répertoire personnel</a></li>
          <li><a href="directory.php"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Mes répertoires partagés</a></li>
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

    <div class="jumbotron">

      <h1 class="text-center"><strong>Files Manager</strong></h1>

      <br/><br/>

      <h2><strong>Répertoire "<?= $internal->name ?>" :</strong></h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom du fichier/dossier</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php

          if($dh = opendir($internal->location)){
            $type = 'link';
            $existe = 0;
            $id_link = 0;
            while(($entry = readdir($dh)) !== false){
              if($entry != "." && $entry != ".."){
                $existe = 1;
                $id_link++;

                echo '<tr>
                        <td>'.$entry.'</td>
                        <td>
                        <div class="btn-group">
                          <a href="download.php?id='.$id_link.'&type='.$type.'" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Télécharger le fichier</a>';
                  $extension = explode(".", $entry);
                  $count = count($extension);
                  $number = $count-1;
                  if($extension[$number] == 'mp4' || $extension[$number] == 'webm' || $extension[$number] == 'mkv'){
                    echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a href="file.php?id='.$id_link.'&type='.$type.'"><span class="glyphicon glyphicon-film"></span> Regarder en streaming</a></li>
                          </ul>';
                  }elseif($extension[$number] == 'mp3' || $extension[$number] == 'ogg' || $extension[$number] == 'wav'){
                    echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a href="file.php?id='.$id_link.'&type='.$type.'"><span class="glyphicon glyphicon-music"></span> Jouer le son</a></li>
                          </ul>';
                  }elseif($extension[$number] == 'jpg' || $extension[$number] == 'jpeg' || $extension[$number] == 'png' || $extension[$number] == 'gif'
                   || $extension[$number] == 'bmp' || $extension[$number] == 'svg' || $extension[$number] == 'icon'){
                    echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a href="file.php?id='.$id_link.'&type='.$type.'" target="_blank"><span class="glyphicon glyphicon-picture"></span> Obtenir l\'url</a></li>
                          </ul>';
                  }elseif($extension[$number] == 'pdf'){
                    echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a href="file.php?id='.$id_link.'&type='.$type.'" target="_blank"><span class="glyphicon glyphicon-eye-open"></span> Visualiser le document</a></li>
                          </ul>';
                  }
                  echo '</div>
                        </td>
                      </tr>';
              }
            }
            if($existe == 0){
              echo '<tr class="text-center">
                      <td colspan="2">Aucun fichier/dossier</td>
                    </tr>';
            }
            closedir($dh);
          }

          ?>

        </tbody>
      </table>

    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
