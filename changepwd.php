<?php
/**
 * Created by PhpStorm.
 * User: afidb
 * Date: 11/12/2015
 * Time: 23:55
 */
session_start();
require_once("includes/config.php");

$email = nl2br(htmlspecialchars(trim($_GET['email'])));
$token = nl2br(htmlspecialchars(trim($_GET['sec'])));

$pwd1 = trim($_POST['pwd1']);
$pwd2 = trim($_POST['pwd2']);

$title = "Changepwd";
$mode = trim($_POST["mode"]);
if ($mode == "change_password") {
 // Si mot de passe non renseigné
 if ($pwd1 == "" || $pwd2 == "") {
  $_SESSION["errorType"] = "danger";
  $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Veuillez renseigner tous les champs";
  $_SESSION["login"] = null;
  $logger->log('erreurs', 'err_changepwd', "les deux champs mots de passe ne sont pas renseignés pour le compte $email", Logger::GRAN_MONTH);
 }
 // Fin Si mot de passe non renseigné
 // Si mot de passe different
 elseif ($pwd1 != $pwd2 ) {
  $_SESSION["errorType"] = "danger";
  $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Les deux mots de passe ne sont pas identiques";
  $logger->log('erreurs', 'err_changepwd', "les deux mots de passe sont differents pour le compte $email", Logger::GRAN_MONTH);
 }
 // Fin Si mot de passe different
 // Si Formulaire corectement renseigné
 else {
  $sql = "SELECT * FROM labo_employee WHERE email = :email AND token = :token ";
   try {
    $stmt = $DB->prepare($sql);
    // bind the values
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":token", $token);
    // execute Query
    $stmt->execute();
    $results = $stmt->fetchAll();
    // Si résultat dans Base de donnée
    if (count($results) > 0) {
     // Si compte actif
     if ($results[0]["active"] == 1) {
      // Si date token superieur a date actuelle Token encore Valide
      if ($results[0]["token_expire"] > date('Y-m-d H:i:s')) {
      // Changement de mot de passe
       $sql = "UPDATE labo_employee SET password = :pwd1, token='', token_expire = '', last_passwd_gen=now() WHERE email = :email and token = :token";
       try {
        $stmt = $DB->prepare($sql);
        $stmt->bindValue(":pwd1", md5($pwd1));
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":token", $token);
        $stmt->execute();
        if ($stmt->rowCount()) {
         $_SESSION["errorType"] = "success";
         $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Votre mot a été modifié avec succès.';
         $logger->log('succes', 'succes_changepwd', "Mots de passe modifié avec succès pour le compte $email", Logger::GRAN_MONTH);
        } else {
         $_SESSION["errorType"] = "warning";
         $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
         $logger->log('erreurs', 'err_changepwd', "Une erreur est survenue pour le login: $username", Logger::GRAN_MONTH);
        }
       } catch (Exception $e){
       $_SESSION["errorType"] = "warning";
       $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
       $logger->log('erreurs', 'err_changepwd', "Une erreur est survenue pour le login: $username", Logger::GRAN_MONTH);
      }
      // Fin Changement de mot de passe
      redirect("login.php");
      exit;
      }
      // Fin Si date token Valide
      // Si date token invalide
      else{
       $_SESSION["errorType"] = "warning";
       $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Votre demande n'est plus valide";
       $logger->log('erreurs', 'err_changepwd', "Demande changement de mot de passe invalide pour le login: $username", Logger::GRAN_MONTH);
       redirect("forgotpwd.php");
      }
      // Fin Si date token invalide
     // Fin Si compte actif
     // Si compte suspendu
     } else {
      $_SESSION["errorType"] = "warning";
      $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Votre compte est suspendu.";
      $logger->log('erreurs', 'err_changepwd', "Accès suspendu lors de la demande changement de mot de passe pour le login: $username", Logger::GRAN_MONTH);
      redirect("index.php");
     }
     // Fin Si compte suspendu
    }
    // Fin Si résultat dans Base de donnée
    // Si tentative de Hack
    else {
     $_SESSION["errorType"] = "warning";
     $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Votre demande ne peut aboutir";
     $logger->log('erreurs', 'err_changepwd', "Demande changement de mot de passe invalide, Tentative de Hack ", Logger::GRAN_MONTH);
     redirect("forgotpwd.php");
    }
    // Fin Si tentative de Hack
   }
   // Si erreur SQL
   catch (Exception $ex){
    $_SESSION["errorType"] = "danger";
    $_SESSION["errorMsg"] = $ex->getMessage();
    $logger->log('erreurs', 'err_login', $ex->getMessage(), Logger::GRAN_MONTH);
    redirect("index.php");
   }
   // Si erreur SQL
 }
 // Fin Si Formulaire corectement renseigné

redirect("changepwd.php?email=" . $email . "&sec=".$token."");
// recherche de la validité du mail et du token

} // Fin de Validation du formulaire
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
  <div class="alert no-marge alert-dismissable alert-<?php echo $ERROR_TYPE ?>">
   <button data-dismiss="alert" class="close" type="button">x</button>
   <p><?php echo $ERROR_MSG; ?></p>
  </div>
 <?php } ?>

 <div class="login-box-body">

  <h3 class="text_center">Changer votre mot de passe</h3>
  <p>Dans le premier champ saisisez votre nouveau mot de passe choisi, puis confirmez le dans le second. Cliquez sur Valider.</p>

  <form action="changepwd.php?email=<?php echo $email; ?>&sec=<?php echo $token; ?>" method="post" name="change_password" id="change_password">
   <input type="hidden" name="mode" value="change_password">

   <div class="form-group has-feedback">
    <input type="password" class="form-control" placeholder="Mot de passe" id="pwd1" name="pwd1">
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
   </div>

   <div class="form-group has-feedback">
    <input type="password" class="form-control" placeholder="Confirmer votre Mot de passe" id="pwd2" name="pwd2">
    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
   </div>
   <div class="row">
    <div class="col-xs-8">
    </div>
    <!-- /.col-xs-8 -->
    <div class="col-xs-4">
     <button type="submit" id="submit" class="btn btn-success btn-block btn-flat disabled">Valider</button>
    </div>
    <!-- /.col-xs-4 -->
   </div> </form>
   <div class="form-group has-feedback fsPwdMessage">
    <p>Le mot de passe doit être conforme à la politique de sécurité mise en place, à savoir que sa taille doit être d'au minimum 8 caractères, ne doit contenir le nom, prénom ou identifiant du compte utilisateur et doit contenir au moins 3 types de caractères différents :<br /><br />

     <button id="chiffres" class="btn btn-block btn-danger btn-xs text_left">Chiffres [ 0 1 2 3 4 5 6 7 8 9 ]</button>
     <button id="minuscules" class="btn btn-block btn-danger btn-xs text_left">Lettres minuscules [ a b c d e f g h ... x y z ]</button>
     <button id="majuscules" class="btn btn-block btn-danger btn-xs text_left">Lettres majuscules [ A B C D E F G H ... X Y Z ]</button>
     <button id="speciaux" class="btn btn-block btn-danger btn-xs text_left">Caractères spéciaux [ ~ ! @ # $ % ^ & * ( ) - _ = + [ ] { } ; : , .]</button>
     <button id="nombre" class="btn btn-block btn-danger btn-xs text_left">Nombre de caractères : Minimum 8</button>
     <button id="identique" class="btn btn-block btn-danger btn-xs text_left">Mot de passe identique</button>
    </ul>
    </p>
   </div>
   <!-- /.row -->

 </div>
 <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {

 $('#pwd1, #pwd2').on('keyup', function(e) {
  // Doit contenir des chiffres
  var chiffreRegex = new RegExp("[0-9]", "g");
  // Doit contenir des minuscules
  var minusculeRegex = new RegExp("[a-z]", "g");
  // Doit contenir des majuscules
  var majusculeRegex = new RegExp("[A-Z]", "g");
  // Doit contenir un charactere special
  var speciauxRegex = new RegExp("[!~@#$%()_=+{.};:\"\,$£¤^?\[\\]\\\\'§&\-/\]", "g");
  // Doit contenir 8 characteres minumum
  var nombreRegex = new RegExp("(?=.{8,}).*", "g");

 var testchiffre = 'false';
 var testminuscule ='false';
 var testmajuscule = 'false';
 var testnombre = 'false';
 var testspeciaux='false';
 var testidentique = 'false';

  if (chiffreRegex.test($(this).val()) === false) {
   $('#chiffres').removeClass().addClass('btn btn-block btn-danger btn-xs text_left');
   testchiffre = 'false';
  } else {
   $('#chiffres').removeClass().addClass('btn btn-block btn-success btn-xs text_left');
   testchiffre = 'true';
  }

  if (minusculeRegex.test($(this).val()) === false) {
   $('#minuscules').removeClass().addClass('btn btn-block btn-danger btn-xs text_left');
   testminuscule = 'false';
  } else {
   $('#minuscules').removeClass().addClass('btn btn-block btn-success btn-xs text_left');
   testminuscule = 'true';
  }

  if (majusculeRegex.test($(this).val()) === false) {
   $('#majuscules').removeClass().addClass('btn btn-block btn-danger btn-xs text_left');
   testmajuscule = 'false';
  } else {
   $('#majuscules').removeClass().addClass('btn btn-block btn-success btn-xs text_left');
   testmajuscule = 'true';
  }

  if (nombreRegex.test($(this).val()) === false) {
   $('#nombre').removeClass().addClass('btn btn-block btn-danger btn-xs text_left');
   testnombre = 'false';
  } else {
   $('#nombre').removeClass().addClass('btn btn-block btn-success btn-xs text_left');
   testnombre = 'true';
  }

  if (speciauxRegex.test($(this).val()) === false) {
   $('#speciaux').removeClass().addClass('btn btn-block btn-danger btn-xs text_left');
   testspeciaux='false';
  } else {
   $('#speciaux').removeClass().addClass('btn btn-block btn-success btn-xs text_left');
   testspeciaux='true';
  }

  if($('#pwd1').val() != '' && $('#pwd2').val() != '' && $('#pwd1').val() === $('#pwd2').val()){
   $('#identique').removeClass().addClass('btn btn-block btn-success btn-xs text_left');
   testidentique = 'true';
  } else {
   $('#identique').removeClass().addClass('btn btn-block btn-danger btn-xs text_left');
   testidentique = 'false';
  }

  if (testchiffre ==='true' && testminuscule ==='true' && testmajuscule ==='true' && testnombre ==='true' && testspeciaux ==='true' && testidentique ==='true'){
   $('#submit').removeClass().addClass('btn btn-success btn-block btn-flat');
  } else {
   $('#submit').removeClass().addClass('btn btn-success btn-block btn-flat disabled');
  }
 });
});
</script>
</body>
</html>