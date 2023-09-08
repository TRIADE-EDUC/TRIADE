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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("profadmin");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Savoir / être" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
$idclasse=$_POST["idclasse"];
$idmatiere=$_POST["idmatiere"];

$anneeScolaire=anneeScolaireViaIdClasse($idclasse);
$nb=$_POST["nb"];
if (isset($_POST["adminIdprof"])) {
	$idpers=$_POST["adminIdprof"];
}else{
	$idpers=$_SESSION["id_pers"];
}
for($i=0;$i<$nb;$i++) {
	$ideleve=$_POST["ideleve_$i"];
	$ponct=$_POST["ponct_$i"];
	$motiv=$_POST["motiv_$i"];
	$dynam=$_POST["dynam_$i"];
	
	$ponct=preg_replace('/\n/',' ',$ponct);
	$motiv=preg_replace('/\n/',' ',$motiv);
	$dynam=preg_replace('/\n/',' ',$dynam);
		
	$ponct=preg_replace('/\r/',' ',$ponct);
	$motiv=preg_replace('/\r/',' ',$motiv);
	$dynam=preg_replace('/\r/',' ',$dynam);

	if ((trim($ponct) == "") && (trim($motiv) == "") && (trim($dynam) == "")) continue;
	if ($ideleve > 0) saveSavoirEtre($ideleve,$idclasse,$anneeScolaire,$ponct,$motiv,$dynam,$idpers,$idmatiere);
}

print "<br><br><center><font class='T2 shadow'>".LANGDONENR."</font></center><br><br>";
if (isset($_POST["adminIdprof"])) { 
	print "<table align='center'><tr><td><script>buttonMagicRetour('notevisuadmin.php?saisie_pers=".$_POST["adminIdprof"]."','_parent')</script></td></table><br><br>";
}else{
	print "<table align='center'><tr><td><script>buttonMagicRetour('savoiretre.php','_parent')</script></td></table><br><br>";
}
?>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
