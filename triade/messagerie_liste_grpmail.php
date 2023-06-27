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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
//error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post name="formulaire" action='./messagerie_creat_grpmail2.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS28?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut -->
<table width=100% border=0>
<?php

if (isset($_GET["supp"])) {
	mail_grp_supp($_GET["supp"],$_SESSION["id_pers"]);
}

$data=liste_grp_mail($_SESSION["id_pers"]);
//id,idpers,liste_id,libelle
for($i=0;$i<count($data);$i++) {
	$cacher=$data[$i][4];
	if ($cacher == 1) { $cacher="(Liste non visible)"; }else{ $cacher="(Liste visible)"; }
	print "<tr><td>".LANGMESS29." : <b>".$data[$i][3]."</b>  - <i>$cacher</i>  </td></tr>";
	print "<tr><td>".LANGMESS30." : ";
	$data2=liste_idpers_grp_mail($data[$i][2]);
	foreach($data2 as $liste_pers) {
		print "<b>".recherche_personne($liste_pers)." (".recherche_type_personne($liste_pers).")- </b>";
	}
	print "</td></tr>";
?>
<tr><td ><input type=button onclick="open('messagerie_liste_grpmail.php?supp=<?php print $data[$i][0]?>','_parent','')" value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"> <input type=button onclick="open('messagerie_creat_grpmail.php?id=<?php print $data[$i][0]?>','_parent','')" value="<?php print LANGPER30?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td></tr>
<tr><td ><hr width=100%></td></tr>
<?php
}
?>
</table>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
