<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_admin();

  if(isset($_GET['delete'])){
    if(checkCsrf() === true){
      $id = $pdo->quote($_GET['delete']);
      clear_directory($id);
      remove_directory($id);
      $pdo->query("DELETE FROM directory WHERE id = $id");
      $_SESSION['flash']['success'] = 'Le dossier a bien était supprimé';
      header('Location: directory.php');
      exit();
    }
  }

?>


  <div class="container">

    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-right">
          <li><a href="../index.php"><span class="glyphicon glyphicon-home"></span> Retourner à l'accueil</a></li>
          <li><a href="../logout.php"><span class="glyphicon glyphicon-log-out"></span> Se déconnecter</a></li>
        </ul>

      </nav>
      <h3>Liste des dossiers :</h3>
    </div>

    <div class="jumbotron">

      <a href="create.php" class="btn btn-success input-margin"><span class="glyphicon glyphicon-plus"></span> Créer un dossier</a>
      <a href="#" class="btn btn-success input-margin"><span class="glyphicon glyphicon-hdd"></span> Ajouter un dossier existent</a>
      <br/><br/>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom du dossier</th>
            <th>L'utilisateur propriétaire</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php

            $req = $pdo->query('SELECT * FROM directory');
            while($data = $req->fetch()){
              $user = $pdo->quote($data->id_user);
              $req = $pdo->query("SELECT username FROM users WHERE id = $user");
              $result = $req->fetch();

              echo '<tr>
                      <td>'.$data->name.'</td>
                      <td>'.$result->username.'</td>
                      <td>
                        <a href="directory.php?delete='.$data->id.'&'.csrf().'" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');"><span class="glyphicon glyphicon-trash"></span> Supprimer le dossier</a>
                      </td>
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
