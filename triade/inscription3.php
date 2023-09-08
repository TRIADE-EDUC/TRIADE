<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {
	header('Location: ./acces_refuse.php');
	exit;
}

include_once("./librairie_php/lib_error.php");
if  ($_POST["passok"] != "1") {
	header('Location: ./inscription.php?err');
	exit;
}

include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (!verif_compte($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"])) {
	header('Location: ./acces_depart.php');	
}
PgClose();


/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
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


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php define("CHARSET","iso-8859-1"); ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css2.css" />
		<title>Triade Inscription</title>
	</head>

<body>
<?php 
include_once("./librairie_php/lib_licence2.php");
include_once("./librairie_php/langue.php");
include_once("./common/config2.inc.php");
$cnx=cnx();
?>

		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center;">
			<div id="mainInst" style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);' >
<?php
				if ((LAN == "oui") && (AGENTWEB == "oui")) {
					$nomeleve=$_SESSION["nom"];
					$prenomeleve=$_SESSION["prenom"];
			//	$vocal=urlencode(stripHTMLtags("Votre compte, $nomeleve, $prenomeleve, est maintenant validé. Si vous avez des questions, n'hésitez p &agrave; contacter votre administrateur Triade, via le lien question , sur la page de connexion."));
 			//	$mess="<iframe width='120' height='350' src=\"https://www.triade-educ.org/agentweb/agentpers.php?inc=6&mess=$vocal&m=M9\"  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>";
			//	print $mess;
			}
?>
				<img src="./image/logo_triade_licence.gif"
				     alt="logo_triade_licence" /><br /><br />

<?php
if ($_SESSION["membre"] == "menuprof") {
	$idpers=$_SESSION["id_suppleant"];
}else{	
	$idpers=$_SESSION["id_pers"];
}

if (isset($_POST["suiteavecpass"])) { 
	if ((isset($_SESSION["idparent"]))  && ($_SESSION["idparent"] == "2")) {
		update_passwd_parent2($_POST["confirm_passwd"],$_SESSION["membre"],$idpers);
	}else{	
		update_passwd($_POST["confirm_passwd"],$_SESSION["membre"],$idpers);  
	}
}

if ( ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || 
		($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuparent") || 
		((EMAILCHANGEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) ) {
		if (isset($_POST["mail"])) { update_mail($_POST["mail"],$_SESSION["membre"],$idpers); }
}

statUtilisateur($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);
enr_statUtilisateur($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"],$_SESSION["id_SESSION"]);
print "<center>".LANGMODIF24." <b> ".stripslashes($_SESSION["nom"])." ".stripslashes($_SESSION["prenom"])." </b> ".LANGMODIF24ter.". </center>";
print "<br /><br /><center><input type=button value='TERMINE'  onclick=\"open('acces2.php','_parent','');\"  class='BUTTON' ></center>";
Pgclose();
?>			
</div></div></div>
<?php
include_once("installation/librairie/pied_page.php");
?>
</body>
</html>
