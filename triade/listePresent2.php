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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Liste des présents" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<!-- // fin  -->
<table border=1 width='100%' >
	<tr>
	<td bgcolor="yellow" width='20%' align='center'><?php print "&nbsp;Date&nbsp;" ?></td>
	<td bgcolor="yellow" >Nom - Prénom</td>
	<td bgcolor="yellow" width='30%' >&nbsp;<?php print LANGPER17 ?>&nbsp;</td>
	</tr>
<?php 
$dateDebut=$_POST["saisie_date_debut"];
$dateFin=$_POST["saisie_date_fin"];

$data=listingPresent($dateDebut,$dateFin); //id,ideleve,idpers,idmatiere,date,horaire,e.nom,e.prenom
for($i=0;$i<count($data);$i++) {
	$nomEleve=strtoupper($data[$i][6]);
	$prenomEleve=ucwords($data[$i][7]);
	$matiere=chercheMatiereNom($data[$i][3]);
	$horaire=recupInfoCreneau($data[$i][5]); //libelle,dep_h,fin_h
?>
	<tr   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td id='bordure' align='center' ><?php print dateForm($data[$i][4]) ?><br><?php print timeForm($horaire[0][1])."-".timeForm($horaire[0][2]) ?></td>
	<td id='bordure' valign=top >&nbsp;<?php print $nomEleve." ".$prenomEleve ?></td>
	<td id='bordure' valign=top title="<?php print $matiere ?>" >&nbsp;<?php print trunchaine($matiere,25) ?></td>
	</tr>
<?php
}
?>
</table>
<br><br>
<table border=0 align=center><tr><td>
<script language=JavaScript>buttonMagicRetour('listePresent.php','_parent') //text,nomInput</script></td></tr></table>
</form>
<br>
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
</BODY></HTML>
