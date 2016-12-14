<?php
  $page = "register";
  require_once 'inc/header.php';
  is_admin();

  // On récupère les informations de l'utilisateur selectionné pour pré-remplir le formulaire
  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
      $id = $pdo->quote($_GET['id']);
      $req = $pdo->query("SELECT * FROM users WHERE id = $id");

      if(!empty($result_user = $req->fetch())){
          $rank_user = $result_user->id_rank;
      }else{
          $_SESSION['flash']['danger'] = "L'utilisateur selectionné n'existe pas";
          header('location: index.php');
          exit();
      }
  }

  // On enregistre les informations du formulaire si aucune erreur n'est detecté
  if(!empty($_POST)){
    $errors = array();

    if(empty($_POST['username']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['username'])){
        $errors['username'] = "Ce nom d'utilisateur n'est pas valide";
    }else{
        $req = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $req->execute([$_POST['username']]);
        $user = $req->fetch();
        if(!empty($user)){
          if($user->id != $_GET['id']){
            $errors['username'] = "Ce nom d'utilisateur est déjà  pris";
          }
        }
    }

    if($_POST['password'] || $_POST['password2']){
      if((empty($_POST['password']) || empty($_POST['password2'])) || ($_POST['password'] != $_POST['password2'])){
          $errors['password'] = "Vous devez rentrer un mot de passe identique dans les 2 champs";
      }else{
        $pass = true;
      }
    }

    if(empty($errors)){
      if(checkCsrf() === true){
        if($pass === true){
          $req = $pdo->prepare("UPDATE users SET username = ?, password = ?, id_rank = ? WHERE id = ?");
          $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
          $req->execute([$_POST['username'], $password, $_POST['rank'], $id]);
        }else{
          $req = $pdo->prepare("UPDATE users SET username = ?, id_rank = ? WHERE id = ?");
          $req->execute([$_POST['username'], $_POST['rank'], $id]);
        }
        $_SESSION['flash']['success'] = 'Le compte a bien était enregistré';
        header('location: index.php');
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
        <h3>Modification de l'utilisateur : <?php echo $result_user->username; ?></h3>
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
            <input class="form-control" id="username" name="username" value="<?php echo $result_user->username; ?>" type="text">
          <br/>
            <label class="control-label" for="password">Nouveau mot de passe</label>
            <input class="form-control" id="password" name="password" type="password">
          <br/>
            <label class="control-label" for="password2">Confirmation le nouveau mot de passe</label>
            <input class="form-control" id="password2" name="password2" type="password">
          <br/>
            <label class="control-label" for="group">Groupe</label>
            <select class="form-control" id="group" name="rank">
              <?php
              $ranks = $pdo->query('SELECT * FROM ranks');
              while($ranks_list = $ranks->fetch()){
                if($ranks_list->id == $rank_user){
                  echo '<option value="'.$ranks_list->id.'" selected>'.$ranks_list->name.'</option>';
                }else{
                  echo '<option value="'.$ranks_list->id.'">'.$ranks_list->name.'</option>';
                }
              }
              ?>
            </select>
            <?php echo csrfInput(); ?>
          <br/>
            <input type="submit" class="btn btn-success" value="Enregistrer"/>

          </div>
        </form>

      </div><!-- /jumbotron -->

    </div> <!-- /container -->

<?php
  require_once 'inc/footer.php';
?>
