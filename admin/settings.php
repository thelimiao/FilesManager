<?php
  $page = "settings";
  require_once 'inc/header.php';
  is_admin();

  if(!empty($_POST)){
    if(!empty($_POST['size'])){
      if($_POST['format'] === 'mo'){
        $size = moConvert($_POST['size']);
        $req = $pdo->prepare("UPDATE settings SET upload_size = ?");
        $req->execute([$size]);

      }elseif($_POST['format'] === 'go'){
        $size = goConvert($_POST['size']);
        $req = $pdo->prepare("UPDATE settings SET upload_size = ?");
        $req->execute([$size]);
      }

      $_SESSION['flash']['success'] = 'Les paramètres on bien étais enregistrés';
      header('location: settings.php');
      exit();
    }else{
      $_SESSION['flash']['danger'] = 'Le champ "Taille d\'upload max" est vide';
      header('location: settings.php');
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
      <h3>Les paramètres de Files Manager :</h3>
    </div>

    <div class="jumbotron">
      <form class="form-group" action="" method="post">
        <br/>
        <p>Modifier la taille max des fichiers uploadés:</p>
          <label for="select" class="control-label">Format de taille :</label>
          <select class="form-control" id="select" name="format">
            <option value="mo" selected>Mo</option>
            <option value="go">Go</option>
          </select>
          <br/>
          <?php
            $req = $pdo->query("SELECT * FROM settings");
            $settings = $req->fetch();
          ?>
          <label class="control-label" for="maxSize">Taille d'upload max :</label>
          <input class="form-control" id="maxSize" value="<?php echo octetConvertToMo($settings->upload_size); ?>" type="number" name="size">

          <?php echo csrfInput(); ?>
          <br/>
          <button type="submit" id="btnSubmit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Enregistrer</button>
      </form>

    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
