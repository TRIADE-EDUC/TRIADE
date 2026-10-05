<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sessions.inc.php,v 1.69.2.3.2.1 2025/02/11 09:20:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions de gestion des sessions

// prevents direct script access
if(preg_match('/sessions\.inc\.php/', $REQUEST_URI)) {
	include('./forbidden.inc.php'); forbidden();
}

global $class_path, $include_path;
require_once "{$include_path}/pmb_cookie.inc.php";
require_once "{$class_path}/cache_factory.class.php";

define( 'CHECK_USER_NO_SESSION', 1 );
define( 'CHECK_USER_SESSION_DEPASSEE', 2 );
define( 'CHECK_USER_SESSION_INVALIDE', 3 );
define( 'CHECK_USER_AUCUN_DROITS', 4 );
define( 'CHECK_USER_PB_ENREG_SESSION', 5 );
define( 'CHECK_USER_PB_OUVERTURE_SESSION', 6 );
define( 'CHECK_USER_SESSION_OK', 7 );

// checkUser : authentification de l'utilisateur
function checkUser($SESSNAME, $allow=0,$user_connexion='') {
	global $nb_per_page_author,$nb_per_page_publisher,$nb_per_page_collection,$nb_per_page_subcollection,$nb_per_page_serie;
	global $nb_per_page_a_search,$nb_per_page_p_search,$nb_per_page_s_search,$nb_per_page_empr ;
	global $nb_per_page_a_select,$nb_per_page_c_select,$nb_per_page_sc_select,$nb_per_page_p_select,$nb_per_page_s_select ;
	global $param_popup_ticket, $param_sounds , $param_licence ;
	
	global $nb_per_page_search, $nb_per_page_select, $nb_per_page_gestion ;
	global $nb_per_page, $nb_per_page_custom;
	
	global $PMBuserid, $PMBusername;
	global $checkuser_type_erreur ;
	global $stylesheet ;
	global $explr_invisible ;
	global $explr_visible_unmod ;
	global $explr_visible_mod ;
	global $check_messages;
	global $pmb_session_reactivate, $pmb_session_maxtime;
	// par défaut : pas de session ouverte
	$checkuser_type_erreur = CHECK_USER_NO_SESSION ;
	
	// récupère les infos de session dans les cookies
	$PHPSESSID = (isset($_COOKIE["$SESSNAME-SESSID"]) ? $_COOKIE["$SESSNAME-SESSID"] : '');
	if ($user_connexion) $PHPSESSLOGIN = $user_connexion; 
	else $PHPSESSLOGIN = (isset($_COOKIE["$SESSNAME-LOGIN"]) ? $_COOKIE["$SESSNAME-LOGIN"] : '');
	$PHPSESSNAME = (isset($_COOKIE["$SESSNAME-SESSNAME"]) ? $_COOKIE["$SESSNAME-SESSNAME"] : '');

	// message de debug messages ?
	if ($check_messages==-1) {
	    pmb_setcookie($SESSNAME."-CHECK-MESSAGES", 0, 0);
	    //ré-affectation de la clé à la volée pour utilisation sur la même page
	    //pmb_setcookie n'a pas d'effet immediat
	    $_COOKIE["$SESSNAME-CHECK-MESSAGES"] = 0;
	}
	if ($check_messages==1) {
	    pmb_setcookie($SESSNAME."-CHECK-MESSAGES", 1, 0);
	    //ré-affectation de la clé à la volée pour utilisation sur la même page
	    //pmb_setcookie n'a pas d'effet immediat
	    $_COOKIE["$SESSNAME-CHECK-MESSAGES"] = 1;
	}

	// on récupère l'IP du client
	$ip = $_SERVER['REMOTE_ADDR'];

	// recherche de la session ouverte dans la table
	$query = "SELECT SESSID, login, IP, SESSstart, LastOn, SESSNAME FROM sessions WHERE ";
	$query .= "SESSID='".addslashes($PHPSESSID)."' and login = '".addslashes($PHPSESSLOGIN)."'";
	$txt_er = $query;
	$result = pmb_mysql_query($query);
	$numlignes = pmb_mysql_num_rows($result);

	if(!$result || !$numlignes) {
		$checkuser_type_erreur = CHECK_USER_NO_SESSION ;
		define('SESSname', '');
		return FALSE;
	}
	
	// vérification de la durée de la session
	$session = pmb_mysql_fetch_object($result);
	// durée depuis le dernier rafraichissement
	if(!defined('SESSION_REACTIVATE')) {
		if(!empty($pmb_session_reactivate)) {
			define('SESSION_REACTIVATE', $pmb_session_reactivate);
		} else {
			define('SESSION_REACTIVATE', 7200); // refresh max = 120 minutes
		}
	}
	if(($session->LastOn+SESSION_REACTIVATE) < time()) {
		// On supprime l'ancienne session avant de renvoyer l'erreur
		sessionDelete('PhpMyBibli');

		$checkuser_type_erreur = CHECK_USER_SESSION_DEPASSEE ;
		return FALSE;
	}
	// durée depuis le début de la session
	if(!defined('SESSION_MAXTIME')) {
		if(!empty($pmb_session_maxtime)) {
			define('SESSION_MAXTIME', $pmb_session_maxtime);
		} else {
			define('SESSION_MAXTIME', 86400);	// durée de vie maximum d'une session = 24h
		}
	}
	if(($session->SESSstart+SESSION_MAXTIME) < time()) {
		// On supprime l'ancienne session avant de renvoyer l'erreur
		sessionDelete('PhpMyBibli');

		$checkuser_type_erreur = CHECK_USER_SESSION_DEPASSEE ;
		define('SESSname', '');
		return FALSE;
	}
	
	// il faut stocker le sessid parce FL réutilise le tableau session pour aller lire les infos de users !!!
	if($session->SESSID=="") {
		$checkuser_type_erreur = CHECK_USER_SESSION_INVALIDE ;
		define('SESSname', '');
		return FALSE;
	} else {
		$id_session = $session->SESSID ;
		$SESSstart_session = $session->SESSstart ;
	}
	// contrôle des droits utilisateurs
	$query = "SELECT * FROM users WHERE username='".addslashes($PHPSESSLOGIN)."'";
	$result = pmb_mysql_query($query);
	$session = pmb_mysql_fetch_object($result);

	if($allow) {
		if(!($allow & $session->rights)) {
			$checkuser_type_erreur = CHECK_USER_AUCUN_DROITS;
			define('SESSname', '');
			return FALSE;
		}
	}

	// authentification OK, on remet LAstOn à jour
	$t = time();
	$id = $id_session;
	
	// on en profite pour récupérer l'id du user
	$PMBuserid = $session->userid;
	$PMBusername = $session->username;
	
	// on avait bien stocké le sessid, on va pouvoir remettre à jour le laston, avec sessid dans la clause where au lieu de id en outre.
	$query = "UPDATE sessions SET LastOn='$t' WHERE sessid='$id' ";
	$result = pmb_mysql_query($query) or die (pmb_mysql_error());

	if(!$result) {
		$checkuser_type_erreur = CHECK_USER_PB_ENREG_SESSION ;
		define('SESSname', '');
		return FALSE;
	}
	
	// récupération de la langue de l'utilisateur

	// mise à disposition des variables de la session
	if(!defined('SESSlogin')) define('SESSlogin'	, addslashes($PHPSESSLOGIN));
	if(!defined('SESSname')) define('SESSname'	, addslashes($SESSNAME));
	if(!defined('SESSid')) define('SESSid'		, addslashes($PHPSESSID));
	if(!defined('SESSstart')) define('SESSstart'	, $SESSstart_session);
	if(!defined('SESSlang')) define('SESSlang'	, $session->user_lang);
	if(!defined('SESSrights')) define('SESSrights'	, $session->rights);
	if(!defined('SESSuserid')) define('SESSuserid'	, $session->userid);
	
	/* Nbre d'enregistrements affichés par page */
	/* l'usager a demandé à voir plus de résultats dans sa liste paginée */
	$nb_per_page = intval($nb_per_page);
	$nb_per_page_custom = intval($nb_per_page_custom);
	if($nb_per_page) $nb_per_page_custom = $nb_per_page;
	if($nb_per_page_custom) {
		$session->nb_per_page_search = $nb_per_page_custom;
		$session->nb_per_page_select = $nb_per_page_custom;
		$session->nb_per_page_gestion = $nb_per_page_custom;
		$nb_per_page = $nb_per_page_custom;
	}
	/* gestion */ 
	$nb_per_page_author = $session->nb_per_page_gestion ;
	$nb_per_page_publisher = $session->nb_per_page_gestion ;
	$nb_per_page_collection = $session->nb_per_page_gestion ;
	$nb_per_page_subcollection = $session->nb_per_page_gestion ;
	$nb_per_page_serie = $session->nb_per_page_gestion ;
	$nb_per_page_search = $session->nb_per_page_search ;
	$nb_per_page_select = $session->nb_per_page_select ;
	$nb_per_page_gestion = $session->nb_per_page_gestion ;
	
	/* param par défaut */
	load_user_param();
	/* on va chercher la feuille de style du user */
	global $deflt_styles;
	$stylesheet = $deflt_styles ;
	
	/* param de la localisation */
	load_location_param();
	
	/* recherches */
	/* author */
	$nb_per_page_a_search = $session->nb_per_page_search ;
	/* publisher */
	$nb_per_page_p_search = $session->nb_per_page_search ;
	/* subject */
	$nb_per_page_s_search = $session->nb_per_page_search ;
	
	/* lecteur */
	$nb_per_page_empr = $session->nb_per_page_search ;
	
	/* selectors */
	/* author */
	$nb_per_page_a_select = $session->nb_per_page_select ;
	/* collection */
	$nb_per_page_c_select = $session->nb_per_page_select ;
	/* sub-collection */
	$nb_per_page_sc_select = $session->nb_per_page_select ;
	/* publisher */
	$nb_per_page_p_select = $session->nb_per_page_select ;
	/* serie */
	$nb_per_page_s_select = $session->nb_per_page_select ;

	// pour visibilite des exemplaires :
	$explr_invisible = $session->explr_invisible ;
	$explr_visible_unmod = $session->explr_visible_unmod ;
	$explr_visible_mod = $session->explr_visible_mod ;
	
	//on a une langue par défaut, on fixe
	$pmb_indexation_lang = $session->user_lang;
	
	return TRUE;
}

// startSession : fonction de démarrage d'une session
function startSession($SESSNAME, $login, $database=LOCATION) {
	global $stylesheet; /* pour qu'à l'ouverture de la session le user récupère de suite son style */
	global $PMBuserid, $PMBusername;
	global $checkuser_type_erreur ;
	global $PMBdatabase ;
	
	if (!$PMBdatabase) $PMBdatabase=$database;
	
	// nettoyage des sessions 'oubliées'
	cleanTable($SESSNAME);

	// génération d'un identificateur unique

	// initialisation du générateur de nombres aléatoires
	mt_srand((float) microtime()*1000000);

	// nombre aléatoire entre 1111111111 et 9999999999
	$SESSID = mt_rand(1111111111, (int)9999999999);

	// début session (date UNIX)
	$SESSstart = time();

	// adresse IP du client
	$IP = $_SERVER['REMOTE_ADDR'];

	$query = "SELECT rights, user_lang FROM users WHERE username='".addslashes($login)."'";
	$result = pmb_mysql_query($query);
	$ff = pmb_mysql_fetch_object($result);
	$flag = $ff->rights;

	// inscription de la session dans la table
	$query = "INSERT INTO sessions (SESSID, login, IP, SESSstart, LastOn, SESSNAME) VALUES(";
	$query .= "'".addslashes($SESSID)."'";
	$query .= ", '".addslashes($login)."'";
	$query .= ", '$IP'";
	$query .= ", '".$SESSstart."'";
	$query .= ", '".$SESSstart."'";
	$query .= ", '".addslashes($SESSNAME)."' )";

	$result = pmb_mysql_query($query);
	if(!$result) {
		$checkuser_type_erreur = CHECK_USER_PB_OUVERTURE_SESSION ;
		return CHECK_USER_PB_OUVERTURE_SESSION ;
	}

	// cookie pour le login de l'utilisateur
	pmb_setcookie($SESSNAME."-LOGIN", $login, 0);

	// cookie pour le nom de la session
	pmb_setcookie($SESSNAME."-SESSNAME", $SESSNAME, 0);

	// cookie pour l'ID de session
	pmb_setcookie($SESSNAME."-SESSID", $SESSID, 0);

	// cookie pour la base de donnée
	pmb_setcookie($SESSNAME."-DATABASE", $PMBdatabase, 0);

	// mise à disposition des variables de la session
	define('SESSlogin'	, addslashes($login));
	define('SESSname'	, addslashes($SESSNAME));
	define('SESSid'		, addslashes($SESSID));
	define('SESSstart'	, $SESSstart);
	define('SESSlang'	, $ff->user_lang);
	define('SESSrights'	, $flag);
	
	/* param par défaut */	
	load_user_param();
	
	define('SESSuserid'	, $PMBuserid);
	/* on va chercher la feuille de style du user */
	global $deflt_styles;
	$stylesheet = $deflt_styles ;
	
	//Ouverture de la session php
	header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: post-check=0, pre-check=0",false);
	session_cache_limiter('must-revalidate');
	session_name("pmb".SESSid);
	session_start();
	
	//Récupération  de l'historique
    $admin_session = new admin_session();
    $admin_session->load();

	return CHECK_USER_SESSION_OK ;
}

// cleanTable : nettoyage des sessions terminées (user non-deconnecté)
function cleanTable($SESSNAME) {
	// heure courante moins une heure
	$time_out = time() - SESSION_MAXTIME;

	// suppression des sessions inactives
	$query = "DELETE FROM sessions WHERE LastOn < ".$time_out." and SESSNAME = '".addslashes($SESSNAME)."'";
	pmb_mysql_query($query);
}

// sessionDelete : fin d'une session
function sessionDelete($SESSNAME) {
	$login = $_COOKIE[$SESSNAME.'-LOGIN'];

	$PHPSESSID = $_COOKIE["$SESSNAME-SESSID"];
	$PHPSESSLOGIN = $_COOKIE["$SESSNAME-LOGIN"];
	$PHPSESSNAME = $_COOKIE["$SESSNAME-SESSNAME"];

	// altération du cookie-client (au cas où la suppression ne fonctionnerait pas)

	pmb_setcookie($SESSNAME."-LOGIN", "no_login", 0);
	pmb_setcookie($SESSNAME."-SESSNAME", "no_session", 0);
	pmb_setcookie($SESSNAME."-SESSID", "no_id_session", 0);

	// tentative de suppression ddes cookies

	pmb_setcookie($SESSNAME."-SESSNAME");
	pmb_setcookie($SESSNAME."-SESSID");
	pmb_setcookie($SESSNAME."-LOGIN");

	//Destruction de la session php
	session_destroy();

	// effacement de la session de la table des sessions

	$query = "DELETE FROM sessions WHERE login='".addslashes($login)."'";
	$query .= " AND SESSNAME='".addslashes($SESSNAME)."' and SESSID='".addslashes($PHPSESSID)."'";

	$result = pmb_mysql_query($query);
	if($result)
		return TRUE;

	return FALSE;
}

function load_user_param(){
	$nxtweb_params=array();
	
	$cache_php=cache_factory::getCache();
	if ($cache_php) {
		$db_user_parameters_name = SQL_SERVER.DATA_BASE.SESSlogin."_users";
		$db_user_parameters_datetime = SQL_SERVER.DATA_BASE.SESSlogin."_users_datetime";
		$tmp_user_parameters_datetime=$cache_php->getFromCache($db_user_parameters_datetime);
		if($tmp_user_parameters_datetime){
			$re_date="select if ((SELECT IF(UPDATE_TIME IS NULL,'3000-01-01 01:01:01',UPDATE_TIME) from information_schema.tables where table_schema='".DATA_BASE."' and table_name='users' ) >= '".$tmp_user_parameters_datetime."', 0, 1)";
			$cache_up_to_date = pmb_sql_value($re_date);
			if ($cache_up_to_date) {
				$nxtweb_params = $cache_php->getFromCache($db_user_parameters_name);
				if(is_countable($nxtweb_params) && count($nxtweb_params)){
					foreach( $nxtweb_params as $param_name => $param_value ) {
						if($param_name == "menusarray"){//Paramètre particulié
							if (is_array($param_value)) $_SESSION["AutoHide"]=$param_value;
						}else{
							global ${$param_name};
							${$param_name} = $param_value;
						}
					}
					return;//On a récupéré les paramètres
				}
			}
		}
	}
	
	global $PMBusernom, $PMBuserprenom, $PMBuseremail, $PMBgrp_num, $PMBuseremailbcc, $PMBuserid, $PMBusername, $pmb_indexation_lang;
	$requete_param = "SELECT * FROM users WHERE username='".SESSlogin."' LIMIT 1 ";
	$res_param = pmb_mysql_query($requete_param);
	$field_values = pmb_mysql_fetch_row( $res_param );
	$i = 0;
	while ($i < pmb_mysql_num_fields($res_param)) {
		$field = pmb_mysql_field_name($res_param, $i) ;
		$field_deb = substr($field,0,6);
		switch ($field_deb) {
			case "deflt_" :
			case "deflt2" :
			case "param_" :
			case "value_" :
			case "xmlta_" :
			case "deflt3" :
				global ${$field};
				${$field}=$field_values[$i];
				$nxtweb_params[$field]=$field_values[$i];
				break;
			default :
				break ;
		}
		if ($field == 'user_lang') {
			$pmb_indexation_lang = $field_values[$i];
			$nxtweb_params["pmb_indexation_lang"]=$field_values[$i];
		}
		$i++;
	}
	
	pmb_mysql_data_seek($res_param, 0);
	$param_nom = pmb_mysql_fetch_object($res_param);
	
	$PMBusernom=$param_nom->nom ;
	$nxtweb_params["PMBusernom"]=$param_nom->nom ;
	
	$PMBuserprenom=$param_nom->prenom ;
	$nxtweb_params["PMBuserprenom"]=$param_nom->prenom ;
	
	$PMBuseremail=$param_nom->user_email ;
	$nxtweb_params["PMBuseremail"]=$param_nom->user_email;
	
	$PMBgrp_num=$param_nom->grp_num;
	$nxtweb_params["PMBgrp_num"]=$param_nom->grp_num;
	
	$PMBuseremailbcc=$value_email_bcc ;
	$nxtweb_params["PMBuseremailbcc"]=$value_email_bcc;
	
	// pour que l'id user soit dispo partout
	$PMBuserid = $param_nom->userid;
	$nxtweb_params["PMBuserid"]=$param_nom->userid;
	
	$PMBusername = $param_nom->username;
	$nxtweb_params["PMBusername"]=$param_nom->username;
	
	$menusarray=unserialize($param_nom->environnement);
	$nxtweb_params["menusarray"]=$menusarray;
	
	if (is_array($menusarray)) $_SESSION["AutoHide"]=$menusarray;
		
	if($cache_php){
		$cache_php->setInCache($db_user_parameters_datetime, pmb_sql_value("select now()"));
		$cache_php->setInCache($db_user_parameters_name, $nxtweb_params);
	}
	
	return;
}

function load_location_param(){
    global $biblio_name,$biblio_adr1,$biblio_adr2,$biblio_cp,$biblio_town,$biblio_state,$biblio_country,$biblio_phone,$biblio_email;
    global $biblio_website,$biblio_logo,$biblio_commentaire;
    global $pmb_sur_location_activate, $deflt2docs_location;
    
    if ($deflt2docs_location) {
        $requete_param = "SELECT * FROM docs_location where idlocation='$deflt2docs_location'";
    } else {
        $requete_param = "SELECT * FROM docs_location limit 1";
    }
    $res_param = pmb_mysql_query($requete_param);
    $obj_location = pmb_mysql_fetch_object( $res_param ) ;
    $biblio_name=         translation::get_translated_text($deflt2docs_location, 'docs_location', 'name', $obj_location->name, SESSlang);
    $biblio_adr1=         translation::get_translated_text($deflt2docs_location, 'docs_location', 'adr1', $obj_location->adr1, SESSlang);
    $biblio_adr2=         translation::get_translated_text($deflt2docs_location, 'docs_location', 'adr2', $obj_location->adr2, SESSlang);
    $biblio_cp=           $obj_location->cp ;
    $biblio_town=         translation::get_translated_text($deflt2docs_location, 'docs_location', 'town', $obj_location->town, SESSlang);
    $biblio_state=        $obj_location->state ;
    $biblio_country=      $obj_location->country ;
    $biblio_phone=        $obj_location->phone ;
    $biblio_email=        $obj_location->email ;
    $biblio_website=      $obj_location->website ;
    $biblio_logo=         $obj_location->logo ;
    $biblio_commentaire=  $obj_location->commentaire ;
    
    if($pmb_sur_location_activate && $deflt2docs_location){
        if($obj_location->surloc_num && $obj_location->surloc_used){
            $requete="SELECT * FROM sur_location WHERE surloc_id='".$obj_location->surloc_num."' LIMIT 1";
            $sur_loc_session = pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete");
            if(pmb_mysql_num_rows($sur_loc_session)) {
                $sur_loc_session=pmb_mysql_fetch_object($sur_loc_session);
                $biblio_name=         (empty($sur_loc_session->surloc_name) ? $sur_loc_session->surloc_libelle : translation::get_translated_text($obj_location->surloc_num, 'sur_location', 'surloc_name', $sur_loc_session->surloc_name, SESSlang));
                $biblio_adr1=         translation::get_translated_text($obj_location->surloc_num, 'sur_location', 'surloc_adr1', $sur_loc_session->surloc_adr1, SESSlang);
                $biblio_adr2=         translation::get_translated_text($obj_location->surloc_num, 'sur_location', 'surloc_adr2', $sur_loc_session->surloc_adr2, SESSlang);
                $biblio_cp=           $sur_loc_session->surloc_cp;
                $biblio_town=         translation::get_translated_text($obj_location->surloc_num, 'sur_location', 'surloc_town', $sur_loc_session->surloc_town, SESSlang);
                $biblio_state=        $sur_loc_session->surloc_state;
                $biblio_country=      $sur_loc_session->surloc_country;
                $biblio_phone=        $sur_loc_session->surloc_phone;
                $biblio_email=        $sur_loc_session->surloc_email;
                $biblio_website=      $sur_loc_session->surloc_website;
                $biblio_logo=         $sur_loc_session->surloc_logo;
                $biblio_commentaire=  $obj_location->commentaire;
            }
        }
    }
}
