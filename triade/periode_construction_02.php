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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
      set_time_limit(6000);
}
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPER1?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
	verif_profp_class($_SESSION["id_pers"],$_POST["saisie_classe"]);
}else{
	validerequete("2");
}
$debut=deb_prog();

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=preg_replace('/_/',' ',$data[0][1]);
?>
<ul><font class="T2">
      <?php print LANGPER2?> : <?php print $_POST["saisie_date_debut"] ?><br> <br>
      <?php print LANGPER3?> : <?php print $_POST["saisie_date_fin"] ?> <br> <br>
      <?php print LANGPER4?> : <?php print $classe_nom?><br> <br>
    </font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');


if ($_POST["nom_periode"] == "periode1") { $period="1ere"; }
if ($_POST["nom_periode"] == "periode2") { $period="2eme"; }
if ($_POST["nom_periode"] == "periode3") { $period="3eme"; }
if ($_POST["nom_periode"] == "periode4") { $period="4eme"; }
if ($_POST["nom_periode"] == "periode5") { $period="5eme"; }
if ($_POST["nom_periode"] == "periode6") { $period="6eme"; }
if ($_POST["nom_periode"] == "periode7") { $period="7eme"; }
if ($_POST["nom_periode"] == "periode8") { $period="8eme"; }
if ($_POST["nom_periode"] == "periode9") { $period="9eme"; }


// recuperation des coordonnées
// de l etablissement
$data=visu_paramViaIdSite(chercheIdSite($_POST["saisie_classe"]));
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
}
// fin de la recup


// Gestion des dates de debut et fin
$dateDebut=trim($_POST["saisie_date_debut"]);
$dateFin=trim($_POST["saisie_date_fin"]);
$idClasse=$_POST["saisie_classe"];

$ordre=ordre_matiere($_POST["saisie_classe"]); // recup ordre matiere

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve
for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=trim(ucwords($eleveT[$j][0]));
	$prenomEleve=trim(ucfirst($eleveT[$j][1]));
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

$pdf->AddPage();
$pdf->SetTitle("Période - $nomEleve $prenomEleve");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Relevé de notes"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


// declaration variable
$coordonne0=strtoupper($nom_etablissement);
$coordonne1=$adresse;
$coordonne2=$postal." - ".ucwords($ville);
$coordonne3="Téléphone : ".$tel;
$coordonne4="E-mail : ".$mail;

if ($_POST["nom_periode"] == "periode1") { $periodt=LANG1ER; }
if ($_POST["nom_periode"] == "periode2") { $periodt=LANG2EME; }
if ($_POST["nom_periode"] == "periode3") { $periodt=LANG3EME; }
if ($_POST["nom_periode"] == "periode4") { $periodt=LANG4EME; }
if ($_POST["nom_periode"] == "periode5") { $periodt=LANG5EME; }
if ($_POST["nom_periode"] == "periode6") { $periodt=LANG6EME; }
if ($_POST["nom_periode"] == "periode7") { $periodt=LANG7EME; }
if ($_POST["nom_periode"] == "periode8") { $periodt=LANG8EME; }
if ($_POST["nom_periode"] == "periode9") { $periodt=LANG9EME; }

$titre="<B><U>".LANGBULL20."</U> <U>".$periodt." ".LANGBULL21."</u></B>";

$photo="data/image_eleve/".$idEleve.".jpg";
$nbchaine=20;
if (file_exists($photo)) {
	$nbchaine=40;
}


$nomEleve=strtoupper($nomEleve);
$nomEleve="$nomEleve $prenomEleve";


$infoeleve=LANGBULL16." : <B>".$nomEleve."</B>";
$infoeleve2=LANGELE4." : ";
$infoeleveclasse=$classe_nom;

$titrenote1=LANGPER17;
$titrenote2=LANGBULL17;
$titrenote3=LANGBULL18;


$appreciation=LANGBULL19;
$appreciation2="________________________________________________________________________________________________________________________";
// FIN variables

$xtitre=80;  // sans logo
$xcoor0=3;   // sans logo
$ycoor0=3;   // sans logo


// mise en place du logo
if (file_exists("./data/image_pers/logo_bull.jpg")) {
	$xlogo=3;
	$ylogo=3;
	$xcoor0=30;
	$ycoor0=3;
	$xtitre=90; // avec logo
	$logo="./data/image_pers/logo_bull.jpg";
	$pdf->Image($logo,$xlogo,$ylogo);
}
// fin du logo


// Debut création PDF
// mise en place des coordonnées
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML($coordonne0);
$pdf->SetFont('Arial','',8);
$pdf->SetXY($xcoor0,$ycoor0+5);
$pdf->WriteHTML($coordonne1);
$pdf->SetXY($xcoor0,$ycoor0+10);
$pdf->WriteHTML($coordonne2);
$pdf->SetXY($xcoor0,$ycoor0+15);
$pdf->WriteHTML($coordonne3);
$pdf->SetXY($xcoor0,$ycoor0+20);
$pdf->WriteHTML($coordonne4);
//fin coordonnees


// insertion de la date
$date=date("d/m/Y");
$Pdate="Date: ".$date;
$pdf->SetFont('Courier','',10);
$pdf->SetXY(150,3);
$pdf->WriteHTML($Pdate);
// fin d'insertion

// Titre
$pdf->SetXY($xtitre,20);
$pdf->SetFont('Courier','',18);
$pdf->WriteHTML($titre);
// fin titre

// cadre du haut
$pdf->SetFont('Arial','',11);
$pdf->SetFillColor(220);
$pdf->SetXY(15,35); // placement du cadre du nom de l eleve
$pdf->MultiCell(184,20,'',1,'L',1);

$photoeleve=image_bulletin($idEleve);
$photo=$photoeleve;
$xphoto=17;
$yphoto=36;
$photowidth=10.8;
$photoheight=16.3;
$Xv1=20;
$Xv11=101;
if (!empty($photo)) {
	$photo=$photoeleve;
	$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
	$Xv1=20+9;
	$Xv11=110;
}

$pdf->SetXY($Xv1,36); // placement du nom de l'eleve
$pdf->WriteHTML($infoeleve);
$pdf->SetXY($Xv1,48);
$pdf->WriteHTML($infoeleve2);
$pdf->SetX($Xv1+18);
$pdf->WriteHTML($infoeleveclasse);


// adresse de l'élève
// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
$dataadresse=chercheadresse($idEleve);
for($ik=0;$ik<=count($dataadresse);$ik++) {
	$nomtuteur=$dataadresse[$ik][1];
	$prenomtuteur=$dataadresse[$ik][2];
	$adr1=$dataadresse[$ik][3];
	$code_post_adr1=$dataadresse[$ik][4];
	$commune_adr1=$dataadresse[$ik][5];
	$numero_eleve=$dataadresse[$ik][9];
	$datenaissance=$dataadresse[$ik][11];
	if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
	$regime=$dataadresse[$ik][12];
	$class_ant=trunchaine($dataadresse[$ik][10],20);
	
	$pdf->SetXY($Xv1,40); 
//	$pdf->SetFont('Arial','',8);
//	$pdf->WriteHTML("N°: $numero_eleve ");
	$pdf->SetXY($Xv1 + 33,40);
//	$pdf->WriteHTML("- Né(e) le $datenaissance");
	$pdf->SetXY($Xv1,44); 
//	$pdf->WriteHTML("Regime: $regime ");
	$pdf->SetXY($Xv1+ 33,44);
//	$pdf->WriteHTML("Classe ant.: $class_ant ");
/*
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($Xv1+$Xv11,36);
	$chaine=LANGBULL44." ".trim(strtoupper($nomtuteur))." ".trim(ucwords(strtolower($prenomtuteur)));
	$pdf->WriteHTML(trunchaine($chaine,30));
	$pdf->SetXY($Xv1+$Xv11,42);
	$chaine=trim($num_adr1)." ".trim($adr1);
	$pdf->WriteHTML(trunchaine($chaine,30));;
	$pdf->SetXY($Xv1+$Xv11,48);
	$chaine=trim($code_post_adr1)." ".trim(strtoupper($commune_adr1));
	$pdf->WriteHTML(trunchaine($chaine,30));
 */
}


// cadre des notes
// ---------------
// Barre des titres
$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(220);
$pdf->SetXY(15,60);
$pdf->MultiCell(184,8,'',1,'C',1);
$pdf->SetXY(19,62);
$pdf->WriteHTML($titrenote1);
$pdf->SetX(60+8);
$pdf->WriteHTML($titrenote2);
$pdf->SetX(125);
$pdf->WriteHTML($titrenote3);
// fin des titres

// Mise en place des matieres et nom de prof
$Xmat=15;
$Ymat=68;
$Xmatcont=15;
$Ymatcont=$Ymat+1;

$Xprof=55;
$Yprof=$Ymat;
$XnomProfcont=56;
$YnomProfcont=$Ymatcont;
$Xnote=$Xmat + 80;
$Ynote=$Ymat;
$YnotVal=$Ynote ;
$YsujetNote=$YnotVal + 2;
$hauteurMatiere=$_POST["hauteur"];

for($i=0;$i<count($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$nomprof=recherche_personne2($ordre[$i][1]);
	$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
	if ($verifGroupe) { continue; } // verif pour l eleve de l affichage de la matiere

	if ($Ynote >= 250) {
		$pdf->AddPage();
		$Xmat=15;
		$Ymat=20;
		$Xmatcont=16;
		$Ymatcont=20;

		$Xprof=55;
		$Yprof=$Ymat;
		$XnomProfcont=56;
		$YnomProfcont=$Ymatcont;
		$Xnote=$Xmat + 80;
		$Ynote=$Ymat;
		$YnotVal=$Ynote;
		$YsujetNote=$YnotVal + 2;
	}

	$XnotVal=$Xnote + 1 ;
	$XsujetNote=$XnotVal;
	// mise en place des notes
	$note=recupNote($idEleve,$ordre[$i][0],$dateDebut,$dateFin);
	// note,elev_id,code_mat,date,sujet,typenote
	$aaa=0;
	for($b=0;$b<count($note);$b++) {
		$aaa++;
		$noteaff=$note[$b][0];
		$sujet=$note[$b][4];
		$typenote=$note[$b][5];
		$notationSur=$note[$b][6];
		if ($ordre[$i][0] == $note[$b][2]) {
			if ($note[$b][0] == -1.00) { $noteaff="abs"; }
			if ($note[$b][0] == -2.00) { $noteaff="disp"; }
			if ($note[$b][0] == -3.00) { $noteaff="";$sujet=""; }
			if ($note[$b][0] == -4.00) { $noteaff="DNN"; }
			if ($note[$b][0] == -5.00) { $noteaff="DNR"; }
			if ($note[$b][0] == -6.00) { $noteaff="VAL"; }
			if ($note[$b][0] == -7.00) { $noteaff="NVAL"; }


			if (trim($typenote) == "en") {
				$noteaff=recherche_note_en($note[$b][0]);
			}else{
				$noteaff=preg_replace('/\.00/',"",$noteaff);
				$noteaff=preg_replace('/\.50/',".5",$noteaff);
				if ($notationSur == "") { $notationSur=20; }
				if (($noteaff != "") && ($noteaff != "abs") &&($noteaff != "disp") &&($noteaff != "DNN")&& ($noteaff != "DNR") && ($noteaff != "VAL") ){  
					$notationSur="$notationSur"; 
				}else{
					$notationSur="";
				}
			}

			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($XnotVal+8,$YnotVal);
			if ($notationSur != "") {
				$moyP=$notationSur/2;
				if ($noteaff < $moyP) {
					$font="<FONT COLOR='RED'>";
					$fontF='</FONT>';
				}else{
					$font='';
					$fontF='';
				}
				$notationSur="/$notationSur";
			}

			if (trim($typenote) == "en") {
				$pdf->WriteHTML("$noteaff");
			}else{
				$pdf->WriteHTML("$font$noteaff$fontF$notationSur");
			}
			$notationSur="";
			$font='';
			$fontF='';
			$pdf->SetXY($XsujetNote+8,$YsujetNote);
			$pdf->SetFont('Arial','',6);
			$sujet = strtolower(substr($sujet, 0, 6));  // decoupe la chaine du sujet
			$pdf->WriteHTML("<i>".$sujet."</i>");
			$pdf->SetXY($XnotVal+8,$YnotVal);
			$XnotVal=$XnotVal + 9;
			$XsujetNote=$XsujetNote + 9;

			if ($aaa >= 10) {
				/*
				$YnotVal= $YnotVal + 10;
				$XnotVal= 95 ;
				$XsujetNote=$XnotVal;
				$YsujetNote=$YnotVal + 4;
				*/
				$aaa=0;
				break;



			}

			continue;
		}
	}
	$YnotVal=$YnotVal + $hauteurMatiere;
	$YsujetNote=$YsujetNote + $hauteurMatiere;

	// mise en place des matieres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell(48,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),25).'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place des noms professeurs
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($Xprof+8,$Yprof);
	$pdf->MultiCell(40,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($XnomProfcont+8,$YnomProfcont);
	$pdf->WriteHTML(ucwords(trunchaine($nomprof,20)));
	$Yprof=$Yprof + $hauteurMatiere;
	$YnomProfcont=$YnomProfcont + $hauteurMatiere;
	// mise en place du cadre note
	$pdf->SetXY($Xnote+8,$Ynote);
	$pdf->MultiCell(96,$hauteurMatiere,'',1,'',0);
	$Ynote=$Ynote + $hauteurMatiere;


}

if ($Ynote >= 240) {
	$pdf->AddPage();
	$Ynote=15;
}


// fin de la mise en place des matiere
$pos1=$Ynote + 5 ;
// coordonée
$pos2 = $pos1 + 5;
$pos3 = $pos2 + 8;
$pos4 = $pos3 + 5;

// fin notes
// --------
// cadre appréciation

$comEleve=recherche_com_profP($idEleve,$_POST["nom_periode"]);

$pdf->SetFont('Arial','',7);
$pdf->SetFillColor(220); // 220
$pdf->SetXY(15,$pos2); // +5 en bas
$pdf->MultiCell(184,8,$appreciation,1,'C',1);
$pdf->SetXY(15,$pos3); // +7 en bas
$pdf->MultiCell(184,28,'',1,'C',0);
$pdf->SetXY(18,$pos4);// +7 en bas
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(140,6,$comEleve,0,'L',0);
$pdf->SetXY(160,$pos4+12);// +7 en bas
//$pdf->SetFont('Arial','',6);
//$pdf->MultiCell(40,6,"[ Signature ]",0,'C',0);
$pdf->SetFont('Arial','',7);

if ($_POST["type_pdf"] == "pers"){
	$classe_nom=TextNoAccent($classe_nom);
	$classe_nom=TextNoCarac($classe_nom);
	$classe_nom=preg_replace('/\//',"_",$classe_nom);
	if (!is_dir("./data/pdf_bull/periode_${classe_nom}_$period")) { mkdir("./data/pdf_bull/periode_${classe_nom}_$period"); }
	$fichier=urlencode($fichier);
	$fichier="./data/pdf_bull/periode_${classe_nom}_$period/periode_".$nomEleve."_".$prenomEleve."_".$period.".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);
	$pdf->close();
	$pdf=new PDF();

}

//FIN appréciation
// sortie dans le fichier
} // fin du for on passe à l'eleve suivant



$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);

if ($_POST["type_pdf"] == "global"){
	$classe_nom=TextNoAccent($classe_nom);
	$classe_nom=TextNoCarac($classe_nom);
	$classe_nom=preg_replace('/\//',"_",$classe_nom);
	$fichier="./data/pdf_bull/periode_".$classe_nom."_".$period.".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);
	$bttexte=LANGPARAM33;
}

// fin PDF

if ($_POST["type_pdf"] == "pers"){
	include_once('./librairie_php/pclzip.lib.php');
	@unlink('./data/pdf_bull/periode_'.$classe_nom.'_'.$period.'.zip');
	$archive = new PclZip('./data/pdf_bull/periode_'.$classe_nom.'_'.$period.'.zip');
	$archive->create('./data/pdf_bull/periode_'.$classe_nom.'_'.$period);
	$fichier='./data/pdf_bull/periode_'.$classe_nom.'_'.$period.'.zip';
	$bttexte="Récupérer les fichiers PDF";
	@nettoyage_repertoire('./data/pdf_bull/periode_'.$classe_nom.'_'.$period);
	@rmdir('./data/pdf_bull/periode_'.$classe_nom.'_'.$period);
}


// fin PDF

?>


<br>
<ul><ul>
<input type=button onclick="open('visu_pdf_bulletin.php?id=<?php print $fichier?>&idclasse=<?php print $_POST["saisie_classe"] ?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul></ul>
<br /><br />
     <!-- // fin  -->
     </td></tr></table>
     </form>

     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
   </BODY></HTML>
<?php
// gestion d'historie
/*
$data=destructionPeriode($fichier,$classe_nom,$_POST[nom_periode],$dateDebut,$dateFin);
for ($i=0;$i<count($data);$i++) {
	unlink($data[$i][1]);
	supp_history_periode($data[$i][0]);
}
*/
$dateDebut=dateFormBase($dateDebut);
$dateFin=dateFormBase($dateFin);
$cr=historyPeriode($fichier,$classe_nom,$_POST["nom_periode"],$dateDebut,$dateFin);
	if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION RELEVE","Classe : $classe_nom");
        	// alertJs("Periode créé -- Service Triade");
	}else{
		error(0);
	}
fin_prog($debut);
Pgclose();
?>
