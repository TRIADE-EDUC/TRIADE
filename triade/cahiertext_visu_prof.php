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
include_once("./librairie_php/lib_error.php");
include("./common/config.inc.php"); // futur : auto_prepend_file
include("./librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

if (isset($_POST["saisie_idprof"])) {
	$idprof=$_POST["saisie_idprof"];
	$inputprof="<input type=hidden name='saisie_idprof' value='$idprof' />";
	$getprof="&saisie_idprof=$idprof";
}

$date=dateDMY();
if (isset($_GET["iddate"])) {
	$date=dateForm($_GET["iddate"]);
	if (isset($_GET["saisie_idprof"])) { 
		$idprof=$_GET["saisie_idprof"]; 
		$getprof="&saisie_idprof=$idprof";  
		$inputprof="<input type='hidden' name='saisie_idprof' value='$idprof' />";
	}
}
if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
}

$nomprof=recherche_personne_nom($idprof,'ENS');
$prenomprof=recherche_personne_prenom($idprof,'ENS');
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print LANGPROF37 ?>  </b><font id="color2"><?php print strtoupper($nomprof)." ".ucwords($prenomprof) ?></font></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<table width=100% border=0>
<ul>
<tr><td colspan=2>
<form method=post name=formulaire>
<table border=0>
<tr><td>
<?php print "Le" ?> <input type=text value="<?php print $date ?>" name="saisie_date" size="10" readonly class="bouton2">
<?php
include_once("librairie_php/calendar.php");
calendar('id1','document.formulaire.saisie_date',$_SESSION["langue"],"0");
?>

</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","create"); //text,nomInput</script>
</td></tr></table>
<?php print $inputprof ?>
</form>
</ul>

<?php
print "<table border=1 width='97%'   align='center' >";


print "<tr><td colspan=2 id='bordure'  ><img src='image/commun/on1.gif' align=center width=8 height=8> <b>Devoir à faire :</b><br><br></td></tr>";
$idprof=verif_si_suppleant($idprof);
$data=affdevoirScolaireProf($idprof,$date,"date_devoir"); 
// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, fichier,idprof,tempsestimedevoir
for($i=0;$i<count($data);$i++) {
	$number=$data[$i][8];
	$tempsestime=$data[$i][13];
	if ((trim($tempsestime) != "") && ($tempsestime != "00:00:00"))  {
		$tempsestime="<font class='T1'>Temps de travail estimé à ".timeForm($tempsestime)."</font>";
	}
	$lienFichier="";
	if (trim($number)  != "" ) {
		$fichier=$data[$i][9];	
		$lienFichier="<br><img src='image/stockage/defaut.gif' align='center'> Fichier : <a href='telecharger.php?fichier=data/DevoirScolaire/${number}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,20)."</a>";
	}

	print "<TR>";
        print "<td width=40% valign=top bgcolor='#FFFFFF' ><font color=blue>&nbsp;".ucfirst(chercheMatiereNom($data[$i][1]))."</font> &nbsp;";
        print "<i>".LANGPROFK." ".dateForm($data[$i][2]);
	print LANGTE13." ".timeForm($data[$i][3])."</i>";
	print $tempsestime;
	print "&nbsp;".$data[$i][5];
	print "$lienFichier";
	print "</td></tr>\n";
	print "<tr><td id='bordure' ><hr></td></tr>";
}
?>
</table>
<br><br>
<?php
$dateS=datesuivante($date);
$dateP=dateprecedent($date);
?>
<table border=0 width=100% align=center >
<tr><td align=left>
&nbsp;&nbsp;<input type=button value="<-- <?php print LANGPROFR ?>"  class="bouton2" onclick="open('cahiertext_visu_prof.php?iddate=<?php print $dateP.$getprof ?>','_parent','')" >
</td>
<td align=center>
<input type=button value="<?php print LANGPROF34 ?>"  class="bouton2" onclick="open('cahiertext_visu_prof_global.php?iddate=<?php print dateFormBase($date).$getprof ?>','devoir','width=1000,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')" >
</td>

<td align=right>
&nbsp;&nbsp;
<input type=button value="<?php print LANGPROFQ ?> --> "  class="bouton2" onclick="open('cahiertext_visu_prof.php?iddate=<?php print $dateS.$getprof ?>','_parent','')" >
</td></tr>
</table>
     </td></tr></table>
     <!-- // fin  -->
     </td></tr></table>
     <?php
     // Test du membre pour savoir quel fichier JS je dois executer
     if (($_SESSION['membre'] == "menuadmin") || ($_SESSION['membre'] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
       print "</SCRIPT>";
     else :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
       print "</SCRIPT>";
       top_d();
	   print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
       print "</SCRIPT>";
     endif ;
     ?>
<?php include_once("./librairie_php/finbody.php"); ?>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
