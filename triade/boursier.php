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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Boursier" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br>
     <!-- // fin  -->
<font class="T2"><?php print "Pourcentage de boursier " ?> :
<br><br>
<table width="100%" border="1" align="center">
<tr>
<td bgcolor='yellow' ><font class="T2"><?php print "Classe" ?></font></td>
<td bgcolor='yellow' width='5'><font class="T2">&nbsp;Nbr&nbsp;</font></td>
<td bgcolor='yellow' width='5'><font class="T2">&nbsp;Taux&nbsp;</font></td>
</tr>
<?php
$data=visu_affectation(); //code_classe,f.libelle
for($i=0;$i<count($data);$i++) {
	$nbEleve=nb_eleve($data[$i][0]);
	print "<tr bgcolor='#FFFFFF' >";
	print "<td>".$data[$i][1]."</td>";
	$val=number_format(tauxBoursier($data[$i][0],$nbEleve),'2','.','');
	$nbBoursier=nbBoursier($data[$i][0]);
	print "<td>&nbsp;".$nbBoursier." </td>";
	print "<td>&nbsp;".$val."% </td>";
	print "</tr>";
	if ($val > 0) $nbsomme++;	
	$somme+=$val;
	$nbBoursierTotal+=$nbBoursier;
}
	print "<tr bgcolor='#FFFFFF' >";
	print "<td align='right' ><b>Soit : </b></td>";
	$somme=$somme/$nbsomme;
	$val=number_format($somme,'2',',','');
	print "<td>&nbsp;".$nbBoursierTotal."</td>";
	print "<td><b>&nbsp;".$val."%</b> </td>";
	print "</tr>";
?>
</table>

<br>
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
