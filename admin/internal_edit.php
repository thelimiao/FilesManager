<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_admin();

  // On récupère les informations du répertoire selectionné pour pré-remplir le formulaire
  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
      $id = $pdo->quote($_GET['id']);
      $req = $pdo->query("SELECT * FROM internal WHERE id = $id");
      $result_internal = $req->fetch();
      if(empty($result_internal)){
        $_SESSION['flash']['danger'] = "Le répertoire selectionné n'existe pas";
        header('location: directory.php');
        exit();
      }
  }

  if(!empty($_POST)){
    $errors = array();

    if(empty($_POST['name']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['name'])){
        $errors['name'] = "Ce nom de répertoire n'est pas valide";
    }

    if(empty($_POST['location'])){
        $errors['name'] = "Vous n'avez pas défini le chemin de votre répertoire";
    }else{
      if(!file_exists($_POST['location']) || !is_dir($_POST['location'])){
        $errors['name'] = "Le chemin du répertoire n'est pas accéssible";
      }
    }

    $req = $pdo->prepare("SELECT * FROM internal WHERE location = ?");
    $req->execute([$_POST['location']]);
    $data = $req->fetch();
    if(isset($_GET['id'])){
      if(!empty($data) && $data->id != $_GET['id']){
        $errors['name'] = "Ce répertoire est déjà utilisé";
      }
    }else{
      if(!empty($data)){
        $errors['name'] = "Ce répertoire est déjà utilisé";
      }
    }


    if(empty($errors)){
      if(checkCsrf() === true){
        if(isset($_GET['id'])){
          $req = $pdo->prepare("UPDATE internal SET name = ?, location = ? WHERE id = ?");
          $req->execute([$_POST['name'], $_POST['location'], $_GET['id']]);
          $_SESSION['flash']['success'] = 'Les modifications du répertoire a bien était enregistrées';
          header('location: directory.php');
          exit();
        }else{
          $req = $pdo->prepare("INSERT INTO internal SET name = ?, location = ?");
          $req->execute([$_POST['name'], $_POST['location']]);
          $id_directory = $pdo->lastInsertId();
          $_SESSION['flash']['success'] = 'Le répertoire a bien était enregistré';
          header('location: directory.php');
          exit();
        }
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
        <?php
          if(isset($_GET['id'])){
            echo '<h3>Editier le répertoire :</h3>';
          }else{
            echo '<h3>Ajouter un répertoire :</h3>';
          }
        ?>
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
            <input class="form-control" id="name" name="name" type="text" placeholder="nom" value="<?php if(!empty($result_internal)){echo $result_internal->name;}?>">
          <br/>
            <label class="control-label" for="location">Chemin absolut du répertoire</label>
            <input class="form-control" id="location" name="location" type="text" placeholder="/home/user/www/" value="<?php if(!empty($result_internal)){echo $result_internal->location;}?>">
            <?php echo csrfInput(); ?>
          <br/>
          <?php
            if(isset($_GET['id'])){
              echo '<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Enregistrer</button>';
            }else{
              echo '<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Ajouter le répertoire</button>';
            }
          ?>
          <a href="directory.php" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> Annuler</a>

          </div>
        </form>

      </div><!-- /jumbotron -->

    </div> <!-- /container -->

<?php
  require_once 'inc/footer.php';
?>
