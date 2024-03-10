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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des données comptabilités élèves" ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<br />
<font class="T2">
<?php
include_once("./librairie_php/class.writeexcel_workbook.inc.php");
include_once("./librairie_php/class.writeexcel_worksheet.inc.php");

$anneescolairefiltre=$_POST["anneescolairefiltre"];

$fichier="./data/fichier_ASCII/export_compta_".$_SESSION["id_pers"].".xls";
@unlink($fichier);

$idclasse=$_POST["sClasseGrp"];
$nomclasse=chercheClasse_nom($idclasse);

$workbook = &new writeexcel_workbook($fichier);

$worksheet1 =& $workbook->addworksheet("Listing $nomclasse");
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


$worksheet1->write(0, 0, "Nom", $header);
$worksheet1->write(0, 1, "Prénom", $header);
$worksheet1->write(0, 2, "Montant à payer", $header);
$worksheet1->write(0, 3, "Mode de paiement", $header);
$worksheet1->write(0, 4, "Année", $header);


$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
$res=execSql($sql);
$datalisting=chargeMat($res);

for ($i=1;$i<count($datalisting);$i++) {
	$ideleve=$datalisting[$i][1];
	$nom=$datalisting[$i][2];
	$prenom=$datalisting[$i][3];		

	$montant="";

	$data=recupConfigVersementEleveEtClasse($ideleve,$anneescolairefiltre);
	for($j=0;$j<count($data);$j++) {
		$dateVersement=$data[$j][4];
		if ($dateVersement != "") { $dateVersement=dateForm($dateVersement); }
		$montant=number_format($data[$j][3],2,'.','');
		$modepaiement=$data[$j][5];
	
	
		$worksheet1->write($i, 0, "$nom", $center);
		$worksheet1->write($i, 1, "$prenom", $center);
		$worksheet1->write($i, 2, "$montant", $center);
		$worksheet1->write($i, 3, "$modedepaiement", $center);
		$worksheet1->write($i, 4, "$anneescolairefiltre", $center);
	}


}

	$workbook->close();


?>
</font>
</form>
<center>

<table><tr><td><input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2"></td>
<td><script>buttonMagicRetour('compta_listing.php','_self')</script></td></tr></table>
</center>

<br><br>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
