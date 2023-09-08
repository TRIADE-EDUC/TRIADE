<?php

$idNote=$_GET["id"];
$idNombre=$_GET["nombre"];
$nomdudevoir=stripslashes($_GET["nomdudevoir"]);
$typenote=$_GET["typenote"];
$notesur=$_GET["notesur"];

if ($typenote == "en") {
	$typenote="Notes format USA";
}

if ($typenote == "fr") {
	$typenote="Notes sur $notesur ";
}



//------------------------------------------------------------------------//
//-----------------------------------------------------------------------//
include("jpgraph/src/jpgraph.php");
include("jpgraph/src/jpgraph_line.php");
include("jpgraph/src/jpgraph_bar.php");

$idNbEleveTab=explode(",",$idNombre);
$idNoteTab=explode(",",$idNote);

$l2datay=$idNbEleveTab; // note
$datax=$idNoteTab;

$l1datay = array(0);

// Create the graph.
$graph = new Graph(630,250,"auto");
$graph->img->SetMargin(40,130,20,40);
$graph->SetScale("textlin");
$graph->SetShadow();

// Create the bar plot
$l2plot = new BarPlot($l2datay);
$l2plot->SetFillColor("orange");
$l2plot->SetLegend("Répartition des notes");

// Add the plots to the graph
$graph->Add($l2plot);

$graph->title->Set("$nomdudevoir"); // legende en haut
$graph->xaxis->title->Set("$typenote");
$graph->yaxis->title->Set("Nombre Eleves");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->SetTickLabels($datax);
//$graph->xaxis->SetTextTickInterval(2);

// Display the graph
$graph->Stroke();



?>
