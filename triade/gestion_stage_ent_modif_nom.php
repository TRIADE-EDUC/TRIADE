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
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_entreprise.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Modification d'entreprise</font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br><br>

<script language="JavaScript">
var etat=0;
function bul(form,identr) {
	if (etat == 0) {
		form.liste.value="Info -";
		AffBulle3('Information Entreprise','./image/commun/info.jpg',"<?php print $infoEntreprise?>");
		searchEntreprise(identr);
		etat=1;
	}else{
		etat=0;
		form.liste.value="Info +";
		HideBulle();
	}
}
</script>

<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("3");


$recherche=$_POST["recherche"];
print "<font class='T2'><ul>";
print "<u>Recherche </u> : <b> $recherche </b><br><br><br>";
$data=recherche_entreprise_nom($recherche);
//id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus
	if (count($data) > 0 ) {

		for($i=0;$i<count($data);$i++) {
?>
			<table bgcolor="#FFFFFF" border=1 bordercolor="#000000" width=80% >
			<tr><td id='bordure'>
			<font class=T2>Société : <font color=red><?php print $data[$i][1] ?></font></font><br>
			<font class=T2>Activité principale :  </font><?php print  $data[$i][7] ?></font><br>
			<font class=T2>Ville : <?php print $data[$i][5] ?>  <?php print $data[$i][4] ?></font>  <br>
			<form>
			<div align=right>
			<input type=button onclick="open('gestion_stage_ent_modif3.php?id=<?php print $data[$i][0] ?>','_parent','')" value="<?php print LANGPER30?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">&nbsp;&nbsp;
			<input type=button onclick="bul(this.form,'<?php print $data[$i][0]?>')" name="liste" class="BUTTON"  value="Info +"  />&nbsp;&nbsp;&nbsp;</div></form>
			</td></tr></table><br><br>
<?php	
		}	 
	}else {
		 print "<font class=T2>aucune entreprise pour ce nom.</font><br><br>";
	}
print "<br>";
print "</font>[<a href='gestion_stage_ent_modif.php'>autre recherche</a>]<br><br> ";
print "</ul>";
?>

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
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
 
