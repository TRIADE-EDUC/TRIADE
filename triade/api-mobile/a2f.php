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
include_once("./a2fhash.php");

$cnx=cnx();
/* a2f.php, le demandeur le consulte en mode POST avec session PHP */

/* NOTE : Le code A2F fourni valide (ou non) la double authentifiaction du compte stocké temporairement (sans estampillé en tant que connecté) dans la session */

if(isset($_SESSION["connecte"]) and isset($_POST["code"]) and $_SERVER['REQUEST_METHOD'] === 'POST'){
    if ($_SESSION["connecte"] == false){
        header('Content-Type: application/json; charset=utf-8');

	include_once("../librairie_php/GoogleAuthenticator.php");
        $secret=recupSecretAuthentificator($_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent']);
	$ga = new PHPGangsta_GoogleAuthenticator();
        $code = $_POST["code"];
        if (empty($secret)) {
            echo json_encode(["code" => "500", "message" => "Secret Google Authenticator non configuré pour ce compte", "id_pers" => $_SESSION['id_pers'], "membre" => $_SESSION['membre']]);
            Pgclose(); die();
        }
        $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
 
        if ($checkResult){
	    $jeton=verifJetonMobile($_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent']);
	    if (trim($jeton) == "") {
		    $jeton=genererHashA2F($_SESSION['id_pers'],$_SESSION['membre']);
		    memoJetonMobile($_SESSION['id_pers'],$_SESSION['membre'],$jeton,$_SESSION['idparent']);
	    }
	    // Enregistre l'IP comme de confiance pour 60 jours
	    ajouterOuMajIpMobile($_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent'],getClientIP());
            $_SESSION["connecte"] = true;
            $message = [ "code" => "200" , "message" => "A2F effectué avec succès", "jeton" => "$jeton" ];
        }else{
            http_response_code(401);
            $message = [ "code" => "401-A-E" , "message" => "Echec de l'A2F" ];
        }
    } else {
        http_response_code(409);
        header('Content-Type: application/json; charset=utf-8');
        $message = [ "code" => "409" , "message" => "Vous avez soumis un code alors que vous êtes déjà connecté" ];
    }
    Pgclose();
    echo json_encode($message);
} else { /* Le demandeur ne possède pas de sessison */
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    $message = [ "code" => "403" , "message" => "Accès interdit" ];
    echo json_encode($message);
    die();
}


?>
