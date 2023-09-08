<?php
error_reporting(0);
session_start();
if (empty($_SESSION["nom"])){
	header('Location: ./acces_refuse.php');
	exit;
}
include_once("common/config.inc.php");
include_once("common/config2.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (!verif_compte($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"])) {
	header('Location: ./acces_depart.php');	
	PgClose();
	exit;
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
		<script type="text/javascript" src="./librairie_js/prototype.js"></script>
		<script type="text/javascript" src="./librairie_js/ajax_pass.js"></script>
		<script type="text/javascript" src="./librairie_js/function.js"></script>
	</head>
<body>
<?php 
include_once("./librairie_php/lib_licence2.php");
include_once("./librairie_php/langue.php");
$cnx=cnx();
//--------Stat-------------//
// pour le navigateur
@statNavigateur($_POST["statNomNavigateur"],$_POST["statVersion"],$_POST["statOs"],$_POST["statLangue"]);
// pour le stat Screen
@statScreen($_POST["statEcran"]);
// pour le stat du debit
@statdebit($_POST["statDebit"]);
//------------------------//
/*
print "$_POST[statNomNavigateur]<br>";
print "$_POST[statVersion]<br>";
print "$_POST[statOs]<br>";
print "$_POST[statLangue]<br>";
print "$_POST[statEcran]<br>";
print "$_SESSION[nom]<br>";
print "$_SESSION[prenom]<br>";
print "$_POST[membrestat]<br>";
print "$_POST[statDebit]<br>";
*/
?>
		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center;">
			<div id="mainInst" style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);'>
				<img src="./image/logo_triade_licence.gif"
				     alt="logo_triade_licence" />
<?php
	include_once("./common/version.php");
	include_once("./common/productId.php");
	
	if ($_SESSION["membre"] == "menuprof") {
		$idpers=$_SESSION["id_suppleant"];
	}else{	
		$idpers=$_SESSION["id_pers"];
	}

	if ($_SESSION["membre"] == "menueleve") { 
		if (VATEL == 1) {
			$email=cherchemailelevePro($_SESSION["id_pers"]); 
		}else{
			$email=cherchemaileleve($_SESSION["id_pers"]); 
		}
	}
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) { $email=cherchemailpersonnel($_SESSION["id_pers"]); }
	if ($_SESSION["membre"] == "menuprof") { $email=cherchemailpersonnel($_SESSION["id_suppleant"]); }
	if ($_SESSION["membre"] == "menuparent") {$email=cherchemailparent($_SESSION["id_pers"]); }
?>
<center><br><?php print LANGMODIF24 ?> <b><?php print stripslashes($_SESSION["nom"])?> <?php print stripslashes($_SESSION["prenom"])?></b> <?php print LANGMODIF24bis ?>
<?php
	if ( ((VALIDPWD == "oui") && ($_SESSION["membre"] == "menuadmin")) || 
	((VALIDPWD == "oui") && ($_SESSION["membre"] == "menuscolaire")) ||
	((VALIDPWD == "oui") && ($_SESSION["membre"] == "menupersonnel")) ||
	((VALIDPWD == "oui") && ($_SESSION["membre"] == "menututeur")) ||
	((PWDPROF == "oui") && ($_SESSION["membre"] == "menuprof")) ||
	((PWDPARENT == "oui") && ($_SESSION["membre"] == "menuparent")) ||
	((PWDELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) ) 
	{
		
		$affiche1=affichageMessageSecurite3();
		$security=SECURITE;
?>
<br><?php print LANGMODIF23 ?>.</center>
<br />
<br />
<form method=post action="inscription3.php" align=center name="formulaire" >
<table border=0 align=center width=70%   >
<tr ><td align=right  ><font class="T2"><?php print LANGPASS1 ?></font> :</td>
<td  ><input type="password" id="passwd" name="passwd" value=""  maxlength="20" onclick="document.getElementById('visu1').style.visibility='hidden'"  onblur="document.getElementById('visu1').style.visibility='visible';verifPass(document.formulaire.passwd.value,'<?php print $security ?>','visu1');" > <input type=button value='ok' class='BUTTON2' /></td><td><span id="visu1"></span></td></tr>
<tr ><td align=right  ><font class="T2"><?php print LANGPASS1bis ?></font> :</td>
	<td  ><input type="password" id="confirm_passwd" name="confirm_passwd" maxlength="20" onclick="document.getElementById('visu2').style.visibility='hidden'"   onblur="document.getElementById('visu2').style.visibility='visible';verifPass2(document.formulaire.passwd.value,document.formulaire.confirm_passwd.value,'<?php print $security ?>','visu2');"  >  <input type=button value='ok' class='BUTTON2' /></td><td width=10%><span id="visu2">&nbsp;</span></td></tr>
</table>
<center><font size=1><?php print $affiche1 ?></font></center>
<br /><br />
<?php if ( ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menupersonnel") || ($_SESSION["membre"] == "menututeur") || ($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuparent") || ((EMAILCHANGEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) )   { ?>
<table border=0 align=center width=70%>
<tr><td align=right><font class="T2"><?php print "Votre Email" ?></font> :</td><td><input type=text name=mail  maxlength=150 size=40 value="<?php print trim($email) ?>" ></td></tr>
</table>
<?php } ?>
<br /><br />
<center><input type=submit value="<?php print LANGBTS ?>"  class="BUTTON"  name="suiteavecpass" ></center>
<input type=hidden value="0"  name="passok" >
</form>
<script>document.formulaire.suiteavecpass.disabled=true;</script>
<script>document.formulaire.confirm_passwd.disabled=true;</script>

<?php
}else{
	statUtilisateur($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);
?>
<br /><br />
<form method=post action="inscription3.php" name='formulaire2'>
<?php if ( ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menupersonnel") || ($_SESSION["membre"] == "menututeur") || ($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuparent") || ((EMAILCHANGEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) )   { ?>
<table border=0 align=center width=70%  bordercolor="#000000">
<tr bordercolor="#FFFFFF"><td align=right id='bordure' ><?php print "Votre Email" ?> :</td><td><input type=text name=mail maxlength=50 size=40 value="<?php print $email ?>" onblur='verifEmail(this)' ></td></tr>
</table>
<?php } ?>
<br /><br />
<input type=hidden name="passok" value="1" />
<center><input type=submit value="<?php print LANGBTS ?>"  class="BUTTON" name="suitenopasse" ></center>
</form>
<?php  } ?>
</p>
<?php Pgclose(); ?>			



	</div>		</div>	</div>

<?php
	include_once("installation/librairie/pied_page.php");
?>

	</body>
</html>
