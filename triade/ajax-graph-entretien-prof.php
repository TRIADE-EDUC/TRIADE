<?php
session_start();
if ($_SESSION["membre"] != "menuadmin") {
	exit;
}
include_once("./jpgraph/src/jpgraph.php");
include_once("./jpgraph/src/jpgraph_pie.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$data=listingEntretienEnseignant(); // idprof,duree,idclasse,date_saisie,reference,ideleve
for($i=0;$i<count($data);$i++) {
	$idclasse=$data[$i][2];
	$seconde=conv_en_seconde($data[$i][1]);
	if ($seconde != "") {
		$tabClasse[$idclasse]+=$seconde;
	}
}
foreach($tabClasse as $idclasse=>$value) {
	$statTotal+=$value;
}
$data=array();
$tablegende=array();
$tabColor=array();

$i=0;
foreach($tabClasse as $idclasse=>$value) {
	$stat=number_format(($value/$statTotal)*100,'2','.',' ') ;
	array_push($data,$stat);
	$classe=chercheClasse_nom($idclasse);
	array_push($tablegende,$classe);
	$i++;
}

Pgclose();

$graph = new PieGraph(450,350,"auto");
$graph->SetShadow();
setlocale (LC_ALL, 'et_EE.ISO-8859-1');

$graph->title->Set("Pourcentage par classe");
$graph->title->SetFont(FF_FONT1,FS_BOLD);

$p1 = new PiePlot($data);
$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->SetSize(0.3);
$p1->SetCenter(0.4);
$p1->SetLegends($tablegende);
$graph->Add($p1);


// Display the graph
$fd=md5(date("HisdmY"));
$graph->Stroke("./data/tmp/$fd.png");
header("Content-type : image/jpeg");
readfile("./data/tmp/$fd.png");
?>
