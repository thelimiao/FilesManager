<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_admin();

  if(!empty($_POST)){
    $errors = array();

    if(empty($_POST['name']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['name'])){
        $errors['name'] = "Ce nom de répertoire n'est pas valide";
    }

    $req = $pdo->prepare("SELECT * FROM directory WHERE id_user = ?");
    $req->execute([$_POST['user']]);
    $data = $req->fetch();
    if(!empty($data)){
      $errors['name'] = "Cette utilisateur possède déjà un répertoire";
    }


    if(empty($errors)){
      if(checkCsrf() === true){
        $req = $pdo->prepare("INSERT INTO directory SET name = ?, id_user = ?");
        $req->execute([$_POST['name'], $_POST['user']]);
        $id_directory = $pdo->lastInsertId();
        new_directory($id_directory);
        $_SESSION['flash']['success'] = 'Le répertoire a bien était créer';
        header('location: directory.php');
        exit();
      }
    }
  }

?>

    <div class="container">

      <div class="header clearfix">
        <nav>

          <ul class="nav nav-pills pull-right">
            <li><a href="../index.php"><span class="glyphicon glyphicon-home"></span> Retourner à l'accueil</a></li>
            <li><a href="../logout.php"><span class="glyphicon glyphicon-log-out"></span> Se déconnecter</a></li>
          </ul>

        </nav>
        <h3>Créer un répertoire :</h3>
      </div>

      <div class="jumbotron">

        <?php if(!empty($errors)): ?>

        <div class="alert alert-danger">
          <p>Vous n'avez pas rempli le formulaire correctement</p>
            <ul>
              <?php foreach($errors as $error): ?>
                <li><?= $error; ?></li>
              <?php endforeach; ?>
            </ul>
        </div>

        <?php endif; ?>

        <form method="post" action="">
          <div class="form-group">

            <label class="control-label" for="name">Nom du répertoire</label>
            <input class="form-control" id="name" name="name" type="text">
          <br/>
            <label class="control-label" for="user">Propriétaire du répertoire</label>
            <select class="form-control" id="user" name="user">
              <?php
              $req = $pdo->query('SELECT * FROM users');
              while($users_list = $req->fetch()){
                  echo '<option value="'.$users_list->id.'">'.$users_list->username.'</option>';
              }
              ?>
            </select>
            <?php echo csrfInput(); ?>
          <br/>
            <input type="submit" class="btn btn-success" value="Créer le répertoire"/>

          </div>
        </form>

      </div><!-- /jumbotron -->

    </div> <!-- /container -->

<?php
  require_once 'inc/footer.php';
?>
