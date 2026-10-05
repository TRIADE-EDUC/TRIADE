<?php
session_start();

/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
$idClasse=$_SESSION["idClasse"];			



/* get-agenda.php, le demandeur le consulte en mode GET avec session PHP */

if(isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true and  $_SERVER['REQUEST_METHOD'] === 'GET'){
	header('Content-Type: application/json; charset=utf-8');
	$cnx=cnx();
    	/* IMPORTANT : Pour les DST, n'afficher que ceux da classe du demandeur (si le demandeur est en 4e1, n'afficher que les DST de 4e1), pour éviter une pollution des widgets par des infos inutiles */
    	/* IMPORTANT : En cas d'apparition de DST et d'évènements simulatnés, classez les DST en premier puis ensuite les évènements */
    	if ($_GET["mode"] == "date" and isset($_GET["date"])) {
        	/* Remplir avec le script nécessaire pour fournir l'agenda donné à une date précise */
		$date = preg_replace('/[^0-9\/\-]/', '', $_GET["date"]);
		if (!$date) {
			http_response_code(400);
			echo json_encode(["code" => "400", "message" => "Paramètre date invalide"]);
			Pgclose(); die();
		}

		$data=affDst22($idClasse,$date); //id_dst,date,matiere,code_classe,heure,duree,idsalle
		$data2=affEvenementjour($date); // id_evenement,date,evenement

		$nb=count($data)+count($data2);
		$ii=0;                
		$agenda = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];

	  	for($i=0;$i<count($data);$i++) {
			$ii++;
			$matiere=$data[$i][2];
			$classe=$data[$i][3];
			$heure=$data[$i][4];
			$duree=$data[$i][5];
			$salle=$data[$i][6];
			$date=$data[$i][1];
			array_push($agenda["contenu"],
                        array(                        
			"type" => "DST",
                        "description" => "$matiere",                        
			"classe" => "$classe",
                        "salle" => "$salle",
                        "date" => "$date",
                        "heure" => "$heure",
                        "duree" => "$duree"
                        ) );
		}			
	  	for($i=0;$i<count($data2);$i++) {
			$ii++;
			$date=$data2[$i][1];
			$description=$data2[$i][2];
			array_push($agenda["contenu"],
                        array(                        
			"type" => "EVENEMENT",
                        "description" => "$description"                        
                        ) );
		}			

    	}elseif ($_GET["mode"] == "defaut") { // Si mode = defaut alors fourniture des DST et évènements à venir sur la semaine glissante
		$data=affDst2($idClasse); //id_dst,date,matiere,code_classe,heure,duree,idsalle
		$data2=affEvenement();
		$nb=count($data)+count($data2);

		$ii=0;                
		$agenda = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];

	  	for($i=0;$i<count($data);$i++) {
			$ii++;	
			$matiere=$data[$i][2];
			$classe=$data[$i][3];
			$heure=$data[$i][4];
			$duree=$data[$i][5];
			$salle=$data[$i][6];
			$date=$data[$i][1];
			array_push($agenda["contenu"],
                        array(                        
			"type" => "DST",
                        "description" => "$matiere",                        
			"classe" => "$classe",
                        "salle" => "$salle",
                        "date" => "$date",
                        "heure" => "$heure",
                        "duree" => "$duree"
                        ) );
		}			
	  	for($i=0;$i<count($data2);$i++) {
			$ii++;
			$description=$data2[$i][2];
			$date=$data2[$i][1];
			array_push($agenda["contenu"],
                        array(                        
			"type" => "EVENEMENT",
                        "description" => "$description",
			"date"=>"$date"
                        ) );
		}			
    }else{
    	http_response_code(400);
        $agenda = ["code" => "400", "message" => "La demande n'a pas été formulée correctement" ];
    }
    echo json_encode($agenda);
}else{ /* Le demandeur ne possède pas de sessison */
	http_response_code(403);
    	header('Content-Type: application/json; charset=utf-8');
    	$message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas autorisé à accéder à ce contenu" ];
    	echo json_encode($message);
    	die();
}

Pgclose();

?>
