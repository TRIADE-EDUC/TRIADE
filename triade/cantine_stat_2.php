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
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Relevé complet des passages à la cantine" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
$cnx=cnx();
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
?>

<!-- // fin  -->
<br><br>
<?php

	$dateDebut=$_POST["saisie_date_debut"];
	$dateFin=$_POST["saisie_date_fin"];
	


	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";
		
	if (!is_dir("./data/cantine/")) {
		mkdir("./data/cantine/");
		htaccess("./data/cantine/");
	}

	$fichier="./data/cantine/export_cantine_".$_SESSION["id_pers"].".xls";
	@unlink($fichier);
//	$fname = tempnam("/tmp", "$fichier");
	
	$workbook = new writeexcel_workbook($fichier);

	$worksheet1 = $workbook->addworksheet("Listing");
//	$worksheet1->freeze_panes(1, 0); # 0 row
	
	$header =& $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');

	$header1 =& $workbook->addformat();
	$header1->set_color('black');
	$header1->set_align('vcenter');
	$header1->set_pattern();
	$header1->set_fg_color('yellow');


	$center =& $workbook->addformat();
	$center->set_align('left');

	#
	# Sheet 1
	#

//	$worksheet1->set_column('A:I', 16);
//	$worksheet1->set_row(0, 20);
	$worksheet1->set_selection('A0');
	
	$titre=$colonne[$i];
	$worksheet1->write(0, 0, utf8_decode("Période du $dateDebut au $dateFin "), $header1);
	$worksheet1->write(1, 0, "Plateau", $header);
	$worksheet1->write(1, 1, "Prix du Plateau", $header);
	$worksheet1->write(1, 2, "Nb de Plateau", $header);
	$worksheet1->write(1, 3, "Total", $header);


	$a=2;
	$datalisting=listingCantine($dateDebut,$dateFin);  // sum(prix),plateau,count(plateau)u
	for ($i=0;$i<count($datalisting);$i++) {
		if (trim($datalisting[$i][1]) == "") continue;
		$donnee=urldecode($datalisting[$i][1]);
		$donnee=utf8_decode($donnee);
		$worksheet1->write($a, 0, "$donnee", $center);
		$donnee=preg_replace("/^-/",'',$datalisting[$i][3]);
		$donnee=utf8_decode($donnee);
		$worksheet1->write($a, 1, "$donnee", $center);
		$donnee=$datalisting[$i][2];
		$donnee=utf8_decode($donnee);
		$worksheet1->write($a, 2, "$donnee", $center);
		$donnee=preg_replace("/^-/",'',$datalisting[$i][0]);
		$donnee=utf8_decode($donnee);
		$worksheet1->write($a, 3, "$donnee", $center);
		$a++;
	}

	$workbook->close();


?>
</font>
</form>
<center>
<table><tr><td><input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2"></td>
<td><script language=JavaScript>buttonMagicRetour('cantine.php','_self')</script></td></tr></table>
<br /></center>
<br><br>



<?php }else{ ?>
<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<?php } ?>

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
