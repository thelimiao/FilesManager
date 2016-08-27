<?php
  $page = "index";
  require_once 'inc/header.php';
?>


  <div class="container">

    <div class="header clearfix">
      <h3 class="text-muted">Liste des utilisateurs :</h3>
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

            while ($data = $req->fetch()){
              $rank = $data->id_rank;
              $req2 = $pdo->query("SELECT * FROM ranks WHERE id = $rank");
              $result = $req2->fetch();

              echo '<tr>
                      <td>' . htmlspecialchars($data->username) . '</td>
                      <td>' . $result->name . '</td>
                      <td>
                        <a href="user.php?id=' . $data->id . '" class="btn btn-warning input-margin">Editer l\'utilisateur</a>
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
