<?php
  $page = "index";
  require_once 'inc/header.php';
  $rank = check_rank($_SESSION['auth']->id_rank);
  $directory = check_directory($_SESSION['auth']->id);

  $maxUploadSize = maxUploadSize();

  if(isset($_GET['delete']) && preg_match("/^[0-9]+$/i",$_GET['delete'])){
    checkCsrf();

    remove_file($_GET['delete']);

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
    // Vérification de la taille du fichier
    if($_FILES["file"]["size"] > $maxUploadSize){
      $_SESSION['flash']['warning'] = 'Le fichier est trop grand';
      header('location: index.php');
      exit();
    }
      /*
       * Envoie de fichier sur serveur
       */
      $file_exist = false;
      $directory_id = $pdo->quote($directory);
      $req = $pdo->query("SELECT * FROM files WHERE id_directory = $directory_id");
      while($data = $req->fetch()){
        if($_FILES['file']['name'] == $data->name){
          $file_exist = true;
        }
      }
      if($file_exist == false){
        $target_dir = dirname(__FILE__)."/directory/".$directory."/";
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
  }


?>

  <div class="container">
    <br/>
    <div class="header clearfix">
      <nav>

        <ul class="nav nav-pills pull-left">
          <li><a href="index.php"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Mon répertoire personnel</a></li>
          <li><a href="directory.php"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;Mes répertoires partagés</a></li>
        </ul>

        <ul class="nav nav-pills pull-right">
          <?php

          if($rank === "admin"){
            echo '<li><a href="admin/index.php"><span class="glyphicon glyphicon-user"></span> Panel admin</a></li>';
          }
          ?>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Se déconnecter</a></li>
        </ul>

      </nav>

    </div>
    <br/>

    <div class="jumbotron">

      <h1 class="text-center"><strong>Files Manager</strong></h1>

      <br/><br/>

      <?php
        $id_user = $pdo->quote($_SESSION['auth']->id);
        $req = $pdo->query("SELECT * FROM directory WHERE id_user = $id_user");
        $dir_existe = 0;
        while($data = $req->fetch()){
          $dir_existe = 1;
          echo '<h2><strong>Formulaire d\'upload :</strong></h2>
          <form class="form-group" id="uploadForm" action="" method="post" enctype=multipart/form-data>

              <input id="file" type="file" name="file" class="file">
              <div class="input-group col-xs-12">
                <input type="text" class="form-control" name="file" disabled placeholder="Uploader un fichier">
                <span class="input-group-btn">
                  <button class="browse btn btn-primary" type="button"><i class="glyphicon glyphicon-file"></i> Choisir un fichier</button>
                </span>
              </div>';
          echo csrfInput();
          echo '<br/>
              <div id="thebar" class="progress progress-striped active">
                <div class="progress-bar" style="width: 0%">0%</div>
              </div>

              <span id="alertFile" class="label label-danger"><strong>Fichier trop lourd, veuillez en choisir un autre !</strong></span>
              <div id="status"></div>
              <br/>

              <button type="submit" id="btnSubmit" class="btn btn-success"><span class="glyphicon glyphicon-open"></span> Uploader</button>
          </form>';
        }

        if($dir_existe == 0){
          echo '<h2 class="text-center"><strong>Vous ne disposez pas de répertoire personnel</strong></h2>';
        }
      ?>

      <hr>
      <h2><strong>Répertoire personnel :</strong></h2>
      <?php
      if(!empty($directory)){
        echo '<a href="#" class="btn btn-info disabled"><span class="glyphicon glyphicon-lock"></span> Gérer les accès</a>';
      }
      ?>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom du fichier</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php

            $directory_id = $pdo->quote($directory);
            $req = $pdo->query("SELECT * FROM files WHERE id_directory = $directory_id ORDER BY id DESC");
            $type = 'folder';
            $existe = 0;
            while($data = $req->fetch()){
              $existe = 1;

              echo '<tr>
                      <td>'.$data->name.'</td>
                      <td>
                      <div class="btn-group">
                        <a href="download.php?id='.$data->id.'&type='.$type.'" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Télécharger le fichier</a>';
                $extension = explode(".", $data->name);
                $count = count($extension);
                $number = $count-1;
                if($extension[$number] == 'mp4' || $extension[$number] == 'webm'){
                  echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="file.php?id='.$data->id.'&type='.$type.'"><span class="glyphicon glyphicon-film"></span> Regarder en streaming</a></li>
                        </ul>';
                }elseif($extension[$number] == 'mp3' || $extension[$number] == 'ogg' || $extension[$number] == 'wav'){
                  echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="file.php?id='.$data->id.'&type='.$type.'"><span class="glyphicon glyphicon-music"></span> Jouer le son</a></li>
                        </ul>';
                }elseif($extension[$number] == 'jpg' || $extension[$number] == 'jpeg' || $extension[$number] == 'png' || $extension[$number] == 'gif'
                 || $extension[$number] == 'bmp' || $extension[$number] == 'svg' || $extension[$number] == 'icon'){
                  echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="file.php?id='.$data->id.'&type='.$type.'" target="_blank"><span class="glyphicon glyphicon-picture"></span> Obtenir l\'url</a></li>
                        </ul>';
                }elseif($extension[$number] == 'pdf'){
                  echo '<a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="file.php?id='.$data->id.'&type='.$type.'" target="_blank"><span class="glyphicon glyphicon-eye-open"></span> Visualiser le document</a></li>
                        </ul>';
                }
                echo '</div>
                      <a href="index.php?delete='.$data->id.'&'.csrf().'" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');"><span class="glyphicon glyphicon-trash"></span> Supprimer</a>
                      </td>
                    </tr>';
            }
            if($existe == 0){
              echo '<tr class="text-center">
                      <td colspan="2">Aucun fichier</td>
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

var maxUploadSize = <?= $maxUploadSize ?>;

$('div.progress').hide();
$('span#alertFile').hide();
$("button#btnSubmit").hide();

$(document).on('click', '.browse', function(){
  var file = $(this).parent().parent().parent().find('.file');
  file.trigger('click');
});
$(document).on('change', '.file', function(){
  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));

  if(findSize() > maxUploadSize){
    $('span#alertFile').show();
    $('div.progress').hide();

    $("button#btnSubmit").hide();
  }else{
    $('div.progress').show();
    $('span#alertFile').hide();

    $("button#btnSubmit").show();
  }

});



function findSize() {
    var fileInput =  document.getElementById("file");
    try{
        return fileInput.files[0].size; // Size returned in bytes.
    }catch(e){
        var objFSO = new ActiveXObject("Scripting.FileSystemObject");
        var e = objFSO.getFile( fileInput.value);
        var fileSize = e.size;
        return fileSize;
    }
}
function redirection() {
  window.location.assign("index.php");
}
(function() {

var percent = $('div.progress-bar');
var status = $('#status');
var error = 0;
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
      status.html('<span class="label label-success">Fichier uploadé, la page va s\'actualiser dans 5 secondes...</span>');
      setTimeout(redirection, 5000);
	}
});

})();

</script>
