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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
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
<?php
$id=$_POST["saisie_id"];
$date=dateDMY();
if ($_POST["retard_aucun"] == "oui") {
	aucun_retard($_POST["nomclasse"],$_POST["nommatiere"],$_SESSION["nom"],$_SESSION["prenom"],$date);
	history_cmd($_SESSION["nom"],"RTD/ABS","enr. via prof");
}else{
	list($horaireLibelle,$heure,$horaireFin)=preg_split('/#/',$_POST["saisie_heure"]);
	$idprof=$_POST["idprof"];
	$creneaux="$horaireLibelle#".timeForm($heure)."#".timeForm($horaireFin);
	
	$nbrtd=0;
	$nbabs=0;

	for ($i=0;$i<$id;$i++) {
		$motif="saisie_".$i;
		$duree="saisie_duree_".$i;
		$pers="saisie_pers_".$i;
		$raison="saisie_motif_".$i;
		$justifier=0;
		$justifier="saisie_justifier_".$i;

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
		print "<BR>";
	 */	
		$idmatiere=$_POST["idmatiere"];
		$justifier=$_POST[$justifier];
		if ($justifier != "1") { $justifier=0; }

		if ($_POST[$motif] == "retard" ) {
			if ($idmatiere == "") { $idmatiere="-1"; }
			$cr=create_retard($heure,$_POST[$duree],dateDMY(),$_POST[$pers],dateDMY2(),$_SESSION["nom"],$_POST[$raison],$idmatiere,$justifier,$idprof,$creneaux);
	        	if($cr == 1){
				history_cmd($_SESSION["nom"],"RTD","enr. via prof");
				$nbrtd++;
		        }else {
		       		print "&nbsp;&nbsp;- ".LANGacce1." ".recherche_eleve_nom($_POST[$pers])." ".recherche_eleve_prenom($_POST[$pers])." ".LANGABS55.".<br>";
	        	}
		}

		if ($_POST[$motif] == "absent" ) {
			$cr=create_absent($_POST[$duree],dateDMY(),$_POST[$pers],dateDMY2(),$_SESSION["nom"],$_POST[$raison],$idmatiere,$justifier,$heure,$idprof,$creneaux,'');
	        	if($cr == 1){
				history_cmd($_SESSION["nom"],"ABS","enr. via prof");
				$nbabs++;
		        }else {
		                print "&nbsp;&nbsp;- ".LANGacce1." ".recherche_eleve_nom($_POST[$pers])." ".recherche_eleve_prenom($_POST[$pers])." ".LANGABS54.".<br>";
	        	}
		}
	}
	enrAbsrtdHisto($_POST["nomclasse"],$_POST["nommatiere"],$_SESSION["nom"],$_SESSION["prenom"],$date,$nbabs,$nbrtd);
}
Pgclose();
?>

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
   </BODY></HTML>
