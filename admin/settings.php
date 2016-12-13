<?php
  $page = "settings";
  require_once 'inc/header.php';
  is_admin();

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
        <p>Gestion des uploads :</p>

          <label for="select" class="control-label">Format de taille :</label>
          <select class="form-control" id="select" name="format">
            <option>Go</option>
            <option>Mo</option>
          </select>
          <br/>
          <label class="control-label" for="maxSize">Taille d'upload max :</label>
          <input class="form-control" id="maxSize" value="" type="number" name="size">

          <hr>

          <?php echo csrfInput(); ?>
          <br/>
          <button type="submit" id="btnSubmit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Enregistrer</button>
      </form>

    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
