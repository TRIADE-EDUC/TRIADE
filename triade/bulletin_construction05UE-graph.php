<?php // content="text/plain; charset=utf-8"
include_once('jpgraph/src/jpgraph.php');
include_once('jpgraph/src/jpgraph_bar.php');

if (!is_dir("data/tmp")) mkdir("data/tmp");

function graphBulletin($nomfichier,$data1y,$data2y,$data3y) {

	$fichier="./data/tmp/$nomfichier";

	/*$data1y=array(0,13.5,0);
	$data2y=array(0,15,0);
	$data3y=array('EPS','ANG','Fran');
	*/
	// Create the graph. These two calls are always required
	$graph = new Graph(350,200,'auto');
	$graph->SetScale("textlin");

	$theme_class=new UniversalTheme;
	$graph->SetTheme($theme_class);

	$graph->yaxis->SetTickPositions(array(0,5,10,15,20), array(0,5,10,15,20));
	$graph->SetBox(false);

	$graph->ygrid->SetFill(false);
	$graph->xaxis->SetTickLabels($data3y);
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);

	// Create the bar plots
	$b1plot = new BarPlot($data1y);
	$b2plot = new BarPlot($data2y);

	// Create the grouped bar plot
	$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
	// ...and add it to the graPH
	$graph->Add($gbplot);


	$b1plot->SetColor("white");
	$b1plot->SetFillColor("orange");
	$b1plot->SetLegend('Etudiant');

	$b2plot->SetColor("white");
	$b2plot->SetFillColor("#11cccc");
	$b2plot->SetLegend('Classe');

	$graph->title->Set("");

	// Display the graph
	@unlink($fichier);
	$graph->Stroke($fichier);

}
	

?>
