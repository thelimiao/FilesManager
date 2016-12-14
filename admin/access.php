<?php
  $page = "directory";
  require_once 'inc/header.php';
  is_admin();

  if(isset($_GET['id']) && preg_match("/^[0-9]+$/i",$_GET['id'])){
    $get_id = $_GET['id'];
    $id = $pdo->quote($_GET['id']);
    $req = $pdo->query("SELECT * FROM internal WHERE id = $id");
    $result_internal = $req->fetch();
    if(empty($result_internal)){
      $_SESSION['flash']['danger'] = 'Le répertoire sélectionné n\'existe pas';
      header('location: directory.php');
      exit();
    }
  }else{
    $_SESSION['flash']['danger'] = 'Aucun répertoire de sélectionné';
    header('location: directory.php');
    exit();
  }

  if(isset($_GET['delete']) && preg_match("/^[0-9]+$/i",$_GET['delete'])){
    checkCsrf();

    $id_delete = $pdo->quote($_GET['delete']);
    $req = $pdo->query("DELETE FROM access WHERE id = $id_delete");

    $_SESSION['flash']['success'] = 'L\'utilisateur a bien été supprimé des accès du répertoire';
    header('location: access.php?id='.$_GET['id']);
    exit();
  }

  if(isset($_POST['user'])){
    checkCsrf();
    $id_directory = $pdo->quote($_GET['id']);
    $req = $pdo->prepare("INSERT INTO access SET id_user = ?, id_directory = ?, folder_directory = 0, link_directory = 1");
    $req->execute([$_POST['user'], $_GET['id']]);

    $_SESSION['flash']['success'] = 'L\'utilisateur a bien été supprimé des accès du répertoire';
    header('location: access.php?id='.$_GET['id']);
    exit();
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
      <h3>Les accès du répertoire "<?= $result_internal->name ?>" :</h3>
    </div>

    <div class="jumbotron">
      <p>Utilisateurs ayant l'accès:</p>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nom de l'utilisateur</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
      <?php
      $users_access = array();
        $req = $pdo->query("SELECT * FROM access WHERE id_directory = $id AND link_directory = 1");
        $existe = 0;
        while($result_access = $req->fetch()){
          $existe = 1;
          $users_access[] = $result_access->id_user;

          $id_user = $pdo->quote($result_access->id_user);
          $req_user = $pdo->query("SELECT * FROM users WHERE id = $id_user");
          $result_user = $req_user->fetch();

          echo '<tr>
                  <td>'.$result_user->username.'</td>
                  <td><a href="access.php?id='.$get_id.'&delete='.$result_access->id.'&'.csrf().'" class="btn btn-danger input-margin" onclick="return confirm(\'Êtes vous sur ?\');"><span class="glyphicon glyphicon-trash"></span> Supprimer l\'accès</a></td>
                </tr>';
        }
        if($existe == 0){
          echo '<tr class="text-center">
                  <td colspan="2">Aucun utilisateur</td>
                </tr>';
        }
      ?>
        </tbody>
      </table>
      <hr>
      <p>Ajouter un utilisateur pour cette accès:</p>
      <form class="form-group" action="" method="post">
          <label for="select" class="control-label">Utilisateur :</label>
          <select class="form-control" id="select" name="user">
            <?php
            $req = $pdo->query('SELECT * FROM users');
            while($users_list = $req->fetch()){
                if(!in_array($users_list->id, $users_access)){
                  echo '<option value="'.$users_list->id.'">'.$users_list->username.'</option>';
                }

            }
            ?>
          </select>
          <?php echo csrfInput(); ?>
          <br/>
          <button type="submit" id="btnSubmit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Ajouter</button>
      </form>

    </div><!-- /jumbotron -->

  </div><!-- /container -->


<?php
  require_once 'inc/footer.php';
?>
