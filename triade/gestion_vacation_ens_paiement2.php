<?php
session_start();
$dateDebut=$_POST["saisie_date_debut"];
$dateFin=$_POST["saisie_date_fin"];
setcookie("date_paiem_debut",$dateDebut,time()+3600*24*2);
setcookie("date_paiem_fin",$dateFin,time()+3600*24*2);

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
validerequete("menuadmin");
$cnx=cnx();
nettoyage_EDT();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="125">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Relevé vacation enseignant" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td ><br>

<?php
$idpers=$_POST["saisie_pers"];

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

/*
if (isset($_POST["create2"])) {
	$cr=updateFichePersonnel($idpers,$_POST["PE"],$_POST["lieuenseignant"],'ENS');
	if ($cr) { 
		alertJs(LANGDONENR);	
	}
}
 */

?>
<form method="post" action="gestion_vacation_ens_paiement2.php" >
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

if (preg_match('/:/',$idClasse)) { list($idClasse)=preg_split('/:/',$idClasse); }
?>
	</select><input type="hidden" name="saisie_pers" value="<?php print $idpers ?>" /> &nbsp;&nbsp;<span id=recharge style="visibility:hidden" >Chargement en cours... <img src='image/temps1.gif' align='center' /></span>
	<input type="hidden" value="<?php print $dateDebut ?>" name="saisie_date_debut" />
	<input type="hidden" value="<?php print $dateFin ?>" name="saisie_date_fin" />


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
$dateDebutAff=dateForm($dateDebut);
$dateFinAff=dateForm($dateFin);

$listeMatiere=preg_replace('/ Et /'," et ",$listeMatiere);
$listeSousMatiere=preg_replace('/ Et /'," et ",$listeSousMatiere);

$totalUnite=recupNetBrut($idpers);
if ($totalUnite == 1) { $totalUnite="NET"; } else {  $totalUnite="BRUT"; }  
?>
	<font class="T2">&nbsp;&nbsp;Pôle d'enseignement (PE) :</font> <?php print $listeMatiere ?>
<br><br>
<font class="T2">&nbsp;&nbsp;Matière :</font>  <?php print $listeSousMatiere ?>
<br>

<form name="formulaire4" action="gestion_vacation_ens_paiement.php" method="post" >
<font class="T2">&nbsp;&nbsp;Période du <b><?php print $dateDebutAff ?></b> au <b><?php print $dateFinAff ?></b></font>
&nbsp;&nbsp;<input type="submit" class="button" value="Autre période" /><input type=hidden name="idprof" value="<?php print $idpers ?>" /></form>
<hr>

<table width="100%" border=1 bordercolor="#000000" bgcolor="#FFFFFF" >
<tr>	<td id="bordure" bgcolor="yellow"  ><font class=T1>PRESTATION</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class=T1>Nb HEURES</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class=T1>BASE</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class=T1>TOTAL <?php print $totalUnite?></font></td>
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
$pdf->MultiCell(210,7,"RELEVE DE VACATION D'ENSEIGNEMENT \n DIPLOME EN STRATEGIE ET DECISION PUBLIQUE ET POLITIQUE",0,'C',0);
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
$pdf->MultiCell(190,5,"Les enseignements sont dispensés à $lieuenseignant ",0,'L',0);
$Y+=10;

$pdf->SetXY($X,$Y);
$pdf->WriteHTML("Période du <b>$dateDebutAff</b> au <b>$dateFinAff</b>");

 
$Y+=10;
$X-=10;
$pdf->SetXY($X,$Y);
$pdf->SetFillColor(230,230,255);
$pdf->MultiCell(60,10,"Type de prestation",1,'C',1);
$X+=60;$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,10,"Nbr d'heures",1,'C',1);
$X+=30;$pdf->SetXY($X,$Y);
$pdf->MultiCell(40,10,"Nbr de prestations",1,'C',1);
$X+=40;$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,10,"Base",1,'C',1);
$X+=30;$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,10,"Total $totalUnite",1,'C',1);

$X=10;
$Y+=10;
$data=affEvalHoraire();  //  id,libelle,taux,type_prestation
$pdf->SetFont('Arial','',10);
$ii=0;
for($i=0;$i<count($data);$i++) {
	$taux=$data[$i][2];
	/* -------------------------------- */
	$TabnbHeureTOTAL=nbHeureVacation($idpers,$data[$i][0],$dateDebut,$dateFin);
	$secondeTOTAL=0;
	$nbSeanceTOTAL=0;
	for($j=0;$j<count($TabnbHeureTOTAL);$j++) {
		$ok=0;
		if (($TabnbHeureTOTAL[$j][7] == $idClasse) || ($idClasse == "tous")) {
			if ($TabnbHeureTOTAL[$j][12] != 1) { 
				$dureeTOTAL=$TabnbHeureTOTAL[$j][5];	
				$secondeTOTAL+=conv_en_seconde($dureeTOTAL);
				$nbSeanceTOTAL++;
			}
			if ($TabnbHeureTOTAL[$j][10] == "cours") { $ok=1; }
			if ($TabnbHeureTOTAL[$j][10] == "eval") { $ok=2; }
		}
	}
	if ($ok == 1) {
		$nbHeureTOTAL=calcul_hours($secondeTOTAL);
		$taux=$taux/3600;
		$nbTotalBrutTOTAL=$secondeTOTAL*$taux;
		$TotalBrutCommande+=$nbTotalBrutTOTAL;
		$ok=0;
	}

	if ($ok == 2) {
		$nbHeureTOTAL=calcul_hours($secondeTOTAL);
//		$taux=$taux/3600;
		$nbTotalBrutTOTAL=$taux*$nbSeanceTOTAL;
		$TotalBrutCommande+=$nbTotalBrutTOTAL;
		$ok=0;
	}

	/* ---------------------------------- */
	$TabnbHeure=nbHeureVacationParDate($idpers,$data[$i][0],$dateDebut,$dateFin); 
	// id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,type_prestation,idmatiere,s.coursannule
	$seconde=0;
	$nbSeance=0;
	for($j=0;$j<count($TabnbHeure);$j++) {
		$ok=0;
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
	//	$taux=$taux/3600;
		$nbTotalBrut=$seconde*$taux;
		$totalRegler+=$nbTotalBrut;
		$nbTotalBrut=affichageFormatMonnaie($nbTotalBrut);
		$nbTotalBrutHTML=preg_replace('/ /','&nbsp;',$nbTotalBrut);
		print "<tr>";
		print "<td id='bordure'><font class=T1>".$data[$i][1]." (".$nommatiere.")</font></td>";
		print "<td id='bordure' align='center' >".timeForm($nbHeure)."</td>";
		print "<td id='bordure' align='right' >".affichageFormatMonnaie($data[$i][2])."</td>";
		print "<td id='bordure' align='right' >".$nbTotalBrutHTML."</td>";
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
		$pdf->MultiCell(30,8,"$nb",1,'C',0);
		$X+=30;

                $pdf->SetXY($X,$Y);
                $pdf->MultiCell(40,8,"",1,'C',0);
                $X+=40;

		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,8,affichageFormatMonnaie($data[$i][2]),1,'C',0);
		$X+=30;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,8,"$nbTotalBrut",0,'R',0);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,8,"",1,'R',0);
		$X+=40;
		$Y+=8;
		$X=10;
		unset($nbTotalBrutHTML);
		$ii++;
	}

	if ($ok == 2) {
		$nbHeure=calcul_hours($seconde);
//		$taux=$taux/3600;
		$nbTotalBrut=$taux*$nbSeance;
		$totalRegler+=$nbTotalBrut;
		$nbTotalBrut=affichageFormatMonnaie($nbTotalBrut);
		$nbTotalBrutHTML=preg_replace('/ /','&nbsp;',$nbTotalBrut);
		print "<tr>";
		print "<td id='bordure'><font class=T1>".$data[$i][1]." (".$nommatiere.")</font></td>";
		print "<td id='bordure' align='center' >".timeForm($nbHeure)." ($nbSeance)</td>";
		print "<td id='bordure' align='right' ><font class=T2>".affichageFormatMonnaie($data[$i][2])."</font></td>";
		print "<td id='bordure' align='right' >".$nbTotalBrutHTML."</td>";
		print "</tr>";
		$ok=0;
		unset($nbTotalBrutHTML);
		
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
		$pdf->MultiCell(30,8,"",1,'C',0);
		$X+=30;

		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(40,8,"$nbSeance",1,'C',0);
		$X+=40;
		
		$pdf->SetXY($X,$Y);
		$info=affichageFormatMonnaie($data[$i][2]);
		$pdf->MultiCell(30,8,"$info",1,'C',0);
		$X+=30;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,8,"$nbTotalBrut",0,'R',0);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,8,"",1,'R',0);
		$X+=40;
		$Y+=8;
		$X=10;

		$ii++;
	}

}



$haut=17;
if (defined("TVAVACATION")) { 
	if (TVAVACATION == "oui") { 
		$haut=33; 
	} 
}

$haut+=8;

$Y+=15;
$X=100;
$pdf->SetFont('Arial','',8);
$pdf->SetXY($X,$Y);
$pdf->RoundedRect($X-10, $Y, 100, $haut, 3.5, 'DF');
$pdf->SetXY($X,$Y);
$pdf->MultiCell(50,8,"ACOMPTE(S) VERSE(S) :",0,'R',0);
$X+=50;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','',8);
$TotalBrutCommande=affichageFormatMonnaie($TotalBrutCommande);
//$pdf->MultiCell(25,8,"$TotalBrutCommande",0,'R',0);
$pdf->MultiCell(25,8,"0",0,'R',0);
$pdf->SetFont('Arial','',8);
$Y+=8;
$X=100;


$dataP=paiementEffectue($idpers); // montant_ht,montant_tc,montant_tv
for($j=0;$j<count($data);$j++) {
	$montantverseht+=$dataP[$j][0];
	$paiementeffectue+=$dataP[$j][1];
}
$paiementeffectue=affichageFormatMonnaie($paiementeffectue);

/*
$pdf->SetXY($X,$Y);
$pdf->MultiCell(50,8,"PAIMENT(S) DEJA VERSE(S) :",0,'R',0);
$X+=50;
$pdf->SetXY($X,$Y);
$pdf->MultiCell(25,8,"$paiementeffectue",0,'R',0);
$Y+=8;
*/
$X=100;
$pdf->SetXY($X,$Y);

$texte="TOTAL $totalUnite A REGLER :";
$pdf->SetFont('Arial','B',10);
if (defined("TVAVACATION")) {
	if (TVAVACATION == "oui") {
		$texte="TOTAL BRUT REGLE :";
		$pdf->SetFont('Arial','',8);
	}
}


$pdf->MultiCell(50,8,"$texte",0,'R',0);
$X+=50;
$pdf->SetXY($X,$Y);
$totalReglerAff=affichageFormatMonnaie($totalRegler);
$pdf->MultiCell(25,8,"$totalReglerAff",0,'R',0);


if (defined("TVAVACATION")) {
	if (TVAVACATION == "oui") {
		$Y+=8;
		$X=100;
		$totalTVA=($totalRegler*TVAVACATIONTAUX) / 100;
		$totalReglerTTC=(($totalRegler*TVAVACATIONTAUX) / 100) + $totalRegler;
		$totalReglerTTC=affichageFormatMonnaie($totalReglerTTC);
		$totalTVA=affichageFormatMonnaie($totalTVA);
		$totalRegler=affichageFormatMonnaie($totalRegler);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(50,8,"TOTAL TTC REGLE :",0,'R',0);
		$X+=50;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','B',10);
		$pdf->MultiCell(25,8,"$totalReglerTTC",0,'R',0);
		$pdf->SetFont('Arial','',8);
		$X=100;
		$Y+=8;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(50,8,"TVA (".TVAVACATIONTAUX.") :",0,'R',0);
		$X+=50;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(25,8,"$totalTVA",0,'R',0);
		$Y+=8;
		$X=100;
	}
}

$dateJour=dateDMY();
$pdf->SetFont('Arial','',8);
$pdf->SetXY($X,$Y+7);
$pdf->MultiCell(80,10,"Relevé édité le : $dateJour",0,'L',0);

// $dateDebutAff
// $dateFinAff


$liste=heureEnseignantparDate2($idpers,$idClasse,$dateDebutAff,$dateFinAff,'cours','0'); 
// s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule,reportle,reporta
$pdf->AddPage();
$X=20;$Y=15;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(40,8,"* Dates des cours",0,'L',0);
$pdf->SetFont('Arial','',12);
$X=20;$Y+=10;

$uu=0;
for($i=0;$i<count($liste);$i++) {

	$dateDuCours=dateForm($liste[$i][3]);
	$heureDuCours=timeForm($liste[$i][4]);
	$duree=timeForm($liste[$i][5]);$duree=preg_replace('/:/',"h",$duree);
	$annule=$liste[$i][11];
	$dateDuCours=dateLettre($dateDuCours);
	$listing="Le $dateDuCours à $heureDuCours durant $duree";

	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(90,5,"$listing",0,'L',0);

	if ($annule == 1) { 
	
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(90,5,"--------------------------------------------------",0,'L',0);
		if (($liste[$i][12] != "0000-00-00") && ($liste[$i][13] != "hh:mm")) {
			$report="(Reporter le ".dateForm($liste[$i][12])." à ".$liste[$i][13].")";
		}else{
			$report="(Annulé)";
		}
		$X+=90;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','I',10);
		$pdf->MultiCell(80,5,"$report",0,'L',0);
	}
	$Y+=5;
	if ($Y >= 270) { $Y=15; $pdf->AddPage(); }
	$X=20;

}




$liste=heureEnseignantparDate2($idpers,$idClasse,$dateDebutAff,$dateFinAff,'eval','0'); 
// s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule,reportle,reporta
$X=20;$Y+=5;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(60,8,"* Date des évaluations",0,'L',0);
$pdf->SetFont('Arial','',12);
$X=20;$Y+=10;
$pdf->SetXY($X,$Y);
for($i=0;$i<count($liste);$i++) {
	$dateDuCours=dateForm($liste[$i][3]);
	$heureDuCours=timeForm($liste[$i][4]);
	$duree=timeForm($liste[$i][5]);$duree=preg_replace('/:/',"h",$duree);
	$annule=$liste[$i][11];
	$dateDuCours=dateLettre($dateDuCours);
	$listing="Le $dateDuCours à $heureDuCours durant $duree";

	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(90,5,"$listing",0,'L',0);

	if ($annule == 1) { 
	
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(90,5,"--------------------------------------------------",0,'L',0);
		if (($liste[$i][12] != "0000-00-00") && ($liste[$i][13] != "hh:mm")) {
			$report="(Reporter le ".dateForm($liste[$i][12])." à ".$liste[$i][13].")";
		}else{
			$report="(Annulé)";
		}
		$X+=90;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','I',10);
		$pdf->MultiCell(80,5,"$report",0,'L',0);
	}
	$Y+=5;
	if ($Y >= 270) { $Y=15; $pdf->AddPage(); }
	$X=20;
}



$nomClasse=TextNoAccent($nomClasse);
$nomClasse=TextNoCarac($nomClasse);
$nomprenom=TextNoAccent($nomprenom);
$nomprenom=TextNoCarac($nomprenom);
$nomClasse=preg_replace('/\//',"_",$nomClasse);
$nomprenom=preg_replace('/\//',"_",$nomprenom);
$nomClasse=preg_replace('/\(/',"_",$nomClasse);
$nomClasse=preg_replace('/\)/',"_",$nomClasse);
if (!is_dir("./data/pdf_vacation")) { mkdir("./data/pdf_vacation");  htaccess("./data/pdf_vacation");  }
if (!is_dir("./data/pdf_vacation/$idpers")) { mkdir("./data/pdf_vacation/$idpers"); htaccess("./data/pdf_vacation/$idpers"); }
$fichier="vacation_${nomClasse}_${nomprenom}";
$fichier=urlencode($fichier);
$fichier="./data/pdf_vacation/$idpers/${fichier}.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();

?>
</table>

<?php 
$BRUT="";
if (defined("TVAVACATION")) {
	if (TVAVACATION == "oui") {
		$BRUT="BRUT";
	}
}
?>

<br /><br />
<table width="60%" border=1 bordercolor="#000000" bgcolor="#FFFFFF" align="center" >
<tr><td align='right' id=bordure ><font class="T2">TOTAL <?php print $BRUT." ".$totalUnite ?> REGLE : </font></td><td align='right' bgcolor="#CCCCCC" ><?php print $totalRegler ?>&nbsp;</td></tr>
<?php 
if (defined("TVAVACATION")) {
	if (TVAVACATION == "oui") {
?>
<tr><td align='right' id=bordure ><font class="T2">TOTAL TTC REGLE : </font></td><td align='right' bgcolor="#CCCCCC" ><b><?php print $totalReglerTTC ?></b>&nbsp;</td></tr>
<tr><td align='right' id=bordure ><font class="T1">TVA (<?php print TVAVACATIONTAUX ?>) : </font></td><td align='right' bgcolor="#CCCCCC" ><?php print $totalTVA ?>&nbsp;</td></tr>
<?php } } ?>
</table>
<br /><br />


<script language="JavaScript">
var etat=0;
function bul(form) {
	if (etat == 0) {
		form.liste.value="Cacher Listing";
		AffBulle3('Cours du <?php print $dateDebut ?> au <?php print $dateFin ?>','./image/commun/info.jpg',"<?php print $listeHoraire?>");
		searchCoursDate('<?php print $idpers ?>','<?php print $idClasse ?>','<?php print $dateDebut ?>','<?php print $dateFin ?>');
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
		AffBulle3('Cours du <?php print $dateDebut ?> au <?php print $dateFin ?>','./image/commun/info.jpg',"<?php print $listeHoraire?>");
		searchCoursDate2('<?php print $idpers ?>','<?php print $idClasse ?>','<?php print $dateDebut ?>','<?php print $dateFin ?>');
		etat2=1;
	}else{
		etat2=0;
		form.liste.value="Listing";
		HideBulle();
	}
}


</script>

<center>
<input type=button  name="liste" class="bouton2"  value="Imprimer vacation" onClick="open('visu_pdf_admin.php?id=<?php print $fichier?>&renamefic=releve_vac_<?php print $nomprenom ?>_<?php print dateDMY2() ?>.pdf','_blank','');" />
</center>
<br />

<hr>

<form name="formulaire2" >
<font class=T2>&nbsp;&nbsp;Jours et horaires des cours : </font> <input type=button onclick="bul(this.form)" name="liste" class="BUTTON"  value="Listing"  />
</form>

<form name="formulaire3" >
<font class=T2>&nbsp;&nbsp;Date des évaluations : </font> <input type=button onclick="bul2(this.form)" name="liste" class="BUTTON"  value="Listing"  />
</form>

<ul>		
<script language=JavaScript>buttonMagicRetour2("gestion_vacation_releve_ens.php","_parent","<?php print "Autre enseignant" ?>");</script>
<script language=JavaScript>buttonMagicRetour2("gestion_vacation.php","_parent","<?php print "Menu vacation" ?>");</script>
</ul>
<br /><br />

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>
<?php Pgclose(); ?>

