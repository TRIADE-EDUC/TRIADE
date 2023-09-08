<?php
include_once('jpgraph/src/jpgraph.php');
include_once('jpgraph/src/jpgraph_bar.php');

include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");

$id=$_GET["id"];
if ($id == "classe") {
	$cnx=cnx();
	$max=0;
	$data=affClasse(); // code_class,libelle,
	for($i=0;$i<count($data);$i++) {
		$nb=nbabstotalClasse($data[$i][0]);
		if ($nb > 0) {
			$listInfoX.=$data[$i][1]."###";
			$listInfoY.=$nb."###";
			if ($nb >= $max) $max=$nb;
		}
	}
	$datay=explode("###",$listInfoY); 
	$datax=explode("###",$listInfoX);
	Pgclose();
}

$height=10+count($data)*17;
$width=550;

if ($height < 250 ) $height=250 ;

 
// Set the basic parameters of the graph
$graph = new Graph($width,$height);
$graph->SetScale('textlin');
 
$top = 60;
$bottom = 50;
$left = 80;
$right = 30;
$graph->Set90AndMargin($left,$right,$top,$bottom);
 
// Nice shadow
$graph->SetShadow();
 
// Setup labels
$lbl = $datax;
//$lbl = array("Andrew\nTait","Thomas\nAnderssen","Kevin\nSpacey","Nick\nDavidsson","David\nLindquist","Jason\nTait","Lorin\nPersson");
$graph->xaxis->SetTickLabels($lbl);
 
// Label align for X-axis
$graph->xaxis->SetLabelAlign('right','center','right');
 
// Label align for Y-axis
$graph->yaxis->SetLabelAlign('center','bottom');
 
// Titles
$graph->title->Set('Absences par classe.');
 
// Create a bar pot
$bplot = new BarPlot($datay);
$bplot->SetFillColor('orange');
$bplot->SetWidth(0.5);
$bplot->SetYMin($max);
 
$graph->Add($bplot);
 
$graph->Stroke();



?>
