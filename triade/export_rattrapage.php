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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des rattrapages" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br><br>
     <!-- // fin  -->
<form method=post name=formulaire  action="export_rattrapage.php">
<table width="100%" border="0" align="center">
<tr>
<td align="right"><font class="T2"><?php print LANGDISC47 ?> :</font></td>
<td colspan="2"   align="left"><input type="text"  name="saisie_date_debut" TYPE="text" size=13  class=bouton2 value="<?php print $_POST["saisie_date_debut"] ?>" onKeyPress="onlyChar(event)" >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0");
?>
</td>
</tr>
<tr>
<td  align="right"><br><font class="T2"><?php print LANGDISC48 ?> : </font></td>
<td colspan="2"  align="left"><br><input type="text"  name="saisie_date_fin" TYPE="text" size=13 class=bouton2 value="<?php print $_POST["saisie_date_fin"] ?>" onKeyPress="onlyChar(event)" >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0");
?>
</td>
</tr>
</table>
<br>
<table border=0 align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print "Exportation" ?>","export",""); //text,nomInput</script><script language=JavaScript>buttonMagicRetour("gestion_abs_retard.php","_parent")</script>&nbsp;&nbsp;</td></tr></table>
</form>

<?php
if (isset($_POST["export"])) {
	print "<br><br><hr>";
	print "<br>";
	$dateDebut=$_POST["saisie_date_debut"];
	$dateFin=$_POST["saisie_date_fin"];
	// si valeur "tous"

	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

	$fichier="./data/fichier_ASCII/export_rattrapage".$_SESSION["id_pers"].".xls";
	@unlink($fichier);

	$workbook = &new writeexcel_workbook($fichier);

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
	$intituleeleve=ucwords(INTITULEELEVE);

	$worksheet1->write(0, 0, "Classe", $header);
	$worksheet1->write(0, 1, "$intituleeleve", $header);
	$worksheet1->write(0, 2, "Absent le", $header);
	$worksheet1->write(0, 3, "Durée", $header);
	$worksheet1->write(0, 4, "Motif", $header);
	$worksheet1->write(0, 5, "Rattrapage le Date ", $header);
	$worksheet1->write(0, 6, "Rattrapage le Heure ", $header);
	$worksheet1->write(0, 7, "Rattrapage Durée ", $header);
	$worksheet1->write(0, 8, "Rattrapage Effectué ", $header);
	$worksheet1->write(0, 9, "Rattrapage le Date ", $header);
	$worksheet1->write(0, 10, "Rattrapage le Heure ", $header);
	$worksheet1->write(0, 11, "Rattrapage Durée ", $header);
	$worksheet1->write(0, 12, "Rattrapage Effectué ", $header);
	$worksheet1->write(0, 13, "Rattrapage le Date ", $header);
	$worksheet1->write(0, 14, "Rattrapage le Heure ", $header);
	$worksheet1->write(0, 15, "Rattrapage Durée ", $header);
	$worksheet1->write(0, 16, "Rattrapage Effectué ", $header);

	$data=recupRattrapage(dateFormBase($dateDebut),dateFormBase($dateFin));
	//  id,date,heure_depart,duree,ref_id_absrtd,valider 
	//
	for($i=0;$i<count($data);$i++) {
		$id=$data[$i][0];
		$date=dateForm($data[$i][1]);
		$heure_depart=dateForm($data[$i][2]);
		$duree=timeForm($data[$i][3]);
		$ref_id_absrtd=$data[$i][4];
		$valider=$data[$i][5]; 
		$tab[$ref_id_absrtd][$id]="$date#$heure_depart#$duree#$valider";
	}
	
	//print_r($tab);
	$A=1;
	foreach($tab as $key=>$value) {
		$ref_id_absrtd=$key;
		$info=recupInfoRattrapageAbs($ref_id_absrtd);
		// elev_id, date_ab, duree_ab, motif, id_matiere, justifier, creneaux, idrattrapage, duree_heure
		$ideleve=$info[0][0];
		$absle=dateForm($info[0][1]);
		$duree=$info[0][2];
		$motif=$info[0][3];
		$idmatiere=$info[0][4];
		$justifier=$info[0][5];
		$creneaux=$info[0][6];

		if ($duree == "-1") {
			$duree=$info[0][8]."h";

		}elseif((preg_match('/h/',$duree)) || (preg_match('/mn/',$duree))) {
			// rien
			
		}else{
			
			$duree="${duree} j";
		} 	

		$classe=chercheClasse_nom(chercheClasseEleve($ideleve));
		$intituleeleve=rechercheEleveNomPrenom($ideleve);

		$worksheet1->write($A, 0, "$classe", $center);
		$worksheet1->write($A, 1, "$intituleeleve", $center);
		$worksheet1->write($A, 2, "$absle", $center);
		$worksheet1->write($A, 3, "$duree", $center);
		$worksheet1->write($A, 4, "$motif", $center);

		$B=5;
		foreach($value as $key2=>$value2) {
			
			list($date,$heure_depart,$duree,$valider)=preg_split('/#/',$value2);
			// print "$date,$heure_depart,$duree,$valider,$ref_id_absrtd<br>";
			$date=dateForm($date);
			$heure_depart=timeForm($heure_depart);
			$duree=timeForm($duree);

			
			$worksheet1->write($A, $B, "$date", $center);
			$B++;
			$worksheet1->write($A, $B, "$heure_depart", $center);
			$B++;
			$worksheet1->write($A, $B, "$duree", $center);
			$B++;
			$valider = ($valider == 1) ? "oui" : "non";
			$worksheet1->write($A, $B, "$valider", $center);
			$B++;
		}
		$A++;
	}

	$workbook->close();


	print "<center>";
	print "<input type=button onclick=\"open('visu_document.php?fichier=$fichier','_blank','');\" value=\"Récupération de l'exportation\"  class='bouton2'>";
	print "<br /></center>";
}


?>


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
