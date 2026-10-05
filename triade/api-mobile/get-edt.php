<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../librairie_php/langue-text-fr.php");

/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */

/* get-edt.php, le demandeur le consulte en mode GET avec session PHP */

$idClasse=$_SESSION["idClasse"];

if(isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true and  $_SERVER['REQUEST_METHOD'] === 'GET'){
	header('Content-Type: application/json; charset=utf-8');
    	$cnx=cnx();
	$_tz_h = intval(TIMEZONE);
	$_tz_m = intval(TIMEZONEMINUTE);
    	if (isset($_GET["date"])) { // date=AAAA-MM-JJ (l'argument GET "date" est une date au format ISO)

        	/* Remplir avec le script nécessaire pour fournir l'edt donné à une date précise */
	        /* Note : quand deux séquences sont programmées à la même heure (voire même à la même durée), ils sont triés selon le nom de leur matière, 
	        donc (pour exemple) la séquence "FRANCAIS" apparaîtra à l'ordre n sur la liste JSON tandis que la séquence "MATHS" programmé à la même heure apparaitra à l'ordre n+1 */

		$date = $_GET["date"];
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
		    http_response_code(400);
		    echo json_encode(["code" => "400", "message" => "Format de date invalide (AAAA-MM-JJ attendu)"]);
		    Pgclose(); die();
		}

		$data=recupCoursDuJourViaClasse(dateForm($date),$idClasse);
	        // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,idgroupe,id_resa_liste

		$nb=count($data);
	        $ii=0;
		$edt = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];

		for($i=0;$i<count($data);$i++) {
        		$ii++;
	                $id=$data[$i][0];
	                $code=$data[$i][1];
	                $enseignement=$data[$i][2];
	                $date=dateForm($data[$i][3]);                 
			$duree=$data[$i][5];
			$heure=$data[$i][4];
	                $idclasse=$data[$i][7];
	                $idprof=$data[$i][8];
	                $prestation=$data[$i][9];
	                $idmatiere=$data[$i][10];
	                $coursannule=($data[$i][11] == 1) ? "true" : "false" ;
	                $idgroupe=$data[$i][12];
	                $idsalle=$data[$i][13];
			$salle=chercheNomMatos($idsalle);
			$classe=chercheClasse_nom($idclasse);
			$matiere=chercheMatiereNom($idmatiere);
			$prof=recherche_personne($idprof);
			if ($prof == "Message Automatique") $prof="";
			if ($idgroupe == "0") { 
				$groupe="--NON--";
			}else{
				$groupe=chercheGroupeNom($idgroupe);
			}
			$heure=timeForm($heure);
			list($_h, $_m) = explode(':', $heure);
			$heure = date('H:i', mktime(intval($_h) + $_tz_h, intval($_m) + $_tz_m, 0, 1, 1, 2000));
			$heurdefin=calculerHeureDeFin($heure,$duree);
               		array_push($edt["contenu"],
			array(
			"id" => "$id",
			"classe" => "$classe",
               		"matiere" => "$matiere",
               		"enseignant" => "$prof", 
               		"salle" => "$salle", 
               		"groupe" => "$groupe", 
               		"date" => "$date", 
               		"heuredebut" => "$heure", 
               		"heurefin" => "$heurdefin", 
               		"annule" => "$coursannule" 
			) );
                                
	   	}
	}else{
	        http_response_code(400);
        	$edt = ["code" => "400", "message" => "La demande n'a pas été forumée correctement" ];
	}
	echo json_encode($edt);
} else { /* Le demandeur ne possède pas de section */
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    $message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas connecté" ];
    echo json_encode($message);
    die();
}

?>
