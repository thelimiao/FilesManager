<?php
  require_once 'inc/database.php';
  require_once 'inc/function.php';


  $file = "/var/www/monsite/protected/MonFichier.zip";
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: Binary');
header('Content-disposition: attachment; filename="' . basename($file) . '"');


?>
