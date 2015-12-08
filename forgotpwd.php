<?php
session_start();
require_once("includes/config.php");
// si la session existe
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
          // envoie de mail
          define('MAIL_SUJET','Votre identifiant');
          $token = md5(uniqid(mt_rand()));
          // Préparation Envoie du mail et update de la la table users
          // Préparation de l'entête du mail:
          $mail_entete  = 'From: "cmandgo.me"<'.ADMIN_MAIL.'>'."\r\n";
          $mail_entete .= 'Reply-To: "cmandgo.me"<'.ADMIN_MAIL.'>'."\r\n";
          $mail_entete .= 'Mime-Version: 1.0'."\r\n";
          $mail_entete .= 'Content-type: text/html; charset=utf-8'."\r\n";
          // Corp du mail
          // préparation du corps du mail
          $mail_corps  = "<b>Votre identification sur cmandgo.me</b><br />";
          $mail_corps .= "Monsieur : ".$results[0]['firstname']." ".$results[0]['lastname']."<br />";
          $mail_corps .="Pour finaliser le changement de votre mot de passe, nous vous invitons &agrave; cliquer sur le lien ci-dessous. Vous acc&eacute;derez directement &agrave; notre site afin de modifier votre mot de passe en toute simplicit&eacute; : <hr>";
          $mail_corps .= "<a href=http://".$_SERVER['HTTP_HOST'].REPSITE."/passwordconfirm.php?email=$email&ConnID=$token>Modifier mon mot de passe</a><hr>";
          $mail_corps .="En cas de dysfonctionnement, veuillez copier puis coller le lien <strong>http://".$_SERVER['HTTP_HOST']."/passwordconfirm.php?email=$email&ConnID=$token</strong> dans le champ \"adresse\" de votre navigateur Internet (Internet Explorer, Netscape, Mozilla...).<hr>";
          $mail_corps .="Ce lien restera disponible 48h, &agrave; la suite de quoi vous devrez r&eacute;it&eacute;rer votre demande. <br />";
          $mail_corps .="Si vous ne savez pas pourquoi vous avez re&ccedil;u cet e-mail, ignorez-le simplement ou bien contactez notre service Support.<br />";
          $mail_corps .="Nous vous remercions d'avoir utilis&eacute; <a href='afid.noip.me/labo'>afid.noip.me/labo</a> et vous souhaitons une agr&eacute;able visite sur notre site.";
          // fin corp du mail
          if (mail($email,MAIL_SUJET,$mail_corps,$mail_entete)) {
          // Mise a jour du token
            //$sql = "UPDATE labo_employee SET token='token' WHERE email = $email";
            $sql = "UPDATE labo_employee SET token = :token, token_expire = now()+ INTERVAL 2 DAY WHERE email = :email";
            try {
              $stmt = $DB->prepare($sql);
              $stmt->bindValue(":token", $token);
              $stmt->bindValue(":email", $email);
              $stmt->execute();
              if ($stmt->rowCount()) {
                $_SESSION["errorType"] = "success";
                $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Veuillez verifier votre boite mail.';
              } else {
                $_SESSION["errorType"] = "warning";
                $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Une erreur est survenue.';
              }
            } catch (Exception $e){
              $_SESSION["errorType"] = "warning";
              $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';

            }
          // Fin Mise a jour du token
          }
          // fin envoi de mail
          redirect("forgotpwd.php");
          exit;
        } else {
          $_SESSION["errorType"] = "warning";
          $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Votre compte n\'est pas activ&eacute;.';
        }
      } else {
        $_SESSION["errorType"] = "warning";
        $_SESSION["errorMsg"] = '<i class="icon fa fa-ban"></i>OUPS... Votre email est inexistant';
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
        <p class="text-aqua text_left">Indiquez votre email puis cliquez sur "Demande de mot de passe".</p>
        <p> Vous recevrez un email qui vous expliquera les &eacute;tapes &agrave; suivre.</p>
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