<?php
  $page = "users";
  require_once 'inc/header.php';
  is_admin();

  if(isset($_GET['delete'])){
    if(checkCsrf() === true){
      $id = $pdo->quote($_GET['delete']);
      $pdo->query("DELETE FROM users WHERE id = $id");

      $req = $pdo->query("SELECT id FROM directory WHERE id_user = $id");
      if(!empty($recup = $req->fetch())){
        $id = $recup->id;
        clear_directory($id);
        remove_directory($id);
        $pdo->query("DELETE FROM directory WHERE id = $id");
      }

      $_SESSION['flash']['success'] = 'Le compte a bien était supprimé';
      header('Location: users.php');
      exit();
    }
  }

?>


  <div class="container">

    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-right">
          <li><a href="../index.php">Retourner à l'accueil</a></li>
          <li><a href="../logout.php">Se déconnecter</a></li>
        </ul>

      </nav>
      <h3>Liste des utilisateurs :</h3>
    </div>

    <div class="jumbotron">

      <a href="register.php" class="btn btn-success input-margin">Créer un utilisateur</a>
      <br/><br/>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom d'utilisateur</th>
            <th>Groupe</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php

            $req = $pdo->query('SELECT * FROM users');
            while($data = $req->fetch()){
              $rank = $data->id_rank;
              $req2 = $pdo->query("SELECT * FROM ranks WHERE id = $rank");
              $result = $req2->fetch();

              echo '<tr>
                      <td>'.$data->username.'</td>
                      <td>'.$result->name.'</td>
                      <td>
                        <a href="update.php?id='.$data->id.'" class="btn btn-warning input-margin">Editer le compte</a>
                        <a href="access.php?id='.$data->id.'" class="btn btn-info input-margin">Gérer les répertoires</a>
                        <a href="users.php?delete='.$data->id.'&'.csrf().'" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');">Supprimer l\'utilisateur</a>
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
