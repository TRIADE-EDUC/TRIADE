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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<FORM method=POST action="">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Module d'importation de fichier" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<BR>
<BR>
<?php
	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

	$fichier="./data/fichier_ASCII/export_entreprise.xls";
	@unlink($fichier);
//	$fname = tempnam("/tmp", "$fichier");
	
	$workbook = &new writeexcel_workbook($fichier);

	$worksheet1 =& $workbook->addworksheet('Listing Entreprrise');
//	$worksheet1->freeze_panes(1, 0); # 0 row
	
	$header =& $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');

	$center =& $workbook->addformat();
	$center->set_align('left');

	#
	# Sheet 1
	#

//	$worksheet1->set_column('A:I', 16);
//	$worksheet1->set_row(0, 20);
	$worksheet1->set_selection('A0');
	$j=0;
	$worksheet1->write(0, $j++, "Entreprise", $header);
	$worksheet1->write(0, $j++, "adresse", $header);
	$worksheet1->write(0, $j++, "code postal", $header);
	$worksheet1->write(0, $j++, "ville", $header);
	$worksheet1->write(0, $j++, "pays", $header);
	$worksheet1->write(0, $j++, "contact", $header);
	$worksheet1->write(0, $j++, "tel", $header);
	$worksheet1->write(0, $j++, "email", $header);

	$datalisting=listingEntreprise(); // nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent
	$ii=1;
	for ($i=0;$i<count($datalisting);$i++) {
		$j=0;
		$entreprise=$datalisting[$i][0];
		$adresse=$datalisting[$i][2];
		$code_postal=$datalisting[$i][3];
		$ville=$datalisting[$i][4];
		$pays=$datalisting[$i][13];
		$contact=$datalisting[$i][1];
		$tel=$datalisting[$i][7];
		$email=$datalisting[$i][9];

		$worksheet1->write($ii, $j++, "$entreprise", $center);
	        $worksheet1->write($ii, $j++, "$adresse", $center);
	        $worksheet1->write($ii, $j++, "$code_postal", $center);
	        $worksheet1->write($ii, $j++, "$ville", $center);
	        $worksheet1->write($ii, $j++, "$pays", $center);
	        $worksheet1->write($ii, $j++, "$contact", $center);
	        $worksheet1->write($ii, $j++, "$tel", $center);
	        $worksheet1->write($ii, $j++, "$email", $center);
		$ii++;
	}

	$workbook->close();

?>
<center>
&nbsp;&nbsp;<input type=button class="BUTTON" value='<?php print "retour" ?>' onclick="open('gestion_stage.php','_self','')" />
<input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2">
<br /></center>
<br>
<br>
<!-- // fin  -->
</td></tr></table> </form>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
</BODY></HTML>
