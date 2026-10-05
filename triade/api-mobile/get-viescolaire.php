<?php session_start();

/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 * 
*/
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

/* get-viescolaire.php, le demandeur le consulte en mode GET avec session PHP */

if(isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true and $_SERVER['REQUEST_METHOD'] === 'GET'){

    $profil=$_SESSION["profil"]; // 1 = étudiant, 2 = parent, 3 = enseignant
    $idpers=$_SESSION["id_pers"];
    $idclasse=$_SESSION["idClasse"];

    /*
    $idpers=94;	
    $profil=1;
    $idclasse=1;
    */

    $cnx=cnx();

    header('Content-Type: application/json; charset=utf-8');
    if (isset($_GET["categorie"])) { 
        if ($_GET["categorie"] == "absences") {
		if ($profil == "1" || $profil == "2") {
			$data=affAbsence($idpers,anneeScolaireViaIdClasse($idclasse));
                 	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier,  heure_saisie, heuredabsence, creneaux, smsenvoye , idrattrapage
			$nb=count($data);
        	        $viesco = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];	
			for($i=0;$i<count($data);$i++) {
				$date=dateForm($data[$i][1]);
				$saisie=dateForm($data[$i][2]);
				$duree=$data[$i][4];
				$motif=$data[$i][6];

				if ($duree == "-1") $duree=$data[$i][7];
				$duree=preg_replace('/\./',"h",$duree);

				array_push($viesco["contenu"],
				array(
		                        "date" => "$date",
		                        "saisie" => "$saisie",
		                        "duree" => "$duree",
		                        "motif" => "$motif"
		                ) );
			}
		}

        } else if ($_GET["categorie"] == "retards") {
		if ($profil == "1" || $profil == "2") {
			$data=affRetard($idpers,anneeScolaireViaIdClasse($idclasse));
        		//elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux , idrattrapage 
			$nb=count($data);
        	        $viesco = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];	
			for($i=0;$i<count($data);$i++) {
				$date=dateForm($data[$i][2]);
				$heure=timeForm($data[$i][1]);
				$duree=$data[$i][5];
				$motif=$data[$i][6];

				if ($duree == "-1") $duree=$data[$i][7];
				$duree=preg_replace('/\./',"h",$duree);

				array_push($viesco["contenu"],
				array(
				"date" => "$date", // date au format JJ/MM/AAAA
        		        "heure" => "$heure", // heure de début de retard, au format HH:MM
				"duree" => "$duree", // durée,en minutes (15min) ou en heures (1h15) (en fonction d
                        	"motif" => "$motif", // Motif, s'il y en a aucun, mettre "--inconnu--"

		                ) );
			}
		}
        } else if ($_GET["categorie"] == "dispenses") {

	    $data=affDispence($idpers,anneeScolaireViaIdClasse($idclasse));
            // elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 
	    $nb=count($data);
            $viesco = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];
            for($i=0;$i<count($data);$i++) {
		    $matiere=trim(chercheMatiereNom($data[$i][1]));
		    $motif=$data[$i][7];
		    $datedebut=dateForm($data[$i][2]);
		    $datefin=dateForm($data[$i][3]);
		    $heure1=$data[$i][8];
		    $jours1=$data[$i][9];
		    $heure2=$data[$i][10];
		    $jours2=$data[$i][11];
		    $heure3=$data[$i][12];
		    $jours3=$data[$i][13];

		    array_push($viesco["contenu"],
                    array(
                        "matiere" => "$matiere", // Matiere concerné
                        "motif" => "$motif", // Motif, s'il y en a aucun, mettre "--inconnu--"
                        "datedebut" => "$datedebut", // date au format JJ/MM/AAAA 
                        "datefin" => "$datefin", // date au format JJ/MM/AAAA 
                        "jours" => "$jours1, $jours2, $jours3 ", // Liste de jours abrégés L, M, Me, J, V
                        "heures" => "$heure1, $heure2, $heure3 ", //listes des heures de dispenses
                    ) );
	    }
        } else if ($_GET["categorie"] == "discipline-sanctions") {

		$data=affSanction_par_eleve($idpers,anneeScolaireViaIdClasse($idclasse));
       		// id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire,description_fait FROM 
	    	$nb=count($data);
	        $viesco = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];
        	for($i=0;$i<count($data);$i++) {
			$datesaisie=dateForm($data[$i][4]);
			$auteur=$data[$i][7];
			$category=rechercheSanction($data[$i][3]);
			$typesanction=rechercheSanction($data[$i][3]);
			$motif=$data[$i][2];
			$devoir=$data[$i][8];
			array_push($viesco["contenu"],
                	array(
			"saisie" => "$datesaisie", // date au format JJ/MM/AAAA
                        "auteur" => "$auteur", // Auteur de la sanction
                        "categorie" => "$category", // Catégorie de l'incident disciplinaire (Incivilité, Perturbations, ...)
                        "type" => "$typesanction", // Type de sanction
                        "description" => "$motif", // date au format JJ/MM/AAAA
                        "devoir" => "$devoir", // Devoir à faire
                	) );
		}
        } else if ($_GET["categorie"] == "discipline-retenues") {

		$data=affRetenuTotal_par_eleve($idpers,anneeScolaireViaIdClasse($idclasse));
        	/*
id_elev
date_de_la_retenue
heure_de_la_retenue
date_de_saisie
origi_saisie
id_category
retenue_effectuer
motif
attribuer_par
signature_parent
duree_retenu
devoir_a_faire
description_fait
courrier_env
repport_du 
*/
		$nb=count($data);
	        $viesco = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];
        	for($i=0;$i<count($data);$i++) {
			$datesaisie=dateForm($data[$i][3]);
			$auteur=$data[$i][8];
			$category=rechercheSanction($data[$i][5]);
			$typesanction=rechercheSanction($data[$i][5]);
			$motif=$data[$i][7];
			$devoir=$data[$i][11];
			$retenueffectue=($data[$i][6] == "0") ? "non" : "oui";
			$dateretenue=dateForm($data[$i][1]);
			$heureretenue=timeForm($data[$i][2]);
			$dureeretenue=timeForm($data[$i][10]);
			array_push($viesco["contenu"],
                	array(
			"saisie" => "$datesaisie", // date de saisie de la retenue au format JJ/MM/AAAA
                        "auteur" => "$auteur", // Auteur de la sanction/retenue
                        "categorie" => "$category", // Catégorie de l'incident disciplinaire (Incivilité, Perturbations, ...)
                        "type" => "$category", // Type de sanction/retenue
                        "description" =>"$motif", // date au format JJ/MM/AAAA
                        "devoir" => "$devoir", // Devoir à faire (lors de la retenue dans ce contexte)
                        "effectue" => "$retenueffectue", // Si la retenue a été effectué ou non
                        "dateretenue" => "$dateretenue", // date au format JJ/MM/AAAA
                        "heureretenue" => "$heureretenue", // Heure de début de la retenue au format HH:MM
			"dureeretenue" => "$dureeretenue", // Durée de la retenue au format HH:MM

                	) );
		}
        } else {
            http_response_code(400);
            $viesco = ["code" => "400", "message" => "La demande n'a pas été formulée correctement" ];
        }
    } else {
        http_response_code(400);
        $viesco = ["code" => "400", "message" => "La demande n'a pas été formulée correctement" ];
    }
    echo json_encode($viesco);
} else { /* Le demandeur ne possède pas de session */
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    $message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas connecté" ];
    echo json_encode($message);
    die();
}

Pgclose();
?>
