<?php
session_start();
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/style.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$idclasse=$_GET["idclasse"];
$libelle=$_GET["libelle"];

if (isset($_POST["update"])) {
	$nb=$_POST["nb"];
	$idclasse=$_POST["idclasse"];
	$libelle=$_POST["libelle"];
	for($i=0;$i<$nb;$i++) {
		$idmatiere=$_POST["idmatiere_$i"];
		$coef=$_POST["coef_$i"];
		miseAjourCoefBrevet($idclasse,$libelle,$idmatiere,$coef);
	}

	print "<center><font color=red>Mise à jour effectuée</font></center>";
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0'>
<td height="2"> <strong><font  id='menumodule1' ><?php print "Configuration des coefficients" ?></strong></font></td>
<tr id="cadreCentral0" >
<td height='100%' valign='top' >
<form method='post' >
<br />
&nbsp;&nbsp;<font class='T2'>Modifier les coefficients pour les matières du brevet.</font><br /> 
<?php print listMatiereBrevet2($libelle,$idclasse) ?>

<table border="0" align="center">
<tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGABS45?>","update"); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagicFermeture()</script></td>
</tr></table>

<input type='hidden' name='idclasse' value='<?php print $idclasse ?>' />
<input type='hidden' name='libelle' value="<?php print $libelle ?>" />
</form>
<!-- // fin form -->
</td></tr></table>
<?php Pgclose(); ?>
</BODY>
</HTML>
