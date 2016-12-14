<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);

  if(isset($_GET['delete']) && preg_match("/^[0-9]+$/i",$_GET['delete'])){
    checkCsrf();
    if(isset($_GET['type']) && $_GET['type'] == 'link'){

      $id = $pdo->quote($_GET['delete']);
      $req = $pdo->query("DELETE FROM access WHERE id = $id");

      $_SESSION['flash']['success'] = 'L\'accès a bien été supprimer';
      header('location: directory.php');
      exit();

    }elseif(isset($_GET['type']) && $_GET['type'] == 'folder'){

      $_SESSION['flash']['warning'] = 'Fonctionnalité pas encore disponible';
      header('location: directory.php');
      exit();
    }

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

      <h2><strong>Répertoires partagés :</strong></h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom du répertoire</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <tr class="text-center">
            <td colspan="2">Zone non disponible pour le moment</td>
          </tr>

        </tbody>
      </table>

      <hr>
      <h2><strong>Répertoires serveur :</strong></h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom du répertoire</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php

            $id_user = $pdo->quote($_SESSION['auth']->id);
            $req = $pdo->query("SELECT * FROM access WHERE id_user = $id_user AND link_directory = 1 ORDER BY id DESC");
            $existe = 0;
            $type = 'link';
            while($data = $req->fetch()){
              $existe = 1;

              $id_directory = $pdo->quote($data->id_directory);
              $req2 = $pdo->query("SELECT * FROM internal WHERE id = $id_directory");
              $data_directory = $req2->fetch();

              echo '<tr>
                      <td>'.$data_directory->name.'</td>
                      <td>
                        <a href="explorer.php?id='.$data->id_directory.'&type='.$type.'" class="btn btn-primary"><span class="glyphicon glyphicon-globe"></span> Explorer</a>
                        <a href="directory.php?delete='.$data->id.'&type='.$type.'&'.csrf().'" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');"><span class="glyphicon glyphicon-trash"></span> Supprimer mes accès</a>
                      </td>
                    </tr>';
            }
            if($existe == 0){
              echo '<tr class="text-center">
                      <td colspan="2">Aucun répertoire</td>
                    </tr>';
            }

          ?>

        </tbody>
      </table>

    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
