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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
include_once('librairie_php/recupnoteperiode.php');
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print CUMUL01 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
     <!-- // fin  -->
<form method=post name=formulaire  action="gestion_abs_sconet2.php">
<?php
$tri=$_POST["saisie_trimestre"];
$idclasse=$_POST["saisie_classe"];
if ($tri == "trimestre1") { $tri="T1"; }
if ($tri == "trimestre2") { $tri="T2"; }
if ($tri == "trimestre3") { $tri="T3"; }

if (isset($_POST["create"])) {
	$nbrabs=$_POST["nbrabs"];
	$nbrabsnonjust=$_POST["nbrabsnonjust"];
	$nbrrtd=$_POST["nbrrtd"];
	$ideleve=$_POST["ideleve"];
	$i=0;
	foreach($ideleve as $key=>$value) {
		$nbrabsvalue=$nbrabs[$i];
		$nbrabsnonjustvalue=$nbrabsnonjust[$i];
		$nbrrtdvalue=$nbrrtd[$i];
		MiseAbsRtdSconet($value,$nbrabsvalue,$nbrabsnonjustvalue,$nbrrtdvalue,$tri);
		$i++;
	}
}



print "<table width='100%'>";
print "<tr>";
print "<td bgcolor='yellow' >Nom</td>";
print "<td bgcolor='yellow' >Prenom</td>";
print "<td bgcolor='yellow' >Nbr Abs.</td>";
print "<td bgcolor='yellow' >Nbr Rtd.</td>";
print "<td bgcolor='yellow' >Nbr Abs. non justifié</td>";
	
print "</tr>";

$eleveT=recupEleve($idclasse);
for($j=0;$j<count($eleveT);$j++) { 
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$idEleve=$eleveT[$j][4];
	$data=recupAbsRtdSconet($idEleve,$tri); // nb_abs,nb_abs_no_just,nb_rtd
	$nbabs=$data[0][0];
	$nbrabsnonjust=$data[0][1];
	$nbrrtd=$data[0][2];
	if (trim($nbabs) == "") $nbabs="0";
	if (trim($nbrabsnonjust) == "") $nbrabsnonjust="0";
	if (trim($nbrrtd) == "") $nbrrtd="0";

	print "<tr class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">";
	print "<td>$nomEleve</td>";
	print "<td>$prenomEleve</td>";
	print "<td><input type='text' name='nbrabs[]' size=5 value='$nbabs' /></td>";
	print "<td><input type='text' name='nbrabsnonjust[]' size=5 value='$nbrabsnonjust' /></td>";
	print "<td><input type='text' name='nbrrtd[]' size=5  value='$nbrrtd' /></td>";
	print "<input type='hidden' name='ideleve[]' value='$idEleve' />";
	print "</tr>";
}
print "</table>";
?>
<br>

<table border=0 align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","create",""); //text,nomInput</script></td></tr></table>

<input type=hidden name="saisie_trimestre" value='<?php print $tri ?>' />
<input type=hidden name="saisie_classe" value='<?php print $idclasse ?>' />
</form>
<br>
     <!-- // fin  -->
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
