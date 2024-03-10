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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
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
<form  method=post onsubmit="return verifcreatclasse()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP24?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<table width=100% >
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=affClasseSansOffline(); //code_class,libelle,desclong,offline,idsite
// code_class,libelle,desclong,offline,idsite,niveau
for($i=0;$i<count($data);$i++)
{
	if ($data[$i][0] == 0) {
			$disabled="disabled='disabled'";
	}else{
			$disabled="";
	}
	$description_long=stripslashes(trim($data[$i][2]));
	$niveau="";
	if ($data[$i][5] != "") $niveau="(".$data[$i][5].")";
	$idsite=$data[$i][4];
	if ($idsite > 0) {
		$site=recupSite($idsite);
	}
	if ($description_long != "") {
		$description_long=" ($description_long)";
	}
        print "<tr class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >\n";
	print "<td>";
	if ($data[$i][3] == 1) {
		print "<img src='./image/commun/img_ssl_mini.png' alt='Inactif' /> ";
	}
	print "<b>".$data[$i][1]."</b> <font color=green>$niveau</font> <i>".$description_long."</i>&nbsp;<font size=1>(rattach&eacute;e au site : $site)</font> \n";
	if ($data[$i][6] == "1") print "<font color=red size=1><i>Connexion impossible des &eacute;tudiants de cette classe</i></font>";
        print "</td>\n";

	print "<td width=5><input type=button class=button value=\"".LANGPER30."\" onclick=\"open('modif_classe.php?id=".$data[$i][0]."','_parent','');\" $disabled ></td>\n";
	print "</tr>\n";
	$description_long="";
}
Pgclose();
?>
</table>
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>



