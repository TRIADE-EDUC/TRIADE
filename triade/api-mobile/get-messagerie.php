<?php 
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../librairie_php/langue-text-fr.php");

$idClasse=$_SESSION["idClasse"];
$cnx=cnx();

/* get-cahierdetextes.php, le demandeur le consulte en mode GET avec session PHP */
/* Forme URL de la requête : http://serveur/triade/api-mobile/get-cahierdetextes.php?categorie=[categorie]&date=AAAA-MM-JJ */

$_SESSION['membre']="menueleve";
$_SESSION['id_pers']="99";
$offset=0;
$limit=10;

if ($_SESSION["membre"] == "menuadmin") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ADM);
$type_personne="ADM";}
if ($_SESSION["membre"] == "menututeur") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],TUT);
$type_personne="TUT";}
if ($_SESSION["membre"] == "menupersonnel") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],PER);
$type_personne="PER";}
if ($_SESSION["membre"] == "menuprof") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ENS);
$type_personne="ENS";}
if ($_SESSION["membre"] == "menuscolaire") {
$destinataire=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],MVS);
$type_personne="MVS";}
if ($_SESSION["membre"] == "menuparent") {
$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
$type_personne="PAR";}
if ($_SESSION["membre"] == "menueleve") {
$destinataire=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);
$type_personne="ELE";}


//if(isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true and $_SERVER['REQUEST_METHOD'] === 'GET'){
if (true) {
	header('Content-Type: application/json; charset=utf-8');
	$data=affichage_messagerie_limit($type_personne,$_SESSION['id_pers'],$offset,$limit,"","",'0');
	// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur 

	$nb=count($data);
        $ii=0;
        $messagerie = [ "nombre" => "$nb" , "contenu" => ["init" => "init"] ];	

	for($i=0;$i<count($data);$i++) {
		$ii++;
		$datesaisie=$data[$i][4];
		$objet=$data[$i][8];
		$contenu=$data[$i][3];
		$id_message=$data[$i][0];

		array_push($messagerie["contenu"],
                array(
			"datesaisie" => "$datesaisie", // Date de saisie du devoir
                        "objet" => "$objet",
                        "contenu" => "$contenu", // Contneu du devoir
                        "id_message" => "$id_message"

			) );
	}
	echo json_encode($messagerie);
}else{ /* Le demandeur ne possède pas de session */
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    $message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas connecté" ];
    echo json_encode($message);
    die();
}

?>
