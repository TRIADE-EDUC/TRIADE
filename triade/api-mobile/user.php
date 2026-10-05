<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");


if (isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true){
	header('Content-Type: application/json; charset=utf-8');
	$cnx=cnx();
	$regime = "";
	$ine    = "";
	$a2f    = false;
	$profil = $_SESSION["profil"];

	if ($profil == "1") {
		$regime = recupRegime($_SESSION["id"]);
		$sql = "SELECT ine, googleAuthenEleve FROM " . PREFIXE . "eleves WHERE elev_id='" . intval($_SESSION["id_pers"]) . "' LIMIT 1";
		$res = execSql($sql);
		if ($res !== null && !DB::isError($res)) {
			$d = chargeMat($res);
			if (!empty($d)) {
				$ine = trim($d[0][0] ?? '');
				$a2f = intval($d[0][1] ?? 0) === 1;
			}
		}
	} elseif ($profil == "2") {
		$sql = "SELECT googleAuthenTuteur1 FROM " . PREFIXE . "eleves WHERE elev_id='" . intval($_SESSION["id_pers"]) . "' LIMIT 1";
		$res = execSql($sql);
		if ($res !== null && !DB::isError($res)) {
			$d = chargeMat($res);
			if (!empty($d)) $a2f = intval($d[0][0] ?? 0) === 1;
		}
	} elseif ($profil == "3" || $profil == "4" || $profil == "5") {
		$sql = "SELECT googleAuthen FROM " . PREFIXE . "personnel WHERE pers_id='" . intval($_SESSION["id_pers"]) . "' LIMIT 1";
		$res = execSql($sql);
		if ($res !== null && !DB::isError($res)) {
			$d = chargeMat($res);
			if (!empty($d)) $a2f = intval($d[0][0] ?? 0) === 1;
		}
	}

	// Vérification compte désactivé (offline)
	$offline = false;
	if ($profil == '1' || $profil == '2') {
		$res_off = execSql("SELECT compte_inactif FROM " . PREFIXE . "eleves WHERE elev_id='" . intval($_SESSION["id_pers"]) . "' LIMIT 1");
		$d_off = chargeMat($res_off);
		if (!empty($d_off) && ($d_off[0][0] == '1' || $d_off[0][0] === 't')) $offline = true;
	} elseif ($profil == '3' || $profil == '4' || $profil == '5') {
		$res_off = execSql("SELECT offline FROM " . PREFIXE . "personnel WHERE pers_id='" . intval($_SESSION["id_pers"]) . "' LIMIT 1");
		$d_off = chargeMat($res_off);
		if (!empty($d_off) && ($d_off[0][0] == '1' || $d_off[0][0] === 't')) $offline = true;
	}
	if ($offline) {
		Pgclose();
		echo json_encode(["code" => "disabled", "message" => "Votre compte a été désactivé par l'administration. Vous allez être déconnecté."]);
		die();
	}

	$info = [ "profil" => $profil, "nom" => $_SESSION["nom"], "prenom" => $_SESSION["prenom"], "id" => $_SESSION["id"], "membre" => $_SESSION["membre"], "codebarre" => $_SESSION["codebarre"], "regime" => "$regime", "ine" => $ine, "a2f" => $a2f ];
	Pgclose();
	echo json_encode($info);
}else{
	http_response_code(403);
	header('Content-Type: application/json; charset=utf-8');
	$message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas connecté" ];
	echo json_encode($message);
	die();
}

?>
