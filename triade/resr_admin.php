<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");	
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/jquery.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGRESA1?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
$demresaSalle=consult_resa2('2');
$demresaEquip=consult_resa2('1');
Pgclose();

?>
<br><br>
<table border=0 align=center width=95%>
<tr>
<form action='resr_equip_liste.php'>
<td align=right width=50%><font class=T2><?php print LANGRESA3?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT288 ?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='resr_equip_ajout.php'>
<td align=right><font class=T2><?php print LANGRESA5?> : </font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<tr><td></td></tr>
<!---
<tr><td></td></tr>
<tr>
<form action='resr_equip_modif.php'>
<td align=right><font class=T2><?php print LANGRESA6?> : </font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<tr><td></td></tr>
-->
<tr><td></td></tr>
<tr>
<form action='resr_equip_supp.php'>
<td align=right><font class=T2><?php print LANGRESA7?> : </font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT50?>","rien"); //text,nomInput</script>
</td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='calendrier_reser_equi.php'>
<td align=right><font class=T2><?php print LANGRESA44?> : </font></td>
<td align=left><table><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA57?>","rien"); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagic("<?php print "Listing" ?>","listing_resa.php?id=equip","_self","",""); //text,nomInput</script>
</td><td><script language=JavaScript>buttonMagic("<?php print "E.D.T." ?>","edt_visu.php?equip","_blank","",""); //text,nomInput</script>
</td></tr></table>
</td></td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='resr_equip_confirmer.php'>
<td align=right><font class=T2><?php print LANGRESA56?> : </font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA58?>","rien"); //text,nomInput</script>
<?php 
if (count($demresaSalle) > 0) {
	print "<img src='image/commun/important.png' id='imp1' />";
}
?>
</td>
</form>
</tr>
</table>
<br><br>
</td></tr></table>
<!-- // fin  -->
<!-- --------------------------------------------------------- -->
<br><br>
<a name="salle"></a>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGRESA2?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center width=95%>
<tr>
<form action='resr_salle_visu.php'>
<td align=right width=50%><font class=T2><?php print LANGRESA4?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT288?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='resr_salle_ajout.php'>
<td align=right><font class=T2><?php print LANGRESA8?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='resr_salle_supp.php'>
<td align=right><font class=T2><?php print LANGRESA10?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBT50?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='calendrier_reser_salle.php'>
<td align=right><font class=T2><?php  print LANGRESA51?> : </font></td>
<td align=left>
<table><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA57?>","rien"); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagic("<?php print "Listing" ?>","listing_resa.php?id=salle","_self","",""); //text,nomInput</script>
</td><td><script language=JavaScript>buttonMagic("<?php print "E.D.T." ?>","edt_visu.php?equip","_blank","",""); //text,nomInput</script>
</td></tr></table>
</td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='resr_salle_confirmer.php'>
<td align=right><font class=T2><?php  print LANGRESA56?> : </font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA58?>","rien"); //text,nomInput</script>
<?php 
if (count($demresaEquip) > 0) {
	print "<img src='image/commun/important.png' id='imp2' />";
}
?>
</td>
</form>
</tr>
</table>
<br><br>
<!-- // fin  -->
</td></tr></table>
<!-- --------------------------------------------------------- -->
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGRESA11?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center width=95%>
<tr>
<form action='resr_equip.php'>
<td align=right><font class=T2><?php print LANGRESA12?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA14?>","rien"); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagic("<?php print "Réserver via E.D.T." ?>","edt_visu.php?equip","_blank","",""); //text,nomInput</script>
</td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='resr_salle.php'>
<td align=right width=50%><font class=T2><?php print LANGRESA13?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGRESA14?>","rien"); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagic("<?php print LANGMESS244 ?>","edt_visu.php?equip","_blank","",""); //text,nomInput</script>

</td>
</tr>
</form>
</table>
<br><br>
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
	    <script>

$(document).ready(function() { repeat(); });
function repeat() {
<?php  if (count($demresaSalle) > 0) { ?>
	$('img#imp1').hide("slow");
	$('img#imp1').show("slow");
<?php } ?>
<?php if (count($demresaEquip) > 0) { ?>
	$('img#imp2').hide("slow");
	$('img#imp2').show("slow");
<?php } ?>
	setTimeout("repeat()","3000");
}
</script>

</BODY></HTML>
