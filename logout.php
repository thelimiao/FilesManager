<?php
  require_once 'inc/header.php';
  unset($_SESSION['auth']);
  $_SESSION['flash']['success'] = 'Vous êtes maintenant déconnecté';
  header('location: login.php');
?>
