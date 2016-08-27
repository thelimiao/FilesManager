<?php
$page = "index";
  require_once 'inc/header.php';
  is_authenticated();

  $rank = $_SESSION['auth']->id_rank;
  $req = $pdo->query("SELECT name FROM ranks WHERE id = $rank");
  $result = $req->fetch();
  if(!empty($result) && $result->name === "admin"){
    $admin = True;
  }

?>

  <div class="container">
    <br/>
    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-right">
          <?php
          if(isset($admin) && $admin = True){
            echo "<li><a href=\"/admin\">Panel admin</a></li>";
          }
          ?>
          <li><a href="logout.php">Se déconnecter</a></li>
        </ul>

      </nav>
      <h3 class="text-muted">Files Lister</h3>
    </div>

    <div class="jumbotron">
      <p>fichiers</p>
    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
