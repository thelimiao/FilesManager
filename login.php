<?php
  $page = "login";
  require_once 'inc/header.php';

  if(isset($_SESSION['auth'])){
    header('location: index.php');
    exit();
  }

  if(!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])){
    $req = $pdo->prepare('SELECT * FROM users WHERE (username = :username)');
    $req->execute(['username' => $_POST['username']]);
    $user = $req->fetch();

      if(password_verify($_POST['password'], $user->password)){

        $remember_token = str_random(250);
        $pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?')->execute([$remember_token, $user->id]);
        setcookie('remember', $user->id . '==' . $remember_token . sha1($user->id . 'adtr'), time() + 60 * 60 * 24 * 7);

        $_SESSION['auth'] = $user;
        $_SESSION['flash']['success'] = 'Vous êtes maintenant connecté';
        redirection_link('index');
        exit();
      }else{
        $_SESSION['flash']['danger'] = "Identifiant ou mot de passe incorrecte";
        redirection_link('login');
        exit();
      }
  }

?>


  <div class="container">
    <br/><br/>
    <div class="jumbotron">

      <h1 class="text-center"><strong>Files Manager</strong></h1>
      <h2>Identifiez-vous :</h2>

      <?php if(!empty($errors)): ?>

      <div class="alert alert-danger">
        <p>Tentative de connexion échoué :</p>
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
          <input class="form-control" id="username" name="username" type="text">
        <br/>
          <label class="control-label" for="password">Mot de passe</label>
          <input class="form-control" id="password" name="password" type="password">
        <br/>
          <button type="submit" class="btn btn-success" ><span class="glyphicon glyphicon-log-in"></span> Se connecter</button>

        </div>
      </form>

    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
