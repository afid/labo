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
 }
 // Fin Si mot de passe non renseigné
 // Si mot de passe different
 elseif ($pwd1 != $pwd2 ) {
  $_SESSION["errorType"] = "danger";
  $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Les deux mots de passe ne sont pas identiques";
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
       $sql = "UPDATE labo_employee SET password = :pwd1, token='', token_expire = '' WHERE email = :email and token = :token";
       try {
        $stmt = $DB->prepare($sql);
        $stmt->bindValue(":pwd1", md5($pwd1));
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":token", $token);
        $stmt->execute();
        if ($stmt->rowCount()) {
         $_SESSION["errorType"] = "success";
         $_SESSION["errorMsg"] = '<i class="icon fa fa-check"></i>Votre mot a été modifié avec succès.';
        } else {
         $_SESSION["errorType"] = "warning";
         $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
        }
       } catch (Exception $e){
       $_SESSION["errorType"] = "warning";
       $_SESSION["errorMsg"] = '<i class="icon fa fa-info"></i>Une erreur est survenue.';
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
       redirect("forgotpwd.php");
      }
      // Fin Si date token invalide
     // Fin Si compte actif
     // Si compte suspendu
     } else {
      $_SESSION["errorType"] = "warning";
      $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Votre compte est suspendu.";
      redirect("index.php");
     }
     // Fin Si compte suspendu
    }
    // Fin Si résultat dans Base de donnée
    // Si tentative de Hack
    else {
     $_SESSION["errorType"] = "warning";
     $_SESSION["errorMsg"] = "<i class=\"icon fa fa-ban\"></i>Votre demande ne peut aboutir";
     redirect("forgotpwd.php");
    }
    // Fin Si tentative de Hack
   }
   // Si erreur SQL
   catch (Exception $ex){
    $_SESSION["errorType"] = "danger";
    $_SESSION["errorMsg"] = $ex->getMessage();
    redirect("index.php");
   }
   // Si erreur SQL
 }
 // Fin Si Formulaire corectement renseigné

redirect("changepwd.php?email=" . $email . "&sec=".$token."");
// recherche de la validité du mail et du token

 // si oui
  // recherche date de validité du token
   // si oui Modification du mot de passe et suppression du token et date token
    // Redirection page de login
   // si date token non valide Message demande plus a jour
   // Redirection page demande changement mot de passe
 // si non Message demande non valide
 // Redirection page demande changement mot de passe




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
  <!-- <div class="col-lg-12 login-box"> -->
  <div class="alert no-marge alert-dismissable alert-<?php echo $ERROR_TYPE ?>">
   <button data-dismiss="alert" class="close" type="button">x</button>
   <p><?php echo $ERROR_MSG; ?></p>
  </div>
  <!-- </div> -->
 <?php } ?>
 <!-- /.login-logo -->
 <div class="login-box-body">

  <p class="login-box-msg">Changer votre mot de passe</p>
  <p>Dans le premier champ saisisez votre nouveau mot de passe choisi, puis confirmez le dans le second.</p>

  <form action="changepwd.php?email=<?php echo $email; ?>&sec=<?php echo $token; ?>" method="post" name="change_password" id="change_password">
   <input type="hidden" name="mode" value="change_password">

   <div class="form-group has-feedback">
    <input type="password" class="form-control" placeholder="Mot de passe" id="pwd1" name="pwd1">
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
   </div>


   <div class="form-group has-feedback">
    <input type="password" class="form-control" placeholder="Confirmer votre Mot de passe" id="pwd2" name="pwd2">
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
   </div>
   <div class="row">
    <div class="col-xs-8">
    </div>
    <!-- /.col-xs-8 -->
    <div class="col-xs-4">
     <button type="submit" class="btn btn-primary btn-block btn-flat">Valider</button>
    </div>
    <!-- /.col-xs-4 -->
   </div>
   <div class="form-group has-feedback fsPwdMessage">
    <p>Le mot de passe doit être conforme à la politique de sécurité mise en place, à savoir que sa taille doit être d'au minimum 8 caractères, ne doit contenir le nom, prénom ou identifiant du compte utilisateur et doit contenir au moins 3 types de caractères différents dans les familles de caractères suivantes :
     <ul>
     <li>Chiffres : 0 1 2 3...</li>
     <li>Lettres minuscules : a b c... z</li>
     <li>Lettres majuscules : A B C... Z</li>
     <li>Caractères spéciaux : $ % # @ ; , . ...</li>
    </ul>
    </p>
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

</body>
</html>