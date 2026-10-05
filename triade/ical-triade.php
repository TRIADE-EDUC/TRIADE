<?php
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");
include_once("./common/config-module.php");
include_once('./librairie_php/zapcallib.php');
if (defined("PRODUCTID")) $productId=PRODUCTID;
$cnx=cnx();

if(isset($_GET['ref'])) {

	$membre=recupMembreViaIndex($_GET['index']);
	$idpers=recupIdPers($_GET['ref'],$membre);

	if (!is_numeric($idpers)) exit; 
	if ($membre=="") exit; 

	if (($membre == "menueleve") || ($membre == "menuparent")) {
		$idclasse=chercheIdClasseDunEleve($idpers);
		$idprof="";
	}

        $startOfWeek = date("Y-m-d 00:00:00"); ;
	$dateencours=date("d/m/Y");
	$elements=preg_split('/\//',$dateencours);
	$nb=12; // 12 jours
	$annee=$elements[2];
	$mois=$elements[1];
	$jour=$elements[0];
	$resultat=mktime(0,0,0,$mois,$jour,$annee);
	$resultat=$resultat + $nb * 86400 ;
	list($annee,$mois,$jours)=preg_split("/-/",strftime("%Y-%m-%d",$resultat));
	$endOfWeek = date("Y-m-d H:i:s",mktime(0,0,0,$mois,$jours,$annee));
	$idRessource="";

	$data=listEdt($startOfWeek,$endOfWeek,$idclasse,$idprof,$idRessource,$membre);
	// code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,idressource,id_resa_liste,idgroupe	
	
	//print_r($data);


	// create the ical object
	$icalobj = new ZCiCal();
	// create the event within the ical object

	for($i=0;$i<countTriade($data);$i++) {
		
		$description="";
		$duree=$data[$i][4];

		$title = $data[$i][1];
		// date/time is in SQL datetime format
		// "2025-01-13 12:00:00"
		$event_start = $data[$i][2]." ".$data[$i][3];
		$datefin=calculerHeureDeFin($data[$i][3],$duree);
		$event_end = $data[$i][2]." ".$datefin;


		if ($title == "") $title="non communiqué";

		if ($data[$i][6] > 0) {
			$nomclasse=chercheClasse_nom($data[$i][6]);
			$description="Classe : $nomclasse";
		}
		if ($data[$i][9] > 0) {
			$nommatiere=chercheMatiereNom($data[$i][9]);
			$description.=" - Matière : $nommatiere";
		}
		
		$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

		// add title
		$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
	
		// add start date
		$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
	
		// add end date
		$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));
	
		// UID is a required item in VEVENT, create unique string for this event
		// Adding your domain to the end is a good way of creating uniqueness
		$uid = date('Y-m-d-H-i-s')."-$i". "@triade-educ.org";
		$eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
	
		// DTSTAMP is a required item in VEVENT
		$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
	
		// Add description
		$eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent("$description")));
	}	
	// write iCalendar feed to stdout
	if (!is_dir("data/ical")) mkdir("data/ical");
	$file = "data/ical/$membre-$idpers.ics";
	@unlink($file);

	// Ouverture du fichier en mode écriture ("w" pour écraser le contenu, "a" pour ajouter à la fin)
	$handle = fopen($file, "w");

	// Vérification si le fichier s'est ouvert avec succès
	$data=$icalobj->export();
	$bytesWritten = fwrite($handle, $data);
    	// Fermeture du fichier
	fclose($handle);
	//	echo $icalobj->export();
	header('Content-Type: text/calendar; charset=utf-8');
   	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    	// Envoie le contenu du fichier
	readfile($file);
}
Pgclose();
@unlink($file);
?>
