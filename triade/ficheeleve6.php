<?php
session_start();
include_once("./common/config2.inc.php");
if ($_SESSION["adminplus"] != "suppreme") {
	if (defined("PASSMODULEINDIVIDUEL")) {
		if (PASSMODULEINDIVIDUEL == "oui") {
			header("Location:base_de_donne_key.php?key=passmodulemedical&eid=".$_GET["eid"]);
		}
	}
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
        <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
        <script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
        <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
        <script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
        <script language="JavaScript" src="./librairie_js/function.js"></script>
        <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
        </head>
        <body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php"); ?>
	<?php
	// connexion (après include_once lib_licence.php obligatoirement)
	include_once("librairie_php/db_triade.php");
	validerequete("3");
	$cnx=cnx();
	?>
        <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
        <?php include("./librairie_php/lib_defilement.php"); ?>
        </TD><td width="472" valign="middle" rowspan="3" align="center">
        <div align='center'><?php top_h(); ?>
        <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de l'élève (lecture seule)
$idEleve=$_GET["eid"];
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROF26 ?>  <font id="color2"><?php print recherche_eleve($idEleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' ><td colspan=2><br>&nbsp;&nbsp;<input type=button class=BUTTON value="<-- <?php print LANGPRECE ?>" onclick="open('ficheeleve3.php?eid=<?php print $_GET["eid"]?>','_parent','')"><br><br>
<table bordercolor="#CCCC00"  width=95% align=center border=1 bgcolor="#FFFFFF" >
<?php
if ( ((defined("INFOMEDIC")) && (INFOMEDIC == "oui")) || ($_SESSION["membre"] == "menuadmin" ) ) {
	$data=profPmedAff($idEleve);
	// id,date,idEleve,nomProf,commentaire
	for($i=0;$i<count($data);$i++) {
	?>
		<tr><td><br />&nbsp;&nbsp;
		<?php print LANGPROF30?>  <b><?php print $data[$i][1]?></b>
		<br><br>
		&nbsp;<?php print $data[$i][4]?><br>
		<div align=right><?php print LANGPROF31 ?> : <?php print $data[$i][3]?> &nbsp;&nbsp;</div>
		<br />
		</td>
		</tr>
	<?php
	}
}
?>
</table>
</td></tr>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
print "</SCRIPT>";
endif ;
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
