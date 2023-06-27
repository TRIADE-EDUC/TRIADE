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
<script language="JavaScript" src="./librairie_js/lib_absrtd.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
$taille="width='100%' height='100%'"; 
?>
<?php if ($_POST["visu"] != "all") { ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<?php
	$taille="width='100%'"; 
} ?>
<table border="0" cellpadding="3" cellspacing="1" <?php print $taille ?> bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS35 ?> <?php print $_POST["datecal"] ?> </font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php
// affichage de la liste d'élèves trouvées
$user=$_SESSION["nom"];
$nb_pers=$_POST["nb_pers"];

for($i=0;$i<$nb_pers;$i++)  {
	$id_i=$_POST["id_i"][$i];
	$refRattrapage="idrattrapage$id_i";
	$refRattrapage=$_POST[$refRattrapage];
	suppRattrappage($refRattrapage);
}


for($i=0;$i<$nb_pers;$i++)  {
	$id_i=$_POST["id_i"][$i];

	$rattra_heure_1="";
	$rattra_date_1="";
	$rattra_duree_1="";
	$rattra_heure_2="";
	$rattra_date_2="";
	$rattra_duree_2="";
	$rattra_heure_3="";
	$rattra_date_3="";
	$rattra_duree_3="";

	$type="saisie_$id_i";
	$duree="saisie_duree_$id_i";
	$motif="saisie_motif_$id_i";
	$heure="saisie_heure_$id_i";
	$heure2="saisie_heure_2$id_i";
	$id_eleve="saisie_pers_$id_i";
	$justifier="saisie_justifier_$id_i";
	$heurederetard="heurederetard_$id_i";
	$idmatiere="matiere_$id_i";
	$time="time_$id_i";

	$rattra_heure_1="rattra_heure_1$id_i";
	$rattra_date_1="rattra_date_1$id_i";
	$rattra_duree_1="rattra_duree_1$id_i";
	$rattra_heure_2="rattra_heure_2$id_i";
	$rattra_date_2="rattra_date_2$id_i";
	$rattra_duree_2="rattra_duree_2$id_i";
	$rattra_heure_3="rattra_heure_3$id_i";
	$rattra_date_3="rattra_date_3$id_i";
	$rattra_duree_3="rattra_duree_3$id_i";

	$refRattrapage="idrattrapage$id_i";

	$rattra_heure_1=$_POST[$rattra_heure_1];
	$rattra_date_1=$_POST[$rattra_date_1];
	$rattra_duree_1=$_POST[$rattra_duree_1];
	$rattra_heure_2=$_POST[$rattra_heure_2];
	$rattra_date_2=$_POST[$rattra_date_2];
	$rattra_duree_2=$_POST[$rattra_duree_2];
	$rattra_heure_3=$_POST[$rattra_heure_3];
	$rattra_date_3=$_POST[$rattra_date_3];
	$rattra_duree_3=$_POST[$rattra_duree_3];
	$departideleve=$_POST[$id_eleve];

	$refRattrapage=$_POST[$refRattrapage];

	$type=$_POST[$type];
	$duree=$_POST[$duree];
	$motif=$_POST[$motif];
	$heure=$_POST[$heure];
	$heure2=$_POST[$heure2];
	$justifier=$_POST[$justifier];
	$datecal=dateFormBase($_POST["datecal"]);


	$nomeleve=recherche_eleve($departideleve);

	$base_depart=$_POST["base_depart"][$i];
	$departdate=$_POST["departdate"][$i];
	$departdatesaisie=$_POST["departdatesaisie"][$i];
	$departheurertd=$_POST[$heurederetard];
	$idmatiere=$_POST[$idmatiere];
	$time=$_POST[$time];
	$heuredoriginret=$_POST["departheurertd"][$i];
	$heuredoriginsaisie=$_POST["heuredatesaisie"][$i];
	$date_de_saisie=$departdatesaisie;
	$heuredabsence=$_POST["heuredabsence"][$i];

	$saisie_date_ret_origine=$departdate;

/*
	print "$type - $base_depart<br>";
	print "$heure<br>";
	print "$departdate<br>";
	print "$departdatesaisie<br>";
	print "$heuredoriginret<br>";
*/
	if ($type == "100") {
		supp_absretard($base_depart,$departideleve,$heure,$departdate,$time,$idmatiere);
		continue;
	}
	if (($motif == "inconnu" )  || ($motif == "") || ($duree == "0" )) {
		continue;
	}else{
		if ( ($rattra_date_1 != "") || ($rattra_date_2 != "") || ($rattra_date_3 != "")) {
			if ($refRattrapage == "") $refRattrapage="$departideleve#ref#".rand(0000,9999);
			if ($rattra_date_1 != "") enrRattrappage($refRattrapage,$rattra_date_1,$rattra_heure_1,$rattra_duree_1);
			if ($rattra_date_2 != "") enrRattrappage($refRattrapage,$rattra_date_2,$rattra_heure_2,$rattra_duree_2);
			if ($rattra_date_3 != "") enrRattrappage($refRattrapage,$rattra_date_3,$rattra_heure_3,$rattra_duree_3);
		}	
		if ($type == "absent") {
			if ($base_depart == "retard") {
	                	//$duree,$date,$saisie_pers,$date_saisie,$user,$motif
	                	$cr=create_absent($duree,$heure,$departideleve,$date_de_saisie,$user,$motif,$idmatiere,$justifier,$heure2,$refRattrapage);
				if ($cr) {
					history_cmd($_SESSION["nom"],"Abs/rtd",$nomeleve);
	                 		$departheurertd=preg_replace('/h/',":",$departheurertd);
					suppression_retard($departideleve,$departheurertd,$departdate);
				}
			}else {
				if (preg_match('/\//',$heure)) { $heure=dateFormBase($heure); }
				history_cmd($_SESSION["nom"],"Abs/rtd",$nomeleve);
				modif_absence($departideleve,$heure,$date_de_saisie,$user,$motif,$duree,$time,$idmatiere,$justifier,$heuredoriginsaisie,$saisie_date_ret_origine,$heuredabsence,$refRattrapage);
			}
		}
		if ($type == "retard") {
			if ($base_depart == "absent") {
	            $departheurertd=preg_replace('/h/',":",$departheurertd);
			 	$cr=create_retard2($departheurertd,$duree,$heure,$departideleve,$date_de_saisie,$user,$motif,'',$justifier,$datecal,$refRattrapage);
				if ($cr) {
					history_cmd($_SESSION["nom"],"Abs/rtd",$nomeleve);
						suppression_absence($departideleve,$departdate,$time,$idmatiere);
				}
			 }else {
				history_cmd($_SESSION["nom"],"Abs/rtd",$nomeleve);
				modif_retard($departideleve,$heure,$departdate,$duree,$motif,$date_de_saisie,$user,$justifier,$heuredoriginret,$heuredoriginsaisie,$refRattrapage);
			 }
		}
	}
}
?>
<br>
<br>
<?php if ($_POST["visu"] != "all") { ?>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGGRP61 ?>","gestion_abs_retard_du_jour_misaj.php?date=<?php print $_POST["date_ref"] ?>&filtre=<?php print $_POST["filtre"] ?>","_parent","",""); //text,nomInput</script>
</td></tr></table>
<?php 
}else{
?>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicFermeture()</script>
</td></tr></table>
<?php } ?>

<br><br>
<br>
     <!-- // fin  -->
     </td></tr></table>
<?php if ($_POST["visu"] != "all") { ?>
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
}
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
