<?php
session_start();
error_reporting(0);

include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(300);
}

include_once('common/config.inc.php');
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();

include_once("./common/config-color.php");
$colorTBD=COLORTBD;
$colorBD=COLORBD;
$colorMD=COLORMD;
$colorID=COLORID;
$colorTBF=COLORTBF;
$colorBF=COLORBF;
$colorMF=COLORMF;
$colorIF=COLORIF;


$ideleve=$_GET["saisie_eleve"];
$validenoteviescolaire=$_SESSION["validenoteviescolaire"];
$ideleverecup=$ideleve;
$idclasse=$_GET["saisie_classe"];
$ordre=ordre_matiere_visubull($idclasse); // recup ordre matiere
$eleveT=recupEleve($idclasse); // recup liste eleve

//************************************************************/
// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse);
for($j=0;$j<count($dateRecup);$j++) {
  	$dateDebut1=$dateRecup[$j][0];
  	$dateFin1=$dateRecup[$j][1];
}
$dateDebutT1=dateForm($dateDebut1);
$dateFinT1=dateForm($dateFin1);
//-----/
$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse);
for($j=0;$j<count($dateRecup);$j++) {
       	$dateDebut2=$dateRecup[$j][0];
        $dateFin2=$dateRecup[$j][1];
}
$dateDebutT2=dateForm($dateDebut2);
$dateFinT2=dateForm($dateFin2);
//-----/
$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse);
for($j=0;$j<count($dateRecup);$j++) {
       	$dateDebut3=$dateRecup[$j][0];
        $dateFin3=$dateRecup[$j][1];
}
$dateDebutT3=dateForm($dateDebut3);
$dateFinT3=dateForm($dateFin3);
//-----/
$moyenClasseGenT1="";
$moyenClasseGenT2="";
$moyenClasseGenT3="";
// idclasse,tableaueleve,datedebut,datefin,ordrematriere
$moyenClasseGenT1=calculMoyenClasse($idclasse,$eleveT,$dateDebutT1,$dateFinT1,$ordre);
$moyenClasseGenT2=calculMoyenClasse($idclasse,$eleveT,$dateDebutT2,$dateFinT2,$ordre);
$moyenClasseGenT3=calculMoyenClasse($idclasse,$eleveT,$dateDebutT3,$dateFinT3,$ordre);
if (($moyenClasseGenT1 == "") || ($moyenClasseGenT1 < 0)) {$moyenClasseGenT1=""; }
if (($moyenClasseGenT2 == "") || ($moyenClasseGenT2 < 0))  {$moyenClasseGenT2=""; }
if (($moyenClasseGenT3 == "") || ($moyenClasseGenT3 < 0)) {$moyenClasseGenT3=""; }

// Fin du Calcul moyenne classe
//************************************************************//

// calcul moyenne eleve
function moyenEleveGraph($eleveT,$idclasse,$dateDebut,$dateFin,$ordre,$ideleverecup,$tri,$validenoteviescolaire) {
	$noteMoyEleG=0;
	$coefEleG=0;
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne
        	// variable eleve
	        $lv1Eleve=$eleveT[$j][2];
	        $lv2Eleve=$eleveT[$j][3];
	        $idEleve=$eleveT[$j][4];
	        if ($idEleve != $ideleverecup) { continue; }
		$coeffaffTotal=0;
		for($i=0;$i<count($ordre);$i++) {
		        $idMatiere=$ordre[$i][0];
		        //$matiere=chercheMatiereNom($idMatiere);
		        $verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idclasse,$ordre[$i][2]);
		        if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
		        // mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idclasse,$ordre[$i][2]);
		        // mise en place moyenne eleve
	       		// mise en place des notes
			$idprof=recherche_prof($idMatiere,$idclasse,$ordre[$i][2]);
			if ($idgroupe == "0") {
		        	$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
			}else{	
				$noteaff=moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
			
	       		// pour le calcul de la moyenne general de l'eleve
		        if (trim($noteaff) != "" ) {
	                	$noteMoyEleGTempo = $coeffaff * $noteaff;
	                	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
	                	$coefEleG=$coefEleG + $coeffaff;
	      		}
		}
		if ($validenoteviescolaire == "oui") {
			$recupInfo=recupCaractVieScolaire($idclasse);
			$persVieScolaire=$recupInfo[0][4];
			$coefBull=$recupInfo[0][1];
			$coefProf=$recupInfo[0][2];
			$coefVieScol=$recupInfo[0][3];
			$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$tri);
			if (trim($noteaff) != "") {
				 $noteMoyEleGTempo = $noteaff * $coefBull;
	       		       	  $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
       	        		 $coefEleG=$coefEleG + $coefBull;
       	 		}
		}
	}
	if ($coefEleG == 0) { return -1; }
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	//alertJs($moyenEleve);
	return $moyenEleve;
}
// fin calcul moyenne eleve

$moyenEleveGenT1=moyenEleveGraph($eleveT,$idclasse,$dateDebutT1,$dateFinT1,$ordre,$ideleverecup,"trimestre1",$validenoteviescolaire);
$moyenEleveGenT2=moyenEleveGraph($eleveT,$idclasse,$dateDebutT2,$dateFinT2,$ordre,$ideleverecup,"trimestre2",$validenoteviescolaire);
$moyenEleveGenT3=moyenEleveGraph($eleveT,$idclasse,$dateDebutT3,$dateFinT3,$ordre,$ideleverecup,"trimestre3",$validenoteviescolaire);



$moyenEleveGenT1=preg_replace ('/,/',".", $moyenEleveGenT1);
$moyenEleveGenT2=preg_replace ('/,/',".", $moyenEleveGenT2);
$moyenEleveGenT3=preg_replace ('/,/',".", $moyenEleveGenT3);
if (($moyenEleveGenT1 == "") || ($moyenEleveGenT1 < 0)) {$moyenEleveGenT1=""; }
if (($moyenEleveGenT2 == "") || ($moyenEleveGenT2 < 0)) {$moyenEleveGenT2=""; }
if (($moyenEleveGenT3 == "") || ($moyenEleveGenT3 < 0)) {$moyenEleveGenT3=""; }
Pgclose(); 
//--------------------------------------------------//

include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_line.php");
include ("jpgraph/src/jpgraph_error.php");

// exemple
//$datay = array(10.5,10,"");
//$datay2 = array(12.5,12,"");

// datay moyenne eleve

$datay = array($moyenEleveGenT1,$moyenEleveGenT2,$moyenEleveGenT3);
// datay2 moyenne classe
$datay2 = array($moyenClasseGenT1,$moyenClasseGenT2,$moyenClasseGenT3);
$datax=array("Trimestre 1","Trimestre 2","Trimestre 3");


$graph = new Graph(430,280,"auto");
$graph->img->SetMargin(40,40,20,70);

$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->title->Set("");  // titre
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->title->Set(""); // titre en y
$graph->yaxis->title->Set(""); // titre en x

// Use 20% "grace" to get slightly larger scale then min/max of
// data
$graph->yscale->SetGrace(20);
$graph->xaxis->SetTickLabels($datax);
$graph->yaxis->scale->SetAutoMin(0);

// P1 moyenne eleve
$p1 = new LinePlot($datay);
$graph->Add($p1);
$p1->SetColor("$colorMD");
$p1->SetLegend("Moyenne Eleve");
$p1->mark->SetType(MARK_IMG_DIAMOND,'red',0.3);
//$p1->mark->SetColor("$colorMD");
//$p1->mark->SetFillColor("$colorMD");
$p1->SetCenter();

// P2 moyenne classe
$p2 = new LinePlot($datay2);
$graph->Add($p2);
$p2->SetColor("$colorTBD");
$p2->mark->SetType(MARK_IMG_DIAMOND,'blue',0.3);
//$p2->mark->SetColor("$colorTBD");
//$p2->mark->SetFillColor("$colorTBD");
$p2->SetCenter();
$p2->SetLegend("Moyenne Classe");


$graph->xaxis->SetColor("black");  // couleur axe en x
$graph->yaxis->SetColor("black");  // couleur axe en y
$graph->ygrid->SetLineStyle("longdashed");
$graph->ygrid->SetColor("black");
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5,0.95,"center","center");



$graph->Stroke();


?>


