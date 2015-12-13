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
            $mail_corps = '<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0" marginwidth="0" marginheight="0" width="100%"><div bgcolor="#e0e2e4"><div dir="" style="padding:0px;"><table align="center" bgcolor="#e0e2e4" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td><table style="margin:0 auto" align="center" bgcolor="#e0e2e4" cellpadding="0" cellspacing="0" width="600"><tbody><tr><td><table style="border:0" align="center" border="0" cellpadding="0" cellspacing="0" width="600"><tbody><tr><td style="padding:0px">&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table align="center" bgcolor="#e0e2e4" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td><table style="margin:0 auto" align="center" bgcolor="#e0e2e4" cellpadding="0" cellspacing="0" width="600"><tbody><tr><td style="border:1px solid #d9d9da;border-bottom:0px" bgcolor="#fcfefc"><table style="width:100%;margin:0 auto!important" align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><table style="margin:0 auto!important;padding:15px 0" align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding:0 0 0 10px" height="40"><h1 style="font-family:Arial;font-size:22px;font-weight:normal;letter-spacing:-1px;margin:0!important">R&eacute;nitialisez votre mot de passe</h1></td></tr></tbody></table></td></tr></tbody></table><table style="padding:2px 0;width:100%;margin:0 auto!important" align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><table style="margin:0 auto!important" align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="text-align:center;text-align:center" height="26"><span style="margin:0 auto;color:#9e9ea2;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;font-size:12px">Bonjour '.$results[0]["firstname"].' '.$results[0]["lastname"].', vous avez r&eacute;cemment demand&eacute; un nouveau mot de passe.</span></td></tr><tr><td style="text-align:center;text-align:center"><span style="margin:0 auto;color:#9e9ea2;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;font-size:12px">Cliquez sur le bouton "R&eacute;initialiser mon mot de passe".</span></td></tr></tbody></table></td></tr></tbody></table><table style="width:100%;margin:0 auto!important;padding:15px 0" align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><table style="margin:0 auto!important" align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding:10px 15px;font-weight:bold;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;font-size:12px;text-align:center" bgcolor="#f07355"><a href="http://'.$_SERVER["HTTP_HOST"].REPSITE.'/changepwd.php?email='.$email.'&sec='.$token.'" style="text-decoration:none;color:#ffffff" title="R&eacute;initialiser mon mot de passe" target="_blank">R&eacute;initialiser mon mot de passe</a></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="border:1px solid #d9d9da;border-bottom:0px;border-top:0px;padding:15px 0" align="center" bgcolor="#f6f7f8"><table style="margin:0 auto" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td style="text-align:center"><span style="color:#9f9f9f;font-size:11px;margin:0 auto;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;padding:0"> Ce lien de r&eacute;initialisation est valable 48h. Au del&agrave;, vous devrez faire une <a href="http://'.$_SERVER["HTTP_HOST"].REPSITE.'/forgotpwd.php" title="Faire une nouvelle demande" style="color:#42a2d6;font-weight:bold;text-decoration:none" target="_blank">nouvelle demande</a>. </span></td></tr><tr><td style="text-align:center"><span style="color:#9f9f9f;font-size:11px;margin:0 auto;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;padding:0"> Vous n\'&ecirc;tes pas &agrave; l\'origine de cette demande ? <a href="mailto:afid.benayad@gmail.com" title="Nous contacter" style="color:#42a2d6;font-weight:bold;text-decoration:none" target="_blank">Contactez-nous</a>. </span></td></tr></tbody></table></td></tr></tbody></table><table style="margin:0!important;padding:0!important" align="center" bgcolor="#e0e2e4" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td><table style="margin:0 auto!important;padding:0!important" align="center" bgcolor="#e0e2e4" cellpadding="0" cellspacing="0"><tbody><tr style="margin:0!important;padding:0!important"><td style="margin:0!important;padding:0!important"><img src="http://afid.noip.me'.REPSITE.'/dist/img/DCR.jpg" alt="&nbsp;" style="display:block;margin:0 auto!important;padding:0!important" border="0"></td></tr><tr><td><table style="margin:0 auto" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding:5px 0 0 0!important"><span style="color:#9f9f9f;font-size:9px;margin:0 auto;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;width:100%;text-align:center;display:block">Ce message est destin&eacute; &agrave; Afid Benayad</span></td></tr></tbody></table></td></tr><tr><td><table style="margin:0 auto" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td><span style="color:#9f9f9f;font-size:9px;margin:0 auto;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;width:100%;text-align:center;display:block"> Vous rencontrez des difficult&eacute;s ? Consultez notre <a href="http://'.$_SERVER["HTTP_HOST"].'/howto.html" title="Aide" style="color:#545357;text-decoration:none" target="_blank">aide en ligne</a>. </span></td></tr><tr><td><span style="color:#9f9f9f;font-size:9px;margin:0 auto;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;width:100%;text-align:center;display:block">A tr&egrave;s bient&ocirc;t, </span></td></tr><tr><td>&nbsp;</td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></div></body>';
            // fin corp du mail
            if (mail($email,MAIL_SUJET,$mail_corps,$mail_entete)) {
              // Mise a jour du token
              $sql = "UPDATE labo_employee SET token = :token, token_expire = now()+ INTERVAL 2 DAY WHERE email = :email";
              try {
                $stmt = $DB->prepare($sql);
                $stmt->bindValue(":token", $token);
                $stmt->bindValue(":email", $email);
                $stmt->execute();
                if ($stmt->rowCount()) {
                  $_SESSION["errorType"] = "success";
                  $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Veuillez verifier votre boite mail.';
                  $logger->log('succes', 'succes_changepwd', "Demande changement mot de passe avec succès pour: $email", Logger::GRAN_MONTH);
                } else {
                  $_SESSION["errorType"] = "warning";
                  $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Une erreur est survenue.';
                  $logger->log('erreurs', 'err_changepwd', "Une erreur lors de la demande de changement mot de passe est survenue pour: $email , mot de passe: $pass", Logger::GRAN_MONTH);
                }
              } catch (Exception $e){
                $_SESSION["errorType"] = "warning";
                $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
                $logger->log('erreurs', 'err_changepwd', "Une erreur lors de la demande de changement mot de passe est survenue pour: $email , mot de passe: $pass", Logger::GRAN_MONTH);
              }
              // Fin Mise a jour du token
              redirect("login.php");
              exit;
            }
            // fin envoi de mail
            // Si le compte n'est pas actif
          } else {
            $_SESSION["errorType"] = "warning";
            $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Votre compte n\'est pas activ&eacute;.';
            $logger->log('erreurs', 'err_changepwd', "le compte $email est non actif lors de la demande de changement mot de passe", Logger::GRAN_MONTH);
          }
        } else {
          // Si email introuvable en DB
          $_SESSION["errorType"] = "warning";
          $_SESSION["errorMsg"] = '<i class="icon fa fa-ban"></i>OUPS... Votre email est inexistant';
          $logger->log('erreurs', 'err_changepwd', "le compte $email est inexistant lors de la demande de changement mot de passe", Logger::GRAN_MONTH);
        }
      } catch (Exception $ex) {
        $_SESSION["errorType"] = "danger";
        $_SESSION["errorMsg"] = $ex->getMessage();
        $logger->log('erreurs', 'err_changepwd', $ex->getMessage(), Logger::GRAN_MONTH);
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
    <h3 class="text_center">Réinitialiser votre mot de passe</h3>

    <form action="#" method="post" name="contact_form" id="contact_form">
      <input type="hidden" name="mode" value="get_password">

      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" id="email" name="email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <p class="text-aqua text_left">Après avoir saisi votre email, consultez votre messagerie : vous y trouverez un mail vous expliquant comment créer un nouveau mot de passe.</p>
				<p>En effet, pour des raisons de confidentialité, nous ne renvoyons pas les mots de passe par email.</p>
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