<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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

<?php

if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEDATE == "oui")) || ($_SESSION["membre"] == "menuadmin") || (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 1) )  { ?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE1?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center >
<tr>
<form action='gestion_stage_date_visu.php' method='post'>
<td align=right><font class="T2"><?php print LANGSTAGE2?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEDATE == "oui")) || ($_SESSION["membre"] == "menuadmin") ) {  ?>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_date_aj.php' method='post'>
<td align=right><font class="T2"><?php print LANGSTAGE5?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_date_modif.php' method='post'>
<td align=right><font class="T2"><?php print LANGSTAGE6?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_date_supp.php' method='post'>
<td align=right><font class="T2"><?php print LANGSTAGE7 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT50?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<?php } ?>

</table>
<br><br>
</td></tr></table>
<br><br>
<?php } ?>


<!-- // fin  -->
<!-- --------------------------------------------------------- -->

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE8?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center >
<tr>
<form action='gestion_stage_ent_visu.php'>
<td align=right><font class="T2"><?php print LANGSTAGE9 ?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>

<tr>
<form action='publipostagesociete.php'>
<td align=right><font class="T2"><?php print LANGTMESS513 ?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>


<?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEENT == "oui")) || ($_SESSION["membre"] == "menuadmin"))  { ?>

<tr>
<form action='gestion_stage_ent_ajout.php'>
<td align=right><font class="T2"><?php print LANGSTAGE10?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_ent_modif.php'>
<td align=right><font class="T2"><?php print LANGSTAGE11 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_ent_supp.php'>
<td align=right><font class="T2"><?php print LANGSTAGE12?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT50?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>

<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='base_de_donne_importation72.php?id=entreprisexls' >
<td align=right><font class="T2"><?php print LANGTMESS514 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagic("<?php print LANGAGENDA86 ?>","base_de_donne_importation72.php?id=entreprisexls","_self","",""); //text,nomInput</script></td>
</tr>
</form>

<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='base_de_donne_importation72.php?id=entreprisexls' >
<td align=right><font class="T2"><?php print LANGTMESS514." version Pigier" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagic("<?php print LANGAGENDA86 ?>","base_de_donne_importation92.php?id=entreprispigierexls","_self","",""); //text,nomInput</script></td>
</tr>
</form>


<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='export_entreprise.php' >
<td align=right><font class="T2"><?php print "Exportation des entreprises" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Exporter" ?>","rien"); //text,nomInput</script></td>
</tr>
</form>



<?php }  ?>


</table>
<br><br>
<!-- // fin  -->
</td></tr></table>
<!-- --------------------------------------------------------- -->
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE13?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center >
<tr>
<form action='gestion_stage_visu_eleve_liste.php'>
<td align=right><font class="T2"><?php print LANGSTAFE91 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
</form>
<tr>
<form action='gestion_stage_visu_eleve.php'>
<td align=right><font class="T2"><?php print LANGSTAGE14?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEETUDIANT == "oui")) || ($_SESSION["membre"] == "menuadmin"))  { ?>

<tr>
<form action='gestion_stage_affec_eleve.php'>
<td align=right><font class="T2"><?php print LANGSTAGE15?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE4?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='indemnitestage.php'>
<td align=right><font class="T2"><?php print LANGTMESS515 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>

<tr>
<form action='gestion_stage_modif_eleve.php'>
<td align=right><font class="T2"><?php print LANGSTAGE16?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_supp_eleve.php'>
<td align=right><font class="T2"><?php print LANGSTAGE17?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBT50 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td colspan=2><hr></td></tr>
<tr><td></td></tr>
<tr>
<td align=right><font class="T2"><?php print LANGSTAGE89 ?>:</font></td>
<td align=left><script language=JavaScript>buttonMagic("<?php print LANGPROFB3 ?>","gestion_stage_param_convention.php","conven_create","scrollbars=yes,width=730,height=750",""); //text,nomInput</script></td>
</tr>
<?php } ?>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_convention_eleve.php'>
<td align=right><font class="T2"><?php print LANGSTAGE90 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEETUDIANT == "oui")) || ($_SESSION["membre"] == "menuadmin"))  { ?>
<tr><td></td></tr>
<tr><td colspan=2><hr></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_demande_convention_dir.php'>
<td align=right><font class="T2"><?php print LANGTMESS516 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<?php } ?>
</table>

<br><br>
<!-- // fin  -->
</td></tr></table>





<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
