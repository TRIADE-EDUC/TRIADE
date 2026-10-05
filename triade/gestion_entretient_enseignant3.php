<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Prochain rendez-vous</title>
<style>
body { background:#eef0f8; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
.rdv-wrap { width:360px; }
</style>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();

if (isset($_GET["eid"])) {
	$eid = $_GET["eid"];
	if ($eid) {
		$sql  = "SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM {$prefixe}personnel WHERE pers_id='$eid'";
		$res  = execSql($sql);
		$data = chargeMat($res);
		$nomProf    = ucwords($data[0][1]);
		$prenomProf = ucfirst($data[0][2]);
	}
}
if (isset($_POST["create"])) {
	$eid = $_POST["idpers"];
	if ($eid) {
		$sql  = "SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM {$prefixe}personnel WHERE pers_id='$eid'";
		$res  = execSql($sql);
		$data = chargeMat($res);
		$nomProf    = ucwords($data[0][1]);
		$prenomProf = ucfirst($data[0][2]);
	}
}
?>

<div class="rdv-wrap">
  <div class="card">
    <div class="card-header">
      <span class="card-title">Prochain rendez-vous</span>
    </div>
    <div class="card-body">

      <div style="font-size:13px;color:#080A66;font-weight:700;margin-bottom:14px;">
        <?php print $nomProf." ".$prenomProf ?>
      </div>

<?php
if (isset($_POST["create"])) {
	$idpers    = $_POST["idpers"];
	$heureR    = $_POST["heure"];
	$dateR     = $_POST["saisiedate"];
	$Qui       = recherche_personne2($_SESSION["id_pers"]);
	$message1  = "Rendez vous pour entretien individuel le ".$dateR." à $heureR avec $Qui";
	$number    = md5(uniqid(rand()));
	$emetteur  = $_SESSION["id_pers"];
	$destinataire = $idpers;
	$objet     = "Entretien individuel le $dateR";
	$text      = $message1;
	$date      = dateDMY2();
	$heure     = dateHIS();
	$type_personne      = "ADM";
	$type_personne_dest = "ENS";
	$idpiecejointe      = '';

	$cr = envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe);
	if ($cr == 1) {
		history_cmd($_SESSION["nom"],"MESSAGERIE","envoi à $nomProf");
		if (FORWARDMAIL == "oui") {
			@ini_set("sendmail_from",MAILCONTACT);
			$nomemetteur    = strtolower($_SESSION["nom"]);
			$prenomemetteur = strtolower($_SESSION["prenom"]);
			$membre_dest    = "menuprof";
			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
				$email = mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
				$http  = protohttps();
				$lien  = "$http".$_SERVER["SERVER_NAME"]."/";
				envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire);
			}
		}
	}

	$emetteur           = "$idpers";
	$destinataire       = $_SESSION["id_pers"];
	$type_personne      = "ENS";
	$type_personne_dest = "ADM";
	$number   = md5(uniqid(rand()));
	$message2 = "Rendez vous pour entretien individuel le $dateR à $heureR avec $nomProf $prenomProf";
	$text = $message2;
	$cr = envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe);
	if ($cr == 1) {
		$personne_envoi = $_SESSION["nom"];
		history_cmd($nomProf,"MESSAGERIE","envoi à $personne_envoi");
		if (FORWARDMAIL == "oui") {
			@ini_set("sendmail_from",MAILCONTACT);
			$nomemetteur    = strtolower($nomProf);
			$prenomemetteur = strtolower($prenomProf);
			$membre_dest    = $_SESSION["membre"];
			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
				$email = mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
				$http  = protohttps();
				$lien  = "$http".$_SERVER["SERVER_NAME"]."/";
				envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire);
			}
		}
	}
?>
      <div class="alert alert-success" style="margin-bottom:14px;">
        Rendez-vous enregistré et messages envoyés.
      </div>
      <div style="text-align:center;">
        <script language=JavaScript>buttonMagicFermeture();</script>
      </div>
<?php } else { ?>
      <form name="formulaire" method="post" action="gestion_entretient_enseignant3.php">
        <div class="form-row">
          <label class="form-label">Date</label>
          <input type="text" name="saisiedate" readonly="readonly" class="form-control" style="max-width:120px;">
          <?php include_once("librairie_php/calendar.php"); calendarDim('id2','document.formulaire.saisiedate',$_SESSION["langue"],"1","0"); ?>
        </div>
        <div class="form-row">
          <label class="form-label">Heure</label>
          <input type="text" name="heure" onclick="this.value=''" class="form-control" style="max-width:80px;" value="hh:mm" onKeyPress="onlyChar2(event)">
        </div>
        <input type="hidden" name="idpers" value="<?php print $eid ?>">
        <div style="padding:12px 0;text-align:center;border-top:1px solid #eef0f8;margin-top:8px;">
          <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
        </div>
      </form>
<?php } ?>

    </div>
  </div>
</div>

<?php @Pgclose(); ?>
</body>
</html>
