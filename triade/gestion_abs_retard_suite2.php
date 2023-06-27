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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS25?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR><center><font class=T2><B> <?php print LANGABS28?></B></font></center><BR><BR>
<table width="100%" align="center" border=0 ><tr><td align="center"  >
<form method="post" action="gestion_abs_retard.php">
<script language=JavaScript>buttonMagicSubmit("Autres absences","rien");</script>
</form>
</td><td align="center" >
<form method="post" action="gestion_abs_retard_suite.php">
<input type="hidden" name="<?php print $_POST["type"] ?>" value="<?php print $_POST["typevaleur"] ?>" />
<?php 
if ($_POST["type"] == "saisie_classe") { $nomchamps="class"; }
if ($_POST["type"] == "saisie_groupe") { $nomchamps="grp"; }
if ($_POST["type"] == "saisie_etude") { $nomchamps="etude"; }
?>
<script language=JavaScript>buttonMagicSubmit("Autres absences pour la même classe","<?php print $nomchamps ?>");</script>
</form>
</td></tr></table>
<br><br><br>
<?php
$id=$_POST["saisie_id"];

if ($_POST["retard_aucun"] == "oui") {
	aucun_retard($_POST["nomclasse"],$_POST["nommatiere"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["datedepart"]);
	history_cmd($_SESSION["nom"],"RTD/ABS","enr. via prof");
}else{

	for ($i=0;$i<$id;$i++) {
		$motif="saisie_".$i;
		$heure="saisie_heure";
		$duree="saisie_duree_".$i;
		$duree1="saisie_duree1_".$i;
		$pers="saisie_pers_".$i;
		$raison="saisie_motif_".$i;
	/*
		print "<BR>a)";
		print $_POST[$motif];
		print "<BR>b)";
		print $_POST[$heure];
		print "<BR>c)";
		print $_POST[$duree];
		print "<BR>d)";
		print $_POST[$pers];
		print "<BR>e)";
		print "Par ".$_SESSION["nom"];
		print "<BR>f)";
		print $_POST["idmatiere"];
		print "<BR>g)";
		print $_POST["idprof"];
		print "<BR>h)";
		print $_POST[$raison];
		print "<BR>i)";
		print $_POST[$duree1];
		print "<hr>";
	 */




		$etape=$_POST[$duree];
		$idmatiere=$_POST["idmatiere"];

		if (ISMAPP == 1) {
			$idmatiere=chercheIdMatiere($idmatiere);
		}

		$justifier="saisie_justifie_$i";

		if ($_POST[$justifier] != 1) {
			$justifier=0;
		}else{
			$justifier=1;
		}
		
		$heure=$_POST[$heure];
		list($horaireLibelle,$heure,$horaireFin)=preg_split('/#/',$heure);
		$creneaux="$horaireLibelle#".timeForm($heure)."#".timeForm($horaireFin);
		$refRattrapage="";
		if ($_POST[$motif] == "retard" ) {

			if ($idmatiere == "") { $idmatiere="-1"; }
			$cr=create_retard($heure,$_POST[$duree],$_POST["datedepart"],$_POST[$pers],dateDMY2(),$_SESSION["nom"],$_POST[$raison],$idmatiere,$justifier,$_POST["idprof"],$creneaux);
	        	if($cr == 1){
				history_cmd($_SESSION["nom"],"RTD","enr. via Vie Scolaire");
		        }else {
		       		print "&nbsp;&nbsp;- ".LANGacce1." ".recherche_eleve_nom($_POST[$pers])." ".recherche_eleve_prenom($_POST[$pers])." ".LANGABS55.".<br>";
	        	}
		}

		if ($_POST[$motif] == "absent" ) {
			$duree=$_POST[$duree];
			if ($_POST[$duree] == "autre") {
				$duree=$_POST[$duree1];
			}
			if ($etape == "heure") {
				$heure=strtoupper($_POST[$duree1]);
				$heure=preg_replace('/H/','.',$heure);
			}
			$cr=create_absent($duree,$_POST["datedepart"],$_POST[$pers],dateDMY2(),$_SESSION["nom"],$_POST[$raison],$idmatiere,$justifier,$heure,$_POST["idprof"],$creneaux,$refRattrapage);
	        	if($cr == 1){
				history_cmd($_SESSION["nom"],"ABS","enr. via Vie Scolaire");
		        }else {
		                print "&nbsp;&nbsp;- ".LANGacce1." ".recherche_eleve_nom($_POST[$pers])." ".recherche_eleve_prenom($_POST[$pers])." ".LANGABS54.".<br>";
	        	}
		}
	}
}
Pgclose();
?>

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
