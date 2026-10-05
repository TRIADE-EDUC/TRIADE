<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.inc.php,v 1.62.2.2 2024/12/16 15:34:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $base_nocheck, $dbh;

require_once($include_path."/sessions.inc.php");

if(basename($_SERVER['SCRIPT_FILENAME']) !== "cms_vign.php"){
	header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: post-check=0, pre-check=0",false);
}

session_cache_limiter('must-revalidate');

session_start();

$result=pmb_mysql_query("SELECT CURRENT_DATE()");
$today = pmb_mysql_result($result, 0, 0);

//Pas la peine de faire le checkEmpr si nocheck sauf si on veut peter la session de l'utilisateur
if(empty($base_nocheck)) {
	$check_empr = checkEmpr("PmbOpac");
	if (!$check_empr) {
	    //on reteste la connexion car parfois le serveur mysql n'est plus accessible et entraine une deconnexion
	    $dbh = connection_mysql();
	    $check_empr = checkEmpr("PmbOpac");
	    if (!$check_empr) {
	        unset($_SESSION["user_code"]);
	    }
	}
}

if(isset($_GET["logout"])) {
	$logout = $_GET["logout"];
}
if (!isset($logout)) $logout=0;

//Sauvegarde de l'environnement
if (isset($_SESSION["user_code"]) && $_SESSION["user_code"]) {
	$requete="select count(*) from opac_sessions where empr_id='".intval($_SESSION["id_empr_session"])."'";
	if(!pmb_mysql_result(pmb_mysql_query($requete), 0, 0)) {
		//Première connexion à l'OPAC
		$_SESSION['empr_first_authentication'] = 1;
	} else {
		$_SESSION['empr_first_authentication'] = 0;
	}
	$requete="replace into opac_sessions (empr_id,session) values(".intval($_SESSION["id_empr_session"]).",'".addslashes(serialize($_SESSION))."')";
	pmb_mysql_query($requete);
}

//Si logout = 1, destruction de la session
if ($logout) { 
    logout();
}

//Si session en cours, récupération des préférences utilisateur
if (isset($_SESSION["user_code"]) && $_SESSION["user_code"]) {
	
	if(isset($_SESSION["user_expired"]) && $_SESSION["user_expired"]){
		$req_param = "select valeur_param from parametres where sstype_param='adhesion_expired_status' and type_param='opac'";
		$res_param = pmb_mysql_query($req_param);
		if($res_param && pmb_mysql_result($res_param,0,0)){
			$req = "select * from empr_statut where idstatut='".pmb_mysql_result($res_param,0,0)."'";
			$res = pmb_mysql_query($req);
			$data_expired = pmb_mysql_fetch_array($res);
			$droit_loan= $data_expired['allow_loan'];
			$droit_loan_hist= $data_expired['allow_loan_hist'];
			$droit_book= $data_expired['allow_book'];
			$droit_opac= $data_expired['allow_opac'];
			$droit_dsi= $data_expired['allow_dsi'];
			$droit_dsi_priv= $data_expired['allow_dsi_priv'];
			$droit_sugg= $data_expired['allow_sugg'];
			$droit_dema= $data_expired['allow_dema'];
			$droit_prol= $data_expired['allow_prol'];
			$droit_avis= $data_expired['allow_avis'];
			$droit_tag= $data_expired['allow_tag'];
			$droit_pwd= $data_expired['allow_pwd'];
			$droit_liste_lecture = $data_expired['allow_liste_lecture'];
			$droit_self_checkout = $data_expired['allow_self_checkout'];
			$droit_self_checkin = $data_expired['allow_self_checkin'];
			$droit_serialcirc = $data_expired['allow_serialcirc'];
			$droit_scan_request = $data_expired['allow_scan_request'];
			$droit_contribution = $data_expired['allow_contribution'];
			$droit_pnb = $data_expired['allow_pnb'];
		}	else {
			$droit_loan= 1;
			$droit_loan_hist=1;
			$droit_book= 1;
			$droit_opac= 1;
			$droit_dsi= 1;
			$droit_dsi_priv=1;
			$droit_sugg= 1;
			$droit_dema= 1;
			$droit_prol= 1;
			$droit_avis=1 ;
			$droit_tag= 1;
			$droit_pwd= 1;
			$droit_liste_lecture = 1;
			$droit_self_checkout=1;
			$droit_self_checkin=1;
			$droit_serialcirc=1;
			$droit_scan_request = 1;
			$droit_contribution = 1;
			$droit_pnb = 1;
		}		
	} else {
		$droit_loan= 1;
		$droit_loan_hist=1;
		$droit_book= 1;
		$droit_opac= 1;
		$droit_dsi= 1;
		$droit_dsi_priv=1;
		$droit_sugg= 1;
		$droit_dema= 1;
		$droit_prol= 1;
		$droit_avis=1 ;
		$droit_tag= 1;
		$droit_pwd= 1;
		$droit_liste_lecture = 1;
		$droit_self_checkout=1;
		$droit_self_checkin=1;
		$droit_serialcirc=1;
		$droit_scan_request = 1;
		$droit_contribution = 1;
		$droit_pnb = 1;
	}
	//Préférences utilisateur
	$query0 = "select * from empr, empr_statut where empr_login='".addslashes($_SESSION['user_code'])."' and idstatut=empr_statut limit 1";
	$req0 = pmb_mysql_query($query0);
	$data0 = pmb_mysql_fetch_array($req0);
	$id_empr = $data0['id_empr'];
	$empr_cb = $data0['empr_cb'];
	$empr_nom = $data0['empr_nom'];
	$empr_prenom= $data0['empr_prenom'];
	$empr_adr1= $data0['empr_adr1'];
	$empr_adr2= $data0['empr_adr2'];
	$empr_cp= $data0['empr_cp'];
	$empr_ville= $data0['empr_ville'];
	$empr_mail= $data0['empr_mail'];
	$empr_tel1= $data0['empr_tel1'];
	$empr_tel2= $data0['empr_tel2'];
	$empr_prof= $data0['empr_prof'];
	$empr_year= $data0['empr_year'];
	$empr_categ= $data0['empr_categ'];
	$empr_codestat= $data0['empr_codestat'];
	$empr_sexe= $data0['empr_sexe'];
	$empr_login= $data0['empr_login'];
	$empr_password= $data0['empr_password'];
	$empr_location= $data0['empr_location'];
	$empr_date_adhesion= $data0['empr_date_adhesion'];
	$empr_date_expiration= $data0['empr_date_expiration'];
	$empr_statut= $data0['empr_statut'];
	
	// droits de l'utilisateur
	$allow_loan= $data0['allow_loan'] & $droit_loan;
	$allow_loan_hist= $data0['allow_loan_hist'] & $droit_loan_hist;
	$allow_book= $data0['allow_book'] & $droit_book;
	$allow_opac= $data0['allow_opac'] & $droit_opac;
	$allow_dsi= $data0['allow_dsi'] & $droit_dsi;
	$allow_dsi_priv= $data0['allow_dsi_priv'] & $droit_dsi_priv;
	$allow_sugg= $data0['allow_sugg'] & $droit_sugg;
	$allow_dema= $data0['allow_dema'] & $droit_dema;
	$allow_prol= $data0['allow_prol'] & $droit_prol;
	$allow_avis= $data0['allow_avis'] & $droit_avis;
	$allow_tag= $data0['allow_tag'] & $droit_tag;
	$allow_pwd= $data0['allow_pwd'] & $droit_pwd;
	$allow_liste_lecture = $data0['allow_liste_lecture'] & $droit_liste_lecture;
	$allow_self_checkout= $data0['allow_self_checkout'] & $droit_self_checkout;
	$allow_self_checkin= $data0['allow_self_checkin'] & $droit_self_checkin;
	$allow_serialcirc= $data0['allow_serialcirc'] & $droit_serialcirc;
	$allow_scan_request = $data0['allow_scan_request'] & $droit_scan_request;
	$allow_contribution = $data0['allow_contribution'] & $droit_contribution;
	$allow_pnb = $data0['allow_pnb'] & $droit_pnb;
}else{

    //Pour la deconnexion externe
    //Il faut conserver l'id de la config d'authentification externe
    $ext_auth_config_id = 0;
    if( !empty($_SESSION['ext_auth_config_id']) ) {
        $ext_auth_config_id = $_SESSION['ext_auth_config_id'];
    }
    //Il faut conserver les attributs d'authentification externe
    $ext_auth_attrs = [];
    if( !empty($_SESSION['ext_auth_attrs']) ) {
        $ext_auth_attrs = $_SESSION['ext_auth_attrs'];
    }

	//pas de session authentifiée... AR veut une trace quand même
	check_anonymous_session('PmbOpac');
	$allow_loan= 0;
	$allow_loan_hist= 0;
	$allow_book= 0;
	$allow_opac= 0;
	$allow_dsi= 0;
	$allow_dsi_priv= 0;
	$allow_sugg= 0;
	$allow_dema= 0;
	$allow_prol= 0;
	$allow_avis= 0;
	$allow_tag= 0;
	$allow_pwd= 0;
	$allow_liste_lecture = 0;
	$allow_self_checkout= 0;
	$allow_self_checkin= 0;
	$allow_serialcirc= 0;
	$allow_scan_request = 0;
	$allow_contribution = 0;
	$allow_pnb = 0;

	//Pour la deconnexion externe
	$_SESSION['ext_auth_config_id'] = $ext_auth_config_id;
	$_SESSION['ext_auth_attrs'] = $ext_auth_attrs;

}

// message de debug messages ?
if (isset($check_messages)) {
    if ($check_messages==-1) $_SESSION["CHECK-MESSAGES"] = 0;
    if ($check_messages==1) $_SESSION["CHECK-MESSAGES"] = 1;
}

if(!isset($_SESSION["id_empr_session"])) $_SESSION["id_empr_session"] = '';
if(!isset($_SESSION["user_code"])) $_SESSION["user_code"] = '';
	