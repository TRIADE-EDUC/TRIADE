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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
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
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Statistiques absences et retards" ?>  / <?php print LANGBULL3 ?> : <font id='color2'><?php print $_COOKIE["anneeScolaire"] ?></font></font></b></td></tr>
<tr id='cadreCentral0' ><td valign='top'>
<!-- // fin  -->
<br>
<ul>
<table border='1' style="border-collapse: collapse;" >
<tr>
<td bgcolor='yellow'>&nbsp;Enseignant&nbsp;</td>
<td bgcolor='yellow'>&nbsp;Nbr d'absences&nbsp;</td>
<td bgcolor='yellow'>&nbsp;Nbr de retard&nbsp;</td>
</tr>
<?php
print "<tr bgcolor='#FFFFFF' >";
print "<td>&nbsp;Vie Scolaire </td>";
print "<td align='center'>".nbabstotalEnseignant('0')."</td>";
print "<td align='center'>".nbretardtotalEnseignant('0')."</td>";
print "</tr>";
$data=affPersActif('ENS'); //pers_id, civ, nom, prenom, identifiant, offline, email
for ($i=0;$i<count($data);$i++){
	$nbabstotalEnseignant=nbabstotalEnseignant($data[$i][0]);
	$nbretardtotalEnseignant=nbretardtotalEnseignant($data[$i][0]);
	if (($nbabstotalEnseignant == 0) && ($nbretardtotalEnseignant == 0)) continue;
	print "<tr bgcolor='#FFFFFF' >";
	print "<td>&nbsp;".$data[$i][2]." ".$data[$i][3]."&nbsp;</td>";
	print "<td align='center'>&nbsp;$nbabstotalEnseignant&nbsp;</td>";
	print "<td align='center'>&nbsp;$nbretardtotalEnseignant&nbsp;</td>";
	print "</tr>";
}
?>
</table>
</ul>

<table align='center'><tr><td><script>buttonMagicRetour('gestion_abs_statistique.php','_self')</script></td></tr></table>
<br><br>
<!-- // fin  -->
</td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      print "</SCRIPT>";
      top_d();
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
      print "</SCRIPT>";
    endif ;

// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
