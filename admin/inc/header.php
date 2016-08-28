<?php
  require_once '../inc/database.php';
  require_once '../inc/function.php';
  is_session();
  is_admin();
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Files Lister application">
    <meta name="author" content="Fukotaku">
    <link rel="shortcut icon" href="../favicon.png" />

    <title>Admin Files Lister</title>

    <!-- Bootstrap core CSS -->
    <link href="../asset/css/bootstrap.css" rel="stylesheet">

  </head>
  <body>

    <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <a class="navbar-brand"><span class="glyphicon glyphicon-blackboard"></span> &nbsp;Files Lister : Panel administrateur</a>
      </div>

      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li <?php if(isset($page) && $page == "index"){echo "class=\"active\"";} ?>><a href="index.php"><span class="glyphicon glyphicon-info-sign"></span> &nbsp;Liste des utilisateurs</a></li>
          <li <?php if(isset($page) && $page == "register"){echo "class=\"active\"";} ?>><a href="register.php"><span class="glyphicon glyphicon-plus-sign"></span> &nbsp;Créer un utilisateur</a></li>
          <li <?php if(isset($page) && $page == "delete"){echo "class=\"active\"";} ?>><a href="delete.php"><span class="glyphicon glyphicon-minus-sign"></span> &nbsp;Supprimer des utilisateurs</a></li>
        </ul>
      </div>

    </div>
  </nav>

  <?php if(isset($_SESSION['flash'])): ?>
      <?php foreach($_SESSION['flash'] as $type => $message): ?>
          <div class="alert text-center alert-<?= $type; ?>">
              <?= $message; ?>
          </div>
      <?php endforeach; ?>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>
