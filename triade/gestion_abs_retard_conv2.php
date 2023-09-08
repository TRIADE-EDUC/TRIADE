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
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Convertir absence ou retard" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<?php

$id=$_POST["saisie_id_champ"];
$ideleve=$_POST["saisie_eleve_id"];
$conversion=$_POST["conversion"];

if ($conversion == "abs") {
	$action="gestion_abs_retard_conv3.php";
	
}else{
	$action="gestion_abs_retard_conv4.php";

}

$duree=$_POST["saisie_duree_$id"];
$modif=preg_replace('/"/',"'",$_POST["saisie_modif_$id"]);
$justifier=$_POST["saisie_justifier_$id"];
if ($justifier == 1) { $checked="checked='checked'"; }else{ $checked=""; }
$date_ret=dateForm($_POST["saisie_date_ret"]);
$heure_ret=$_POST["saisie_heure_ret"]; 

$nom=recherche_eleve_nom($ideleve);
$prenom=recherche_eleve_prenom($ideleve);
$i=0;
?>


<FORM name="formulaire_<?php print $i?>" method="post" action='<?php print $action ?>' >
<table border="1" bordercolor="#000000" width="100%" bgcolor="#FFFFFF">
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2"><?php print LANGNA1?> : </font><B><?php print ucwords($nom)?></b></td></tr>
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2"><?php print LANGNA2?>: </font><b><?php print ucwords($prenom)?></b></td>
</tr>
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2"><?php print LANGABS12?> : </font><input type=text name="saisie_motif_<?php print $i?>" value="<?php print $modif ?>" size=40 >
( <input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" <?php print $checked ?> > Justifié)
<br>
</td>
</tr>
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2">Matière : <?php print chercheMatiereNom($_POST["saisie_matiere"]) ?></font>
</td>
</tr>
</table>
<table border="1" bordercolor="#000000" width="100%" bgcolor="#FFFFFF">
<tr bordercolor="#FFFFFF">
<td align=center  bgcolor="#FFFFFF"  > Sera :
<?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i?>" onChange="absplanifier(<?php print $val?>)">
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<?php if ($conversion == "abs") { ?>
<option value="absent" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS?></option>
<?php }else{ ?>
<option value="retard" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGRTD?></option>
<?php } ?>
</select></td>
<td bgcolor="#FFFFFF" align=center> le
<input type=text size=12 name="saisie_date_<?php print $i?>" value='<?php print $date_ret ?>' onKeyPress="onlyChar(event)">
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1$i","document.formulaire_$i.saisie_date_$i",$_SESSION["langue"],"0");
?>
 à
 <input type=text size=12 name="saisie_heure_<?php print $i?>" value="<?php print $heure_ret ?>" onKeyPress="onlyChar2(event)"  >
</td>
<td  bgcolor="#FFFFFF" align=center>pendant
<select name="saisie_duree_<?php print $i?>" onChange="absplanifier2(<?php print $i?>)" >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
</select>
<input type="hidden" value="<?php print $i?>" name="saisie_id_champ" >
<input type="hidden" value="<?php print $_POST["origine_saisie"] ?>" name="saisie_pers" >
<input type="hidden" name="saisie_duree_retourner_<?php print $i?>" ></td>
</tr>
</table>
<BR>

<input type="hidden"  value="<?php print $duree?>" name="rtd_duree" >
<input type="hidden"  value="<?php print $modif?>" name="rtd_modif" >
<input type="hidden"  value="<?php print $justifier?>" name="rtd_justifier" >
<input type="hidden"  value="<?php print $date_ret?>" name="rtd_date_ret" >
<input type="hidden"  value="<?php print $heure_ret?>" name="rtd_heure_ret" >
<input type="hidden"  value="<?php print $ideleve?>" name="rtd_ideleve" >
<input type="hidden"  value="<?php print $_POST["saisie_date_ret"] ?>" name="abs_date_depart" >
<input type="hidden"  value="<?php print $_POST["saisie_matiere"] ?>" name="abs_idmatiere" >
<input type="hidden"  value="<?php print $_POST["saisie_time"] ?>" name="abs_time" >


<center><input type=submit  value="<?php print LANGABS27?> <?php print ucwords(trim($nom)) ?>" class="bouton2"></center><BR>
</form>
<BR><BR><BR>


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
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
