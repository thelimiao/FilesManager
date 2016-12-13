<?php
$page = "index";
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);
  $directory = check_directory($_SESSION['auth']->id);


  if(isset($_GET['delete'])){
    checkCsrf();

    $id = $pdo->quote($_GET['delete']);
    $req = $pdo->query("DELETE FROM files WHERE id = $id");

    $_SESSION['flash']['success'] = 'Le fichier à bien été supprimé';
    header('location: index.php');
    exit();
  }


  /*
   * Upload de fichier
   */
  if(!empty($_FILES['file']['name'])){
    checkCsrf();


    if ($_FILES["file"]["size"] > 104857600) {
      $_SESSION['flash']['warning'] = 'Le fichier est trop grand';
      header('location: index.php');
      exit();
    }

      /*
       * Envoie de fichier sur serveur
       */
      if(empty($file_exist)){

        $target_dir = dirname(__FILE__)."/admin/directory/".$directory."/";
        $target_file = $target_dir . basename($_FILES['file']['name']);
        $extension = pathinfo($target_file, PATHINFO_EXTENSION);

        $file_name = $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']["tmp_name"], $target_file);

      }
      /*
       * Enregistrement du fichier en base
       */
      $name = $pdo->quote($_FILES['file']['name']);
      $directory_id = $pdo->quote($directory);

      $pdo->query("INSERT INTO files SET name = $name, id_directory = $directory_id");

      $_SESSION['flash']['success'] = 'Le fichier a bien été uploadé';
      header('location: index.php');
      exit();

  }


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
          <?php echo csrfInput(); ?>
          <br/>
          <input type="submit" id="btnSubmit" value="Uploader" class="btn btn-success" />
      </form>

      <div class="progress progress-striped active">
        <div class="progress-bar" style="width: 0%">0%</div>
      </div>
      <div id="status"></div>
      <br/>
      <p>Vos fichiers:</p>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom du dossier</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php

            $directory_id = $pdo->quote($directory);
            $req = $pdo->query("SELECT * FROM files WHERE id_directory = $directory_id");
            while($data = $req->fetch()){

              echo '<tr>
                      <td>'.$data->name.'</td>
                      <td>
                      <a href="#" class="btn btn-success input-margin">Télécharger</a>
                      <a href="index.php?delete='.$data->id.'&'.csrf().'" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');">Supprimer</a>
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

<script src="asset/js/jquery.ajax.js"></script>
<script type="text/javascript">

$('div.progress').hide();

$(document).on('click', '.browse', function(){
  var file = $(this).parent().parent().parent().find('.file');
  file.trigger('click');
});
$(document).on('change', '.file', function(){
  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
  $('div.progress').show();
});
/*
(function() {

var percent = $('div.progress-bar');
var status = $('#status');

$('form').ajaxForm({
    beforeSend: function() {
      status.empty();
        var percentVal = '0%';
        percent.width(percentVal)
        percent.html(percentVal);
    },
    uploadProgress: function(event, position, total, percentComplete) {
        var percentVal = percentComplete + '%';
        percent.width(percentVal)
        percent.html(percentVal);
    },
    success: function() {
        var percentVal = '100%';
        percent.width(percentVal)
        percent.html(percentVal);
    },
	complete: function(xhr) {
		status.html("Fichier uploadé");
    window.location.assign("index.php");
	}
});

})();
*/
</script>
