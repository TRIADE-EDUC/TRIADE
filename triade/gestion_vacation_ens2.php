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
<script type="text/javascript" src="./librairie_js/xorax_serialize.js"></script>

<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
$cnx=cnx();
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
validerequete("2");


if (isset($_POST["filtre"])) {
	$idclasse=$_POST["filtre"];
	$idpers=$_POST["saisie_pers"];
}

$idpers=$_POST["saisie_pers"];
if (isset($_GET["idsupp"])) {
	$idpers=$_GET["idpers"];
	suppCommandeVocation($_GET["idsupp"]);
}
$nomprenom=recherche_personne($idpers);
$idClasse="tous";
$selectcss="select0";
$nomClasse="L'intégralité";

if (isset($_POST["createcommande"])) {	
	$cr=updateFichePersonnel($idpers,$_POST["PE"],$_POST["lieuenseignant"],'ENS'); 
	$nbheure=$_POST["nbheure"];
	$idclasse=$_POST["idclasse"];
	$prestation=$_POST["prestation"];
	$idmatiere=$_POST["idmatiere"];
	$forfait=$_POST["forfait"];
	$nbforfait=$_POST["nbforfait"];
	$nbforfait=$_POST["nbforfait"];
	$totalEnNet=$_POST["totalEnNet"];
	if ($forfait == '1') { $nbheure="-1"; }
	enrCommande($idpers,$prestation,$idclasse,$nbheure,$idmatiere,$nbforfait,$totalEnNet);
}


?>
<form method="post" action="gestion_vacation_ens2.php" name="formulaireT" >

<input type="hidden" name="saisie_pers" value="<?php print $idpers ?>" />

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
$listeMatiere=preg_replace("/ Et /"," et ",$listeMatiere);
$listeSousMatiere=preg_replace("/ Et /"," et ",$listeSousMatiere);
?>
	<font class="T2">&nbsp;&nbsp;Pôle d'enseignement (PE) :</font> <?php print $listeMatiere ?>
<br><br>
<font class="T2">&nbsp;&nbsp;Matière :</font>  <?php print $listeSousMatiere ?>
<br><br>
<font class="T2">&nbsp;&nbsp;Les enseignements sont dispensés à </font> <input type="text" name="lieuenseignant" size=20 maxlength="60" value="<?php print $lieuenseignant ?>" />
<BR>
<br><?php brmozilla($_SESSION["navigateur"]); ?>
<input type=hidden name="saisie_pers" value="<?php print $idpers ?>" />

<hr>
<font class='T2'>
En classe : <select name="idclasse">
<?php 
if (isset($idclasse)) { 
	print "<option  value='$idclasse' id='select1' >".chercheClasse_nom($idclasse)."</option>";		
}else{
	print "<option  value='' id='select0' >".LANGCHOIX."</option>";
}

select_classe(); // creation des options
?>
</select><br /><br />
Prestation : <select name="prestation">
        <option  value="" id='select0' ><?php print LANGCHOIX?></option>
 	<?php
	select_EvalHoraire(); // creation des options
	?>
</select> <br /> <br />
en : 
<select name="idmatiere">
<?php
if (isset($idmatiere)) { 
	print "<option  value='$idmatiere' id='select1' >".chercheMatiereNom($idmatiere)."</option>";		
}else{
	print "<option  value='' id='select0' >".LANGCHOIX."</option>";
}
select_matiere3(30); // creation des options
?> </select>
<script>
function valideNbHeure() {
	document.getElementById('nbheure').value='';
	if (document.getElementById('nbheure').disabled == true) {
		document.getElementById('nbforfait').style.display='none';
		document.getElementById('nbheure').disabled=false;
	}else{
		document.getElementById('nbheure').disabled=true;
		document.getElementById('nbforfait').style.display='block';
	}
}
</script>
<br /><br />
Total en NET : <input type='checkbox' value='1' name='totalEnNet' /> <i>(oui)</i> 
<br/><br/>
<table >
<tr><td><font class='T2'>Nombre d'heures : <input type='text' size='3' id='nbheure' name="nbheure" /> / Forfait : <input type='checkbox' name='forfait' value='1' onclick="valideNbHeure();"   /></font></td>
    <td><span id="nbforfait" style="display:none" >(<input type='text' name="nbforfait" value='1' size='1' title='Nbr Forfait' />)</span></td>
	<td><input type='submit' value="<?php print VALIDER ?>" class="BUTTON" name='createcommande' /></td>
	</tr></table>
<br>
<hr>
</font>
<?php
$totalUnite="BRUT";
$data=recupCommandeVacation($idpers); // nbheure,idmatiere,type_prestation,idclasse,id,nbforfait,enNet
if (count($data)) $totalUnite=($data[0][6] == '1') ? "NET" : "BRUT";
?>
<table width="100%" border=1 bordercolor="#000000" bgcolor="#FFFFFF" id='table1'>
<tr>
	<td id="bordure" bgcolor="yellow"  ><font class='T1'>Supp.</font></td>
	<td id="bordure" bgcolor="yellow"  ><font class='T1'>PRESTATION</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class='T1'>Nb HEURES</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class='T1'>FORFAIT</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class='T1'>BASE</font></td>
	<td id="bordure" bgcolor="yellow" align='center'><font class='T1'>TOTAL <?php print $totalUnite ?></font></td>
</tr>
<?php
$nbligne=0;
for($i=0;$i<count($data);$i++) {
	$nbheure=$data[$i][0];
	$idprestat=$data[$i][2];
	$id=$data[$i][4];
	$matiere=chercheMatiereNom($data[$i][1]);
	$nomprestat=recherchePrestatNom($idprestat);
	$classe=chercheClasse_nom($data[$i][3]);
	$base=rechercheBasePrestat($idprestat);
	$idclasse=$data[$i][3];

	$tabfiltre[$classe]=$idclasse;

	if ((isset($_POST['filtre'])) && ($_POST['filtre'] != 'tout')) { 
		if ($idclasse != $_POST['filtre']) continue; 

	}
	 
	$nbforfait=$data[$i][5];
	if ($nbheure == "-1") {
		$forfait="$nbforfait";
		$nbheure="";
		$total=$base*$nbforfait;
	}else{
		$forfait="";
		$total=$base*$nbheure;
	}
	print "<tr   class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >
	<td id='bordure' align='center' ><a href='gestion_vacation_ens2.php?idpers=$idpers&idsupp=$id' title='Supprimer' ><img src='image/commun/trash.png' border='0' /></a></td>
	<td id='bordure' ><font class='T1'>$nomprestat - $matiere en $classe</font></td>
	<td id='bordure' align='center'><font class='T1'>$nbheure</font></td>
	<td id='bordure' align='center'><font class='T1'>$forfait</font></td>
	<td id='bordure' align='center'><font class='T1'>".affichageFormatMonnaie($base)."</font></td>
	<td id='bordure' align='center'><font class='T1'>$total</font></td>
	</tr>";
	$totalHT+=$total;

	$nbligne++;
	
}
?>
</table>

<br /><br />
<table width="65%" border=1 bordercolor="#000000" bgcolor="#FFFFFF" align="center" >
<tr><td align='right' id=bordure ><font class="T2">&nbsp;TOTAL&nbsp;<?php print $totalUnite ?>&nbsp;:&nbsp;</font></td><td align='center' bgcolor="#CCCCCC" >
<b><?php print "${totalHT}&nbsp;$unite" ?></b></td></tr>

</table>
<br /><br />


</form>

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
/*list($annee1,$annee2)=split('-',$anneeScolaire);
$annee1+=1;
$annee2+=1;*/
//$anneeScolaire="$annee1 - $annee2";

if (isset($_POST["anneeScolaire"])) {
	 $anneeScolaire=$_POST["anneeScolaire"];
}

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
$X+=60;
$pdf->SetXY($X,$Y);
$pdf->MultiCell(20,10,"",1,'C',1);

$pdf->SetXY($X,$Y+2);
$pdf->MultiCell(20,3,"Nbr d'heures",0,'C',0);
$X+=20;$pdf->SetXY($X,$Y);
$pdf->MultiCell(20,10,"Forfaits",1,'C',1);
$X+=20;$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,10,"Base",1,'C',1);
$X+=30;$pdf->SetXY($X,$Y);
$pdf->MultiCell(40,10,"Total $totalUnite",1,'C',1);

$X=20;
$Y+=10;

$pdf->SetFont('Arial','',10);
$ii=0;
$nbl2=0;
for($i=0;$i<count($data);$i++) {// nbheure,idmatiere,type_prestation,idclasse,id
	$nb=$data[$i][0];
	$idprestat=$data[$i][2];
	$id=$data[$i][4];
	$nommatiere=chercheMatiereNom($data[$i][1]);
	$presta=recherchePrestatNom($idprestat);
	$classe=chercheClasse_nom($data[$i][3]);
	$idclasse=$data[$i][3];
	$base=rechercheBasePrestat($idprestat);
	$nbforfait=$data[$i][5];
	if ($nb == "-1") {
		$nbTotalBrut=$base*$nbforfait;
		$nbheure="";
		$nbforfait="$nbforfait";
	}else{
		$nbheure=$nb;
		$nbforfait="";
		$nbTotalBrut=$base*$nb;
	}
	$totalRegler+=$total;


	if ((isset($_POST['filtre'])) && ($_POST['filtre'] != 'tout')) {
                if ($idclasse != $_POST['filtre']) continue;
        }

	$nbl2++;

	if ($nbligne >= 7) { 	
		$hauteurMatiere=10;
	}else{
		$hauteurMatiere=15;
	}

	if ($nbl2 == 10) {
		$pdf->AddPage();
		$X=20;
		$Y=10;
	}

	$pdf->SetXY($X,$Y+1);
	$pdf->SetFont('Arial','',8);
	
//	$pdf->SetTextColor($tabcoul1[$ii],$tabcoul2[$ii],$tabcoul3[$ii]);
	$info=trunchaine("$presta - $nommatiere ($classe)",120);
	$pdf->MultiCell(60,3,"$info",0,'L',0);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(60,$hauteurMatiere,"",1,'L',0);
//	$pdf->SetTextColor(0);
	
	$pdf->SetFont('Arial','',10);
	$X+=60;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,$hauteurMatiere,"$nbheure",1,'C',0);
	$X+=20;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,$hauteurMatiere,"$nbforfait",1,'C',0);
	$X+=20;
	$pdf->SetXY($X,$Y);
	$base=affichageFormatMonnaie($base);
	$pdf->MultiCell(30,$hauteurMatiere,$base,1,'C',0);
	$X+=30;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(40,$hauteurMatiere,$nbTotalBrut,1,'C',0);
	$X+=40;
	$Y+=$hauteurMatiere;
	$X=20;
	$ii++;
}

$totalRegler=affichageFormatMonnaie($totalRegler);



$Y+=$hauteurMatiere;
$Y2=$Y;
$X=100;
$pdf->SetXY($X,$Y);
$pdf->RoundedRect($X, $Y, 90, 10, 3.5, 'DF');
$pdf->SetXY($X,$Y);
$pdf->MultiCell(50,8,"TOTAL $totalUnite A REGLER :",0,'R',0);
$X+=50;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',10);
$unitePDF=unitemonnaiePdf();
$pdf->MultiCell(40,8,"$totalHT $unitePDF",0,'C',0);
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

$pdf->SetFont('Arial','',9);
$pdf->SetXY(20,$Y2);
$pdf->MultiCell(100,8,"Signature de $nomprenom",0,'L',0);

list($debAnnee,$finAnnee)=preg_split('/-/',$anneeScolaire);

$dateDebutAff="01/09/".$debAnnee;
//$annee1=date("Y")+1;
$dateFinAff="31/08/".$finAnnee;

if ((isset($_POST['filtre'])) && ($_POST['filtre'] != 'tout')) {
	$idClasse=$_POST['filtre'];
}



$liste=heureEnseignantparDate2($idpers,$idClasse,$dateDebutAff,$dateFinAff,'cours','0',''); 
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
	$duree=timeForm($liste[$i][5]);$duree=preg_replace("/:/","h",$duree);
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

unset($liste);

// s.id,s.code,s.enseignement,s.date,s.heure,s.duree,s.bgcolor,s.idclasse,s.idprof,s.prestation,v.taux,s.coursannule,reportle,reporta
$X=20;$Y+=5;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(60,8,"* Date des évaluations",0,'L',0);
$pdf->SetFont('Arial','',12);
$X=20;$Y+=10;
$pdf->SetXY($X,$Y);

$data=recupCommandeVacation($idpers); // nbheure,idmatiere,type_prestation,idclasse,id,nbforfait,enNet

//$dateDebutAff="01/09/".date("Y");
//$dateFinAff="31/08/".$annee1;

for ($ii=0;$ii<count($data);$ii++) {
	$idClasse=$data[$ii][3];
	$typeprestation=$data[$ii][2];


        if ((isset($_POST['filtre'])) && ($_POST['filtre'] != 'tout')) {
                if ($idClasse != $_POST['filtre']) continue;
        }


	$liste=heureEnseignantparDate2($idpers,$idClasse,$dateDebutAff,$dateFinAff,'eval','0',$data[$ii][1],$typeprestation); 

	for($i=0;$i<count($liste);$i++) {
		$dateDuCours=dateForm($liste[$i][3]);
		$heureDuCours=timeForm($liste[$i][4]);
		$duree=timeForm($liste[$i][5]);$duree=preg_replace("/:/","h",$duree);
		$annule=$liste[$i][11];
		$dateDuCours=dateLettre($dateDuCours);
		$nommatiere=chercheMatiereNom($data[$i][1]);
		$listing="Le $dateDuCours à $heureDuCours durant $duree";
	
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',12);
		$pdf->MultiCell(180,5,"$listing",0,'L',0);
	
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
}

$nomprenom=TextNoAccent($nomprenom);
$nomprenom=TextNoCarac($nomprenom);
$nomprenom=preg_replace("/\//","_",$nomprenom);
if (!is_dir("./data/pdf_vacation")) { mkdir("./data/pdf_vacation");  htaccess("./data/pdf_vacation");  }
if (!is_dir("./data/pdf_vacation/$idpers")) { mkdir("./data/pdf_vacation/$idpers"); htaccess("./data/pdf_vacation/$idpers"); }
$fichier="vacation_${nomprenom}";
$fichier=urlencode($fichier);
$fichier="./data/pdf_vacation/$idpers/${fichier}.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output("F",$fichier);
$pdf->close();

?>

<?php if ($_SESSION["membre"] == "menuadmin") { ?>
<form method='POST' >
<input type="hidden" name="saisie_pers"  value="<?php print $_POST["saisie_pers"]; ?>" />
<font class=T2>&nbsp;&nbsp;Ann&eacute;e scolaire : <select name='anneeScolaire' onchange="this.form.submit()" >
<?php
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
}
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select></font>
<br />
</form>
<font class=T2>&nbsp;&nbsp;Imprimer la demande de vacation : </font> <input type=button  name="liste" class="bouton2"  value="Imprimer" onClick="open('visu_pdf_admin.php?id=<?php print $fichier?>&renamefic=cmd_vac_<?php print $nomprenom ?>_<?php print dateDMY2() ?>.pdf','_blank','');" />
<br><br>
<form method='POST' >
<input type=hidden name="saisie_pers" value="<?php print $idpers ?>" />
<font class=T2>&nbsp;&nbsp;Filtre sur la classe : </font> 
<select onChange="this.form.submit()" name="filtre" >

<?php  if ("tout" == $_POST["filtre"]) $select="selected='selected'" ; ?>

<option value='tout' id='select0' <?php print $select ?>  ><?php print "Toutes les classes" ?></option>
<?php 
unset($select);
ksort($tabfiltre);
foreach($tabfiltre as $key=>$value) {
	if ($value != "tout") { 
		if ($value == $_POST["filtre"]) { 
			$select="selected='selected'" ;	
		}else{
			$select="";
		}
	}
	print "<option id='select1' value='$value' $select >$key </option>";
}
 ?>
</select>
<br><br>

<?php } ?>
<br /><br /><br />


<!-- // fin form -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       }else{
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       }
     ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>
<?php pgClose(); ?>

