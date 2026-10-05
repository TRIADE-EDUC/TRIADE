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
include_once("../librairie_php/recupnoteperiode.php");
include_once("../librairie_php/timezone.php");

$prefixe=PREFIXE;

/* get-notes.php, le demandeur le consulte en mode GET avec session PHP */
/* Forme URL de la requête : http://serveur/triade/api-mobile/get-notes.php?annee=AAAA&mois=MM */


$_allowedMembresNotes = ['menueleve', 'menuparent'];
if (!in_array($_SESSION['membre'] ?? '', $_allowedMembresNotes)) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["code" => "403", "message" => "Accès réservé aux élèves et parents"]);
    die();
}
if(isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true and $_SERVER['REQUEST_METHOD'] === 'GET'){
//if(true){
	
	$cnx=cnx();

        $profil=$_SESSION["profil"]; // 1 = étudiant, 2 = parent, 3 = enseignant
        $idpers=$_SESSION["id_pers"];
        $idclasse=$_SESSION["idClasse"];
	$annee = intval($_GET["annee"] ?? 0);
	$mois  = intval($_GET["mois"]  ?? 0);

	/*
        $idpers=94;
        $profil=1;
        $idclasse=1;
	$annee=2025;
        $mois=2;	
	*/

	header('Content-Type: application/json; charset=utf-8');
    	if (isset($_GET["annee"]) and isset($_GET["mois"])) { // Arguments fournis
//    	if (true)  { // Arguments fournis


		if ($annee < 2000 || $annee > 2100 || $mois < 1 || $mois > 12) {
			http_response_code(400);
	                $notes = ["code" => "400", "message" => "Paramétres annee ou mois invalides" ];
	        	echo json_encode($notes);
			exit;
        	}

		if ($mois < 10) { if (strlen($mois) < 2) $mois="0".$mois; }
$sql=<<<SQL
SELECT
        m.code_mat,
        coef,
        sujet,
        DATE_FORMAT(date,'%d-%m-%Y'),
        TRUNCATE(note,2),
        n.typenote,
        n.notationsur,
        n.notevisiblele,
	n.noteexam,
	n.prof_id,
	n.id_groupe,
	n.id_classe
FROM
        {$prefixe}notes n, {$prefixe}matieres m
WHERE
        elev_id = '$idpers'
        AND DATE_FORMAT(date,'%m')='$mois'
        AND DATE_FORMAT(date,'%Y')='$annee'
        AND m.code_mat = n.code_mat
SQL;

		$curs=execSql($sql);
		$data=chargeMat($curs);
 

                $nb=count($data);
                $ii=0;
                $notes = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];
                for($i=0;$i<count($data);$i++) {
                        $ii++;
        		$idmatiere=$data[$i][0];
		        $coef=$data[$i][1];
		        $sujet=$data[$i][2];
		        $date=$data[$i][3];
		        $note=$data[$i][4];
		        $typenote=$data[$i][5];
		        $notationsur=$data[$i][6];
		        $notevisiblele=$data[$i][7];
		        $noteexam=$data[$i][8];
			$idprof=$data[$i][9];
			$idgroupe=$data[$i][10];
			$idclasse=$data[$i][11];
			$nommatiere=chercheMatiereNom($idmatiere);
			$moyenne=moyenneDevoir($idmatiere,$date,$idprof,$sujet,$coef,$noteexam,$idgroupe,$idclasse);
                        $moyenne=$moyenne['moy'];
                        $notemin=$moyenne['min'];
                        $notemax=$moyenne['max'];
			
			$note=preg_replace('/-1/','abs',$note);
			$note=preg_replace('/-2/','disp',$note);
			$note=preg_replace('/-3/',' ',$note);
			$note=preg_replace('/-4/','DNN',$note);
			$note=preg_replace('/-5/','DNR',$note);
                        $note=preg_replace('/-6/','VAL',$note);

			$note=preg_replace('/.00$/','',$note);
			
                        array_push($notes["contenu"],
                        	array(
		                    	"date" => "$date", // Date de saisie du devoir
	        		        "matiere" => "$nommatiere",
			                "sujet" => "$sujet", // Sujet du devoir (de la note)
			                "coefficient" => "$coef", // Coefficient au format décimal x.xx
	        		        "note" => "$note",
			                "bareme" => "$notationsur", // Note sur cette valeur (si "bareme" = 20, alors la note est sur 20)
			                "moyenne" => "$moyenne",
				        "notemin" => "$notemin",
		                	"notemax" => "$notemax"
                	        ) 
			);
                }
 	

/*
        $notes = [
            "nombre" => 5,
            "contenu" => [
                "init" => "init", // Très important
                "0" => [
                    "date" => "10/02/2025", // Date de saisie du devoir
                    "matiere" => "FRANCAIS",
                    "sujet" => "DST - Les Misérables", // Sujet du devoir (de la note)
                    "coefficient" => "1.00", // Coefficient au format décimal x.xx
                    "note" => "15",
                    "bareme" => "20", // Note sur cette valeur (si "bareme" = 20, alors la note est sur 20)
                    "moyenne" => "13.83",
                    "notemin" => "9",
                    "notemax" => "18",
                ],
                "1" => [
                    "date" => "10/02/2025", // Date de saisie du devoir
                    "matiere" => "MATHS",
                    "sujet" => "DST sur les unités de mesures", // Sujet du devoir (de la note)
                    "coefficient" => "1.00", // Coefficient au format décimal x.xx
                    "note" => "17",
                    "bareme" => "20", // Note sur cette valeur (si "bareme" = 20, alors la note est sur 20)
                    "moyenne" => "12.10",
                    "notemin" => "5",
                    "notemax" => "19",
                ],
                "2" => [
                    "date" => "10/02/2025", // Date de saisie du devoir
                    "matiere" => "SVT",
                    "sujet" => "TP - Extraction de l'ADN", // Sujet du devoir (de la note)
                    "coefficient" => "1.00", // Coefficient au format décimal x.xx
                    "note" => "20",
                    "bareme" => "25", // Note sur cette valeur (si "bareme" = 20, alors la note est sur 20)
                    "moyenne" => "14.20",
                    "notemin" => "10",
                    "notemax" => "24",
                ],
                "3" => [
                    "date" => "10/02/2025", // Date de saisie du devoir
                    "matiere" => "EPS",
                    "sujet" => "Ping-pong", // Sujet du devoir (de la note)
                    "coefficient" => "1.00", // Coefficient au format décimal x.xx
                    "note" => "18",
                    "bareme" => "20", // Note sur cette valeur (si "bareme" = 20, alors la note est sur 20)
                    "moyenne" => "15.14",
                    "notemin" => "3",
                    "notemax" => "20",
                ],
                "4" => [
                    "date" => "10/02/2025", // Date de saisie du devoir
                    "matiere" => "PHYSIQUE-CHIMIE",
                    "sujet" => "Ondes et signaux (TP)", // Sujet du devoir (de la note)
                    "coefficient" => "1.00", // Coefficient au format décimal x.xx
                    "note" => "14",
                    "bareme" => "20", // Note sur cette valeur (si "bareme" = 20, alors la note est sur 20)
                    "moyenne" => "14.08",
                    "notemin" => "8",
                    "notemax" => "20",
                ],
            ]
    ];
 */
	Pgclose();
	}else{
		http_response_code(400);
        	$notes = ["code" => "400", "message" => "La demande n'a pas été formulée correctement" ];
	}
	echo json_encode($notes);

}else{ /* Le demandeur ne possède pas de session */
	http_response_code(403);
    	header('Content-Type: application/json; charset=utf-8');
    	$message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas connecté" ];
    	echo json_encode($message);
    	die();
}
?>
