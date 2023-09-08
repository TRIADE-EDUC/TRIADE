<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");	
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }

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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_supp_choix('saisie_classe_supp','<?php print LANGRESA31?>')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGRESA35?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<table width=100% border=1 bordercolor="#000000" bgcolor="#FFFFFF" id='bordure' style="border-collapse: collapse;" >
<tr>
<td bgcolor=yellow align=center ><?php print LANGRESA62 ?></td>
<td bgcolor=yellow align=center ><?php print LANGSTAGE37 ?></td>
</tr>

<?php
$data=list_equip();
// id,libelle,info,type
for($i=0;$i<count($data);$i++) {
	print "<tr  class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
	print "<td valign='top'  >&nbsp;<a href='resr_equip_ajout.php?id=".$data[$i][0]."' title='Modifier' >";
	print "<img src='image/commun/editer.gif' border='0' />&nbsp;";
	print stripslashes(preg_replace('/ /',"&nbsp;",$data[$i][1]))."</a>&nbsp;</td>";
	print "<td  >&nbsp;".stripslashes($data[$i][2])."</td>";
	print "</tr>";
}
?>
</table>

<br>
<script language=JavaScript>buttonMagicRetour("resr_admin.php","_parent")</script>
<br><br>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
