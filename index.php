<?php
$page = "index";
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);
?>

  <div class="container">
    <br/>
    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-right">
          <?php

          if($rank === "admin"){
            echo '<li><a href="admin/index.php">Panel admin</a></li>';
          }
          ?>
          <li><a href="logout.php">Se déconnecter</a></li>
        </ul>

      </nav>

    </div>
    <br/>

    <div class="jumbotron">

      <h1 class="text-center">Files Manager</h1>

      <br/><br/>

      <h2>Formulaire d'upload :</h2>
      <form class="form-group" id="uploadForm" action="index.php" method="post" enctype=multipart/form-data>

          <input type="file" name="file" class="file">
          <div class="input-group col-xs-12">
            <input type="text" class="form-control" name="file" disabled placeholder="Uploader un fichier">
            <span class="input-group-btn">
              <button class="browse btn btn-primary" type="button"><i class="glyphicon glyphicon-file"></i> Choisir un fichier</button>
            </span>
          </div>
          <br/>
          <input type="submit" id="btnSubmit" value="Uploader" class="btn btn-success" />
      </form>
      <br/><br/>
      <p>Vos fichiers:</p>
    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
<script type="text/javascript">

$(document).on('click', '.browse', function(){
  var file = $(this).parent().parent().parent().find('.file');
  file.trigger('click');
});
$(document).on('change', '.file', function(){
  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
  $('div#progress-bar-div').show();
});

</script>
