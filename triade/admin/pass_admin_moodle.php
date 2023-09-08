<?php
session_start();
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
<?php
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond2' text='#000000' >
<?php
$erreur="";
$modif="non";
if (isset($_POST["create"])) {
	include("../common/lib_acces_inc.php");
	if ( $_POST["saisie_new"] == $_POST["saisie_renew"] ) {
		$modif="non";
		$cnx=cnx();
		modifpassemoodle($_POST["saisie_new"]);
		history_cmd("Admin Triade","MODIF","Mot de passe Admin Moodle");
		Pgclose($cnx);
	?>
		<center><font class=T2>Mot de passe modifié</font></center>
		<br><br>
		<table align=center border=0>
		<tr><td align=center>
		<script language=JavaScript>buttonMagicFermeture(); //text,nomInput</script>
		</td></tr></table>
	<?php
	}else {
		$modif="oui";
		$erreur="<center><FONT COLOR=red size=3>ERREUR MOT DE PASSE NON CHANGE</font></center><br>";
	}
}else {

	$modif="oui";
}

if ($modif=="oui") {
?>
<form name=formulaire method=post>
<?php print $erreur?>
<table width=100% align=center border=0>
<tr><td align="right"><font class=T2>Nouveau mot de passe</font> :</td><td><input type='password'  name='saisie_new' ></td></tr>
<tr><td align="right"><font class=T2>Confirme mot de passe</font> :</td><td><input type='password'  name='saisie_renew' ></td></tr>
<tr><td colspan=2><br><br>
<table align=center border=0>
<tr><td align=center>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture(); //text,nomInput</script>&nbsp;&nbsp;
</td></tr>
</table>
</td></tr>
</table>
</form>

<?php
}
?>
</BODY>
</HTML>
