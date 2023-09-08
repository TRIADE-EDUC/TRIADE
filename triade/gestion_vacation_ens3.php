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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
nettoyage_EDT();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="125">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Vacation Enseignant" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td ><br>
<?php
if ($_SESSION["membre"] == "menuprof") {
	$idpers=$_SESSION["id_pers"];
}else{
	$idpers=$_POST["saisie_pers"];
}
$nomprenom=recherche_personne($idpers);
$idClasse="tous";
$selectcss="select0";
$nomClasse="L'intégralité";
if (isset($_POST["sClasseGrp"])) { 
	if ($_POST["sClasseGrp"] == "tous") {
		$selectcss="select0";
		$nomClasse="L'intégralité";
	}else{
		$idClasse=$_POST["sClasseGrp"]; 
		$option="<option value='$idClasse' id='select0' >".ucwords(chercheClasse_nom($idClasse))."</option>";
	        $nomClasse=ucwords(chercheClasse_nom($idClasse));
		$selectcss="select1";
	}
}

$sql="SELECT a.code_classe,trim(c.libelle),a.code_matiere, CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ), a.code_groupe,trim(g.libelle) FROM ${prefixe}affectations a, ${prefixe}matieres m, ${prefixe}classes c, ${prefixe}groupes g WHERE code_prof='$idpers' AND a.code_classe = c.code_class AND a.code_matiere = m.code_mat AND a.code_groupe = group_id ORDER BY c.libelle,m.libelle";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array()); // nécessaire pour compatibilité
// patch pour problème sous-matière à 0
for($i=0;$i<count($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=$tmp[0].' '.$tmp[1];
}

if (isset($_POST["create2"])) {
	$cr=updateFichePersonnel($idpers,$_POST["PE"],$_POST["lieuenseignant"],'ENS');
	if ($cr) { 
		alertJs(LANGDONENR);	
	}
}

if (isset($_POST["dateDebut"])) {
	$dateDebut=$_POST["dateDebut"];
	$dateFin=$_POST["dateFin"];
}else{
	$data1=aff_enr_parametrage("dateDebutVacat");
	$data2=aff_enr_parametrage("datFinVacat");
	$dateDebut=$data1[0][1];
	$dateFin=$data2[0][1];
	if ($dateDebut == "") {
		$dateDebut=date("d/m/Y");
		$dateFin=date("d/m/Y");
	}
}


?>
<form method="post" action="gestion_vacation_ens3.php" name="formulaireT" >
<font class="T2">&nbsp;&nbsp;Période : </font> 
<input type="text" name="dateDebut" value="<?php print $dateDebut ?>" size=12 readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id11','document.formulaireT.dateDebut',$_SESSION["langue"],"0","0");?>&nbsp;&nbsp;
<input type="text" name="dateFin"  value="<?php print $dateFin ?>" size=12 readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id12','document.formulaireT.dateFin',$_SESSION["langue"],"0","0");?> <input type='submit' value='Ok' class='button'  />
<br /><br />
<font class="T2">&nbsp;&nbsp;Classe : </font> 

<select name="sClasseGrp" size="1" onChange="document.getElementById('recharge').style.visibility='visible';this.form.submit()">
<?php print $option ?>
<option value="tous" id="<?php print $selectcss?>" > <?php print "Cumul" ?> </option>
<?php
for($i=1;$i<count($data);$i++){
 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
		continue;
	}else {
		// utilisation de l'opérateur ternaire expr1?expr2:expr3;
		$libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
		print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".ucwords($libelle)."</option>\n";
	}
	$gtmp=$data[$i][4];
	$ctmp=$data[$i][0];
}
unset($gtmp);
unset($ctmp);
unset($libelle);

if (preg_match('/:/',$idClasse)) { list($idClasse)=preg_split("/:/",$idClasse); }
?>
	</select><input type="hidden" name="saisie_pers" value="<?php print $idpers ?>" /> &nbsp;&nbsp;<span id=recharge style="visibility:hidden" >Chargement en cours... <img src='image/temps1.gif' align='center' /></span>
</form>



<font class="T2">&nbsp;&nbsp;<b><?php print $nomprenom ?></b></font><br /><br />
<?php
$data=listingMatiereProf($idpers,$idClasse);
$pe=preg_replace('/"/',"'",rechercheInfoPerso($idpers,0));
$lieuenseignant=preg_replace('/"/',"'",rechercheInfoPerso($idpers,1));

for($i=0;$i<count($data);$i++) {
	$matiere=chercheMatiereNom3($data[$i][0]);
	$tablisteMatiere[$matiere]=ucwords($matiere);
	$sousmatiere=chercheSousMatiereNom($data[$i][0]);
	$tablisteSousMatiere[$sousmatiere]=ucwords($sousmatiere);
}
$listeMatiere=implode(",",$tablisteMatiere);
$listeSousMatiere=implode(",",$tablisteSousMatiere);

/*
$tabcoul1=array("0","255","0","0","255","0");
$tabcoul2=array("0","0","0","128","128","128");
$tabcoul3=array("0","0","255","0","0","255");
*/

$listeMatiere=preg_replace('/ Et /'," et ",$listeMatiere);
$listeSousMatiere=preg_replace('/ Et /'," et ",$listeSousMatiere);
?>
	<font class="T2">&nbsp;&nbsp;Pôle d'enseignement (PE) :</font> <?php print $listeMatiere ?>
<br><br>
<font class="T2">&nbsp;&nbsp;Matière :</font>  <?php print $listeSousMatiere ?>
<br><br>
<form method="post" action="gestion_vacation_ens2.php"  name="formulaire" >
<font class="T2">&nbsp;&nbsp;Les enseignements sont dispensés à </font> <input type="text" name="lieuenseignant" size=20 maxlength="60" value="<?php print $lieuenseignant ?>" />
<BR><br>
<?php if ($_SESSION["membre"] == "menuadmin") { ?>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","create2"); //text,nomInput</script></UL></UL></UL><br>
<?php } ?>
<br><?php brmozilla($_SESSION["navigateur"]); ?>
<input type=hidden name="saisie_pers" value="<?php print $idpers ?>" />
</form>

<hr>

<table width="100%" border=1 bordercolor="#000000" bgcolor="#FFFFFF" >
<tr>	<td id="bordure" bgcolor="yellow"  ><font class=T1>PRESTATION</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class=T1>Nb HEURES</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class=T1>BASE</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class=T1>TOTAL BRUT</font></td>
</tr>

<?php 

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Vacation d'enseignement - $nomprenom");
$pdf->SetCreator("TRIADE");
$pdf->SetSubject("Vacation d'enseignement"); 
$pdf->SetAuthor("TRIADE. - http://www.triade-educ.com"); 



$tab=visu_param();
$anneeScolaire=$tab[0][11];



$X=0;
$Y=10;


if (file_exists("./data/image_pers/logo_bulletin.jpg")) {
	$xlogo=170;
	$ylogo=3;
	$logowidth=30;
	$logo="./data/image_pers/logo_bulletin.jpg";
	$pdf->Image($logo,$xlogo,$ylogo,$logowidth);
}


$pdf->SetFont('Arial','',14);
$pdf->SetXY($X,$Y+=10);
$pdf->MultiCell(210,10,"ANNEE ACADEMIQUE $anneeScolaire ",0,'C',0);
$Y+=11;
$pdf->SetFont('Arial','B',14);
$pdf->SetXY($X,$Y+=10); 
$pdf->MultiCell(210,7,"COMMANDE DE VACATION D'ENSEIGNEMENT \n DIPLOME EN STRATEGIE ET DECISION PUBLIQUE ET POLITIQUE",0,'C',0);
$Y+=15;
$pdf->SetFont('Arial','',14);
$pdf->SetXY($X,$Y+=10);
$pdf->MultiCell(210,7,"Grade : $nomClasse",0,'C',0);
$Y+=15;
$X+=20;
$pdf->SetFont('Arial','B',12);
$pdf->SetXY($X,$Y+=10);
$pdf->MultiCell(80,5,"$nomprenom",0,'L',0);
$Y+=10;
$pdf->SetFont('Arial','',12);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,5,"Pôle d'enseignement (PE) : $listeMatiere ",0,'L',0);
$Y+=10;
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,5,"Matière : $listeSousMatiere ",0,'L',0);
$Y+=10;
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,5,"Les enseignements sont dispensés à $lieuenseignant ",0,'L',0);
$Y+=10;
$pdf->SetXY($X,$Y);
$pdf->SetFillColor(230,230,255);
$pdf->MultiCell(60,10,"Type de prestation",1,'C',1);
$X+=60;$pdf->SetXY($X,$Y);
$pdf->MultiCell(40,10,"Nombre d'heures",1,'C',1);
$X+=40;$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,10,"Base",1,'C',1);
$X+=30;$pdf->SetXY($X,$Y);
$pdf->MultiCell(40,10,"Total Brut",1,'C',1);

$X=20;
$Y+=10;
$data=affEvalHoraire();  //  id,libelle,taux,type_prestation
$pdf->SetFont('Arial','',10);
$ii=0;
for($i=0;$i<count($data);$i++) {
	$taux=$data[$i][2];
	$TabnbHeure=nbHeureVacation($idpers,$data[$i][0],$dateDebut,$dateFin); // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,type_prestation,idmatiere,s.coursannule
	$seconde=0;
	$nbSeance=0;
	for($j=0;$j<count($TabnbHeure);$j++) {
		if (($TabnbHeure[$j][7] == $idClasse) || ($idClasse == "tous")) {
			if ($TabnbHeure[$j][12] != 1) { 
				$duree=$TabnbHeure[$j][5];	
				$seconde+=conv_en_seconde($duree);
				$nbSeance++;
			}
			if ($TabnbHeure[$j][10] == "cours") { $ok=1; }
			if ($TabnbHeure[$j][10] == "eval") { $ok=2; }
			$nommatiere=chercheMatiereNom($TabnbHeure[$j][11]);
			$sousmatiere=chercheSousMatiereNom($TabnbHeure[$j][11]);
		}
	}
	if ($ok == 1) {
		$nbHeure=calcul_hours($seconde);
		$taux=$taux/3600;
		$nbTotalBrut=$seconde*$taux;
		$totalRegler+=$nbTotalBrut;
		$nbTotalBrut=affichageFormatMonnaie($nbTotalBrut);
	
		print "<tr  class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<td id='bordure'><font class=T1>".$data[$i][1]." (".$nommatiere.")</font></td>";
		print "<td id='bordure' align='center' >".timeForm($nbHeure)."</td>";
		print "<td id='bordure' align='right' ><font class=T2>".affichageFormatMonnaie($data[$i][2])."</font></td>";
		print "<td id='bordure' align='right' >".$nbTotalBrut."</td>";
		print "</tr>";
		$ok=0;

		
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',9);
		$presta=trunchaine($data[$i][1],40);
	
	//	$pdf->SetTextColor($tabcoul1[$ii],$tabcoul2[$ii],$tabcoul3[$ii]);
		$info=trunchaine("$presta ($nommatiere)",35);
		$pdf->MultiCell(60,8,"$info",1,'L',0);
	//	$pdf->SetTextColor(0);
		
		$pdf->SetFont('Arial','',10);

		$nbTotalBrut=affichageFormatMonnaie($nbTotalBrut);

		$X+=60;
		$pdf->SetXY($X,$Y);
		$nb=timeForm($nbHeure);
		$pdf->MultiCell(40,8,$nb,1,'C',0);
		$X+=40;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,8,affichageFormatMonnaie($data[$i][2]),1,'C',0);
		$X+=30;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(40,8,$nbTotalBrut,1,'C',0);
		$X+=40;
		$Y+=8;
		$X=20;

		$ii++;
	}

	if ($ok == 2) {
		$nbHeure=calcul_hours($seconde);
//		$taux=$taux/3600;
		$nbTotalBrut=$taux*$nbSeance;
		$totalRegler+=$nbTotalBrut;
		$nbTotalBrut=affichageFormatMonnaie($nbTotalBrut);
	
		print "<tr>";
		print "<td id='bordure'><font class=T1>".$data[$i][1]." (".$nommatiere.")</font></td>";
		print "<td id='bordure' align='center' >".timeForm($nbHeure)." ($nbSeance)</td>";
		print "<td id='bordure' align='right' ><font class=T2>".affichageFormatMonnaie($data[$i][2])."</font></td>";
		print "<td id='bordure' align='right' >".$nbTotalBrut."</td>";
		print "</tr>";
		$ok=0;

		
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',9);
		$presta=trunchaine($data[$i][1],40);
	
	//	$pdf->SetTextColor($tabcoul1[$ii],$tabcoul2[$ii],$tabcoul3[$ii]);
		$info=trunchaine("$presta ($nommatiere)",35);
		$pdf->MultiCell(60,8,"$info",1,'L',0);
	//	$pdf->SetTextColor(0);
		
		$pdf->SetFont('Arial','',10);

		$X+=60;
		$pdf->SetXY($X,$Y);
		$nb=timeForm($nbHeure);
		$pdf->MultiCell(40,8,"$nb ($nbSeance)",1,'C',0);
		$X+=40;
		$pdf->SetXY($X,$Y);
		$info=affichageFormatMonnaie($data[$i][2]);
		$pdf->MultiCell(30,8,$info,1,'C',0);
		$X+=30;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(40,8,$nbTotalBrut,1,'C',0);
		$X+=40;
		$Y+=8;
		$X=20;

		$ii++;
	}

}

$totalRegler=affichageFormatMonnaie($totalRegler);



$Y+=15;
$X=100;
$pdf->SetXY($X,$Y);
$pdf->RoundedRect($X, $Y, 90, 10, 3.5, 'DF');
$pdf->SetXY($X,$Y);
$pdf->MultiCell(50,8,"TOTAL BRUT A REGLER :",0,'R',0);
$X+=50;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',10);
$unitePDF=unitemonnaiePdf();
$pdf->MultiCell(40,8,"$totalRegler $unitePDF",0,'C',0);
$pdf->SetFont('Arial','',10);
$Y+=8;
$X=100;
//$pdf->SetXY($X,$Y);
//$pdf->MultiCell(50,8,"PAIMENTS VERSEES :",0,'R',0);
$X+=50;
$pdf->SetXY($X,$Y);
$pdf->MultiCell(40,8,"",0,'C',0);

$X=60;
$Y+=8;
$dateJour=dateDMY();
$pdf->SetFont('Arial','I',8);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(90,3,"Commande éditée le : $dateJour",0,'R',0);

$unite=unitemonnaie();
?>
</table>

<br /><br />
<table width="65%" border=1 bordercolor="#000000" bgcolor="#FFFFFF" align="center" >
<tr><td align='right' id=bordure ><font class="T2">&nbsp;TOTAL&nbsp;BRUT&nbsp;:&nbsp;</font></td><td align='center' bgcolor="#CCCCCC" ><b><?php print "${totalRegler}&nbsp;$unite" ?></b></td></tr>

<?php 
/*
$dataP=paiementEffectue($idpers); // montant_ht,montant_tc,montant_tv
for($j=0;$j<count($data);$j++) {
	$montantverseht+=$dataP[$j][0];
	$montantversettc+=$dataP[$j][1];
}
 */
?>
<!--
<tr><td align='right' id=bordure  width=200 ><font class="T2">&nbsp;PAIMENTS&nbsp;BRUTS&nbsp;VERSEES&nbsp;:&nbsp;</font></td><td align='center' bgcolor="#CCCCCC" ><?php print $montantverseht ?></td> </tr>
-->
</table>
<br /><br />


<script language="JavaScript">
var etat=0;
function bul(form) {
	if (etat == 0) {
		form.liste.value="Cacher Listing";
		AffBulle3('Liste des cours','./image/commun/info.jpg',"<?php print $listeHoraire?>");
		searchCours('<?php print $idpers ?>','<?php print $idClasse ?>','<?php print $dateDebut ?>','<?php print $dateFin ?>');
		etat=1;
	}else{
		etat=0;
		form.liste.value="Listing";
		HideBulle();
	}
}

var etat2=0;
function bul2(form) {
	if (etat2 == 0) {
		form.liste.value="Cacher Listing";
		AffBulle3('Liste des cours','./image/commun/info.jpg',"<?php print $listeHoraire?>");
		searchCours2('<?php print $idpers ?>','<?php print $idClasse ?>','<?php print $dateDebut ?>','<?php print $dateFin ?>');
		etat2=1;
	}else{
		etat2=0;
		form.liste.value="Listing";
		HideBulle();
	}
}


</script>

<?php if ($_SESSION["membre"] == "menuadmin") { ?>
<form name="formulaire4" action="gestion_vacation_ens_paiement.php" method="post" >
<font class=T2>&nbsp;&nbsp;Editer un relevé : <input type="submit" class="button" value="Accèder" /> </font>
<input type=hidden name="idprof" value="<?php print $idpers ?>" />
</form>
<?php } ?>

<form name="formulaire2" >
<font class=T2>&nbsp;&nbsp;Jours et horaires des cours : </font> <input type=button onclick="bul(this.form)" name="liste" class="BUTTON"  value="Listing"  />
</form>


<form name="formulaire3" >
<font class=T2>&nbsp;&nbsp;Date des évaluations : </font> <input type=button onclick="bul2(this.form)" name="liste" class="BUTTON"  value="Listing"  />
</form>

<?php

$pdf->AddPage();

list($anneemoins,$anneeplus)=preg_split("/-/",$anneeScolaire);
$anneeM=substr(trim($anneemoins),-2);
$anneeP=substr(trim($anneeplus),-2);

$X=20;$Y=15;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(60,8,"* Dates des cours",0,'L',0);

$pdf->SetFont('Arial','',10);
$X=20;$Y+=10;
$anneemois=trim($anneemoins)."-09";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,4,"Sept. $anneeM : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;

/* -------------- */
$Y+=5;
$anneemois=trim($anneemoins)."-10";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,4,"Oct. $anneeM : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */

$Y+=5;
$anneemois=trim($anneemoins)."-11";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,4,"Nov. $anneeM : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */
$Y+=5;
$anneemois=trim($anneemoins)."-12";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,4,"Déc. $anneeM : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */
$Y+=5;
$anneemois=trim($anneeplus)."-01";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,4,"Jan. $anneeP : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */
$Y+=5;
$anneemois=trim($anneeplus)."-02";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,4,"Fév. $anneeP : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */
$Y+=5;
$anneemois=trim($anneeplus)."-03";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,4,"Mars. $anneeP : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */
$Y+=5;
$anneemois=trim($anneeplus)."-04";
$liste=heureEnseignant2($idpers,$anneemois,$idClasse);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(180,4,"Avr. $anneeP : ",0,'L',0);
$pdf->SetXY($X+20,$Y);
$pdf->MultiCell(150,4,"$liste ",0,'L',0);
$nb=nbHeureEnseignant2($idpers,$anneemois,$idClasse);
$Y+=$nb*4;
/* -------------- */

$X=20;$Y+=10;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(70,8,"* Date  et nature des évaluations",0,'L',0);
$pdf->SetFont('Arial','',10);
$X=20;$Y+=10;


$data=affEvalHoraire();  // id,libelle,taux
for($i=0;$i<count($data);$i++) {
	$TabnbHeure=nbHeureVacation($idpers,$data[$i][0],$_POST["dateDebut"],$_POST["dateFin"]); //id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,type_prestation,idmatiere,coursannul
	for($jj=0;$jj<count($TabnbHeure);$jj++) { 

		if ((($TabnbHeure[$jj][7] == $idClasse) || ($idClasse == "tous")) && ($TabnbHeure[$jj][10] == "eval") ) {  
			$prestation=$TabnbHeure[$jj][9];
		//	if ($TabnbHeure[$jj][12] == 1) { 
		//		$annule=" (Annulé)";
		//	}else{
				$annule="";
		//	}
			$tabList[$prestation].=" ".dateForm($TabnbHeure[$jj][3])."$annule, ";
		}
	}
}

foreach($tabList as $idpresta=>$datelist) {
	$nb=explode(" ",$datelist);
	$nb=count($nb);
	$tabPresta=affEvalHoraireMotif($idpresta);
	$libelle=$tabPresta[0][1];
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(180,3,"$libelle : $datelist",0,'L',0);
	$nb=($nb/10);
	if ($nb < 1){
		$Y +=5;
	}else{
		$Y += $nb * 5;
	}

}

$nomClasse=TextNoAccent($nomClasse);
$nomClasse=TextNoCarac($nomClasse);
$nomprenom=TextNoAccent($nomprenom);
$nomprenom=TextNoCarac($nomprenom);
$nomClasse=preg_replace('/\//',"_",$nomClasse);
$nomClasse=preg_replace('/\(/',"_",$nomClasse);
$nomClasse=preg_replace('/\)/',"_",$nomClasse);
$nomprenom=preg_replace('/\//',"_",$nomprenom);
if (!is_dir("./data/pdf_vacation")) { mkdir("./data/pdf_vacation");  htaccess("./data/pdf_vacation");  }
if (!is_dir("./data/pdf_vacation/$idpers")) { mkdir("./data/pdf_vacation/$idpers"); htaccess("./data/pdf_vacation/$idpers"); }
$fichier="vacation_${nomClasse}_${nomprenom}";
$fichier=urlencode($fichier);
$fichier="./data/pdf_vacation/$idpers/${fichier}.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
?>

<?php if ($_SESSION["membre"] == "menuadmin") { ?>
<font class=T2>&nbsp;&nbsp;Imprimer la vacation : </font> <input type=button  name="liste" class="bouton2"  value="Imprimer" onClick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" />
<?php } ?>

<?php if ($_SESSION["membre"] == "menuprof") { ?>
<font class=T2>&nbsp;&nbsp;Imprimer la vacation : </font> <input type=button  name="liste" class="bouton2"  value="Imprimer" onClick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');" />
<?php } ?>

<br /><br />

<!-- // fin form -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>
<?php pgClose(); ?>

