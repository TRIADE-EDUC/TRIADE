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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (DSTPROF == "oui") {
	validerequete("3");
}else{
	validerequete("2");
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCALEN10 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<BR>
<form method="post">
&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value="<?php print LANGBT25?>"  onclick="window.location.reload(true)"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php
$data=consult_demande_dst();
if (count($data)) {
	print "<input type=button value='Les demandes de D.S.T'  STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' onclick=\"open('demdst2.php','demdst','width=700,height=400,scrollbars=yes');\">";
}
?>
<br><br>
<font class="T2">&nbsp;&nbsp;<?php print LANGMESS345 ?> : </font>
<select name="sClasseGrp" size="1" onchange="this.form.submit()">
<?php
if (isset($_POST["sClasseGrp"])) {
	if ($_POST["sClasseGrp"] == 'tous') { $libelle="Toutes les classes"; }else{ $libelle=chercheClasse_nom($_POST["sClasseGrp"]); }
	print "<option value='".$_POST["sClasseGrp"]."' id='select0'>$libelle</option>";
}
?>
	<option value='tous' id='select0'><?php print LANGMESS147 ?></option>
<?php 
if ($_SESSION["membre"] == "menuprof") { 
	$data=affClasseAffectationProf($_SESSION["id_pers"]);
}else{
	$data=affClasseAffectationNonProf();
}
for($i=0;$i<count($data);$i++) { // m.libelle,a.code_classe
	print "<option value='".$data[$i][1]."' id='select1' >".$data[$i][0]."</option>";
}
?>

</select>
</form>

<?php 
$saisie_annee_choix=dateY(); 
?>
<?php include("./librairie_php/lib_calendrier_dst.php");?>
     <SCRIPT LANGUAGE="JavaScript"><!--
        // On passe en paramètre le numéro du mois et l'année
	annee(<?php print "$saisie_annee_choix"?>);
      //--></SCRIPT>
<?php
$saisie_annee_plus=dateY();
$saisie_annee_plus++;
$saisie_annee_moin=dateY();
$saisie_annee_moin--;
?>
&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value="<?php print LANGBT25?>"  onclick="window.location.reload(true)"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<BR><BR>
     <center> <A href="./calendrier_config_dst11.php?saisie_annee_choix=<?php print $saisie_annee_moin?>" ><?php print LANCALED1?></A> <------> <A href="./calendrier_config_dst11.php?saisie_annee_choix=<?php print $saisie_annee_plus?>" ><?php print LANCALED2 ?></A></center>

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
     <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
