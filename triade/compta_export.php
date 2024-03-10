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
<?php
$filtre="non précisé";
if (isset($_POST["anneescolairefiltre"])) {
	$filtre=$_POST["anneescolairefiltre"];
	$anneeScolaire=$_POST["anneescolairefiltre"];
}
?>
<form method=post action="compta_export.php" >
<br><ul>
&nbsp;&nbsp;<font class=T2>Filtre : </font><select onChange='this.form.submit()' name='anneescolairefiltre' > <option ><?php print LANGCHOIX ?></option><?php filtreAnneeScolaireSelect($filtre) ?> </select><br><br>
</ul>
</form>
<br>

<font class="T2">
<?php
require_once "./librairie_php/class.writeexcel_workbook.inc.php";
require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

$fichier="./data/fichier_ASCII/export_compta_".$_SESSION["id_pers"].".xls";
@unlink($fichier);
	
$workbook = new writeexcel_workbook($fichier);

$worksheet1 =& $workbook->addworksheet('Listing');
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
$worksheet1->write(0, 2, "Classe", $header);
$worksheet1->write(0, 3, "Montant à payer", $header);
$worksheet1->write(0, 4, "Reste à payer", $header);
$worksheet1->write(0, 5, "Année", $header);

$datalisting=listingEleve(); 
/* elev_id,			0
 * nom, 			1
 * prenom,			2
 * classe,			3
 * lv1,				4
 * lv2,				5
 * option,			6
 * regime,			7
 * date_naissance,		8
 * lieu_naissance,		9
 * nationalite,			10
 * passwd,			11
 * passwd_eleve,		12
 * civ_1,			13
 * nomtuteur,			14
 * prenomtuteur,		15
 * adr1,			16
 * code_post_adr1,		17
 * commune_adr1,		18
 * tel_port_1,			19
 * civ_2,			20
 * nom_resp_2,			21
 * prenom_resp_2,		22
 * adr2,			23
 * code_post_adr2,		24
 * commune_adr2,		25
 * tel_port_2,			26
 * telephone,			27
 * profession_pere,		28
 * tel_prof_pere,		29
 * profession_mere,		30
 * tel_prof_mere,		31
 * nom_etablissement,		32
 * numero_etablissement,	33
 * code_postal_etablissement,	34
 * commune_etablissement,	35
 * numero_eleve,		36
 * photo,			37
 * email,			38
 * email_eleve,			39
 * email_resp_2,		40
 * class_ant,			41
 * annee_ant,			42
 * numero_gep,			43
 * valid_forward_mail_eleve,	44
 * valid_forward_mail_parent,	45
 * tel_eleve,			46
 * code_compta,			47
 * sexe 			48
 */
	for ($i=1;$i<count($datalisting);$i++) {
		$ideleve=$datalisting[$i][0];
		$donnee=$datalisting[$i][1];
		$worksheet1->write($i, 0, "$donnee", $center);
		$donnee=$datalisting[$i][2];		
		$worksheet1->write($i, 1, "$donnee", $center);
		$donnee=chercheClasse_nom($datalisting[$i][3]);		
		$worksheet1->write($i, 2, "$donnee", $center);

		$montant=0;
		$montantnonpayer=0;
		$idclasse=recupIdClasseEleve($ideleve,$anneeScolaire);
		$dataV=recupConfigVersement($idclasse,$filtre); //id,idclasse,libellevers,montantvers,datevers
		if ($dataV == "") { $dataV=array(); }
		$dataVE=recupConfigVersementEleve($ideleve,$filtre);
		if ($dataVE == "") { $dataVE=array(); }
		$dataV=array_merge($dataV,$dataVE);
		for($j=0;$j<count($dataV);$j++) {
			$nb++;
			$affiche=0;
			$id=$dataV[$j][0];
	
			if(verifcomptaExclu($id,$ideleve)) { continue; }
	
			$data=recupInfoVersement($ideleve,$id); // ideleve,idversement,montantvers,datevers,modepaiement
			$dateVersement=$data[0][3];
			$idvers=$data[0][1];
			if ($dateVersement != "") { $dateVersement=dateForm($dateVersement); }
			$montantVers=number_format($data[0][2],2,'.','');
			$modepaiement=nl2br($data[0][4]);
			$dateVersOr=$dataV[$j][4];
			$montantavers=$dataV[$j][3];
			$dateduJour=date("Ymd");
			$dateVersOr=preg_replace('/-/',"",$dateVersOr);
	
			if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
				$montantnonpayer+=$dataV[$j][3];
			}

			if (($montantVers < $dataV[$j][3] ) && ($dateduJour > $dateVersOr)  && ($montantVers != "0.00") ) {
				
				$montantnonpayer+=$dataV[$j][3]-$montantVers;		
			}
			$montant+=$dataV[$j][3];
		}

		
		$worksheet1->write($i, 3, "$montant", $center);
		$worksheet1->write($i, 4, "$montantnonpayer", $center);
		$worksheet1->write($i, 5, "$filtre", $center);
	}

	$workbook->close();


?>
</font>
</form>
<center>
<?php if ($filtre != "non précisé") { ?>
<table><tr><td><input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2"></td>
<td><script>buttonMagicRetour('compta_listing.php','_self')</script></td></tr></table>
<?php } ?>
<br /></center>
<br><br>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
