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
<form method=post onsubmit="return verifcreatmatiere()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP25?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<table width=100%>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=affToutesLesMatieres(); //  code_mat,libelle,sous_matiere,offline,couleur, libelle_longe, code_matiere
Pgclose();
for($i=0;$i<count($data);$i++)  {
	if ($data[$i][1] != "") {
		$code_matiere=$data[$i][6];
		if ($code_matiere != "") { 
			$code_matiere="<b>$code_matiere</b>&nbsp;:&nbsp;";
		}
		$libelleLong=stripslashes($data[$i][5]);
		if (trim($libelleLong) != "") {
			$libelleLong=" ($libelleLong)";
		}
		?>
		<tr class='tabnormal' onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'" >
		<?php
		print "<td >";
		if ($data[$i][3] == 1) {
		print "<img src='./image/commun/img_ssl_mini.png' alt='Inactif' /> ";
		}
		print $code_matiere;
	        print stripslashes($data[$i][1]);
		if (trim($data[$i][2]) != "0") { print " ".stripslashes($data[$i][2])." <i>(sous-matière)</i>"; }
		print "&nbsp;&nbsp;<i>$libelleLong</i> </td>";
		print "<td width=5><input type=button class=button value=\"".LANGPER30."\" onclick=\"open('modif_matiere.php?id=".$data[$i][0]."','_parent','');\" ></td>\n";
		print "</tr>\n";
        }
}
?>
</table>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<BR>
</BODY></HTML>

