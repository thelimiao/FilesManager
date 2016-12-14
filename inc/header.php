<?php
  require_once 'inc/database.php';
  require_once 'inc/function.php';
  is_session();
  reconnect_cookie();

  if($page != 'login'){
    is_authenticated();
  }
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Files Manager application">
    <meta name="author" content="Fukotaku">
    <link rel="shortcut icon" href="favicon.png" />

    <?php
      echo "<title>Files Manager - ".$page."</title>";
    ?>


    <!-- Bootstrap core CSS -->
    <link href="asset/css/bootstrap.css" rel="stylesheet">

  </head>
  <body>

    <?php if(isset($_SESSION['flash'])): ?>
        <?php foreach($_SESSION['flash'] as $type => $message): ?>
            <div class="alert text-center alert-<?= $type; ?>">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= $message; ?>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
