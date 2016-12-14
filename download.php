<?php
  require_once 'inc/database.php';
  require_once 'inc/function.php';

  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
    $id = $pdo->quote($_GET['id']);
    $req = $pdo->query("SELECT * FROM files WHERE id = $id");
    $fileDetect = false;
    while($data = $req->fetch()){
      $fileName = $data->name;
      $dirNumber = $data->id_directory;
      $fileDetect = true;
    }
    if($fileDetect == true){
      $directory = "/admin/directory/".$dirNumber."/".$fileName;
      header('Content-Type: application/octet-stream');
      header('Content-Transfer-Encoding: Binary');
      header('Content-disposition: attachment; filename="' . basename($directory) . '"');
    }
  }

?>
