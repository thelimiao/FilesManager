<?php
  require_once 'inc/header.php';
  unset($_SESSION['auth']);
  unset($_SESSION['csrf']);
  $_SESSION['flash']['success'] = 'Vous êtes maintenant déconnecté';
  redirection_link('login');
?>
