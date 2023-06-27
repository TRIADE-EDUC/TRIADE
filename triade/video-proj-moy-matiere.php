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

$ordre=ordre_matiere_visubull($idclasse); // recup ordre matiere
$eleveT=recupEleve($idclasse); // recup liste eleve

// ---------------------------//
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

	       $matiere=substr($matiere,0,5);
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
$graphTabT1=moyenEleveMat($eleveT,$ordre,$dateDebutT1,$dateFinT1,$ideleverecup,$idclasse);
$graphTabT2=moyenEleveMat($eleveT,$ordre,$dateDebutT2,$dateFinT2,$ideleverecup,$idclasse);
$graphTabT3=moyenEleveMat($eleveT,$ordre,$dateDebutT3,$dateFinT3,$ideleverecup,$idclasse);

if (count($graphTabT1) > 0) {
	$graphT1=$graphTabT1;
}else{
	$graphT1=array();
}

if (count($graphTabT2) > 0) {
	$graphT2=$graphTabT2;
}else{
	$graphT2=array();
}

if (count($graphTabT3) > 0) {
	$graphT3=$graphTabT3;
}else{
	$graphT3=array();
}
/*
if ($trim_en_cours == "trimestre1" ) {
	$graphTabT1=moyenEleveMat($eleveT,$ordre,$dateDebutT1,$dateFinT1,$ideleverecup,$idclasse);
	if (count($graphTabT1) > 0) {
		$graphT1=$graphTabT1;
	}else{
		$graphT1=array();
	}
	$note_en_cours=$graphT1;
	$note_moins_un=array(0);
}

if ($trim_en_cours == "trimestre2" ) {
	$graphTabT2=moyenEleveMat($eleveT,$ordre,$dateDebutT2,$dateFinT2,$ideleverecup,$idclasse);
	if (count($graphTabT2) > 0) {
		$graphT2=$graphTabT2;
	}else{
		$graphT2=array();
	}
	$note_en_cours=$graphT2;
	$graphTabT1=moyenEleveMat($eleveT,$ordre,$dateDebutT1,$dateFinT1,$ideleverecup,$idclasse);
	if (count($graphTabT1) > 0) {
		$graphT1=$graphTabT1;
	}else{
		$graphT1=array();
	}
	$note_moins_un=$graphT1;
}

if ($trim_en_cours == "trimestre3" ) {
	$graphTabT3=moyenEleveMat($eleveT,$ordre,$dateDebutT3,$dateFinT3,$ideleverecup,$idclasse);
	if (count($graphTabT3) > 0) {
		$note_en_cours=$graphTabT3;
	}else{
		$note_en_cours=array();
	}
	$graphTabT2=moyenEleveMat($eleveT,$ordre,$dateDebutT2,$dateFinT2,$ideleverecup,$idclasse);
	if (count($graphTabT2) > 0) {
		$graphT2=$graphTabT2;
	}else{
		$graphT2=array();
	}
	$note_moins_un=$graphT2;
}
 */

//------------------------------------------------------------------------//
//-----------------------------------------------------------------------//
include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_line.php");
include ("jpgraph/src/jpgraph_bar.php");


/*
$data1y=array(12,8,19,3); // note
$data2y=array(8,2,11,7);  // note avant
$datax=array("Français","Mathématique","Anglais","Histoire");
$a="Science et vie de la Terre";
$a=substr($a,0,13);
$nom_matiere=array("français","Mathématiques","Anglais",$a,"EPS");
$note_moins_un=array(12,8,19,3,12);
$note_en_cours=array(8,2,11,7,12);
*/



$data1y=$graphT1; 
$data2y=$graphT2; 
$data3y=$graphT3; 
$datax=$nom_matiere;

// Create the graph. These two calls are always include_onced
$graph = new Graph($largeur_graph,180,"auto");
$graph->img->SetMargin(40,30,20,70);
$graph->SetScale("textlin");
$graph->SetShadow();


$b1plot = new BarPlot($data1y);
$b1plot->SetColor("$colorTBD");
$b1plot->SetFillColor("$colorTBD");
$b1plot->SetLegend("Trimestre 1");

$b2plot = new BarPlot($data2y);
$b1plot->SetColor("$colorBD");
$b1plot->SetFillColor("$colorBD");
$b2plot->SetLegend("Trimestre 2");

$b3plot = new BarPlot($data3y);
$b1plot->SetColor("$colorMD");
$b1plot->SetFillColor("$colorMD");
$b3plot->SetLegend("Trimestre 3");


$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5,0.95,"center","center");

$graph->ygrid->SetLineStyle("longdashed");
$graph->ygrid->SetColor("black");


// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot));

// ...and add it to the graPH
$graph->Add($gbplot);

$graph->xaxis->SetTickLabels($datax);
$graph->yaxis->scale->SetAutoMin(0);

// Display the graph
$graph->Stroke();

Pgclose();

?>
