<?php
session_start();
error_reporting(0);
if (empty($_SESSION["admin1"])) {
    print "<script language='javascript'>";
    print "location.href='./acces_refuse.php'";
    print "</script>";
    exit;
}
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<?php
include_once("../common/lib_admin.php");
include_once("../common/config.inc.php");
include_once("../common/lib_ecole.php");
include_once("../common/config2.inc.php");
?>

<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond2' text='#000000' >

<?php
include_once("../librairie_php/db_triade.php");

$erreur="";
$modif="non";
$cnx=cnx();
if (isset($_POST["create"])) {
	$emailenvoie=$_POST["emailenvoie"];
	if ($_POST["initia"] == "0") {
		initialisePasswordEnseignant($emailenvoie);
		$message="Les mots de passe sont réinitialisés"; 
	}
	if ($_POST["initia"] == "1") { 
		initialisePasswordDefinieEnseignant($_POST["passedef"],$emailenvoie);
		$message="Les mots de passe sont réinitialisés"; 
	}
	history_cmdAdmin("Admin Triade","MODIF","Réinitialisation mot de passe Enseignant");
?>
	<?php 
	if ($erreurPersonne != "") { ?>
		<textarea cols=90 rows=18 >Enseignant non trouvé dans la base, vérifier que la syntaxe soit exactement la même que dans la base de données.

<?php print $erreurPersonne ?></textarea><br />
	<?php }else{ ?>
	<center><font size=3><?php print $message ?></font>	
			<br><br>
	<?php } ?>
<?php
if (file_exists("../data/fic_pass.txt")) {
?>
<br /><br /><input type=button class=BUTTON value="<?php print "Récupération des mots de passe"?>" onclick="open('recupepwens.php','_blank','')"><br /><br /><br /><br />
<?php } ?>
</center>
		<table align=center border=0>
		<tr><td align=center>
		<script language=JavaScript>buttonMagicFermeture(); //text,nomInput</script>
		</td></tr></table>

	<?php	
}else {

	$modif="oui";
}

if ($modif=="oui") {
?>
<form name="formulaire" method="post" enctype="multipart/form-data" onSubmit="document.formulaire.rien.disabled=true" >
<?php print $erreur?>
<table width=100% align=center border=0>
<tr><td >
<ul><font class="T2">Confirmation de la réinitialisation <br />des mots de passe des enseignants</font></ul>
</td></tr>

<?php 
if ((LAN == "oui") && (ValideMail(MAILREPLY))) { ?>
<tr>
<td><input type='checkbox' name='emailenvoie'  value='1' onclick="envoiMailP()"   > <font class="T2">Envoyer un email au compte avec son nouveau mot de passe.</font>  </td>
</tr>

<?php
}
?>
<tr><td ><input type="radio" name="initia" value="0" checked="checked"  > Mot de passe aléatoire. </td></tr>

<tr><td><input type="radio" name="initia" value="1" > Mot de passe définie : <input type=text name="passedef"  /> <br></div></td></tr>

<tr><td><div id='infourl' style="display:none"><br><font class='T2'  id='color3' > AVEZ VOUS VERIFIE L'ADRESSE INTERNET DU SITE TRIADE DANS LE MODULE "CONFIG GENERAL" AVANT VALIDATION !!! </font></div>

</td></tr>

<tr><td colspan=2><br>

<table align=center border=0>
<tr><td align=center>
<script language=JavaScript>buttonMagicSubmit("Confirmer","rien"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture(); //text,nomInput</script>&nbsp;&nbsp;
</td></tr>
</table>
</td></tr>
</table>
<input type='hidden' name='create' />
</form>

	<script>
	function envoiMailP() {
		if (document.getElementById('infourl').checked == false) {
			document.getElementById('infourl').checked=true;
			document.getElementById('infourl').style.display='block';
		}else{
			document.getElementById('infourl').checked=false;
			document.getElementById('infourl').style.display='none';
		}
	}
	document.getElementById('infourl').checked=false;
	</script>
<?php
}
Pgclose($cnx);
?>
</BODY>
</HTML>
