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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

if (defined("PASSMODULEINDIVIDUEL")) {
	if (PASSMODULEINDIVIDUEL == "oui") {
		if (empty($_SESSION["adminplus"])) {
			print "<script>";
			print "location.href='./base_de_donne_key.php?key=passmoduleindividuel'";
			print "</script>";
		}
	}
}



?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<script language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print "Enregistrement d'entretien" ?></b></font></td></tr>
<tr id='cadreCentral0'>
<td >
<br>
<?php
$listeeid=$_POST["listeeid"];
$tab=explode("#",$listeeid);
foreach($tab as $key=>$value) {
	$ideleve=$value;
	enrg_entretien($ideleve,$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["preparation"],$_POST["idpers"]);
}

?>

<font class='T2'><center>Données enregistrées</center></font>
<br><br>
<table align=center><tr><td><script language="JavaScript"  >buttonMagicRetour("entretien.php","_self")</script></td></tr></table>
<br><br>
</td></tr></table>

<br><br>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
