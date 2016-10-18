<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_admin();

  if(!empty($_POST)){
    $errors = array();

    if(empty($_POST['name']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['name'])){
        $errors['name'] = "Ce nom de dossier n'est pas valide";
    }

    if(empty($errors)){
      if(checkCsrf() === true){
        $req = $pdo->prepare("INSERT INTO directory SET name = ?, id_user = ?");
        $req->execute([$_POST['name'], $_POST['user']]);
        $id_directory = $pdo->lastInsertId();
        new_directory($id_directory);
        $_SESSION['flash']['success'] = 'Le dossier a bien était créer';
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
            <li><a href="../index.php">Retourner à l'accueil</a></li>
            <li><a href="../logout.php">Se déconnecter</a></li>
          </ul>

        </nav>
        <h3>Créer un dossier :</h3>
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

            <label class="control-label" for="name">Nom du dossier</label>
            <input class="form-control" id="name" name="name" type="text">
          <br/>
            <label class="control-label" for="user">Propriétaire du dossier</label>
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
            <input type="submit" class="btn btn-success" value="Créer le dossier"/>

          </div>
        </form>

      </div><!-- /jumbotron -->

    </div> <!-- /container -->

<?php
  require_once 'inc/footer.php';
?>
