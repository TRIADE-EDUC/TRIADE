<?php
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) set_time_limit(900);
include_once('librairie_php/db_triade.php');
include_once('librairie_php/fonctions_vatel.php');
validerequete("3");
$cnx=cnx();
$anneeScolaire=$annee_scolaire;
$valeur=visu_affectation_detail($saisie_classe,$anneeScolaire);

list($null,$anneeESS)=preg_split('/ - /',$anneeScolaire);
$anneeESS=substr($anneeESS,2);
if ($saisie_trimestre == "trimestre1" ) $ESS="FEV";
if ($saisie_trimestre == "trimestre2" )	$ESS="JUN";

$tabECTS=array("0"=>0,"6"=>0,"8"=>0,"10"=>0,"11"=>0,"12"=>0,"13"=>0,"14"=>0,"15"=>0,"16"=>0,"17"=>0,"19"=>0,"20"=>0,"21"=>0,"22"=>0,"23"=>0,"24"=>0,"25"=>0,"26"=>0,"27"=>0,"28"=>0,"29"=>0,"30"=>0);

$nbaffichematiere=20;

if (countTriade($valeur)) {
	if ($typetrisem == "trimestre") {
		if ($saisie_trimestre == "trimestre1"){$textTrimestre="1er TRIMESTRE"; $Trimestre = "1er TRIMESTRE"; }
		if ($saisie_trimestre == "trimestre2"){$textTrimestre="2ieme TRIMESTRE"; $Trimestre = "2ieme TRIMESTRE"; }
		if ($saisie_trimestre == "trimestre3"){$textTrimestre="3ieme TRIMESTRE"; $Trimestre = "3ieme TRIMESTRE"; }
	}
	if ($typetrisem == "semestre") {
		if ($saisie_trimestre == "trimestre1" ) { 
			$textTrimestre="1er SEMESTRE";
			$Trimestre="1er SEMESTRE";
			$ue_semestre="1"; 
		}
		if ($saisie_trimestre == "trimestre2" ) { 
			$textTrimestre="2ieme SEMESTRE";
			$Trimestre="2ieme SEMESTRE";
			$ue_semestre="2"; 
		}
	}
}

// recupe du nom de la classe
$titre="aucun titre";
$data=chercheClasse($saisie_classe);
$classe_nom=$data[0][1];
// recup année scolaire
?>
<ul>
<font class="T2">
      <?php print "Relev&eacute;" ?> : <?php print ucwords($textTrimestre)?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l etablissement
$data=visu_param();
for($i=0;$i<countTriade($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=strtolower(trim($data[$i][1]));
       $postal=trim($data[$i][2]);
       $ville=strtolower(trim($data[$i][3]));
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
}
// fin de la recup

// recherche des dates de debut et fin
// Modif ambis 28/04/10 pour tableau annuel -> si ue trimestre = 3 -> $saisie_trimestre = ""
if (($ue_semestre==3) && ($saisie_trimestre=='')) {// Modif ambis 28/04/10 Tableau demandé pour un an
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$saisie_classe,$anneeScolaire);
	for($j=0;$j<countTriade($dateRecup);$j++) {
		$dateDebut=$dateRecup[$j][0];
	}
	$dateDebut=dateForm($dateDebut);
	$dateRecup=recupDateTrimByIdclasse("trimestre2",$saisie_classe,$anneeScolaire);
	for($j=0;$j<countTriade($dateRecup);$j++) {
		$dateFin=$dateRecup[$j][1];
	}
	$dateFin=dateForm($dateFin);

} else { // Modif ambis 28/04/10 Tableau demandé pour un trimestre
	if ($saisie_trimestre == "Semestre 2") $saisie_trimestre="trimestre2";
	if ($saisie_trimestre == "Semestre 1") $saisie_trimestre="trimestre1";
	$dateRecup=recupDateTrimByIdclasse($saisie_trimestre,$saisie_classe,$anneeScolaire);
	for($j=0;$j<countTriade($dateRecup);$j++) {
		$dateDebut=$dateRecup[$j][0];
		$dateFin=$dateRecup[$j][1];
	}
	$dateDebut=dateForm($dateDebut);
	$dateFin=dateForm($dateFin);
}
$idClasse=$saisie_classe;
$ordre=ordre_matiere($saisie_classe,$anneeScolaire); // recup ordre matiere
$ue=gestion_ue($idClasse,$ue_semestre,false,$anneeScolaire); 
// a.code_ue,a.num_ue,trim(a.nom_ue),m.code_ue_detail,m.code_matiere,a.semestre


// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($saisie_classe); // recup liste eleve

$ue_noteEle=0; // modif ambis - pour calcule moyenne  par UE
$ue_coeffEle=0; // modif ambis - pour calcule moyenne  par UE
$nb_mat_gen=0;
$nbeleve=0;

// pour le calcul de moyenne classe
$moyenClasseGenPartiel=calculMoyenClasseVatel($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,$typeExamen);
$moyenClasseGenPeriode=calculMoyenClasseVatel($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,'periode');
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
			
			$moyenne_matiere_part=array();
			$moyenne_matiere_per=array();
		
		$partiel_gen_min=1000;
		$partiel_gen_max='';
		$periode_gen_min=1000;
		$periode_gen_max='';
		
		
		for($g=0;$g<countTriade($eleveT);$g++) { // pour chaque eleve
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
		
			for ($nb_ue=0;$nb_ue<countTriade($ue);$nb_ue++) { // boucle pour chaque UE
				$idMatiere=$ue[$nb_ue][4];
				$ordre_recup=recup_ordre($idMatiere,$idClasse);
				$idprof=recherche_prof($idMatiere,$idClasse,$ordre_recup);
				$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$ordre_recup);
				if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
		
				// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
				$idgroupe = verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre_recup);
		
				if ($ecrit==$ue[$nb_ue][0]) {
					// rien
				} else {
					if ($ecrit!='' ) {
						$id_ue = $ecrit;

						if (($moyenne_ue >=0) && is_numeric($moyenne_ue)){
						$moyenne_gen_partiel+=$moyenne_ue;
						$moyenne_ue=$moyenne_ue/$somme_coef;						
						$somme_coef_gen_partiel+=$somme_coef;	
						
						if ($partiel[$id_ue]['min']=='') {$partiel[$id_ue]['min']=$moyenne_ue;}
							if ($moyenne_ue < $partiel[$id_ue]['min']) { $partiel[$id_ue]['min']=$moyenne_ue; }
							if ($moyenne_ue > $partiel[$id_ue]['max']) { $partiel[$id_ue]['max']=$moyenne_ue; }
						}
						$moyenne_ue='';
						$somme_coef=0;	
						
						// moyenne periode
					if ($nb_note_periode_ue > 0) {
						$moyenne_periode_ue=$moyenne_periode_ue/$nb_mat_per;
//						$moyenne_periode_gen+=$moyenne_periode_ue;
					} else {
						$moyenne_periode_ue='';
					}
					if (($moyenne_periode_ue>=0) && is_numeric($moyenne_periode_ue)) {
						if ($periode[$id_ue]['min']=='') {$periode[$id_ue]['min']=$moyenne_periode_ue;}
						if ($moyenne_periode_ue < $periode[$id_ue]['min']) { $periode[$id_ue]['min'] = $moyenne_periode_ue; }
						if ($moyenne_periode_ue > $periode[$id_ue]['max']) { $periode[$id_ue]['max'] = $moyenne_periode_ue; }
						$nb_mat_gen++;
					}
					$nb_mat_per=0;
					$moyenne_periode_ue='';
					$nb_mat=1;
					$nb_note_periode_ue=0;
					}
					$ecrit=$ue[$nb_ue][0];
			
					if ($moyenne_ue!=''){$moyenne_ue=$moyenne_ue/$somme_coef;}
				}
			
				$notepartiel = recupNotepartiel($idEleve,$idMatiere,$dateDebut,$dateFin,$idClasse,$typeExamen);
				
				if (($notepartiel[0][0] >=0) && ($notepartiel[0][0] != "")) {
					$nb_mat+=1;
					$moyenne_ue+=$notepartiel[0][6]*$notepartiel[0][0];
					$somme_coef+=$notepartiel[0][6];
					// recup chaque note de partiel pour min max et moyene / matiere
					if ($notepartiel[0][0] >= 0)   {$moyenne_matiere_part[$idMatiere][]=$notepartiel[0][0];}
				}
				// calcul moyenne periode		
				$noteperiode= recupNoteperiode($idEleve,$idMatiere,$dateDebut,$dateFin);
				$moyenne_periode='';
				$nb_note_periode=0;
				$somme_coef_periode=0;
				for ($nb_note=0;$nb_note<countTriade($noteperiode);$nb_note++) { 
					if ($noteperiode[$nb_note][0]>=0 && countTriade($noteperiode)>0) {
						$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
						$nb_note_periode+=1;
						$somme_coef_periode+=$noteperiode[$nb_note][6];
					}
				}
				
			        // recup chaque note de periode pour min max et moyene / matiere
				if (($moyenne_periode>=0) && is_numeric($moyenne_periode)) {
					$moyenne_periode=$moyenne_periode/$somme_coef_periode;
					$moyenne_periode_ue+=$moyenne_periode;
					$moyenne_matiere_per[$idMatiere][]=$moyenne_periode;
				}
				if ($nb_note_periode>0) {$nb_note_periode_ue++;$nb_mat_per++;}
				if ($nb_ue==countTriade($ue)-1) {$id_ue = $ue[$nb_ue][0];}
			} // fin boucle UE
		
			// moyenne UE de partiel
			
			if ($moyenne_ue!='') {}
			if (($moyenne_ue >=0) && is_numeric($moyenne_ue))  {
				$moyenne_gen_partiel+=$moyenne_ue;$moyenne_ue=$moyenne_ue/$somme_coef;
				if ($partiel[$id_ue]['min']=='') {$partiel[$id_ue]['min']=$moyenne_ue;}
				$somme_coef_gen_partiel+=$somme_coef;
				if ($moyenne_ue < $partiel[$id_ue]['min']) { $partiel[$id_ue]['min']=$moyenne_ue; }
				if ($moyenne_ue > $partiel[$id_ue]['max']) { $partiel[$id_ue]['max']=$moyenne_ue; }
			}
			// moyenne UE de periode			
			
			if (($moyenne_periode_ue>=0) && is_numeric($moyenne_periode_ue))  { $moyenne_periode_ue= $moyenne_periode_ue/$nb_mat_per;}
			$moyenne_periode_gen += $moyenne_periode_ue;
			
			if (($moyenne_periode_ue >=0 ) && is_numeric($moyenne_periode_ue)) {
			if ($periode[$id_ue]['min']=='') {$periode[$id_ue]['min']=$moyenne_periode_ue;}
				$nb_mat_gen++;
				if ($moyenne_periode_ue < $periode[$id_ue]['min']) { $periode[$id_ue]['min']=$moyenne_periode_ue; }
				if ($moyenne_periode_ue > $periode[$id_ue]['max']) { $periode[$id_ue]['max']=$moyenne_periode_ue; }
			}		
			if ($nb_mat_gen > 0) $moyenne_periode_gen=$moyenne_periode_gen / ($nb_mat_gen);
		
			$moyenne_per[]= $moyenne_periode_gen;
				
			$moyenne_ue='';
			$moyenne_periode_ue='';
			$nb_mat=1;
			
			if ($somme_coef_gen_partiel > 0) $moyenne_gen_partiel= ($moyenne_gen_partiel / $somme_coef_gen_partiel);
			$moyenne_par[]=$moyenne_gen_partiel;
			
			$moyenne_gen_partiel='';
			$somme_coef_gen_partiel=0;
			$moyenne_periode_gen=0;
		// fin notes
		}
		rsort($moyenne_par);
		if (isset($moyenne_matiere_per[4]) && is_array($moyenne_matiere_per[4])) {
    			array_multisort($moyenne_matiere_per[4], SORT_ASC);
		}
		
		
		//////////////// ICI ///////////////////////// resterecup nombre de note pour calcul moyenne avec array search du end
			

		sort($moyenne_per);
		
		// ------------- FIN CALCUL MOYENNE

$moyenne_periode_gen=0;
$moyenne_partiel_gen=0;
$moyenne_ue='';
$somme_coef=0;
$somme_coef_gen_partiel=0;
$moyenne_gen_partiel='';
$nb_mat=1;
$calc_moy_part=0;
$calc_nb_mat_part=0;
$calc_moy_per=0;
$calc_nb_mat_per=0;
$calc_moy_part_tot=0; 
$calc_nb_mat_part_tot=0;
$calc_moy_per_tot=0; 
$calc_nb_mat_per_tot=0;

$deb=45;
$margeGauche=0;
if ($margeGauche > 0) { $deb+=$margeGauche; $deb+=0; } 
$hauteurMatiere=10;
$lMat_mid = 6 ;   //   Attention  $lMat_mid + $lMat_mid2 = $deb
$lMat_mid2 = 6 ;
$lMat=$lMat_mid+$lMat_mid2;

	$pdf->AddPage('L');

	// mise en place du logo
	$logo="./image/banniere/banniere2-vatel.jpg";					 // AMBIS mettre logo vatel
	if (file_exists($logo)) {
		$xlogo=50;
		$ylogo=39;
		$xcoor0=30;
		$ycoor0=3;
		$pdf->Image($logo,0,0);
	}
	if (trim($margeHaut) != "") { $margeH=$margeHaut; } 
	// fin du logo
	
	$pdf->SetTitle("RELEVE DES NOTES");
	$pdf->SetCreator("TRIADE");
	$pdf->SetSubject("Période $textTrimestre ");                // AJOUTER DETE DE DEBUT et FIN DE PERIODES
	$pdf->SetAuthor("TRIADE - www.triade-educ.org"); 
	// declaration variable
	$coordonne0=$adresse;
	$coordonne0.=" - ".$postal." - ".ucwords($ville)." ";
	$coordonne1="Tel : ".$tel."<BR>";
	$coordonne2="$urlsite";

	$Xmat=$deb;
	$Ymat=8;
	$margeHaut=60;
	$margeH=0;
	if (trim($margeHaut) != "") { $Ymat=$margeHaut; $margeH=$Ymat; } 
	$h_ligne=4;
	$Xmat_ue=$deb;
	$Xmat_t=$deb;
	$ecrit_ue = 1;
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($Xmat+20,$Ymat);
	$pdf->MultiCell(180,$h_ligne,"PROCES VERBAL DE SYNTHESE DES NOTES PAR SEMESTRE",0,'C',0);
	$pdf->SetXY($Xmat+20,$Ymat+$h_ligne);
	$pdf->MultiCell(180,$h_ligne,"CLASSE : $classe_nom / ANNEE SCOLAIRE : $anneeScolaire / SEMESTRE : $textTrimestre ",0,'C',0);
	$pdf->SetFillColor(198,209,229);
		
// boucle pour affiche premieres ligne avec UE, matiere et coeffs		
// a.code_ue,a.num_ue,trim(a.nom_ue),m.code_ue_detail,m.code_matiere,a.semestre
	$Xmat_t+=10;
	$Xmat_ue+=10;
	$Xmat+=10;
	
	$legende=array("NB"=>"Note Non Disponible","EL"=>"Eliminer");

for ($nb_ue=0;$nb_ue<countTriade($ue);$nb_ue++) {
	// affiche  UE
		$nbmatiereUE=nb_matiere_ue($ue[$nb_ue][0]);
		if ($ecrit_ue==1) {
			$pdf->SetFont('Arial','',4);
			$pdf->SetXY($Xmat_t,13+$margeH);
			$pdf->line($Xmat_ue,16+$margeH,$Xmat_ue,28+$margeH);
			$pdf->SetXY($Xmat_ue,15+$margeH);
//			$pdf->WriteHTML($ue[$nb_ue][2]);
			$larg1=$nbmatiereUE*12+6;
			$pdf->SetXY($Xmat_ue+0.1,15+$margeH+1);
			$pdf->MultiCell($larg1,4,$ue[$nb_ue][2],'0','L',1); //ICI
			$ecrit_ue=$ue[$nb_ue][2];
			$id_ue=$ue[$nb_ue][0];	
		}elseif ($ecrit_ue!=$ue[$nb_ue][2]) {
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,16+$margeH);
			$pdf->MultiCell($lMat,4,'','T','C',0); //ICI
			$pdf->SetFont('Arial','',4);
			$pdf->SetXY($Xmat,20+$margeH);
			$pdf->MultiCell(6,5,'','1','C',1);
			$pdf->SetXY($Xmat,20+$margeH+0.5);
			$pdf->MultiCell(6,2,'MOY. UE','0','C',0);

			$pdf->SetFont('Arial','',3);
			$pdf->SetXY($Xmat,20+$margeH+5);	
			$pdf->MultiCell($lMat_mid,5,"",1,'C',1);
			$pdf->SetXY($Xmat,20+$margeH+5+0.5);	
			$pdf->MultiCell($lMat_mid,2,"coef :\nUE : $coeff_recup_eu",0,'C',0);
			$coeff_recup_eu=0;
			$pdf->SetFont('Arial','',4);
	//		$pdf->SetFont('Arial','B',4);
	//		$pdf->SetXY($Xmat,28+$margeH); 
	//		$pdf->SetFillColor(198,209,229);
	//		$pdf->MultiCell($lMat_mid,4,format_moyenne($partiel[$id_ue]['min']),1,'C',1); // affiche moyenne Partiel UE Min
	//		$pdf->SetFont('Arial','B',4);
	//		$pdf->SetXY($Xmat,32+$margeH); 
	//		$pdf->SetFillColor(198,209,229);
	//		$pdf->MultiCell($lMat_mid,4,format_moyenne($calc_moy_part/$calc_nb_mat_part),1,'C',1);	 // affiche moyenne Partiel UE 	 	
	//		$pdf->SetFont('Arial','B',4);
	//		$pdf->SetXY($Xmat,36+$margeH); 
	//		$pdf->SetFillColor(198,209,229);
	//		$pdf->MultiCell($lMat_mid,4,format_moyenne($partiel[$id_ue]['max']),1,'C',1); // affiche moyenne Partiel UE Max

			$calc_moy_part_tot+=$calc_moy_part; // somme des moyennes partiel pour moyenne generale
			$calc_nb_mat_part_tot+=$calc_nb_mat_part; // somme des nbre de matiere de partiel pour moyenne generale
			
			$calc_moy_part=0;
			$calc_nb_mat_part=0;
						
	//		$pdf->SetFont('Arial','B',4);
	//		$pdf->SetXY($Xmat+=$lMat_mid,28+$margeH);
	//		$pdf->MultiCell($lMat_mid,4,format_moyenne($periode[$id_ue]['min']),1,'C',0);  // affiche moyenne Periode UE Min
	//		$pdf->SetFont('Arial','B',4);
	//		$pdf->SetXY($Xmat,32+$margeH);
	//		$pdf->MultiCell($lMat_mid,4,format_moyenne($calc_moy_per/$calc_nb_mat_per),1,'C',0);  // affiche moyenne Periode UE 
	//		$pdf->SetFont('Arial','B',4);
	//		$pdf->SetXY($Xmat,36+$margeH);
	//		$pdf->MultiCell($lMat_mid,4,format_moyenne($periode[$id_ue]['max']),1,'C',0);	// affiche moyenne Periode UE Max	
	//		$pdf->SetLineWidth(0.2);

			$calc_moy_per_tot+=$calc_moy_per; // somme des moyennes periode pour moyenne generale
			$calc_nb_mat_per_tot+=$calc_nb_mat_per; // somme des nbre de matiere de periode pour moyenne generale			
			$calc_moy_per=0;
			$calc_nb_mat_per=0;		
			
			$Xmat_ue+=$lMat_mid*2;
			$pdf->SetFont('Arial','',4);
			$pdf->SetXY($Xmat_t,13+$margeH);
			$pdf->line($Xmat_ue-6,16+$margeH,$Xmat_ue-6,20+$margeH);
		//	$pdf->SetXY($Xmat_ue,15+$margeH);
		//	$pdf->WriteHTML($ue[$nb_ue][2]);
			
			$larg1=$nbmatiereUE*12+6;
			$pdf->SetXY($Xmat_ue+0.1-6,15+$margeH+1);
			$pdf->MultiCell($larg1,4,$ue[$nb_ue][2],'0','L',1); //ICI
			
			$ecrit_ue=$ue[$nb_ue][2];
			$Xmat=$Xmat_ue-6;
			$id_ue=$ue[$nb_ue][0];
		}
	// affiche  matiere
		$Nom_matiere = str_replace("environnement","Env.",chercheMatiereNom($ue[$nb_ue][4]));
		$Nom_matiere = str_replace("ressources humaines","R.H.",$Nom_matiere);
		$Nom_matiere = str_replace("applications","Appli.",$Nom_matiere);
		$Nom_matiere = str_replace("informatique","Info.",$Nom_matiere);				
		$Nom_matiere = str_replace("economie","Eco.",$Nom_matiere);
		$Nom_matiere = str_replace("professionnelle","Prof.",$Nom_matiere);
		if (strlen($Nom_matiere>15)) {$ht=4;} else {$ht=4;}; // modif cyril 9/02/09 else {$ht=8;} pour reduire la hauteur des matieres
	
		$pdf->SetFont('Arial','',3);
		$pdf->SetXY($Xmat,20+$margeH);
		$code_matiere=recupCodeMatiere($ue[$nb_ue][4]);

		$legende[$code_matiere]=$Nom_matiere;

		$pdf->MultiCell(6,5,'',1,'C',1);
		$pdf->SetXY($Xmat,20+$margeH+1);
		$pdf->MultiCell(6,3,$code_matiere,0,'C',0);
		$pdf->SetFont('Arial','',3);
		$pdf->SetXY($Xmat+6,20+$margeH);
		$pdf->MultiCell(6,5,"SESS.",1,'C',1);

		$pdf->SetXY($Xmat,28+$margeH);
//		$pdf->MultiCell($lMat,4,'',0,'C',0);
		$pdf->SetFont('Arial','B',4);
		$pdf->SetXY($Xmat,16+$margeH);
		$pdf->MultiCell($lMat,4,'','TB','C',0);
	// affiche moyenne min , moyenne, moyenne max par matiere selon id recupéré de $ue[$nb_ue][4] pour partiel et C continu
		$moy_mat="";
	// PARTIEL	
		$moy_mat=vatel_moyenne($moyenne_matiere_part[$ue[$nb_ue][4]]);
		if ($moy_mat['moy'] == 0) $moy_mat['moy']="";
	// moyenne min
	//	$pdf->SetFont('Arial','B',4);
	//	$pdf->SetXY($Xmat,28+$margeH); 
	//	$pdf->SetFillColor(198,209,229);
	//	$pdf->MultiCell($lMat_mid,4,format_moyenne($moy_mat['min']),1,'C',1);
	// moyenne 
	//	$pdf->SetFont('Arial','B',4);
	//	$pdf->SetXY($Xmat,32+$margeH); 
	//	$pdf->SetFillColor(198,209,229);
	//	$pdf->MultiCell($lMat_mid,4,format_moyenne($moy_mat['moy']),1,'C',1);
	//	$calc_moy_part+=$moy_mat['moy'];
	//	$calc_nb_mat_part++;
	// moyenne min
	//	$pdf->SetFont('Arial','B',4);
	//	$pdf->SetXY($Xmat,36+$margeH); 
	//	$pdf->SetFillColor(198,209,229);
	//	$pdf->MultiCell($lMat_mid,4,format_moyenne($moy_mat['max']),1,'C',1);

	// PERIODE	
	//	$moy_mat=vatel_moyenne($moyenne_matiere_per[$ue[$nb_ue][4]]);
	//	if ($moy_mat['moy'] == 0) $moy_mat['moy']="";
	// moyenne min
	//	$pdf->SetFont('Arial','B',4);
	//	$pdf->SetXY($Xmat+$lMat_mid,28+$margeH);
	//	$pdf->MultiCell($lMat_mid,4,format_moyenne($moy_mat['min']),1,'C',0);
	// moyenne 
	//	$pdf->SetFont('Arial','B',4);
	//	$pdf->SetXY($Xmat+$lMat_mid,32+$margeH);
	//	$pdf->MultiCell($lMat_mid,4,format_moyenne($moy_mat['moy']),1,'C',0);
	//	$calc_moy_per+=$moy_mat['moy'];
	//	$calc_nb_mat_per++;
	// moyenne min
	//	$pdf->SetFont('Arial','B',4);
	//	$pdf->SetXY($Xmat+$lMat_mid,36+$margeH);
	//	$pdf->MultiCell($lMat_mid,4,format_moyenne($moy_mat['max']),1,'C',0);	
		
	// recup et affiche coef partiel / matiere 
		$coeff_recup = recup_coef($ue[$nb_ue][4],$idClasse);
		$pdf->SetXY($Xmat,25+$margeH);
		$pdf->SetFont('Arial','',3);
		$pdf->SetFillColor(198,209,229);  // couleur du cadre de l'eleve
		$pdf->MultiCell($lMat_mid,5,"",1,'C',1);
		$pdf->SetXY($Xmat,25+$margeH+0.5);
		$pdf->MultiCell($lMat_mid,2,"coef :\n$coeff_recup",0,'C',0);
		$pdf->SetFont('Arial','',4);
		$Xmat+=$lMat_mid;
	// reaffiche coef continu / matiere
		$pdf->SetXY($Xmat,44+$margeH);
	//	$pdf->MultiCell($lMat_mid2,4,"1",1,'C',0);		
		$Xmat+=$lMat_mid2;	
		$Xmat_t+=$lMat;
		$Xmat_ue = $Xmat;
		$coeff_recup_total+=$coeff_recup;
		$coeff_recup_eu+=$coeff_recup;
		if ($nb_ue==countTriade($ue)-1) {
			$id_ue = $ue[$nb_ue][0];
			$pdf->SetXY($Xmat,25+$margeH);
			$pdf->MultiCell($lMat_mid,5,"",1,'C',1);
			$pdf->SetXY($Xmat,25+$margeH+0.5);
			$pdf->SetFont('Arial','',3);
	                $pdf->MultiCell($lMat_mid,2,"coef :\nUE : $coeff_recup_eu",0,'C',0);
			$pdf->SetFont('Arial','',4);
			$coeff_recup_eu=0;


			$pdf->SetXY($Xmat+$lMat_mid,25+$margeH);
                        $pdf->MultiCell(10,5,"Coef G. : $coeff_recup_total",1,'C',1);
                        $coeff_recup_total=0;

		}
}
/// on traite la derniere UE
		$pdf->SetFont('Arial','',4);
		$pdf->SetXY($Xmat,16+$margeH);
		$pdf->MultiCell(6,4,'','T','C',0); // ICI
		$pdf->SetFont('Arial','',4);
//		$pdf->SetLineWidth(0.3);
		$pdf->SetXY($Xmat,20+$margeH);
		$pdf->MultiCell(6,5,'','1','C',1);
		$pdf->SetXY($Xmat,20+$margeH+0.5);
		$pdf->MultiCell(6,2,'MOY. UE','0','C',0);
		$pdf->SetXY($Xmat+6,20+$margeH);
		$pdf->MultiCell(10,5,'','1','C',1);
		$pdf->SetXY($Xmat+6,20+$margeH+0.5);
		$pdf->MultiCell(10,2,'MOY. SEMESTRE','0','C',0);
		$pdf->SetXY($Xmat+16,20+$margeH);
		$pdf->MultiCell(6,5,'','1','C',1);
		$pdf->SetXY($Xmat+16,20+$margeH+0.5);
		$pdf->MultiCell(6,2,'CRED OBT.','0','C',0);

/*
		// affiche moy. min max derniere UE partiel / continu			
			$pdf->SetFont('Arial','B',4);		
			$pdf->SetFillColor(198,209,229);
			$pdf->SetXY($Xmat,28+$margeH);
			$pdf->MultiCell($lMat_mid,4,format_moyenne($partiel[$id_ue]['min']),1,'C',1);
			$pdf->SetFont('Arial','B',4);		
			$pdf->SetFillColor(198,209,229);
			$pdf->SetXY($Xmat,32+$margeH);
			$pdf->MultiCell($lMat_mid,4,format_moyenne($calc_moy_part/$calc_nb_mat_part),1,'C',1);
			$pdf->SetFont('Arial','B',4);		
			$pdf->SetFillColor(198,209,229);
			$pdf->SetXY($Xmat,36+$margeH);
			$pdf->MultiCell($lMat_mid,4,format_moyenne($partiel[$id_ue]['max']),1,'C',1);

			$calc_moy_part_tot+=$calc_moy_part; // somme des moyennes partiel pour moyenne generale
			$calc_nb_mat_part_tot+=$calc_nb_mat_part; // somme des nbre de matiere de partiel pour moyenne generale
			$calc_moy_part=0;
			$calc_nb_mat_part=0;
	
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat+=$lMat_mid,28+$margeH);
			$pdf->MultiCell($lMat_mid,4,format_moyenne($periode[$id_ue]['min']),1,'C',0);
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,32+$margeH);
			$pdf->MultiCell($lMat_mid,4,format_moyenne($calc_moy_per/$calc_nb_mat_per),1,'C',0);
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,36+$margeH);
			$pdf->MultiCell($lMat_mid,4,format_moyenne($periode[$id_ue]['max']),1,'C',0);		

			$calc_moy_per_tot+=$calc_moy_per; // somme des moyennes periode pour moyenne generale
			$calc_nb_mat_per_tot+=$calc_nb_mat_per; // somme des nbre de matiere de periode pour moyenne generale	
			$calc_moy_per=0;
			$calc_nb_mat_per=0;			
			
			
			$Xmat_ue+=$lMat_mid*2;
			$pdf->SetFont('Arial','',4);
		*/
			$pdf->SetXY($Xmat_t,13+$margeH);
			$pdf->line($Xmat_ue+6,16+$margeH,$Xmat_ue+6,20+$margeH);
		/*
			$ecrit_ue=$ue[$nb_ue][2];
			$Xmat=$Xmat_ue;
			
			$pdf->SetLineWidth(0.3);
			
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,16+$margeH);
			$pdf->MultiCell($lMat_mid2*2+$lMat_mid-2,4,'MOY. GENERALES',1,'C',0);
			$pdf->SetFont('Arial','B',4);
			
			$pdf->SetXY($Xmat,20+$margeH);		
			$pdf->SetFillColor(198,209,229);
			$pdf->MultiCell($lMat_mid+$lMat_mid2-2,4,'Partiels',1,'C',1);
			$pdf->SetXY($Xmat+$lMat_mid+$lMat_mid2-2,20+$margeH);
			$pdf->MultiCell($lMat_mid2,4,'Cont.',1,'C',0);

		// affiche moy. min max general partiel / continu
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,24+$margeH);		
			$pdf->SetFillColor(198,209,229);
			$pdf->MultiCell($lMat_mid2,4,'Moy.',1,'C',1);
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,28+$margeH);
			$pdf->MultiCell($lMat_mid2,4,format_moyenne(end($moyenne_par)),1,'C',1);
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,32+$margeH);
			$pdf->MultiCell($lMat_mid2,4,format_moyenne($calc_moy_part_tot/$calc_nb_mat_part_tot),1,'C',1);			
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,36+$margeH);		
			$pdf->SetFillColor(198,209,229);
			$pdf->MultiCell($lMat_mid2,4,format_moyenne($moyenne_par[0]),1,'C',1);			
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat+=$lMat_mid2,24+$margeH);		
			$pdf->SetFillColor(198,209,229);
			$pdf->MultiCell($lMat_mid-2,4,'Rg'.'/'.countTriade($eleveT),1,'C',1);
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat+=$lMat_mid-2,24+$margeH);
			$pdf->MultiCell($lMat_mid2,4,'Moy.',1,'C',0);

			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,28+$margeH);
			$pdf->MultiCell($lMat_mid2,4,format_moyenne($moyenne_per[0]),1,'C',0);
			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,32+$margeH);
			$pdf->MultiCell($lMat_mid2,4,format_moyenne($calc_moy_per_tot/$calc_nb_mat_per_tot),1,'C',0);

			$pdf->SetFont('Arial','B',4);
			$pdf->SetXY($Xmat,36+$margeH);
 			$pdf->MultiCell($lMat_mid2,4,format_moyenne(end($moyenne_per)),1,'C',0);					
 */

$Ymat=23+$margeH-3+5;
$Xmat = $deb-25;
$hauteurLigne=5;

$pdf->SetFont('Arial','',5);
$pdf->SetFillColor(198,209,229);  // couleur du cadre de l'eleve
$pdf->SetXY($Xmat,$Ymat); 
$pdf->MultiCell(5,$hauteurLigne,"",1,'L',1);
$pdf->SetXY($Xmat,$Ymat+0.5); 
$pdf->MultiCell(5,$hauteurLigne,"#N",0,'C',0);

$pdf->SetXY($Xmat+5,$Ymat); 
$pdf->MultiCell(10,$hauteurLigne,"",1,'L',1);
$pdf->SetXY($Xmat+5,$Ymat+0.9); 
$pdf->MultiCell(10,2,"Matricule",0,'C',0);

$pdf->SetXY($Xmat+15,$Ymat); 
$pdf->MultiCell(15,$hauteurLigne,'',1,'L',1);
$pdf->SetXY($Xmat+15,$Ymat+0.9); 
$pdf->MultiCell(15,2,"Nom Prénom",0,'L',0);

$pdf->SetXY($Xmat+30,$Ymat);
$pdf->MultiCell(5,$hauteurLigne,'',1,'L',1);
$pdf->SetXY($Xmat+30,$Ymat+0.9);
$pdf->SetFont('Arial','',4);
$pdf->MultiCell(7,2,"Sexe",0,'L',0);
$pdf->SetFont('Arial','',5);


////// fin affichage premieres lignes
$Xmat_t = $deb;
$Xmat = $deb;
$hauteurMatiere=10; // taille du cadre matiere
$Ymat=28+$margeH-3+5;
$moy_per_tot=0;
$nb_ue_per=0;	
$nbligne=0;
for ($j=0;$j<countTriade($eleveT);$j++) {  // premiere ligne de la creation PDF
	$nbligne++;

	$ects=0;
//	print "<hr>";

	// fin entete tableau
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="$nomEleve $prenomEleve";

	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=$classe_nom;

	// FIN variables
	
	// Debut création PDF


	// adresse de l'élève
	// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance,email_eleve,adr_eleve,ccp_eleve,commune_eleve,sexe
	$dataadresse=chercheadresse($idEleve);
	for ($ik=0;$ik<=countTriade($dataadresse);$ik++) {
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
		$matricule=$dataadresse[$ik][9];
		$sexeetudiant=$dataadresse[$ik][24];
		$Xv1=100;
		$Yv1=10;
	}



	// Mise en place des matieres

//	$Ymat+=11;
	$ii=0;

	$XprofVal=22; // x en nom prof
	$YprofVal=$Ymat + 6; // y en nom du prof

//	$liste_matiere=$listematiere;
	$ecrit='';
	$moyenne_ue='';
	$somme_coef=0;
	$somme_coef_gen_partiel=0;
	$moyenne_gen_partiel='';
	$nb_mat=1;
	$nb_mat_gen=0;
	$nb_note_periode_ue=0;
	$nb_mat_per=0;
	$hauteurLigne=10;

	$pdf->SetFont('Arial','',5);

	$pdf->SetXY($Xmat-25,$Ymat); 
	$pdf->MultiCell(5,$hauteurLigne,"$nbligne",1,'L',0);

	$pdf->SetXY($Xmat-20,$Ymat); 
	$pdf->SetFont('Arial','',4);
	$pdf->MultiCell(10,$hauteurLigne,"",1,'C',0);
	$pdf->SetXY($Xmat-20,$Ymat+3); 
	$numero_eleve=chercheNumeroEleve($idEleve);
	$pdf->MultiCell(10,2,"$numero_eleve",0,'C',0);

	$pdf->SetFont('Arial','',4);
	$pdf->SetXY($Xmat-10,$Ymat); 
	$pdf->MultiCell(15,$hauteurLigne,'',1,'L',0);
	$pdf->SetXY($Xmat-10,$Ymat+0.3); 
	$pdf->MultiCell(15,2,$nomprenom,0,'L',0);

	$pdf->SetXY($Xmat+5,$Ymat);
        $pdf->MultiCell(5,$hauteurLigne,'',1,'L',0);
        $pdf->SetXY($Xmat+5,$Ymat+3);
	$sexeetudiant=rechercheSexeEleve($idEleve);
        $pdf->MultiCell(5,2,$sexeetudiant,0,'C',0);

		
	$Xmat+=10;
	for ($nb_ue=0;$nb_ue<countTriade($ue);$nb_ue++) { // boucle pour chaque UE
		$id_ue =$ue[$nb_ue][0];
		$idMatiere=$ue[$nb_ue][4];
		$ordre_recup=recup_ordre($idMatiere,$idClasse);
	
		$matiere=chercheMatiereNom($idMatiere);
		
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre_recup);
		$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$ordre_recup);

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe = verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre_recup);

		// mise en place des matieres

	if ($ecrit==$ue[$nb_ue][0]) {
		// $moyenne_ue+=$notepartiel[0][6]*$notepartiel[0][0];
		// $somme_coef+=$notepartiel[0][6];
		// if ($noteperiode[0][0]!=0 && $noteperiode[0][0]!='') {$nb_mat+=1;}
	}else{
		if ($ecrit!='') {
			$Xmat+=$lMat;
			$id_ue = $ecrit;
			//		print ('$moyenne_gen_partiel : '.$moyenne_gen_partiel.' - $somme_coef_gen_partiel : '.$somme_coef_gen_partiel.'</br>');
			if (($moyenne_ue>=0) && is_numeric($moyenne_ue)) {$moyenne_gen_partiel+=$moyenne_ue;$moyenne_ue=$moyenne_ue/$somme_coef;$somme_coef_gen_partiel+=$somme_coef;}
			
			// Affiche la moy. partiel UE de l'etudiant
			if ((format_moyenne($moyenne_ue) >=0 ) && (format_moyenne($moyenne_ue) != "")) {
				$affiche_note = format_moyenne($moyenne_ue) ;
			} else {
				$affiche_note = "";
			}
			$pdf->SetFont('Arial','',4);
		//	$pdf->SetLineWidth(0.3);
			$pdf->SetXY($Xmat-$lMat_mid-$lMat_mid2,$Ymat);
//			$pdf->MultiCell($lMat_mid,$hauteurLigne,$affiche_note,1,'C',1);
		//	$pdf->SetLineWidth(0.2);

			if ( $moyenne_gen_partiel >=0 ) {}
			$moyenne_ue='';
			$somme_coef=0;
			
			// Affiche la moy. periode UE de l'etudiant
			if ($nb_note_periode_ue>0  ) {
				$moyenne_periode_ue=$moyenne_periode_ue/$nb_mat_per;
		//		$moyenne_periode_gen+=$moyenne_periode_ue;
				$moy_per_tot+=$moyenne_periode_ue;
				$nb_ue_per++;
			} else {
				$moyenne_periode_ue='';
			}
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($Xmat+120,$Ymat);
		//	$pdf->SetLineWidth(0.3);
			$pdf->SetFont('Arial','',4);
			$pdf->SetXY($Xmat-$lMat_mid2-6,$Ymat);
			$pdf->MultiCell($lMat_mid,$hauteurLigne,format_moyenne($moyenne_periode_ue),1,'C',0);
		//	$pdf->SetLineWidth(0.2);
//			if (($moyenne_periode_ue >=0) && ($moyenne_periode_ue != "")) {$nb_mat_gen++;}
			$moyenne_periode_ue='';
			$Xmat=$Xmat-$lMat_mid2+$lMat_mid-6;
			$nb_mat=1;
			$nb_note_periode_ue=0;
			$nb_mat_per=0;
		}
		$nom_ue=$ue[$nb_ue][2];
		$ecrit=$ue[$nb_ue][0];

	}

		// mise en place coef + note partiel
			// coeff
//		$notepartiel = recupNotepartiel($idEleve,$idMatiere,$dateDebut,$dateFin,$idClasse,$typeExamen);
//		print_r($notepartiel);

//		if (($notepartiel[0][0] >=0) && ($notepartiel[0][0] != "") ) {$nb_mat+=1;
//			$moyenne_ue+=$notepartiel[0][6]*$notepartiel[0][0];
//			$somme_coef+=$notepartiel[0][6];
//		}
//		$pdf->SetFont('Arial','',6);
//		$pdf->SetXY($Xmat,$Ymat);

// affiche note de partiel
//		if (($notepartiel[0][0]>=0) && ($notepartiel[0][0] != '')) { 
//			$affiche_note=number_format($notepartiel[0][0],2,'.','') ;
//		} else {
//			$affiche_note="";
//		}

//		$pdf->SetFont('Arial','',4);
//		$pdf->SetXY($Xmat,$Ymat);
//		$pdf->SetFillColor(198,209,229);
//		$pdf->MultiCell($lMat_mid,$hauteurMatiere,$affiche_note,1,'C',1);	
		$Xmat+=$lMat_mid;


//		if ($idgroupe == "0") {
//			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
//		}else{
//			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
//		}

		// Periode 
		$noteperiode= recupNoteperiode($idEleve,$idMatiere,$dateDebut,$dateFin);
		// TRUNC(note,2),elev_id,code_mat,date,sujet,typenote,coef

		// note
		$liste_note='';
		$moyenne_periode='';
		$nb_note_periode=0;
		for ($nb_note=0;$nb_note<countTriade($noteperiode);$nb_note++) { 
			$datenote=$noteperiode[$nb_note][3];
			if ($noteperiode[$nb_note][0] >=0 && countTriade($noteperiode)>0  ) {
				$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
				$liste_note = $liste_note.$noteperiode[$nb_note][0]." - ";
				$nb_note_periode+=1;
				$som_coef_periode+=$noteperiode[$nb_note][6];
				//print ("note : ".$noteperiode[$nb_note][0]." (coef:".$noteperiode[$nb_note][6].") - ");
			} else {
				// $liste_note = $liste_note." Abs. - ";			
			}
		}
	//	print ("<br>");
//  Tester si liste note finie par - et l'enlever
//	print_r ($noteperiode);
		$liste_note=substr($liste_note, 0, -2); 
		
		$moyenne_periode=$moyenne_periode/$som_coef_periode;
		$som_coef_periode_ue+= $som_coef_periode;
		$som_coef_periode=0;
		if (($moyenne_periode>0)&& is_numeric($moyenne_periode)) { $moyenne_periode_ue+=$moyenne_periode;}
		if ($nb_note_periode>0) {$nb_note_periode_ue++;$nb_mat_per++;}

// affiche note de periode
		if (($moyenne_periode>0) && is_numeric($moyenne_periode)) { 
			$affiche_note=number_format($moyenne_periode,2,'.','') ;
			if ($affiche_note < 7) {
				$affiche_note="EL";
			}else{
				if ($affiche_note >= 10) {
					$coeff=recupCoeff($idMatiere,$idClasse,$ordre_recup);
					$ects+=$coeff;
				}
			}
		} else {
			$affiche_note="ND";
			$affiche_UE="ND";

		}
		$pdf->SetFont('Arial','',4);
		$pdf->SetXY($Xmat-6,$Ymat);
		$pdf->MultiCell($lMat_mid2,$hauteurMatiere,$affiche_note,1,'C',0);
		if (($affiche_note >=0 ) && is_numeric($affiche_note)) {
			$nb_mat_gen+=$coeff;
			$moyenne_periode_gen=$moyenne_periode_gen+($affiche_note*$coeff);		
		}
		$pdf->SetXY($Xmat-6+6,$Ymat);
		$pdf->MultiCell($lMat_mid2,$hauteurMatiere,"",1,'C',0);
		$pdf->SetXY($Xmat-6+6,$Ymat+2);
		
		$month = (new DateTime($datenote))->format('m');
		if ($month === '03' || $month === '07') { 
			$ESSR="R";
		}else{
			$ESSR="";
		}

		$pdf->MultiCell($lMat_mid2,2,"$ESSR$ESS $anneeESS",0,'C',0);



		$Xmat+=$lMat_mid2;
		
		if ($moyenne_periode >=0 && $nb_note_periode>0) { 
			$moyenne_periode=number_format($moyenne_periode,2,'.','');
		} else {
			$pdf->SetFont('Arial','',4);
			$pdf->SetXY($Xmat,$Ymat);
		}
		// mise en place du nom du prof
		$profAff=profAff($idMatiere,$idClasse,$ordre_recup);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre_recup);
//		$pdf->SetFont('Arial','',6);
//		$pdf->SetXY($XprofVal,$Ymat+2);
		$profAff=recherche_personne2($profAff);
//		$pdf->WriteHTML(trunchaine($profAff,20));
		$YprofVal=$YprofVal + $hauteurMatiere ;	
	
		if ($nb_ue==countTriade($ue)-1) {$id_ue = $ue[$nb_ue][0];}
	
//	} // fin boucle matiere
} // fin boucle UE
// fin de la mise en place des matieres<br>

// traitement de la derniere UE
	// moyenne UE de partiel
						//	print ('$moyenne_gen_partiel : '.$moyenne_gen_partiel.' - $somme_coef_gen_partiel : '.$somme_coef_gen_partiel.'</br>');
			if (($moyenne_ue>=0) && is_numeric($moyenne_ue)) {
				$moyenne_gen_partiel+=$moyenne_ue;
				$moyenne_ue=$moyenne_ue/$somme_coef;
				$somme_coef_gen_partiel+=$somme_coef;
			}

			if (($moyenne_ue>0) && is_numeric($moyenne_ue)) { 
				$affiche_note=number_format($moyenne_ue,2,'.','') ;
			} else {
				$affiche_note="";
			}
			$pdf->SetFont('Arial','',4);
//			$pdf->SetLineWidth(0.3);
			$pdf->SetXY($Xmat,$Ymat);
//			$pdf->MultiCell($lMat_mid,$hauteurLigne,format_moyenne($affiche_note),1,'C',0);
//			$pdf->SetLineWidth(0.2);
			$moyenne_ue='';

	// moyenne UE de periode			
	// print ('nbre mat: '.$nb_mat_per. '<br>');

			if ($moyenne_periode_ue!=''){$moyenne_periode_ue=$moyenne_periode_ue/($nb_mat_per);}
		//	$moyenne_periode_gen+=$moyenne_periode_ue;
			$som_coef_periode_ue_total+= $som_coef_periode_ue;
			$som_coef_periode_ue=0;
			$moy_per_tot += (float)$moyenne_periode_ue;
			$nb_ue_per++;

			if ($affiche_UE == "ND") {
				$affiche_note="ND";
			}else{
				if ($moyenne_periode_ue>=0 && $moyenne_periode_ue!='') { 
					$affiche_note=number_format($moyenne_periode_ue,2,'.','') ;
					if ($affiche_note < 10) {
        	                        	$affiche_note="EL";
					}
				} else {
					$affiche_note="ND";
				}
			}
			
//			$pdf->SetLineWidth(0.3);
			$pdf->SetFont('Arial','',4);
			$pdf->SetXY($Xmat,$Ymat);
			$pdf->MultiCell($lMat_mid,$hauteurLigne,''.($affiche_note),1,'C',0);
//			$pdf->SetLineWidth(0.2);
		//	$moyenne_periode_gen=$moyenne_periode_gen+$affiche_note;			
		//	if (($affiche_note >=0 ) && is_numeric($affiche_note)) {$nb_mat_gen++;}
			//if (($moyenne_periode_ue >=0 ) && is_numeric($moyenne_periode_ue)) {$nb_mat_gen++;}
			$moyenne_periode_ue='';
			$nb_mat=1;
// fin notes
// --------

//--------------------------------//
//Mise en place des moyenens general partiel et periode
// partiel
 // print ('$moyenne_gen_partiel:'.$moyenne_gen_partiel.'- $somme_coef_gen_partiel:'.$somme_coef_gen_partiel.'<br>');
//print ('<br>--Partiel--<br>');
//print_r ($partiel);
//print ('<br>--periode--<br>');;
//print_r ($periode);

			if ($somme_coef_gen_partiel > 0 ) {
				$moyenne_gen_partiel=$moyenne_gen_partiel/$somme_coef_gen_partiel;
			}
		//	$pdf->SetFont('Arial','',7);
		//	$pdf->SetXY($Xmat,$Ymat+4);
		//	$pdf->MultiCell(80,8,'',1,'C',1);
		//	$pdf->SetXY($Xmat+80,$Ymat+4);
		//	$pdf->MultiCell(100,8,'',1,'C',0);
		//	$pdf->SetFont('Arial','',8);
		//	$pdf->SetXY($Xmat+2,$Ymat);
		//	$pdf->WriteHTML('<b>MOYENNES GENERALES / 20</b>');
			$pdf->SetFont('Arial','B',4); 
			$pdf->SetFillColor(198,209,229);
			$pdf->SetXY($Xmat+=($lMat_mid*1),$Ymat);
//			$pdf->WriteHTML('<b>'.format_moyenne($moyenne_gen_partiel).'</b>' .((array_search($moyenne_gen_partiel, $moyenne_par))+1).'/'.countTriade($eleveT).' '.format_moyenne(end($moyenne_par)).' '.format_moyenne($moyenne_par[0]).'');
//			$pdf->MultiCell($lMat_mid2,$hauteurLigne,format_moyenne($moyenne_gen_partiel),1,'C',1);
			
			$pdf->SetFont('Arial','',4);
			$pdf->SetFillColor(198,209,229);
			$pdf->SetXY($Xmat+=$lMat_mid2,$Ymat);
//			$pdf->MultiCell($lMat_mid-2,$hauteurLigne,((array_search($moyenne_gen_partiel, $moyenne_par))+1),1,'C',1);

	
//			$pdf->SetFont('Arial','',4);
//			$pdf->SetXY($Xmat+=$lMat_mid,$Ymat);
//			$pdf->MultiCell(9,4,format_moyenne(end($moyenne_par)).'-'.format_moyenne($moyenne_par[0]),1,'C',0);
			
			
//			$pdf->SetFont('Arial','',4);
//			$pdf->SetXY($Xmat+6,$Ymat+4);
//			$pdf->WriteHTML('Rang  : '.((array_search($moyenne_gen_partiel, $moyenne_par))+1).'/'.countTriade($eleveT).'  - (min.:'.format_moyenne(end($moyenne_par)).' - max:'.format_moyenne($moyenne_par[0]).')');
			$moyenne_gen_partiel='';
			$somme_coef_gen_partiel=0;
//			$pdf->SetFont('Arial','',7);
//			$pdf->SetXY($Xmat+60,$Ymat+3);
//			$pdf->WriteHTML($moyenne_gen_partiel);

// periode			
//	print ("moyenne tot per :".$moy_per_tot."  nbe tot mat".$nb_ue_per."<br>");
// modif ambis 9/12/08 suite prb myenne gener periode
//			$moyenne_periode_gen=$moyenne_periode_gen / ($nb_mat_gen);


			// modif ambis 16/12/08
//			$moyenne_periode_gen = $moy_per_tot / $nb_ue_per;
	//		print "$moyenne_periode_gen / $nb_mat_gen";
			if ($nb_mat_gen > 0) $moyenne_periode_gen=$moyenne_periode_gen/$nb_mat_gen;
//			$moyenne_periode_gen=number_format($moyenne_periode_gen,2,',','');
			$pdf->SetFont('Arial','B',8);
			$pdf->SetXY($Xmat-6,$Ymat);
			// MOYENNE SEMESTRE <---------

			$pdf->MultiCell(10,$hauteurLigne,format_moyenne($moyenne_periode_gen),1,'C',0);
			$moy_per_tot=0;
			$nb_ue_per=0;
			
			$Xmat+=10;
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($Xmat-6,$Ymat);
			$pdf->MultiCell(6,$hauteurLigne,"",1,'C',0);
			$pdf->SetXY($Xmat-6,$Ymat);
			$pdf->MultiCell(6,$hauteurLigne,"$ects",0,'C',0);

			$tabECTS["$ects"]++;

			$ects=0;
			$pdf->SetFont('Arial','B',4);

//			$pdf->WriteHTML('<b> CONTROLE CONTINU :         '.format_moyenne($moyenne_periode_gen).'</b>');
			//$pdf->SetFont('Arial','',7);
			//$pdf->SetXY($Xmat+150,$Ymat+7);
		//	$pdf->WriteHTML('(min.:'.format_moyenne($moyenne_per[0]).' - max:'.format_moyenne(end($moyenne_par)).')</b>');
			$moyenne_periode_gen=0;
			$nb_mat_gen=0;
//			$pdf->SetFont('Arial','',7);
//			$pdf->SetXY($Xmat+170,$Ymat+3);
//			$pdf->WriteHTML($moyenne_gen_partiel);
			
		//	$Ymat+=14;			
// FIN Mise en place des moyenens general partiel et periode

	//---------------------------------//
	// recherche le nombre de retard
	$nbretard=0;
	$nbretard1=0;
	$nbheureabs=0;
	$nbjoursabs=0;
	$nbabs=0;
	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbretard=countTriade($nbretard);
	
	// recherche le nombre d absence
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	for($o=0;$o<=countTriade($nbabs);$o++) {
		if ($nbabs[$o][4] > 0) {
	       		$nbjoursabs = $nbjoursabs + $nbabs[$o][4];
		}else{
			$nbheureabs = $nbheureabs + $nbabs[$o][7];	
		}
	}
	$nbabs=$nbjoursabs * 2;
	
//	$pdf->SetXY($Xmat,$Ymat);
///	$pdf->MultiCell(180,6,'',1,'',0);
//	$pdf->SetXY($Xmat+2,$Ymat+1);
//	$pdf->SetFont('Arial','',8);
///	$pdf->WriteHTML("<b>Nombre de 1/2 journées d'absences : </b>".$nbabs .' - <b>Nombre de retards :</b> '.$nbretard);
	//---------------------------------//

// cadre appréciation
//$Ycom=$Ymat+10;
//$Xcom=$Xmat;
//$hauteurcom=30;
//$Yappreciation=$Ycom+1;
//$pdf->SetXY($Xcom,$Ycom);
///$pdf->MultiCell($largeurcom,$hauteurcom,'',1,'',0);
//$pdf->SetXY($Xcom+2,$Ycom+1);
//$pdf->SetFont('Arial','',8);
///$pdf->WriteHTML("APPRECIATIONS GENERALES :");
//§$commentairedirection=recherche_com($idEleve,$saisie_trimestre,"montessori_spec");
//$commentairedirection=preg_replace('/\n/'," ",$commentairedirection);
//$pdf->SetXY($Xcom+2,$Ycom+10);
//$pdf->SetFont('Arial','',8);
///$pdf->MultiCell(100,3,$commentairedirection,'','','L',0); // commentaire de la direction (visa)


// fin duplicata






//FIN appréciation
// sortie dans le fichier

		if ($Ymat>=165) {$pdf->AddPage(L);$Ymat=8;} else {$Ymat+=$hauteurLigne;}
	//	$pdf->WriteHTML('<b>'.$Ymat);
		$Xmat = $deb;
		$nbpers+=1;
} // fin du for on passe à l'eleve suivant


// LEGENDE
$Xmat=20;
$Ymat+=5;
if ($Ymat>=165) {$pdf->AddPage(L);$Ymat=8;}
$pdf->SetFillColor(187,210,225);
$pdf->SetXY($Xmat,$Ymat);
$pdf->MultiCell(200,3,"Statistiques de capitalisation sur le programme",1,'C',1);        
$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(200,3,"$nbpers personnes ont composé",1,'L',0);        
$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(200,3,"Nombre Etudiant par plage de credits capitalisés",1,'C',0);        

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"[0,6[ -->".$tabECTS[0]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"[6,8[ -->".$tabECTS[5]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"[8,10[ -->".$tabECTS[8]."personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"10 credits --> ".$tabECTS[10]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"17 credits --> ".$tabECTS[17]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"24 credits --> ".$tabECTS[24]." personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"11 credits --> ".$tabECTS[11]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"18 credits --> ".$tabECTS[18]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"25 credits --> ".$tabECTS[25]." personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"12 credits --> ".$tabECTS[12]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"19 credits --> ".$tabECTS[19]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"26 credits --> ".$tabECTS[26]." personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"13 credits --> ".$tabECTS[13]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"20 credits --> ".$tabECTS[20]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"27 credits --> ".$tabECTS[27]." personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"14 credits --> ".$tabECTS[14]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"21 credits --> ".$tabECTS[21]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"28 credits --> ".$tabECTS[28]." personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"15 credits --> ".$tabECTS[15]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"22 credits --> ".$tabECTS[22]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"29 credits --> ".$tabECTS[29]." personne(s)",1,'C',0);

$pdf->SetXY($Xmat,$Ymat+=3);
$pdf->MultiCell(66.67,3,"16 credits --> ".$tabECTS[16]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"23 credits --> ".$tabECTS[23]." personne(s)",1,'C',0);
$pdf->SetXY($Xmat+66.67+66.67,$Ymat);
$pdf->MultiCell(66.67,3,"30 credits --> ".$tabECTS[30]." personne(s)",1,'C',0);

unset($tabECTS);
$tabECTS=array("0"=>0,"6"=>0,"8"=>0,"10"=>0,"11"=>0,"12"=>0,"13"=>0,"14"=>0,"15"=>0,"16"=>0,"17"=>0,"19"=>0,"20"=>0,"21"=>0,"22"=>0,"23"=>0,"24"=>0,"25"=>0,"26"=>0,"27"=>0,"28"=>0,"29"=>0,"30"=>0);

$Ymat+=5;
if ($Ymat>=165) {$pdf->AddPage(L);$Ymat=8;}
if ($Ymat>=165) {$pdf->AddPage(L);$Ymat=8;}
$pdf->SetFillColor(187,210,225);
$pdf->SetXY($Xmat,$Ymat);
$pdf->MultiCell(200,3,"Legende",1,'L',1);        

$Ymatdeb=$Ymat;
foreach($legende as $key=>$value) {
	if ($Ymat>=165) {
		$pdf->AddPage(L);
		$Ymatdeb=8;
	}
	if ($key == "TCA1312") {
		$Xmat+=100;				
		$Ymat=$Ymatdeb;
	}
	$pdf->SetXY($Xmat,$Ymat+=3);
	$pdf->MultiCell(200,3,"$key : $value",1,'L',0);        
}

$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$fichier="./data/pdf_bull/tableaupp_".$classe_nom."_".$saisie_trimestre.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
?>
<br>
<ul><ul>
<?php
if ($_SESSION["membre"] == "menuadmin") {
?>
	<input type=button onClick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="Récupérer le fichier PDF"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php
}
if ($_SESSION["membre"] == "menuprof") {
?>
	<input type=button onClick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');" value="Récupérer le fichier PDF"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } 

// fin PDF?>
</ul></ul>

</ul></ul>
</form>


<br /><br />
<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$saisie_trimestre,$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom." Prof Principal",$saisie_trimestre,$dateDebut,$dateFin);
if($cr == 1){
	history_cmd($_SESSION["nom"],"CREATION TABLEAU PP","Classe : $classe_nom");
}else{
	error(0);
}
?>
