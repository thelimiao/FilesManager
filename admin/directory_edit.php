<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_admin();

  // On récupère les informations du répertoire selectionné pour pré-remplir le formulaire
  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
      $id = $pdo->quote($_GET['id']);
      $req = $pdo->query("SELECT * FROM directory WHERE id = $id");
      $result_directory = $req->fetch();
      if(empty($result_directory)){
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

    $req = $pdo->prepare("SELECT * FROM directory WHERE id_user = ?");
    $req->execute([$_POST['user']]);
    $data = $req->fetch();
    if(isset($_GET['id'])){
      if(!empty($data) && $data->id != $_GET['id']){
        $errors['name'] = "Cette utilisateur possède déjà un répertoire";
      }
    }else{
      if(!empty($data)){
        $errors['name'] = "Cette utilisateur possède déjà un répertoire";
      }
    }


    if(empty($errors)){
      if(checkCsrf() === true){
        if(isset($_GET['id'])){
          $req = $pdo->prepare("UPDATE directory SET name = ?, id_user = ? WHERE id = ?");
          $req->execute([$_POST['name'], $_POST['user'], $_GET['id']]);
          $_SESSION['flash']['success'] = 'Les modifications du répertoire a bien était enregistrées';
          header('location: directory.php');
          exit();
        }else{
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
            echo '<h3>Créer un répertoire :</h3>';
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
            <input class="form-control" id="name" name="name" type="text" placeholder="nom" value="<?php if(!empty($result_directory)){echo $result_directory->name;}?>">
          <br/>
            <label class="control-label" for="user">Propriétaire du répertoire</label>
            <select class="form-control" id="user" name="user">
              <?php
              $req = $pdo->query('SELECT * FROM users');
              while($users_list = $req->fetch()){
                if(!empty($result_directory)){
                  if($users_list->id == $result_directory->id_user){
                    echo '<option value="'.$users_list->id.'" selected>'.$users_list->username.'</option>';
                  }else{
                    echo '<option value="'.$users_list->id.'">'.$users_list->username.'</option>';
                  }
                }else{
                  echo '<option value="'.$users_list->id.'">'.$users_list->username.'</option>';
                }


              }
              ?>
            </select>
            <?php echo csrfInput(); ?>
          <br/>
          <?php
            if(isset($_GET['id'])){
              echo '<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Enregistrer</button>';
            }else{
              echo '<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Créer le répertoire</button>';
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
