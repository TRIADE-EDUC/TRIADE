<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sessions.inc.php,v 1.9 2018-08-08 09:34:36 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions de gestion des sessions

// prevents direct script access
if(preg_match('/sessions\.inc\.php/', $REQUEST_URI)) {
	include('./forbidden.inc.php'); forbidden();
}

define( 'CHECK_EMPR_NO_SESSION', 1 );
define( 'CHECK_EMPR_SESSION_DEPASSEE', 2 );
define( 'CHECK_EMPR_SESSION_INVALIDE', 3 );
define( 'CHECK_EMPR_AUCUN_DROITS', 4 );
define( 'CHECK_EMPR_PB_ENREG_SESSION', 5 );
define( 'CHECK_EMPR_PB_OUVERTURE_SESSION', 6 );
define( 'CHECK_EMPR_SESSION_OK', 7 );

// checkEmpr : authentification de l'utilisateur
function checkEmpr($SESSNAME, $allow=0,$user_connexion='') {
	global $dbh;
	global $checkempr_type_erreur ;
	global $check_messages;
	global $opac_duration_session_auth;
	// par défaut : pas de session ouverte
	$checkempr_type_erreur = CHECK_EMPR_NO_SESSION ;
	
	// On n'a pa encore globalisé les paramètres, on va chercher la durée de session directement dans la table
	$query = "select valeur_param from parametres where type_param = 'opac' and sstype_param = 'duration_session_auth'";
	$result = pmb_mysql_query($query, $dbh);
	$opac_duration_session_auth = pmb_mysql_result($result, 0, 0);
	
	// récupère les infos de session dans les cookies
	$PHPSESSID = (isset($_COOKIE["$SESSNAME-SESSID"]) ? $_COOKIE["$SESSNAME-SESSID"] : '');
	if ($user_connexion) {
		$PHPSESSLOGIN = $user_connexion; 
	} else {
		if(isset($_COOKIE["$SESSNAME-LOGIN"])) {
			$PHPSESSLOGIN = $_COOKIE["$SESSNAME-LOGIN"];
		} else {
			$PHPSESSLOGIN = '';
		}
	}
	$PHPSESSNAME = (isset($_COOKIE["$SESSNAME-SESSNAME"]) ? $_COOKIE["$SESSNAME-SESSNAME"] : '');
	
	// on récupère l'IP du client
	$ip = $_SERVER['REMOTE_ADDR'];

	// recherche de la session ouverte dans la table
	$query = "SELECT SESSID, login, IP, SESSstart, LastOn, SESSNAME FROM sessions WHERE ";
	$query .= "SESSID='$PHPSESSID'";
	$result = pmb_mysql_query($query, $dbh);
	$numlignes = pmb_mysql_num_rows($result);

	if(!$result || !$numlignes) {
		$checkempr_type_erreur = CHECK_EMPR_NO_SESSION ;
		return FALSE;
	}
	
	// vérification de la durée de la session
	$session = pmb_mysql_fetch_object($result);
	// durée depuis le dernier rafraichissement
	if(($session->LastOn+$opac_duration_session_auth) < time()) {
		$checkempr_type_erreur = CHECK_EMPR_SESSION_DEPASSEE ;
		return FALSE;
	}
	// durée depuis le début de la session, max 12h
	if(($session->SESSstart+43200) < time()) {
		$checkempr_type_erreur = CHECK_EMPR_SESSION_DEPASSEE ;
		return FALSE;
	}
	
	// il faut stocker le sessid parce FL réutilise le tableau session pour aller lire les infos de users !!!
	if($session->SESSID=="") {
		$checkempr_type_erreur = CHECK_EMPR_SESSION_INVALIDE ;
		return FALSE;
	} else {
		$id_session = $session->SESSID ;
		$SESSstart_session = $session->SESSstart ;
	}

	// authentification OK, on remet LAstOn à jour
	$t = time();
	
	// on avait bien stocké le sessid, on va pouvoir remettre à jour le laston, avec sessid dans la clause where au lieu de id en outre.
	$query = "UPDATE sessions SET LastOn='$t' WHERE sessid='$id_session' ";
	$result = pmb_mysql_query($query, $dbh) or die (pmb_mysql_error());

	if(!$result) {
		$checkempr_type_erreur = CHECK_EMPR_PB_ENREG_SESSION ;
		return FALSE;
	}
	
	// récupération de la langue de l'utilisateur

	// mise à disposition des variables de la session
	define('SESSlogin'	, $PHPSESSLOGIN);
	define('SESSname'	, $SESSNAME);
	define('SESSid'		, $PHPSESSID);
	define('SESSstart'	, $SESSstart_session);
	
	return TRUE;
	}

// startSession : fonction de démarrage d'une session
function startSession($SESSNAME, $login) {
	global $dbh; // le lien MySQL
	global $stylesheet; /* pour qu'à l'ouverture de la session le user récupère de suite son style */
	global $checkempr_type_erreur ;
	global $PMBdatabase ;
	
	// nettoyage des sessions 'oubliées'
	cleanTable($SESSNAME);

	// génération d'un identificateur unique

	// initialisation du générateur de nombres aléatoires
	mt_srand((float) microtime()*1000000);

	// nombre aléatoire entre 1111111111 et 9999999999
	$SESSID = mt_rand(1111111111, 9999999999);

	// début session (date UNIX)
	$SESSstart = time();

	// adresse IP du client
	$IP = $_SERVER['REMOTE_ADDR'];

	// inscription de la session dans la table
	$query = "INSERT INTO sessions (SESSID, login, IP, SESSstart, LastOn, SESSNAME) VALUES(";
	$query .= "'$SESSID'";
	$query .= ", '$login'";
	$query .= ", '$IP'";
	$query .= ", '$SESSstart'";
	$query .= ", '$SESSstart'";
	$query .= ", '$SESSNAME' )";

	$result = pmb_mysql_query($query, $dbh);
	if(!$result) {
		$checkempr_type_erreur = CHECK_EMPR_PB_OUVERTURE_SESSION ;
		return CHECK_EMPR_PB_OUVERTURE_SESSION ;
	}

	// cookie pour le login de l'utilisateur
	setcookie($SESSNAME."-LOGIN", $login, 0);

	// cookie pour le nom de la session
	setcookie($SESSNAME."-SESSNAME", $SESSNAME, 0);

	// cookie pour l'ID de session
	setcookie($SESSNAME."-SESSID", $SESSID, 0);

	// cookie pour la base de donnée
	setcookie($SESSNAME."-DATABASE", $PMBdatabase, 0);

	// mise à disposition des variables de la session
	if(!defined('SESSlogin')) define('SESSlogin'	, $login);
	if(!defined('SESSname')) define('SESSname'	, $SESSNAME);
	if(!defined('SESSid')) define('SESSid'		, $SESSID);
	if(!defined('SESSstart')) define('SESSstart'	, $SESSstart);
	
	//Ouverture de la session php
	header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: post-check=0, pre-check=0",false);
	session_cache_limiter('must-revalidate');
	session_name("pmb".SESSid);
	session_start();

	return CHECK_EMPR_SESSION_OK ;
	}

// cleanTable : nettoyage des sessions terminées (user non-deconnecté)
function cleanTable($SESSNAME) {
	global $dbh;
	global $opac_duration_session_auth;
	
	// heure courante moins une heure
	$time_out = time() - $opac_duration_session_auth;

	// suppression des sessions inactives
	$query = "DELETE FROM sessions WHERE LastOn < ".$time_out." and SESSNAME = '".$SESSNAME."'";
	$result = pmb_mysql_query($query, $dbh);
	}

// sessionDelete : fin d'une session
function sessionDelete($SESSNAME) {
	global $dbh;

	$login = $_COOKIE[$SESSNAME.'-LOGIN'];

	$PHPSESSID = $_COOKIE["$SESSNAME-SESSID"];
	$PHPSESSLOGIN = $_COOKIE["$SESSNAME-LOGIN"];
	$PHPSESSNAME = $_COOKIE["$SESSNAME-SESSNAME"];



	// altération du cookie-client (au cas où la suppression ne fonctionnerait pas)

	setcookie($SESSNAME."-LOGIN", "no_login", 0);
	setcookie($SESSNAME."-SESSNAME", "no_session", 0);
	setcookie($SESSNAME."-SESSID", "no_id_session", 0);

	// tentative de suppression ddes cookies

	setcookie($SESSNAME."-SESSNAME");
	setcookie($SESSNAME."-SESSID");
	setcookie($SESSNAME."-LOGIN");

	//Destruction de la session php
	session_destroy();

	// effacement de la session de la table des sessions

	$query = "DELETE FROM sessions WHERE login='".addslashes($login)."'";
	$query .= " AND SESSNAME='".addslashes($SESSNAME)."' and SESSID='".addslashes($PHPSESSID)."'";

	$result = @pmb_mysql_query($query, $dbh);
	if($result)
		return TRUE;

	return FALSE;

	}

function check_anonymous_session($SESSNAME){
	global $dbh;
	global $check_messages;
	// par défaut : pas de session ouverte
	$checkempr_type_erreur = CHECK_EMPR_NO_SESSION ;
	
	// récupère les infos de session dans les cookies
	$PHPSESSID = (isset($_COOKIE["$SESSNAME-SESSID"]) ? $_COOKIE["$SESSNAME-SESSID"] : '');
	if(isset($_COOKIE["$SESSNAME-LOGIN"])) {
		$PHPSESSLOGIN = $_COOKIE["$SESSNAME-LOGIN"];
	} else {
		$PHPSESSLOGIN = '';
	}
	$PHPSESSNAME = (isset($_COOKIE["$SESSNAME-SESSNAME"]) ? $_COOKIE["$SESSNAME-SESSNAME"] : '');
	
	// on récupère l'IP du client
	$ip = $_SERVER['REMOTE_ADDR'];
	
	// recherche de la session ouverte dans la table
	$query = "SELECT SESSID, login, IP, SESSstart, LastOn, SESSNAME FROM sessions WHERE ";
	$query .= "SESSID='".addslashes($PHPSESSID)."'";
	$result = pmb_mysql_query($query, $dbh);
	$numlignes = pmb_mysql_num_rows($result);	
	if(!$numlignes){
		startSession($SESSNAME, "");
	}else{
		// On remet LAstOn à jour
		$t = time();
		// on avait bien stocké le sessid, on va pouvoir remettre à jour le laston, avec sessid dans la clause where au lieu de id en outre.
		$query = "UPDATE sessions SET LastOn='$t' WHERE sessid='$PHPSESSID' ";
		$result = pmb_mysql_query($query, $dbh);
	}
		
}