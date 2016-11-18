<?php
$page = "index";
  require_once 'inc/header.php';
  is_authenticated();
  $rank = check_rank($_SESSION['auth']->id_rank);

  /*
   * La sauvegarde
   */
  if(isset($_POST['name']) && isset($_POST['description']) && isset($_POST['url'])){

      checkCsrf();
      $name = $pdo->quote($_POST['name']);
      $description = $pdo->quote($_POST['description']);
      $url = $pdo->quote($_POST['url']);

      /*
       * Sauvegarde de la réalisation
       */
      if(isset($_GET['id'])){
          $id_achievement = $pdo->quote($_GET['id']);
          $pdo->query("UPDATE achievements SET name = $name, description = $description, url = $url WHERE id = $id_achievement");
      }else{
          $pdo->query("INSERT INTO achievements SET name = $name, description = $description, url = $url");
          $id_achievement = $pdo->lastInsertId();
      }

      /*
       * Envoie des images
       */
      if(!empty($_FILES['file']['name'])){
        $verif = $pdo->query("SELECT * FROM images WHERE id_achievement = $id_achievement");
        $img_exist = $verif->fetch();

        if(empty($img_exist)){

          $target_dir = dirname(dirname(__FILE__))."/asset/img/works/";
          $target_file = $target_dir . basename($_FILES['file']['name']);
          $extension = pathinfo($target_file, PATHINFO_EXTENSION);

          if($extension == 'jpg' || $extension == 'png'){

              $pdo->query("INSERT INTO images SET id_achievement = $id_achievement");
              $image_id = $pdo->lastInsertId();
              $image_name = $image_id . '.' . $extension;
              move_uploaded_file($_FILES['file']["tmp_name"], $target_dir . $image_name);
              $image_name = $pdo->quote($image_name);
              $pdo->query("UPDATE images SET name = $image_name WHERE id = $image_id");

          }else{
            $_SESSION['flash']['danger'] = 'Format non autorisé';
          }

        }else{

          $target_dir = dirname(dirname(__FILE__))."/asset/img/works/";

          $img_delete = $pdo->quote($img_exist->id_achievement);
          $pdo->query("DELETE FROM images WHERE id_achievement = $img_delete");
          unlink($target_dir . $img_exist->name);


          $target_file = $target_dir . basename($_FILES['file']['name']);
          $extension = pathinfo($target_file, PATHINFO_EXTENSION);


          if($extension == 'jpg' || $extension == 'png'){

              $pdo->query("INSERT INTO images SET id_achievement = $id_achievement");
              $image_id = $pdo->lastInsertId();
              $image_name = $image_id . '.' . $extension;
              move_uploaded_file($_FILES['file']["tmp_name"], $target_dir . $image_name);
              $image_name = $pdo->quote($image_name);
              $pdo->query("UPDATE images SET name = $image_name WHERE id = $image_id");

          }else{
            $_SESSION['flash']['danger'] = 'Mauvais format d\'image';
          }
          

        }
      }

      $_SESSION['flash']['success'] = 'Le fichier a bien été uploadé';
      header('location: realisations.php');
      exit();

  }
  /*
 * // La sauvegarde
 */

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
