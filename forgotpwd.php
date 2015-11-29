<?php
session_start();
require_once("includes/config.php");

if (isset($_SESSION["login"]) && $_SESSION["login"] != "") {
  // if logged in send to dashboard page
  redirect("index2.php");
}

$title = "Forgot password";
$mode = trim($_POST["mode"]);
if ($mode == "get_password") {
  $email = trim($_POST['email']);

if (isset($email) && $email == "") {
    $_SESSION["errorType"] = "danger";
    $_SESSION["errorMsg"] = '<i class="icon fa fa-ban"></i>Veuillez renseigner votre Email.';;
  } else {
    $sql = "SELECT * FROM labo_employee WHERE email = :email";

    try {
      $stmt = $DB->prepare($sql);

      // bind the values
      $stmt->bindValue(":email", $email);

      // execute Query
      $stmt->execute();
      $results = $stmt->fetchAll();

      if (count($results) > 0) {
        if ($results[0]["active"] == 1) {
          $_SESSION["errorType"] = "success";
          $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Veuillez verifier votre boite mail.';

          redirect("forgotpwd.php");
          exit;
        } else {
          $_SESSION["errorType"] = "warning";
          $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Votre compte n\'est pas activé.';
        }
      } else {
        $_SESSION["errorType"] = "warning";
        $_SESSION["errorMsg"] = '<i class="icon fa fa-ban"></i>Adresse email introuvable.';
      }
    } catch (Exception $ex) {
      $_SESSION["errorType"] = "danger";
      $_SESSION["errorMsg"] = $ex->getMessage();
    }
  }
  redirect($_SERVER['PHP_SELF']);
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
    <p class="login-box-msg">Perte de mot de passe</p>

    <form action="#" method="post" name="contact_form" id="contact_form">
      <input type="hidden" name="mode" value="get_password">

      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" id="email" name="email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <p class="text-aqua">Vous avez oubli&eacute le mot de passe? <br> Indiquez votre email puis cliquez sur "Demande de mot de passe".<br />
          Vous recevrez un email qui vous expliquera les &eacute;tapes &agrave; suivre.</p>
      </div>
      <div class="row">
        <div class="col-xs-4">
          <label>
            <a href="index.php" class="btn btn-success btn-block btn-flat">Accueil</a>
          </label>
        </div>
        <!-- /.col-xs-8 -->
        <div class="col-xs-8">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Demande de mot de passe</button>
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