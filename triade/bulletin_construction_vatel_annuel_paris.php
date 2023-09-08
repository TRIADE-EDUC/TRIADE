<?php
session_start();
error_reporting(0);
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
	set_time_limit(0);
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
include_once('librairie_php/fonctions_vatel.php');
validerequete("2");
$cnx=cnx();
$debut=deb_prog();
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
$dateDebut='';
$dateFin='';
if (count($valeur)) {

if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="1er TRIMESTRE"; $Trimestre = "1er  TRIMESTRE"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="2ieme TRIMESTRE"; $Trimestre = "2ieme TRIMESTRE"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre="3ieme TRIMESTRE"; $Trimestre = "3ieme TRIMESTRE"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="1er SEMESTRE";$Trimestre = "1er  SEMESTRE";$ue_semestre = "1"; }    // modif ambis
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="2eme SEMESTRE";$Trimestre = "2eme SEMESTRE";$ue_semestre = "2"; } // modif ambis
}



// recupe du nom de la classe
$titre=$_POST["saisie_titre"];
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print ucwords($textTrimestre)?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l etablissement
$data=visu_param();
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=strtolower(trim($data[$i][1]));
       $postal=trim($data[$i][2]);
       $ville=strtolower(trim($data[$i][3]));
	   $pays=strtolower(trim($data[$i][9]));
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
}
// fin de la recup


// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutS1=dateForm($dateDebut);
$dateFinS1=dateForm($dateFin);


$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutS2=dateForm($dateDebut);
$dateFinS2=dateForm($dateFin);

$dateDebutInfo=$dateDebutS1;
$dateFinInfo=$dateFinS2;


$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere
$ue=gestion_ue($idClasse,$ue_semestre); 

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();

$pdf=new PDF();  // declaration du constructeur
$pdf->SetDrawColor(0);
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

$ue_noteEle=0; // modif ambis - pour calcule moyenne  par UE
$ue_coeffEle=0; // modif ambis - pour calcule moyenne  par UE
$nb_mat_gen=0;
$nbeleve=0;

// pour le calcul de moyenne classe
$moyenClasseGenPartiel=calculMoyenClasseVatel($idClasse,$eleveT,$dateDebutS1,$dateFinS1,$ordre,'partiel');
$moyenClasseGenPeriode=calculMoyenClasseVatel($idClasse,$eleveT,$dateDebutS1,$dateFinS1,$ordre,'periode');
$moyenClasseGenPartiel=calculMoyenClasseVatel($idClasse,$eleveT,$dateDebutS2,$dateFinS2,$ordre,'partiel');
$moyenClasseGenPeriode=calculMoyenClasseVatel($idClasse,$eleveT,$dateDebutS2,$dateFinS2,$ordre,'periode');

if ($moyenClasseGen < 0 ) { $moyenClasseGen=""; }

// Calcul moyenne generale classe des partiels
// ----------------------------
// calcul min et max general
//-------------------------
$max_partiel="";
$min_partiel=1000;
$max_periode="";
$min_periode=1000;
// initalisation des tableaux
$periode = array(0=> array ('min'=>'','max'=>''),1=> array ('min'=>'','max'=>''),2=> array ('min'=>'','max'=>''),3=> array ('min'=>'','max'=>''),4=> array ('min'=>'','max'=>''),5=> array ('min'=>'','max'=>''),6=> array ('min'=>'','max'=>''),7=> array ('min'=>'','max'=>''),8=> array ('min'=>'','max'=>''),9=> array ('min'=>'','max'=>''),10=> array ('min'=>'','max'=>''),11=> array ('min'=>'','max'=>''),12=> array ('min'=>'','max'=>''),13=> array ('min'=>'','max'=>''),14=> array ('min'=>'','max'=>''),15=> array ('min'=>'','max'=>''));
$partiel = array(0=> array ('min'=>'','max'=>''),1=> array ('min'=>'','max'=>''),2=> array ('min'=>'','max'=>''),3=> array ('min'=>'','max'=>''),4=> array ('min'=>'','max'=>''),5=> array ('min'=>'','max'=>''),6=> array ('min'=>'','max'=>''),7=> array ('min'=>'','max'=>''),8=> array ('min'=>'','max'=>''),9=> array ('min'=>'','max'=>''),10=> array ('min'=>'','max'=>''),11=> array ('min'=>'','max'=>''),12=> array ('min'=>'','max'=>''),13=> array ('min'=>'','max'=>''),14=> array ('min'=>'','max'=>''),15=> array ('min'=>'','max'=>''));

	$moyenne_par=array();
	$moyenne_per=array();

$partiel_gen_min=1000;
$partiel_gen_max='';
$periode_gen_min=1000;
$periode_gen_max='';

for($g=0;$g<count($eleveT);$g++) {
	// variable eleve

	$ecrit='';
	$moyenne_ue='';
	$somme_coef=0;
	$somme_coef_gen_partiel=0;
	$moyenne_gen_partiel='';
	$nb_mat=1;
	$nb_mat_gen=0;
	$nb_note_periode_ue=0;	
	$nb_mat_per=0;
	$idEleve=$eleveT[$g][4];	

	for ($nb_ue=0;$nb_ue<count($ue);$nb_ue++) { // boucle pour chaque UE
		$idMatiere=$ue[$nb_ue][4];
		$ordre_recup=recup_ordre($idMatiere,$idClasse);
//		$matiere=chercheMatiereNom($idMatiere);
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre_recup);
		$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$ordre_recup);
		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe = verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre_recup);

		if ($ecrit==$ue[$nb_ue][0]) {
//				$moyenne_ue+=$notepartiel[0][6]*$notepartiel[0][0];
//				$somme_coef+=$notepartiel[0][6];
//			if ($notepartiel[0][0]!=0 && $notepartiel[0][0]!='') {$nb_mat+=1;}
		} else {
			if ($ecrit!='' ) {
			$id_ue = $ecrit;
		//	print ($moyenne_ue.' // ' );	
				if ($moyenne_ue >=0 && is_numeric($moyenne_ue)) { 	
		//	print ($moyenne_ue.'<br>' );
					$moyenne_gen_partiel+=$moyenne_ue;
					$moyenne_ue=$moyenne_ue/$somme_coef; 
					$somme_coef_gen_partiel+=$somme_coef;				
					if ($partiel[$id_ue]['min']=='') {$partiel[$id_ue]['min']=$moyenne_ue;}
				//	print ("min:".$partiel[$id_ue]['min']." - moy:".$moyenne_ue."<br>");
					if ($moyenne_ue < $partiel[$id_ue]['min']) { $partiel[$id_ue]['min']=$moyenne_ue; }
					if ($moyenne_ue > $partiel[$id_ue]['max']) { $partiel[$id_ue]['max']=$moyenne_ue; }
				}
//				print ('ID:'.$id_ue.'('.$nb_ue.') -$moyenne_ue:'.$moyenne_ue.'  -  $partiel min:'. $partiel[$id_ue]['min'] .' -partiel max:'.$partiel[$id_ue]['max'].'<br>');
	//			if ( $moyenne_gen_partiel > -1 && $moyenne_gen_partiel!='') {}
				$moyenne_ue='';
				$somme_coef=0;	
				
				// moyenne periode
				if ($nb_note_periode_ue>0 ) {
					$moyenne_periode_ue=$moyenne_periode_ue/$nb_mat_per;
					$moyenne_periode_gen+=$moyenne_periode_ue;
					// print ('('.$moyenne_periode_ue.')'.$moyenne_periode_gen.'-');
				} else {
					$moyenne_periode_ue='';
				}
				if ($moyenne_periode_ue>=0 && is_numeric($moyenne_periode_ue)) {
					if ($periode[$id_ue]['min']=='') {$periode[$id_ue]['min']=$moyenne_periode_ue;}
					if ($moyenne_periode_ue < $periode[$id_ue]['min']) { $periode[$id_ue]['min'] = $moyenne_periode_ue; }
					if ($moyenne_periode_ue > $periode[$id_ue]['max']) { $periode[$id_ue]['max'] = $moyenne_periode_ue; }
					$nb_mat_gen++;
				}
//				print ('ID:'.$id_ue.'('.$nb_ue.') -$moyenne_periode_ue:'.$moyenne_periode_ue .'  -  $periode min:'. $periode[$id_ue]['min']. ' -periode max:'.$periode[$id_ue]['max'].'<br>');
				$nb_mat_per=0;
				$moyenne_periode_ue=0;
				$nb_mat=1;
				$nb_note_periode_ue=0;
			}
//			$nom_ue=$ue[$nb_ue][2];
			$ecrit=$ue[$nb_ue][0];
	
			$moyenne_ue=$moyenne_ue/$somme_coef;
		}

//modif AMBIS 04/06/10 suite prb de rang	
		$notepartiel = recupNotepartiel($idEleve,$idMatiere,$dateDebutS1,$dateFinS2,$idClasse);
//modif AMBIS 04/06/10
//		if ($notepartiel[0][0] > -1  && $notepartiel[0][0]!='') { 
//			$nb_mat+=1;
//			$moyenne_ue+=$notepartiel[0][6]*$notepartiel[0][0];
//			$somme_coef+=$notepartiel[0][6];	
//		}

// ajout modif AMBIS 04/06/10
		for ($nb_note=0;$nb_note<count($notepartiel);$nb_note++) { 
//			if ($notepartiel[$nb_note][0]> -1  && $notepartiel[$nb_note][0]!='' ||  $notepartiel[$nb_note][0]==0  ) {
			if ($notepartiel[$nb_note][0]>=0 ) {
				$nb_mat+=1;
				$moy_partiel_eu+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
				$som_coef_partiel_eu+=$notepartiel[$nb_note][6];
				$moyenne_ue+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
				$somme_coef+=$notepartiel[$nb_note][6];
				$affich_coef_partiel =$notepartiel[$nb_note][6];
			} else {
				$affich_coef_partiel="";}
		}
// fin Ajout

	// clacul moyenne periode	
		$noteperiode= recupNoteperiode($idEleve,$idMatiere,$dateDebutS1,$dateFinS2);
	
//		$liste_note='';
		$moyenne_periode='';
		$nb_note_periode=0;
		$somme_coef_periode=0;
		for ($nb_note=0;$nb_note<count($noteperiode);$nb_note++) { 
			if ($noteperiode[$nb_note][0] >=0 && is_numeric($noteperiode[$nb_note][0]) && count($noteperiode)>0) {
				$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
//				$liste_note = $liste_note.$noteperiode[$nb_note][0]." - ";
				$nb_note_periode+=1;
				$somme_coef_periode+=$noteperiode[$nb_note][6];
			} else {
//				$liste_note = $liste_note." Abs. - ";			
			}
		}
		$moyenne_periode=$moyenne_periode/$somme_coef_periode;
		$moyenne_periode_ue+=$moyenne_periode; 
		if ($nb_note_periode>0) {$nb_note_periode_ue++;$nb_mat_per++;}
		if ($nb_ue==count($ue)-1) {$id_ue = $ue[$nb_ue][0];}
	} // fin boucle UE

	// moyenne UE de partiel

	if ($moyenne_ue >=0 ) {
		$moyenne_gen_partiel+=$moyenne_ue;
		$moyenne_ue=($moyenne_ue/$somme_coef)*1; 	
		// print ($moyenne_ue.'***'.$id_ue.'<br>'); // && $partiel[$id_ue]['min']!=0
		if ($partiel[$id_ue]['min']=='' ) {$partiel[$id_ue]['min']=$moyenne_ue;}
		$somme_coef_gen_partiel+=$somme_coef;
		if ($moyenne_ue < $partiel[$id_ue]['min']) { $partiel[$id_ue]['min']=$moyenne_ue; }
		if ($moyenne_ue > $partiel[$id_ue]['max']) { $partiel[$id_ue]['max']=$moyenne_ue; }
	}
	// moyenne UE de periode			

	$moyenne_periode_ue=$moyenne_periode_ue/$nb_mat_per;	
	$moyenne_periode_gen += $moyenne_periode_ue; 
	
//  $moyenne_periode_ue > -1 && $moyenne_periode_ue!=''

	if ($moyenne_periode_ue >=0 && is_numeric($moyenne_periode_ue)) {
		if ($periode[$id_ue]['min']=='') {$periode[$id_ue]['min']=$moyenne_periode_ue;}
		$nb_mat_gen++;
		if ($moyenne_periode_ue < $periode[$id_ue]['min']) { $periode[$id_ue]['min']=$moyenne_periode_ue; }
		if ($moyenne_periode_ue > $periode[$id_ue]['max']) { $periode[$id_ue]['max']=$moyenne_periode_ue; }
		// print ('//'.$moyenne_periode_ue.'//'.$nb_mat_gen.'.........');
	}		

// print ($moyenne_periode_gen .' / '.$nb_mat_gen.'<br>');
	$moyenne_periode_gen= $moyenne_periode_gen / ($nb_mat_gen);
	$moyenne_per[]= $moyenne_periode_gen;
	//if ($moyenne_periode_gen <= $periode_gen_min) { $periode_gen_min=$moyenne_periode_gen; }
	//if ($moyenne_periode_gen >= $periode_gen_max) { $periode_gen_max=$moyenne_periode_gen; }
		
	$moyenne_ue='';
	$moyenne_periode_ue='';
	$nb_mat=1;
// print ('$moyenne_gen_partiel :'.$moyenne_gen_partiel.' - $somme_coef_gen_partiel :'.$somme_coef_gen_partiel.'<br>');
	$moyenne_gen_partiel= ($moyenne_gen_partiel / $somme_coef_gen_partiel);
	$moyenne_par[]=$moyenne_gen_partiel;
	//if ($moyenne_gen_partiel <= $partiel_gen_min) { $partiel_gen_min=$moyenne_gen_partiel; }
	//if ($moyenne_gen_partiel >= $partiel_gen_max) { $partiel_gen_max=$moyenne_gen_partiel; }
//	print ('$moyenne_gen_partiel:'.$moyenne_gen_partiel.' - $partiel_gen_max: ');
			$moyenne_gen_partiel='';
			$somme_coef_gen_partiel=0;
			$moyenne_periode_gen='';
// fin notes
}
//print_r($partiel);
rsort($moyenne_par);
// print_r($moyenne_par);
//print ($moyenne_par[0].'---'.end($moyenne_par));
//print ('<br>----<br>');
sort($moyenne_per);
// print_r($moyenne_per);
// print ($moyenne_per[0].'---'.end($moyenne_per));
// fin min et max
// -------------

$moyenne_periode_gen='';
$moyenne_partiel_gen='';
$moyenne_ue='';
$somme_coef=0;
$somme_coef_gen_partiel=0;
$moyenne_gen_partiel='';
$nb_mat=1;
for ($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	$pdf->AddPage();
//	$pdf->SetTitle("RELEVE DES NOTES - $nomEleve $prenomEleve");
	$pdf->SetTitle("RELEVE DES NOTES - $classe_nom");
	$pdf->SetCreator("VATEL");
	$pdf->SetSubject("Période $textTrimestre ");                // AJOUTER DETE DE DEBUT et FIN DE PERIODES
	$pdf->SetAuthor("VATEL - www.vatel.fr"); 


	// declaration variable
	$coordonne0=$adresse;
	$coordonne0.=" - ".$postal." - ".ucwords($ville)." - ".ucwords($pays);
	$coordonne1="Tel : ".$tel."<BR>";
	$coordonne2="$urlsite";



	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="<b>$nomEleve</b> $prenomEleve";
	$nomprenom=trunchaine($nomprenom,30);

	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=$classe_nom;

	// FIN variables


	// mise en place du logo
	$logo="./image/banniere/banniere-vatel.jpg";					 // AMBIS mettre logo vatel
	if (file_exists($logo)) {
		$xlogo=118;
		$ylogo=93;
		$xcoor0=30;
		$ycoor0=3;
		$pdf->Image($logo,23,5,25,25);
//		$pdf->Image($logo); // modif ambis 12/02/09 pour resoudre prb erreur defichier PDF pour ecole bordeaux classe L2B  prob de marge
	}
	// fin du logo
	//

	// insertion de la Annee SCOLAIRE
//	$pdf->SetFont('Arial','',8);
//	$pdf->SetXY(175,0);
//	$pdf->MultiCell(25,10,"$Trimestre Année scolaire $anneeScolaire",1,'C',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(165,0);
	$pdf->MultiCell(25,24,"",1,'C',0);
			
			$pdf->SetXY(165,0);
			//$pdf->WriteHTML($Trimestre);
			
			$pdf->SetXY(167,8);
			$pdf->WriteHTML("Année scolaire");
			
			$pdf->SetXY(169,16);
			$pdf->WriteHTML($anneeScolaire);
////	$pdf->SetXY(180,0.5);
////	$pdf->WriteHTML();
	// fin d'insertion



	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(55,23);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetXY(55,23+3);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY(55,23+6);
	$pdf->WriteHTML($coordonne2);

	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
	for ($ik=0;$ik<=count($dataadresse);$ik++) {
		$nomtuteur=$dataadresse[$ik][1];
		$prenomtuteur=$dataadresse[$ik][2];
		$adr1=$dataadresse[$ik][3];
		$code_post_adr1=$dataadresse[$ik][4];
		$commune_adr1=$dataadresse[$ik][5];
		$numero_eleve=$dataadresse[$ik][9];
		$datenaissance=$dataadresse[$ik][11];
		if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
		$regime=$dataadresse[$ik][12];
		$class_ant=trim(trunchaine($dataadresse[$ik][10],20));


		$Xv1=55;
		$Yv1=10;
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($Xv1,$Yv1); 
		$pdf->WriteHTML("<b>NOM Prénom</b> : $nomprenom");
		$pdf->SetXY($Xv1,$Yv1+4);
		
		// Modif AMBIS 03/11/09
		// mise en commentaire suite modif nom de classe
		if (substr($classe_nom,-1,1)=='*') {$pos_annee=substr($classe_nom,-2,1)+0;} else {$pos_annee=substr($classe_nom,-1,1)+0;} // Test si le nom de la classe se termine par * pour recup l'annee du cursus
		if (strtolower(substr($classe_nom,0,1))=='m' && ($pos_annee==1 || $pos_annee==2 || $pos_annee==3)) {
			//print ('**'.$pos_annee.'**');
			if ($pos_annee+0==1) {
				$lib_classe= " Licence 1ère année "; 
			} else {
				$lib_classe= " Licence ".$pos_annee."ème année "; 
			}
		} elseif (strtolower(substr($classe_nom,0,1))=='m' && $pos_annee=='4')  {
			$lib_classe= " Master 1ère année";
		} elseif (strtolower(substr($classe_nom,0,1))=='m' && $pos_annee=='5')  {
			$lib_classe= " Master 2ème année";	
		} elseif (strtolower(substr($classe_nom,0,1))=='p') {
			$lib_classe= " Classe Préparatoire";
		} elseif (strtolower(substr($classe_nom,0,1))=='b') {
			$lib_classe= " Bachelor";
		}  else {
			$lib_classe= " A définir";
		}
		
		// Modif AMBIS 03/11/09
		// mise en commentaire suite modif nom de classe 
			
//		if (substr($classe_nom,-1,1)=='1' || substr($classe_nom,-1,1)=='A') {
//		if (substr($classe_nom,-1,1)=='1') { // modif AMBIS 20/03/09
//			$lib_classe .=" 1ère année ";
//		} elseif (substr($classe_nom,-1,1)=='2'  || substr($classe_nom,-1,1)=='B') { 
//		} elseif (substr($classe_nom,-1,1)=='2' ) { // modif AMBIS 20/03/09
//			$lib_classe .=" 2ème année ";
//		}


// 		$pdf->WriteHTML("<b>Classe </b>: $lib_classe - ".strtoupper($classe_nom));
		$pdf->WriteHTML("<b>Classe </b>: ".Vatel_Classe_desc($idClasse)." - ".strtoupper($classe_nom)); // modif pour integrer le descriptif long de la classe

	}

	$Xmat=20;
	$Ymat=$Yv1+24; //	$Ymat=$Yv1+30;

	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell(180,7,'BULLETIN ANNUEL',1,'C',0);
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($Xmat+117,$Ymat+1);
	$pdf->WriteHTML('Période du '.$dateDebutInfo.' au '.$dateFinInfo);

	// Mise en place des matieres


	$Ymat+=10;
	$ii=0;

	$largeurMat=40;
	$hauteurMatiere=5;
	$largeurBloc=20;
// premiere ligne
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,6,"Unités d'Enseignement",1,'L',0);
	$pdf->SetXY($Xmat+$largeurMat,$Ymat);
	$pdf->SetFillColor(198,209,229);  // couleur du cadre de l'eleve
	$pdf->MultiCell(60,6,'PARTIELS',1,'C',1);
	$pdf->SetFillColor(255);  // fond blanc
	$Xmat1=$Xmat+$largeurMat+60;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(80,6,'CONTROLES CONTINUS',1,'C',0);

	$Xmat=20;
	$Ymat=$Ymat+6;	
// deuxieme ligne
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,6,'Matières',1,'C',0);
	$pdf->SetXY($Xmat+$largeurMat,$Ymat);
	$pdf->SetFillColor(198,209,229);  // couleur du cadre de l'eleve
	$pdf->MultiCell(15,6,'Coef.',1,'C',1);
	$pdf->SetFillColor(198,209,229);  // fond blanc
	$Xmat1=$Xmat+$largeurMat+15;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(15,6,'Partiel 1',1,'C',1);

	$Xmat1=$Xmat1+15;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(15,6,'Partiel 2',1,'C',1);

	$Xmat1=$Xmat1+15;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(15,6,'Moyenne',1,'C',1);


	$Xmat1=$Xmat1+15;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(20,6,'Période 1',1,'C',0);

	$Xmat1=$Xmat1+20;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(20,6,'Période 2',1,'C',0);

	$Xmat1=$Xmat1+20;
	$pdf->SetXY($Xmat1,$Ymat);
	$pdf->MultiCell(40,6,'Moyenne Générale',1,'C',0);

	$Xmat=40;
	$Ymat=$Ymat+$hauteurMatiere;

	$XprofVal=22; // x en nom prof
	$YprofVal=$Ymat + 6; // y en nom du prof

//	$liste_matiere=$_POST["listematiere"];
	$ecrit='';
	$moyenne_ue='';
	$somme_coef=0;
	$somme_coef_gen_partiel=0;
	$moyenne_gen_partiel='';
	$nb_mat=1;
	$nb_mat_per=0;
	$nb_mat_gen=0;
	$nb_note_periode_ue=0;
	$som_coef_periode_ue=0;
	$som_coef_periode_ue_total=0;

	
for ($nb_ue=0;$nb_ue<count($ue);$nb_ue++) { // boucle pour chaque UE

		if ($Ymat > 267) {
			$Ymat=15;
			$pdf->AddPage();
		}

		$id_ue=$ue[$nb_ue][0];
		$idMatiere=$ue[$nb_ue][4];
		$ordre_recup=recup_ordre($idMatiere,$idClasse);
		$Xmat=20;
	
		$matiere=chercheMatiereNom($idMatiere);
		
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre_recup);
		$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$ordre_recup);

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe = verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre_recup);


		// mise en place des matieres
		$largeurMat=40;
		$hauteurMatiere=5; // taille du cadre matiere


	if ($ecrit==$ue[$nb_ue][0]) {
	//		$moyenne_ue+=$notepartiel[0][6]*$notepartiel[0][0];
	//		$somme_coef+=$notepartiel[0][6];
	//		if ($noteperiode[0][0]!=0 && $noteperiode[0][0]!='') {$nb_mat+=1;}
	} else {
		if ($ecrit!='') {
			//print_r ($partiel);
			$id_ue = $ecrit;
			if ($moyenne_ue >=0 && is_numeric($moyenne_ue)) {
				$moyenne_gen_partiel+=$moyenne_ue;
				$moyenne_ue=$moyenne_ue/$somme_coef;
				$somme_coef_gen_partiel+=$somme_coef;

			}
			$pdf->Line($Xmat,$Ymat,$Xmat+180,$Ymat);
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat,$Ymat);
			$pdf->MultiCell(40,6,' Moyenne min.:'.format_moyenne($partiel[$id_ue]['min']).' - max:'.format_moyenne($partiel[$id_ue]['max']),'T','C',0);
			$pdf->SetFont('Arial','',7);

			if ($nbcoefpartiel1 > 0) { 
				$moyenne_partiel1=$moyenne_partiel1/$nbcoefpartiel1;
				$moyenne_annuelle_partiel1+=$moyenne_partiel1*$nbcoefpartiel1;
				$nbcoef_annuelle_partiel1+=$nbcoefpartiel1;
				$moyenne_annuelle_ue+=$moyenne_partiel1*$nbcoefpartiel1;
				$nbcoef_annuelle_ue+=$nbcoefpartiel1;
				$nbcoefpartiel1=0;
			}
			if ($nbcoefpartiel2 > 0) { 
				$moyenne_partiel2=$moyenne_partiel2/$nbcoefpartiel2;
				$moyenne_annuelle_partiel2+=$moyenne_partiel2*$nbcoefpartiel2;
                                $nbcoef_annuelle_partiel2+=$nbcoefpartiel2;
				$moyenne_annuelle_ue+=$moyenne_partiel2*$nbcoefpartiel2;
				$nbcoef_annuelle_ue+=$nbcoefpartiel2;
				$nbcoefpartiel2=0;
			}

			$pdf->SetXY($Xmat+40+15,$Ymat);
			$pdf->MultiCell(15,6,''.format_moyenne($moyenne_partiel1).'',1,'C',1);
			$pdf->SetXY($Xmat+40+30,$Ymat);
			$pdf->MultiCell(15,6,''.format_moyenne($moyenne_partiel2).'',1,'C',1);
			$pdf->SetXY($Xmat+40+45,$Ymat);
			$pdf->MultiCell(15,6,''.format_moyenne($moyenne_ue).'',1,'C',1);

			unset($moyenne_partiel1);
			unset($moyenne_partiel2);


			//$moyenne_gen_partiel+=($moyenne_ue*$somme_coef);
			$moyenne_ue='';
			$somme_coef=0;
			
			// moyenne periode
//			print ('nbre2 : '.$nb_mat);
			if ($nb_note_periode_ue>0) {
				$moyenne_periode_ue=$moyenne_periode_ue/$nb_mat_per;
				$moyenne_periode_gen+=$moyenne_periode_ue;
			} else {
				$moyenne_periode_ue='';
			}
			$pdf->Line($Xmat,$Ymat,$Xmat+180,$Ymat);
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+120,$Ymat);
		//	$pdf->MultiCell(40,6,' Moyenne min.:'.format_moyenne($periode[$id_ue]['min']).'- max:'.format_moyenne($periode[$id_ue]['max']),'T','C',0);
			$pdf->SetFont('Arial','',7);

                        if ($nbcoefperiode1 > 0) {
				$moyenne_periode1=$moyenne_periode1/$nbcoefperiode1 ;
			}
                        if ($nbcoefperiode2 > 0) {
				$moyenne_periode2=$moyenne_periode2/$nbcoefperiode2 ;
			}

                        $pdf->SetXY($Xmat+100,$Ymat);
                        $pdf->MultiCell(20,6,''.format_moyenne($moyenne_periode1).'',1,'C',0);
			if ($moyenne_periode1 >= 0 ) {
				$moyenne_annuelle_periode1+=$moyenne_periode1;
                        	$nbcoef_annuelle_periode1++;
				$moyenne_periode_annuelle_ue+=$moyenne_periode1;
				$nbcoef_periode_annuelle_ue++;
			}
                        $pdf->SetXY($Xmat+120,$Ymat);
                        $pdf->MultiCell(20,6,''.format_moyenne($moyenne_periode2).'',1,'C',0);
			if ($moyenne_periode2 >= 0 ) {
				$moyenne_annuelle_periode2+=$moyenne_periode2;
	                        $nbcoef_annuelle_periode2++;
				$moyenne_periode_annuelle_ue+=$moyenne_periode2;
				$nbcoef_periode_annuelle_ue++;
			}
                        unset($nbcoefperiode1);
                        unset($moyenne_periode1);
                        unset($nbcoefperiode2);
                        unset($moyenne_periode2);

			$pdf->SetXY($Xmat+140,$Ymat);
			$pdf->MultiCell(40,6,''.format_moyenne($moyenne_periode_ue).'',1,'C',0);
			if ($moyenne_periode_ue>=0 && is_numeric($moyenne_periode_ue)) {
				$nb_mat_gen++;

			}
//			print('$moyenne_periode_ue:'.$moyenne_periode_ue. '--$moyenne_periode_gen : ' .$moyenne_periode_gen);
			$moyenne_periode_ue='';
			$nb_mat=1;
			$Ymat+=5;
			$nb_note_periode_ue=0;
				$nb_mat_per=0;
		}
//		$nb_tot_mat_ue=cpte_matiere_ue($nb_ue);
//		$nom_ue='UE'.$ue[$nb_ue][1].' '.$ue[$nb_ue][2];
		$nom_ue=$ue[$nb_ue][2];
		$ecrit=$ue[$nb_ue][0];

		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($Xmat,$Ymat+2);
		$pdf->MultiCell(180,6,''.strtoupper($nom_ue).'',1,'L',0);
//		$pdf->WriteHTML(''.$nom_ue.'');
		$Ymat+=8;
	////	$moyenne_ue=$moyenne_ue/$somme_coef;
	}
	
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->SetFillColor(198,209,229);  // couleur du cadre de l'eleve
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'','LR','L',0);
		$pdf->SetXY($Xmat+1,$Ymat);
		$pdf->WriteHTML(''.trunchaine(strtoupper(sansaccent(strtolower($matiere))),23).'');
		
		
		// mise en place coef + note partiel 1
		// coeff
		$notepartiel = recupNotepartiel($idEleve,$idMatiere,$dateDebutS1,$dateFinS1,$idClasse);
		$moy_partiel_eu='';
		$som_coef_partiel_eu=0;
		// modif ambis 09/03/10 pour calcul moyenne des notes de partiels dans le cas ou il y a plusieurs  notes de partiel
		for ($nb_note=0;$nb_note<count($notepartiel);$nb_note++) { 
			if ($notepartiel[$nb_note][0]>=0 && is_numeric($notepartiel[$nb_note][0])) {$nb_mat+=1;
				$moy_partiel_eu+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
				$som_coef_partiel_eu+=$notepartiel[$nb_note][6];

				$moyenne_ue+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
				$somme_coef+=$notepartiel[$nb_note][6];
				$affich_coef_partiel =$notepartiel[$nb_note][6];
			} else {
				$affich_coef_partiel="";}
		}
		$moy_coef_partiel="";
		if (count($notepartiel)>0) {$moy_coef_partiel=$som_coef_partiel_eu/count($notepartiel);}
		//print ($idEleve.'**'.$idMatiere.'**'.count($notepartiel).'**'.$moy_coef_partiel.'<br>');
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($Xmat+$largeurMat,$Ymat);
		$pdf->MultiCell(15,$hauteurMatiere,$moy_coef_partiel,'LR','C',1);
		$moy_coef_periode=$moy_coef_partiel;
		$affich_coef_partiel='';
//print($nb_ue.'coef_pa'.$notepartiel[0][6].' ;');
			// note
			if ($moy_partiel_eu >=0 && is_numeric($moy_partiel_eu) ) {
			$affiche_note = $moy_partiel_eu/$som_coef_partiel_eu ;
			} else {
			$affiche_note = "";
			}			
			
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($Xmat+$largeurMat+15,$Ymat);
		$pdf->MultiCell(15,$hauteurMatiere,$affiche_note,'LR','C',1);
		if (($affiche_note >= 0) && (is_numeric($affiche_note))) {
			$affiche_moyenne_partiel+=$affiche_note;
			$nbnotemoyenpartiel++;

			$moyenne_partiel1+=$affiche_note*$moy_coef_partiel;
			$nbcoefpartiel1+=$moy_coef_partiel;
		}

		// Partiel 2
		// -------------------------------------------------------------------------------------
		$notepartiel = recupNotepartiel($idEleve,$idMatiere,$dateDebutS2,$dateFinS2,$idClasse);
		$moy_partiel_eu='';
		$som_coef_partiel_eu=0;
		$affiche_note="";
		// modif ambis 09/03/10 pour calcul moyenne des notes de partiels dans le cas ou il y a plusieurs  notes de partiel
		for ($nb_note=0;$nb_note<count($notepartiel);$nb_note++) { 
			if ($notepartiel[$nb_note][0]>=0 && is_numeric($notepartiel[$nb_note][0])) {$nb_mat+=1;
				$moy_partiel_eu+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
				$som_coef_partiel_eu+=$notepartiel[$nb_note][6];

				$moyenne_ue+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
				$somme_coef+=$notepartiel[$nb_note][6];
				$affich_coef_partiel =$notepartiel[$nb_note][6];
			} else {
				$affich_coef_partiel="";}
		}
		$moy_coef_partiel="";
		if (count($notepartiel)>0) {$moy_coef_partiel=$som_coef_partiel_eu/count($notepartiel);}
		$affich_coef_partiel='';
		// note
		if ($moy_partiel_eu >=0 && is_numeric($moy_partiel_eu) ) {
			$affiche_note = $moy_partiel_eu/$som_coef_partiel_eu ;
		} else {
			$affiche_note = "";
		}
		$pdf->SetXY($Xmat+$largeurMat+15+15,$Ymat);
		$pdf->MultiCell(15,$hauteurMatiere,$affiche_note,'LR','C',1);		
		if (($affiche_note >= 0) && (is_numeric($affiche_note))) {
			$affiche_moyenne_partiel+=$affiche_note;
			$nbnotemoyenpartiel++;

			$moyenne_partiel2+=$affiche_note*$moy_coef_partiel;
			$nbcoefpartiel2+=$moy_coef_partiel;
		}
		// ------------------------------------------------------------------------------------

		if ($nbnotemoyenpartiel != 0) { $affiche_moyenne_partiel=$affiche_moyenne_partiel/$nbnotemoyenpartiel;		}

		$pdf->SetXY($Xmat+$largeurMat+15+15+15,$Ymat);
                if ($affiche_moyenne_partiel != "")      {
                         $pdf->MultiCell(15,$hauteurMatiere,format_moyenne($affiche_moyenne_partiel),'LR','C',1);
                }else{
                         $pdf->MultiCell(15,$hauteurMatiere,'','LR','C',1);
                }
		unset($affiche_moyenne_partiel);
		unset($nbnotemoyenpartiel);

		// -------------------------------------------------------------------------------------
		// Moyenne 1 
		$noteperiode= recupNoteperiode($idEleve,$idMatiere,$dateDebutS1,$dateFinS1);
		$moyenne_periode='';
		$nb_note_periode=0;
		$som_coef_periode=0;
		for ($nb_note=0;$nb_note<count($noteperiode);$nb_note++) { 
			if ($noteperiode[$nb_note][0]>=0 && is_numeric($noteperiode[$nb_note][0]) && count($noteperiode)>0) {
				$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
				if ($noteperiode[$nb_note][6]>1) {$aff_coef='('.$noteperiode[$nb_note][6].')';} else {$aff_coef='';}
				$liste_note = $liste_note.$noteperiode[$nb_note][0].$aff_coef." - ";
				$nb_note_periode+=1;
				$som_coef_periode+=$noteperiode[$nb_note][6];
			}
		}
		$moyenne_periode=$moyenne_periode/($som_coef_periode);
		$som_coef_periode_ue+= $som_coef_periode;
		$som_coef_periode=0;
		$moyenne_periode_ue+=$moyenne_periode;
		if ($nb_note_periode>0) {$nb_note_periode_ue++;$nb_mat_per++;}
		if ($moyenne_periode >=0 && is_numeric($moyenne_periode) && $nb_note_periode>0) { 
			$moyenne_periode=number_format($moyenne_periode,2,'.','');
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+$largeurMat+40+20,$Ymat);
			$pdf->MultiCell(20,$hauteurMatiere,$moyenne_periode,'R','C',0);

			$moyenne_periode1+=$moyenne_periode;
			$nbcoefperiode1++;
		} else {
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+$largeurMat+40+20,$Ymat);
			$pdf->MultiCell(20,$hauteurMatiere,'','R','C',0);		
		}

		//-----------------------------------------------------------------------------------------



		
		//-----------------------------------------------------------------------------------------
		// Moyenne 2 
		$noteperiode= recupNoteperiode($idEleve,$idMatiere,$dateDebutS2,$dateFinS2);
		$moyenne_periode='';
		$nb_note_periode=0;
		$som_coef_periode=0;
		for ($nb_note=0;$nb_note<count($noteperiode);$nb_note++) { 
			if ($noteperiode[$nb_note][0]>=0 && is_numeric($noteperiode[$nb_note][0]) && count($noteperiode)>0) {
				$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
				if ($noteperiode[$nb_note][6]>1) {$aff_coef='('.$noteperiode[$nb_note][6].')';} else {$aff_coef='';}
				$liste_note = $liste_note.$noteperiode[$nb_note][0].$aff_coef." - ";
				$nb_note_periode+=1;
				$som_coef_periode+=$noteperiode[$nb_note][6];
			}
		}
		$moyenne_periode=$moyenne_periode/($som_coef_periode);
		$som_coef_periode_ue+= $som_coef_periode;
		$som_coef_periode=0;
		$moyenne_periode_ue+=$moyenne_periode;
		if ($nb_note_periode>0) {$nb_note_periode_ue++;$nb_mat_per++;}
		if ($moyenne_periode >=0 && is_numeric($moyenne_periode) && $nb_note_periode>0) { 
			$moyenne_periode=number_format($moyenne_periode,2,'.','');
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+$largeurMat+40+20+20,$Ymat);
			$pdf->MultiCell(20,$hauteurMatiere,$moyenne_periode,'R','C',0);

			$moyenne_periode2+=$moyenne_periode;
			$nbcoefperiode2++;
		} else {
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+$largeurMat+40+20+20,$Ymat);
			$pdf->MultiCell(20,$hauteurMatiere,'','R','C',0);		
		}
		//-----------------------------------------------------------------------------------------


		//  
		// -------------------------------------------------------------------------------------
		// Moyenne Général 

		$noteperiode= recupNoteperiode($idEleve,$idMatiere,$dateDebutS1,$dateFinS2);
		$moyenne_periode='';
		$nb_note_periode=0;
		$som_coef_periode=0;
		for ($nb_note=0;$nb_note<count($noteperiode);$nb_note++) { 
			if ($noteperiode[$nb_note][0]>=0 && is_numeric($noteperiode[$nb_note][0]) && count($noteperiode)>0) {
				$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
				if ($noteperiode[$nb_note][6]>1) {$aff_coef='('.$noteperiode[$nb_note][6].')';} else {$aff_coef='';}
				$liste_note = $liste_note.$noteperiode[$nb_note][0].$aff_coef." - ";
				$nb_note_periode+=1;
				$som_coef_periode+=$noteperiode[$nb_note][6];
			}
		}
		$moyenne_periode=$moyenne_periode/($som_coef_periode);
		$som_coef_periode_ue+= $som_coef_periode;
		$som_coef_periode=0;
		$moyenne_periode_ue+=$moyenne_periode;
		if ($nb_note_periode>0) {$nb_note_periode_ue++;$nb_mat_per++;}
		if ($moyenne_periode >=0 && is_numeric($moyenne_periode) && $nb_note_periode>0) { 
			$moyenne_periode=number_format($moyenne_periode,2,'.','');
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+$largeurMat+40+20+20+20,$Ymat);
			$pdf->MultiCell(40,$hauteurMatiere,$moyenne_periode,'R','C',0);
		} else {
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+$largeurMat+40+20+20+20,$Ymat);
			$pdf->MultiCell(40,$hauteurMatiere,'','R','C',0);		
		}
		//-----------------------------------------------------------------------------------------
	

		$Xmat=20;
		$Ymat=$Ymat+$hauteurMatiere;
		if ($nb_ue==count($ue)-1) {$id_ue = $ue[$nb_ue][0];}	


//	} // fin boucle matiere
} // fin boucle UE
// fin de la mise en place des matieres
	// moyenne UE de partiel

//			$moyenne_gen_partiel+=($moyenne_ue*$somme_coef);
			if ($moyenne_ue >=0 && is_numeric($moyenne_ue)){
				//print ($idEleve.'**'.$idMatiere.'**'.$moyenne_ue.'<br>');
				$moyenne_gen_partiel+=$moyenne_ue;
				$moyenne_ue=$moyenne_ue/$somme_coef;						
				$somme_coef_gen_partiel+=$somme_coef;
			}

			if ($nbcoefpartiel1 > 0) { 
				$moyenne_partiel1=$moyenne_partiel1/$nbcoefpartiel1;
				$moyenne_annuelle_partiel1+=$moyenne_partiel1*$nbcoefpartiel1;
				$nbcoef_annuelle_partiel1+=$nbcoefpartiel1;
				$moyenne_annuelle_ue+=$moyenne_partiel1*$nbcoefpartiel1;
				$nbcoef_annuelle_ue+=$nbcoefpartiel1;
				$nbcoefpartiel1=0;
			}
			if ($nbcoefpartiel2 > 0) { 
				$moyenne_partiel2=$moyenne_partiel2/$nbcoefpartiel2;
				$moyenne_annuelle_partiel2+=$moyenne_partiel2*$nbcoefpartiel2;
                                $nbcoef_annuelle_partiel2+=$nbcoefpartiel2;
				$moyenne_annuelle_ue+=$moyenne_partiel2*$nbcoefpartiel2;
				$nbcoef_annuelle_ue+=$nbcoefpartiel2;
				$nbcoefpartiel2=0;
			}

			$pdf->Line($Xmat,$Ymat,$Xmat+180,$Ymat);
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat,$Ymat);
			$pdf->MultiCell(40,6,' Moyenne min.:'.format_moyenne($partiel[$id_ue]['min']).' - max:'.format_moyenne($partiel[$id_ue]['max']),'T','C',0);
			$pdf->SetFont('Arial','',7);

			$pdf->SetXY($Xmat+40+15,$Ymat);
			$pdf->MultiCell(15,6,''.format_moyenne($moyenne_partiel1).'',1,'C',1);
			$pdf->SetXY($Xmat+40+30,$Ymat);
			$pdf->MultiCell(15,6,''.format_moyenne($moyenne_partiel2).'',1,'C',1);
			$pdf->SetXY($Xmat+40+45,$Ymat);
			$pdf->MultiCell(15,6,''.format_moyenne($moyenne_ue).'',1,'C',1);
			$moyenne_ue='';

			unset($moyenne_partiel1);
			unset($moyenne_partiel2);

	// moyenne UE de periode			
//	print ('som coef'.$som_coef_periode_ue);


			$moyenne_periode_ue=$moyenne_periode_ue/($nb_mat_per);

			$moyenne_periode_gen+=$moyenne_periode_ue;
			$som_coef_periode_ue_total+= $som_coef_periode_ue;
			$som_coef_periode_ue=0;
			$pdf->Line($Xmat,$Ymat,$Xmat+180,$Ymat);
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+120,$Ymat);
		//	$pdf->MultiCell(40,6,' Moyenne min.:'.format_moyenne($periode[$id_ue]['min']).' - max:'.format_moyenne($periode[$id_ue]['max']),'T','C',0);
			$pdf->SetFont('Arial','',7);




			if ($nbcoefperiode1 > 0) {
				$moyenne_periode1=$moyenne_periode1/$nbcoefperiode1 ;			
			}
			if ($nbcoefperiode2 > 0) {
				$moyenne_periode2=$moyenne_periode2/$nbcoefperiode2 ;			
			}

			$pdf->SetXY($Xmat+100,$Ymat);
                        $pdf->MultiCell(20,6,''.format_moyenne($moyenne_periode1).'',1,'C',0);
			if ($moyenne_periode1 >= 0 ) {
				$moyenne_annuelle_periode1+=$moyenne_periode1;
                                $nbcoef_annuelle_periode1++;
				$moyenne_periode_annuelle_ue+=$moyenne_periode1;
				$nbcoef_periode_annuelle_ue++;
			}
			$pdf->SetXY($Xmat+120,$Ymat);
                        $pdf->MultiCell(20,6,''.format_moyenne($moyenne_periode2).'',1,'C',0);
			if ($moyenne_periode2 >= 0 ) {
				$moyenne_annuelle_periode2+=$moyenne_periode2;
                                $nbcoef_annuelle_periode2++;
				$moyenne_periode_annuelle_ue+=$moyenne_periode2;
                                $nbcoef_periode_annuelle_ue++;

			}

			$pdf->SetXY($Xmat+140,$Ymat);
			$pdf->MultiCell(40,6,''.format_moyenne($moyenne_periode_ue).'',1,'C',0);
			if ($moyenne_periode_ue >=0 && is_numeric($moyenne_periode_ue) ) {$nb_mat_gen++;}
			$moyenne_periode_ue='';
			$nb_mat=1;
			$Ymat+=8;
			unset($nbcoefperiode1);
			unset($moyenne_periode1);
			unset($nbcoefperiode2);
			unset($moyenne_periode2);

// fin notes
// --------

//--------------------------------//
//Mise en place des moyenens general partiel et periode
// partiel
//print ('$moyenne_periode_gen:'.$moyenne_periode_gen.'- $nb_mat_gen:'.$nb_mat_gen.'<br>');
//print ('<br>--Partiel--<br>');
//print_r ($partiel);
//print ('<br>--periode--<br>');;
//print_r ($periode);

			
                        $pdf->SetXY($Xmat,$Ymat);
                        $pdf->MultiCell(40,6,' Moyenne Annuelle :','1','R',1);
                        $pdf->SetFont('Arial','',7);

                        $pdf->SetXY($Xmat+40+15,$Ymat);
			if ($nbcoef_annuelle_partiel1 > 0) $moyenne_annuelle_partiel1=$moyenne_annuelle_partiel1/$nbcoef_annuelle_partiel1;
                        $pdf->MultiCell(15,6,''.format_moyenne($moyenne_annuelle_partiel1).'',1,'C',1);

                        $pdf->SetXY($Xmat+40+30,$Ymat);
			if ($nbcoef_annuelle_partiel2 > 0) $moyenne_annuelle_partiel2=$moyenne_annuelle_partiel2/$nbcoef_annuelle_partiel2;
                        $pdf->MultiCell(15,6,''.format_moyenne($moyenne_annuelle_partiel2).'',1,'C',1);

                        $pdf->SetXY($Xmat+40+45,$Ymat);
			if ($nbcoef_annuelle_ue > 0) $moyenne_annuelle_ue=$moyenne_annuelle_ue/$nbcoef_annuelle_ue;
                        $pdf->MultiCell(15,6,''.format_moyenne($moyenne_annuelle_ue).'',1,'C',1);

                        $pdf->SetXY($Xmat+100,$Ymat);
			if ($nbcoef_annuelle_periode1 > 0) $moyenne_annuelle_periode1=$moyenne_annuelle_periode1/$nbcoef_annuelle_periode1;
                        $pdf->MultiCell(20,6,''.format_moyenne($moyenne_annuelle_periode1).'',1,'C',0);

                        $pdf->SetXY($Xmat+120,$Ymat);
			if ($nbcoef_annuelle_periode2 > 0) $moyenne_annuelle_periode2=$moyenne_annuelle_periode2/$nbcoef_annuelle_periode2;
                        $pdf->MultiCell(20,6,''.format_moyenne($moyenne_annuelle_periode2).'',1,'C',0);

			$pdf->SetXY($Xmat+140,$Ymat);
			if ($nbcoef_periode_annuelle_ue > 0) $moyenne_periode_annuelle_ue=$moyenne_periode_annuelle_ue/$nbcoef_periode_annuelle_ue;
                        $pdf->MultiCell(40,6,''.format_moyenne($moyenne_periode_annuelle_ue).'',1,'C',0);
			$moyenne_periode_gen=format_moyenne($moyenne_periode_annuelle_ue);
		
			unset($moyenne_annuelle_partiel1);
			unset($nbcoef_annuelle_partiel1);
			unset($moyenne_annuelle_partiel2);
			unset($nbcoef_annuelle_partiel2);
			unset($moyenne_annuelle_ue);
			unset($nbcoef_annuelle_ue);
			unset($nbcoef_annuelle_periode1);
			unset($moyenne_annuelle_periode1);
			unset($nbcoef_annuelle_periode2);
			unset($moyenne_annuelle_periode2);
			unset($nbcoef_periode_annuelle_ue);
			unset($moyenne_periode_annuelle_ue);

$Ymat=237; // pour decaller le bloc en bas de la page 230
			$moyenne_gen_partiel=$moyenne_gen_partiel/$somme_coef_gen_partiel;
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat,$Ymat+5);
			$pdf->MultiCell(80,8,'',1,'C',1);
			$pdf->SetXY($Xmat+80,$Ymat+5);
			$pdf->MultiCell(100,8,'',1,'C',0);
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($Xmat+2,$Ymat+1);
			$pdf->WriteHTML('<b>MOYENNE GENERALE ANNUELLE / 20</b>');
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($Xmat+15,$Ymat+5);
			$pdf->WriteHTML('<b> PARTIELS :                              '.format_moyenne($moyenne_gen_partiel).'</b>');
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+30,$Ymat+8);
			$pdf->WriteHTML('Rang  : '.((array_search($moyenne_gen_partiel, $moyenne_par))+1).'/'.count($eleveT).'  - (min.:'.format_moyenne(end($moyenne_par)).' - max:'.format_moyenne($moyenne_par[0]).')');
			$moyenne_gen_partiel='';
			$somme_coef_gen_partiel=0;
//			$pdf->SetFont('Arial','',7);
//			$pdf->SetXY($Xmat+60,$Ymat+3);
//			$pdf->WriteHTML($moyenne_gen_partiel);

// periode
		// print ("moy: ".$moyenne_periode_gen."nb: ".$nb_mat_gen."<br>");
			//$moyenne_periode_gen=$moyenne_periode_gen / ($nb_mat_gen);
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($Xmat+126,$Ymat+5);
			$pdf->WriteHTML('<b> CONTROLE CONTINU :         '.format_moyenne($moyenne_periode_gen).'</b>');
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+150,$Ymat+8);
			$pdf->WriteHTML('(min.:'.format_moyenne($moyenne_per[0]).' - max:'.format_moyenne(end($moyenne_per)).')</b>');
			$moyenne_periode_gen='';
			$nb_mat_gen=0;
//			$pdf->SetFont('Arial','',7);
//			$pdf->SetXY($Xmat+170,$Ymat+3);
//			$pdf->WriteHTML($moyenne_gen_partiel);
			
			$Ymat+=12;			
// FIN Mise en place des moyenens general partiel et periode

	//---------------------------------//
	// recherche le nombre de retard
	$nbretard=0;
	$nbretard1=0;
	$nbheureabs=0;
	$nbjoursabs=0;
	$nbabs=0;
	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebutS1),dateFormBase($dateFinS2)); // ideleve,debutdate,findate
	$nbretard=count($nbretard);
	
	// recherche le nombre d absence
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebutS1),dateFormBase($dateFinS2)); // ideleve,debutdate,findate
	for($o=0;$o<=count($nbabs);$o++) {
		if ($nbabs[$o][4] > 0) {
	       		$nbjoursabs = $nbjoursabs + $nbabs[$o][4];
		}else{
			$nbheureabs = $nbheureabs + $nbabs[$o][7];	
		}
	}
	$nbabs=$nbjoursabs * 2;
	
	$pdf->SetXY($Xmat,$Ymat+2);
	$pdf->MultiCell(180,5,'',1,'',0);
	$pdf->SetXY($Xmat+2,$Ymat+2);
	$pdf->SetFont('Arial','',8);
	//$pdf->WriteHTML("<b>Nombre de 1/2 journées d'absences : </b>".$nbabs .' - <b>Nombre de retards :</b> '.$nbretard);
	$pdf->WriteHTML("<b>Nombre de journées d'absences :                  - nbre d'avertissement / 3 :</b>");
	//---------------------------------//

// cadre appréciation
$Ycom=$Ymat+7;
$Xcom=$Xmat;
$hauteurcom=20;
$largeurcom=180;
$Yappreciation=$Ycom+1;
$pdf->SetXY($Xcom,$Ycom);
$pdf->MultiCell($largeurcom,$hauteurcom,'',1,'',0);
$pdf->SetXY($Xcom+2,$Ycom+1);
$pdf->SetFont('Arial','B',8);
$pdf->WriteHTML("APPRECIATIONS DE LA DIRECTION :");
$pdf->SetDrawColor(200);
$commentairedirection=recherche_com($idEleve,"annuel","default");
$commentairedirection=preg_replace('/\n/'," ",$commentairedirection);
if (trim($commentairedirection) == "") {
        $pdf->Line ($Xcom+2,$Ycom+5,$Xcom+175,$Ycom+5 );
        $pdf->Line ($Xcom+2,$Ycom+10,$Xcom+175,$Ycom+10 );
        $pdf->Line ($Xcom+2,$Ycom+15,$Xcom+175,$Ycom+15 );
}
//$pdf->Line ($Xcom+2,$Ycom+20,$Xcom+175,$Ycom+20 );
//$pdf->Line ($Xcom+2,$Ycom+25,$Xcom+175,$Ycom+25 );
$pdf->SetXY($Xcom+2,$Ycom+5);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(175,3.5,$commentairedirection,'','','L',0); // commentaire de la direction (visa)
$pdf->SetDrawColor(0);
//$montessori=recherchemontessori($idEleve,"montessori_spec");
//if ($montessori == "felicitation")  { $checkedmont1="1"; }else{ $checkedmont1="0"; }
//if ($montessori == "satisfaction")  { $checkedmont2="1"; }else{ $checkedmont2="0"; }
//if ($montessori == "encouragement") { $checkedmont3="1"; }else{ $checkedmont3="0"; }

//$pdf->SetFillColor(000);  // couleur du cadre de l'eleve
//$pdf->SetXY($largeurcom+20,$Yappreciation);
//$pdf->WriteHTML("Félicitations :");
//$pdf->SetXY($largeurcom+50,$Yappreciation+1);
//$pdf->MultiCell(3,3,'',1,'',$checkedmont1);

//$pdf->SetXY($largeurcom+20,$Yappreciation+7);
//$pdf->WriteHTML("Satisfactions :");
//$pdf->SetXY($largeurcom+50,$Yappreciation+8);
//$pdf->MultiCell(3,3,'',1,'',$checkedmont2);

//$pdf->SetXY($largeurcom+20,$Yappreciation+14);
//$pdf->WriteHTML("Encouragements :");
//$pdf->SetXY($largeurcom+50,$Yappreciation+15);
//$pdf->MultiCell(3,3,'',1,'',$checkedmont3);
// fin duplicata
//$pdf->SetFillColor(198,209,229);  // couleur du cadre de l'eleve



//$pdf->SetXY($Xcom,$Ycom+$hauteurcom+2);
//$pdf->WriteHTML("Date :");
//$pdf->SetXY($Xcom,$Ycom+$hauteurcom+10);
//$pdf->WriteHTML("Signature du professeur :");
//$pdf->SetXY($Xcom+100,$Ycom+$hauteurcom+10);
//$pdf->WriteHTML("Signature de la Direction :");


// ----------------------------------------------------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$nomEleve=TextNoCarac($nomEleve);
$nomEleve=TextNoAccent($nomEleve);
$prenomEleve=TextNoCarac($prenomEleve);
$prenomEleve=TextNoAccent($prenomEleve);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$classe_nom=preg_replace('/,/',"_",$classe_nom);
$nomEleve=preg_replace('/\//',"_",$nomEleve);
$prenomEleve=preg_replace('/\//',"_",$prenomEleve);
if (!is_dir("./data/pdf_bull/$classe_nom")) { mkdir("./data/pdf_bull/$classe_nom"); }
$fichier=urlencode($fichier);
$fichier="./data/pdf_bull/$classe_nom/bulletin_".$nomEleve."_".$prenomEleve."_".$_POST["saisie_trimestre"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage($_POST["saisie_trimestre"],$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { $merge->add("$fichier"); }
$listing.="$fichier ";
$pdf=new PDF();
} // fin du for on passe à l'eleve suivant
$merge->output("./data/pdf_bull/$classe_nom/liste_complete.pdf");
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
	$cmd="gs -q -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=./data/pdf_bull/$classe_nom/liste_complete.pdf -dBATCH $listing";
	$null=system("$cmd",$retval);
}
include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/'.$classe_nom.'.zip';
$bttexte="Récupérer le fichier ZIP des bulletins";
@nettoyage_repertoire('./data/pdf_bull/'.$classe_nom);
@rmdir('./data/pdf_bull/'.$classe_nom);
// --------------------------------------------------------------------------------------------------------------------------
?>
<br><ul><ul>
<input type=button onclick="open('visu_pdf_bulletin.php?id=<?php print $fichier?>&idclasse=<?php print $_POST["saisie_classe"] ?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul></ul>
<?php // ----------------------------------------------------------------------------------------------------------------------------   ?>


<br /><br />

<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebutS1,$dateFinS2);
$cr=historyBulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebutS1,$dateFinS2);
if($cr == 1){
	history_cmd($_SESSION["nom"],"CREATION BULLETIN","Classe : $classe_nom");
}else{
	error(0);
}
Pgclose();
?>

<?php
}else {
?>
<br />
<center>
<?php print LANGMESS14?> <br>
<br><br>
<font size=3><?php print LANGMESS15?><br>
<br>
<?php print LANGMESS16?><br>
</center>
<br /><br /><br />
<?php
        }
?>
<!-- // fin  -->
</td></tr></table>
<script language=JavaScript>attente_close();</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();
?>
