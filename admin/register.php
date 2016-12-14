<?php
  $page = "register";
  require_once 'inc/header.php';
  is_admin();

  if(!empty($_POST)){
    $errors = array();

    if(empty($_POST['username']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['username'])){
        $errors['username'] = "Ce nom d'utilisateur n'est pas valide";
    }else{
        $req = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $req->execute([$_POST['username']]);
        $user = $req->fetch();
        if($user){
            $errors['username'] = "Ce nom d'utilisateur est déjà pris";
        }
    }

    if(empty($_POST['password']) || $_POST['password'] != $_POST['password2']){
        $errors['password'] = "Vous devez rentrer un mot de passe identique dans les 2 champs";
    }

    if(empty($errors)){
        if(checkCsrf() === true){
          $req = $pdo->prepare("INSERT INTO users SET username = ?, password = ?, id_rank = ?");
          $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
          $req->execute([$_POST['username'], $password, $_POST['rank']]);

          if($_POST['check']){
            $id_user = $pdo->lastInsertId();
            $req = $pdo->prepare("INSERT INTO directory SET name = ?, id_user = ?");
            $req->execute([$_POST['username'], $id_user]);
            $id_directory = $pdo->lastInsertId();
            new_directory($id_directory);
          }

          $_SESSION['flash']['success'] = 'Le compte a bien était créer';
          header('location: users.php');
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
        <h3>Créer un nouvelle utilisateur :</h3>
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

            <label class="control-label" for="username">Nom d'utilisateur</label>
            <input class="form-control" id="username" name="username" type="text" placeholder="nom">
          <br/>
            <label class="control-label" for="password">Mot de passe</label>
            <input class="form-control" id="password" name="password" type="password">
          <br/>
            <label class="control-label" for="password2">Confirmation du mot de passe</label>
            <input class="form-control" id="password2" name="password2" type="password">
          <br/>
            <label class="control-label" for="group">Groupe</label>
            <select class="form-control" id="group" name="rank">
              <?php
              $req = $pdo->query('SELECT * FROM ranks');
              while($ranks_list = $req->fetch()){
                if($ranks_list->name == "utilisateur"){
                  echo '<option value="'.$ranks_list->id.'" selected>'.$ranks_list->name.'</option>';
                }else{
                  echo '<option value="'.$ranks_list->id.'">'.$ranks_list->name.'</option>';
                }

              }
              ?>
            </select>
          <br/>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="check" value="true" checked=""> Créer son dossier
            </label>
          </div>
            <?php echo csrfInput(); ?>
          <br/>
            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Créer l'utilisateur</button>
            <a href="users.php" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> Annuler</a>

          </div>
        </form>

      </div><!-- /jumbotron -->

    </div> <!-- /container -->

<?php
  require_once 'inc/footer.php';
?>
