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
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE59 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br><br>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("3");


if (isset($_POST["activite"])) {
	$activite="$_POST[activite]";
}

if (isset($_GET["id"])) {
	$activite="$_GET[id]";
}

$fichier="gestion_stage_ent_modif2.php";
$table="stage_entreprise";
$champs="secteur_ac";
$iddest=$activite;
$nbaff=6;

if (isset($_GET["nba"])) {
	$depart=$_GET["limit"];
	$departement=$_GET["departement"];
}else {
	$depart=0;
}

if (isset($_POST["departement"])) {
	$departement=$_POST["departement"];
}

print "<font class=T2><ul>";
	print LANGSTAGE31." : <b> $activite </b><br><br><br>";
        $data=recherche_activite_limit($activite,$depart,$nbaff,$departement,"","","","","","","");
	//$data=recherche_activite($activite);
	//id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus
	for($i=0;$i<count($data);$i++) {
?>
		<table bgcolor="#FFFFFF" border=1 bordercolor="#000000" width=80% >
		<tr><td id=bordure >
		<?php print LANGSTAGE39 ?> : <font color=red><?php print $data[$i][1] ?></font> /
		<?php print LANGSTAGE40 ?> :  <?php print  $data[$i][7] ?><br>
		<?php print LANGSTAGE30 ?> :   <?php print $data[$i][5] ?>  <?php print $data[$i][4] ?> <br>
		<div align=right>
		<input type=button onclick="open('gestion_stage_ent_modif3.php?id=<?php print $data[$i][0] ?>','_parent','')" value="<?php print LANGPER30?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
		&nbsp;&nbsp;
 		[ <a href="#" onclick="open('gestion_stage_ent_info.php?id=<?php print $data[$i][0] ?>','','width=400,height=450,scrollbars=yes')"><?php print LANGSTAGE62 ?> +</a> ]&nbsp;&nbsp;&nbsp;</div>
		<br>
 		</td></tr></table><br><br>
		<?php
	}
print "</font>[<a href='gestion_stage_ent_modif.php'>".LANGSTAGE41."</a>]<br><br> ";
print "</ul>";
?>


<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent($fichier,$table,$depart,$nbaff,$champs,$iddest,$departement); ?><br><br></td>
<td align=right width=33%><br><?php suivant($fichier,$table,$depart,$nbaff,$champs,$iddest,$departement); ?>&nbsp;<br><br></td>
</tr></table>


</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
?>
</BODY></HTML>
