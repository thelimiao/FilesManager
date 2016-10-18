<?php
  $page = "index";
  require_once 'inc/header.php';
  is_admin();

?>


  <div class="container">

    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-right">
          <li><a href="../index.php">Retourner à l'accueil</a></li>
          <li><a href="../logout.php">Se déconnecter</a></li>
        </ul>

      </nav>
      <h3>Les dernières actions :</h3>
    </div>

    <div class="jumbotron">

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Message</th>
            <th>Utilisateur</th>
            <th>Ip</th>
            <th>Date et heure</th>
          </tr>
        </thead>
        <tbody>

          <?php

            $req = $pdo->query('SELECT * FROM logs');
            while($data = $req->fetch()){
              echo '<tr>
                      <td>'.$data->message.'</td>
                      <td>'.$data->username.'</td>
                      <td>'.$data->ip.'</td>
                      <td>Le '.date("d/m/Y à H:i", strtotime($data->date)).'</td>
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
