<?php
session_start();
error_reporting(0);
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
$idclasse=$_GET["saisie_classe"];
$trim_en_cours=$_GET["trimestre"];
$ideleverecup=$ideleve;



// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($trim_en_cours,$idclasse);
for($j=0;$j<count($dateRecup);$j++) {
       $dateDebut=$dateRecup[$j][0];
       $dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);
//------------------------------------------------/



$ordre=ordre_matiere_visubull($idclasse); // recup ordre matiere
$eleveT=recupEleve($idclasse); // recup liste eleve
// ---------------------------------------------- //


function ListeMatiere($eleveT,$ordre,$dateDebut,$dateFin,$ideleverecup,$idclasse) {
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne
	        // variable eleve
	        $lv1Eleve=$eleveT[$j][2];
	        $lv2Eleve=$eleveT[$j][3];
	        $idEleve=$eleveT[$j][4];
	        if ($idEleve != $ideleverecup) { continue; }
	        for($i=0;$i<count($ordre);$i++) {
        	       $idMatiere=$ordre[$i][0];
	               $matiere=chercheMatiereNom($idMatiere);
				   $codeMatiere=chercheCodeMatiere($idMatiere);
				   if ($codeMatiere != "") $matiere=$codeMatiere;
	               $verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idclasse,$ordre[$i][2]);
        	       if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
		       $matiere=substr($matiere,0,6);
	               $matieretab[]=" ".ucwords($matiere)." ";
	       }
	}
	return $matieretab;
}

// ---------------------------//
function moyenEleveMat($eleveT,$ordre,$dateDebut,$dateFin,$ideleverecup,$idclasse) {
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne
	        // variable eleve
	        $lv1Eleve=$eleveT[$j][2];
	        $lv2Eleve=$eleveT[$j][3];
	        $idEleve=$eleveT[$j][4];
        	if ($idEleve != $ideleverecup) { continue; }
	        for($i=0;$i<count($ordre);$i++) {
	               $idMatiere=$ordre[$i][0];
	               $verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idclasse,$ordre[$i][2]);
	               if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
	               // mise en place moyenne eleve
		        $idprof=recherche_prof($idMatiere,$idclasse,$ordre[$i][2]);
	                $noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
	      		if ($noteaff == "") {
      				$noteaff="0";   // si pas de note alors "0"
		      		}
	       		$noteaff=preg_replace('/,/',".",$noteaff);
	      	 	$notetab[]=$noteaff;
	       }	
	}
	return $notetab;
}


function moyenEleveGenGraph($eleveT,$ordre,$dateDebut,$dateFin,$ideleverecup,$idclasse) {
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne
	        // variable eleve
	        $lv1Eleve=$eleveT[$j][2];
	        $lv2Eleve=$eleveT[$j][3];
	        $idEleve=$eleveT[$j][4];
        	if ($idEleve != $ideleverecup) { continue; }
	        for($i=0;$i<count($ordre);$i++) {
	               $idMatiere=$ordre[$i][0];
	               $verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idclasse,$ordre[$i][2]);
	               if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
	               // mise en place moyenne eleve
		        $idprof=recherche_prof($idMatiere,$idclasse,$ordre[$i][2]);
		       $noteaff=moyeMatGen($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof);
	      		if ($noteaff == "") {
      				$noteaff="0";   // si pas de note alors "0"
		      		}
	       		$noteaff=preg_replace('/,/',".",$noteaff);
	      	 	$notetab[]=$noteaff;
	       }	
	}
	return $notetab;
}






$matieretab=ListeMatiere($eleveT,$ordre,$dateDebut,$dateFin,$ideleverecup,$idclasse);
if (count($matieretab) > 0) {
	$nom_matiere=$matieretab;
}else{
	$nom_matiere=array();
}
// taille du graph
$largeur_graph=300;
$nbmatiere=count($matieretab);
if ($nbmatiere > 3) {
	for( $pp=5;$pp<$nbmatiere;$pp++) {
		$largeur_graph=$largeur_graph+45;
	}
}
// --------------
$graphTab=moyenEleveMat($eleveT,$ordre,$dateDebut,$dateFin,$ideleverecup,$idclasse);
$graphTabM=moyenEleveGenGraph($eleveT,$ordre,$dateDebut,$dateFin,$ideleverecup,$idclasse);

if (count($graphTab) > 0) {
	$graph=$graphTab;
}else{
	$graph=array();
}

if (count($graphTabM) > 0) {
	$graphM=$graphTabM;
}else{
	$graphM=array();
}

//------------------------------------------------------------------------//
//-----------------------------------------------------------------------//
include("jpgraph/src/jpgraph.php");
include("jpgraph/src/jpgraph_radar.php");

$data1y=$graph; // trimestre en cours
$data2y=$graphM; 
$datax=$nom_matiere;



$graph = new RadarGraph(500,500);
$graph->SetScale('lin',0,20);
$graph->yscale->ticks->Set(5,5);
$graph->SetColor('#FFFFFF');
$graph->SetShadow();
 
$graph->SetCenter(0.5,0.55);
 
$graph->axis->SetFont(FF_FONT1,FS_BOLD);
$graph->axis->SetWeight(1);
 
// Uncomment the following lines to also show grid lines.
$graph->grid->SetLineStyle("solid");
$graph->grid->SetColor("navy");
$graph->grid->Show();
$graph->HideTickMarks();

 
$graph->title->Set('Moyenne des matieres');
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->SetTitles($datax);
 
$plot = new RadarPlot($data1y);
$plot->SetLegend('Moy. Elève');
$plot->SetColor('red','lightred');
$plot->SetFill(false);
$plot->SetLineWeight(3);

$plot1 = new RadarPlot($data2y);
$plot1->SetLegend('Moy. Classe');
$plot1->SetColor('blue','lightblue');
$plot1->SetFill(false);
$plot1->SetLineWeight(3);

$graph->Add($plot);
$graph->Add($plot1);
$graph->Stroke();

Pgclose();

?>
