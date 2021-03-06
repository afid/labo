<?php
session_start();
require_once("includes/config.php");

if (isset($_SESSION["login"]) && $_SESSION["login"] != "") {
  // if logged in send to dashboard page
  redirect("index2.php");
}

$title = "Login";
$mode = trim($_POST["mode"]);
if ($mode == "login") {
  $username = trim($_POST['username']);
  $pass = trim(md5($_POST['user_password']));

  if ($username == "" || $pass == "") {

    $_SESSION["errorType"] = "danger";
    $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Veuillez renseigner tous les champs";
    $_SESSION["id_employee"] = null;
    $_SESSION["id_lang"] = null;
    $_SESSION["login"] = null;
  } else {
    $sql = "SELECT * FROM labo_employee WHERE login = :uname AND password = :upass ";

    try {
      $stmt = $DB->prepare($sql);

      // bind the values
      $stmt->bindValue(":uname", $username);
      $stmt->bindValue(":upass", $pass);

      // execute Query
      $stmt->execute();
      $results = $stmt->fetchAll();

      if (count($results) > 0) {
        if ($results[0]["active"] == 1) {
          $sql = "UPDATE labo_employee SET last_connection_date =now() WHERE login = :uname AND password = :upass";
          try {
            $stmt = $DB->prepare($sql);
            $stmt->bindValue(":uname", $username);
            $stmt->bindValue(":upass", $pass);
            $stmt->execute();
            if ($stmt->rowCount()) {
              $_SESSION["errorType"] = "success";
              $_SESSION["errorMsg"] = "<i class=\"icon fa fa-check\"></i>Connexion avec succès.";
              $_SESSION["id_employee"] = $results[0]["id_employee"];
              $_SESSION["id_lang"] = $results[0]["id_lang"];
              $_SESSION["login"] = $results[0]["login"];
              $_SESSION["lastname"] = $results[0]["lastname"];
              $_SESSION["firstname"] = $results[0]["firstname"];
              $logger->log('succes', 'succes_login', "Connexion avec succès pour le login: $username", Logger::GRAN_MONTH);
              redirect("index2.php");
              exit;
            } else {
              $_SESSION["errorType"] = "warning";
              $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
              $logger->log('erreurs', 'err_login', "Une erreur est survenue pour le login: $username , mot de passe: $pass", Logger::GRAN_MONTH);
            }
          } catch (Exception $e){
            $_SESSION["errorType"] = "warning";
            $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
            $logger->log('erreurs', 'err_login', "Une erreur est survenue pour le login: $username , mot de passe: $pass", Logger::GRAN_MONTH);
          }
        } else {
          $_SESSION["errorType"] = "warning";
          $_SESSION["errorMsg"] = "<i class=\"icon fa fa-check\"></i>Votre compte n'est pas activé.";
          $_SESSION["id_employee"] = null;
          $_SESSION["id_lang"] = null;
          $_SESSION["login"] = null;
          $logger->log('erreurs', 'err_login', "Accès suspendu pour le login: $username , mot de passe: $pass", Logger::GRAN_MONTH);
        }


      } else {
        $_SESSION["errorType"] = "warning";
        $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Login ou Mot de passe incorrect.";
        $_SESSION["id_employee"] = null;
        $_SESSION["id_lang"] = null;
        $_SESSION["login"] = null;
        $logger->log('erreurs', 'err_login', "Login ou Mot de passe incorrect pourle login: $username , mot de passe: $pass", Logger::GRAN_MONTH);
      }
    } catch (Exception $ex) {
      $_SESSION["errorType"] = "danger";
      $_SESSION["errorMsg"] = $ex->getMessage();
      $logger->log('erreurs', 'err_login', $ex->getMessage(), Logger::GRAN_MONTH);
    }
  }
  redirect("index.php");
} // end if login
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  require_once 'includes/metas.php';
  ?>
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
</head>
<body class="hold-transition login-page background_img">


<div class="login-box">

  <?php if ($ERROR_MSG <> "") { ?>
    <!-- <div class="col-lg-12 login-box"> -->
    <div class="alert no-marge alert-dismissable alert-<?php echo $ERROR_TYPE ?>">
      <button data-dismiss="alert" class="close" type="button">x</button>
      <p><?php echo $ERROR_MSG; ?></p>
    </div>
    <!-- </div> -->
  <?php } ?>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <h3 class="text_center">Commencer votre session</h3>

    <form action="index.php" method="post" name="contact_form" id="contact_form">
      <input type="hidden" name="mode" value="login">

      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Login" id="username" name="username">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Mot de passe" id="user_password"
               name="user_password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <a href="forgotpwd.php">J'ai oubli&eacute; mon mot de passe</a>
            </label>
          </div>
        </div>
        <!-- /.col-xs-8 -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
        </div>
        <!-- /.col-xs-4 -->
      </div>
      <!-- /.row -->
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>