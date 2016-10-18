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
            echo "<li><a href=\"".WEBROOT."admin\">Panel admin</a></li>";
          }
          ?>
          <li><a href="logout.php">Se déconnecter</a></li>
        </ul>

      </nav>
      <h3>Files Manager</h3>

    </div>

    <div class="jumbotron">

      <form class="form-group" id="uploadForm" action="index.php" method="post" enctype=multipart/form-data>

          <input type="file" name="img[]" class="file">
          <div class="input-group col-xs-12">
            <input type="text" class="form-control" name="theFile" disabled placeholder="Uploader un fichier">
            <span class="input-group-btn">
              <button class="browse btn btn-primary" type="button"><i class="glyphicon glyphicon-file"></i> Choisir un fichier</button>
            </span>
          </div>
          <br/>
          <input type="submit" id="btnSubmit" value="Uploader" class="btn btn-success" />
          <br/><br/>
          <div id="progress-bar-div" class="progress progress-striped active">
            <div id="progress-bar" class="progress-bar progress-bar-success" style="width: 0%">0%</div>
          </div>

      </form>
      <div id="loader-icon" style="display:none;"><img src="LoaderIcon.gif" /></div>

      <p>Vos fichiers:</p>
    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
<script type="text/javascript">
$(document).ready(function() {
  $('div#progress-bar-div').hide();
  $('#uploadForm').submit(function(e) {
      if($('#theFile').val()) {
          e.preventDefault();
          $('#loader-icon').show();
          $(this).ajaxSubmit({
              target:   '#targetLayer',
              beforeSubmit: function() {
                  $("#progress-bar").width('0%');
              },
              uploadProgress: function (event, position, total, percentComplete){
                  $("#progress-bar").css('width', percentComplete + '%');
                  $("#progress-bar").html(percentComplete + '%')
              },
              success:function (){
                  $('#loader-icon').hide();
              },
              resetForm: true
          });
          return false;
      }
  });
});

$(document).on('click', '.browse', function(){
  var file = $(this).parent().parent().parent().find('.file');
  file.trigger('click');
});
$(document).on('change', '.file', function(){
  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
  $('div#progress-bar-div').show();
});

</script>
