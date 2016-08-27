<?php
  $page = "delete";
  require_once 'inc/header.php';
  is_admin();

  if(isset($_GET['delete'])){
    checkCsrf();
    $id = $pdo->quote($_GET['delete']);
    $pdo->query("DELETE FROM users WHERE id = $id");
    $_SESSION['flash']['danger'] = 'Le compte a bien était supprimé';
    header('Location: delete.php');
    exit();
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
      <h3 class="text-muted">Supprimer des utilisateurs :</h3>
    </div>

    <div class="jumbotron">

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom d'utilisateur</th>
            <th>groupe</th>
            <th>Action</th>
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
                      <td>' . htmlspecialchars($data->username) . '</td>
                      <td>' . $result->name . '</td>
                      <td>
                        <a href="delete.php?id=' . $data->id . '" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');">Supprimer le compte</a>
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
