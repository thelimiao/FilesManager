<?php
  $page = "directory";
  require_once 'inc/header.php';
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
            $id_file = 0;
            while(($entry = readdir($dh)) !== false){
              if($entry != "." && $entry != ".."){
                $existe = 1;
                $id_link++;

                // Si dossier
                if(is_dir($internal->location."/".$entry)){
                  echo '<tr>
                        <td>'.$entry.'</td>
                        <td>
                          <button class="btn btn-primary" data-toggle="collapse" data-target="#'.$id_link.'"><span class="glyphicon glyphicon-globe"></span> Explorer</button>
                          <div id="'.$id_link.'" class="collapse">
                          <table class="table table-striped table-hover">
                          <thead>
                            <tr>
                              <th>Nom du fichier</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>';
                  if($dh_dir = opendir($internal->location."/".$entry)){
                    while(($file = readdir($dh_dir)) !== false){
                      if($file != "." && $file != ".."){
                        if(!is_dir($internal->location."/".$entry."/".$file)){
                          $id_file++;
                          $trans = array("+" => "@");
                          $the_entry = strtr($entry, $trans);
                          echo '<tr>
                                  <td>'.$file.'</td>
                                  <td>
                                    <a href="download.php?id='.$internal->id.'&type='.$type.'&dir='.$the_entry.'&file='.$id_file.'" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Télécharger le fichier</a>
                                  </td>
                                </tr>';
                        }
                      }
                    }
                    closedir($dh_dir);
                  }
                  $id_file = 0;

                  echo '</tbody>
                      </table>
                     </div>
                    </tr>';

                // Si fichier
                }else{
                  echo '<tr>
                          <td>'.$entry.'</td>
                          <td>
                            <a href="download.php?id='.$internal->id.'&type='.$type.'&file='.$id_link.'" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Télécharger le fichier</a>
                          </td>
                        </tr>';
                }// Fin si fichier

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
