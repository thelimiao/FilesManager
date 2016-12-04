<?php
$page = "index";
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);
  $directory = check_directory($_SESSION['auth']->id);

  /*
   * Upload de fichier
   */
  if(!empty($_FILES['file']['name'])){
    checkCsrf();

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

    $image = 0;
    $video = 0;


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
      <br/><br/>
      <p>Vos fichiers:</p>
      <br/>
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
