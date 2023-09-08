<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: do_resa.php,v 1.79 2019-06-04 13:22:53 ngantier Exp $

$base_path='.';

require_once($base_path.'/includes/init.inc.php');

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/plugins.inc.php');

// fonctions de gestion de formulaire
require_once($base_path.'/includes/templates/common.tpl.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// classe de gestion des réservations
require_once($base_path.'/classes/resa.class.php');
require_once($base_path.'/classes/resa_planning.class.php');

require_once($base_path.'/classes/quick_access.class.php');

// pour l'affichage correct des notices
require_once($base_path.'/includes/templates/notice.tpl.php');
require_once($base_path.'/includes/navbar.inc.php');
require_once($base_path.'/includes/explnum.inc.php');
require_once($base_path.'/includes/notice_affichage.inc.php');
require_once($base_path.'/includes/bulletin_affichage.inc.php');

require_once($base_path.'/includes/empr.inc.php');
require_once($base_path.'/includes/connexion_empr.inc.php');
// pour fonction de vérification de connexion
require_once($base_path.'/includes/empr_func.inc.php');

// autenticazione LDAP - by MaxMan
require_once($base_path.'/includes/ldap_auth.inc.php');

// RSS
require_once($base_path.'/includes/includes_rss.inc.php');

if ( ($lvl=='make_sugg' || $lvl=='valid_sugg') && $opac_show_suggest == 2) {
	//Suggestion possible sans authentification
	$log_ok = 1;
} else {
	//Vérification de la session
	// si paramétrage authentification particulière et pour la re-authentification ntlm
	$empty_pwd=true;
	$ext_auth=false;
	if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
		require_once($base_path.'/includes/ext_auth.inc.php');
	}
	$log_ok=connexion_empr();
}

if($opac_parse_html || $cms_active){
	ob_start();
}

if ($opac_resa_popup) {
	print $popup_header;
} else {
	if ($opac_show_homeontop==1) $std_header= str_replace('!!home_on_top!!',$home_on_top,$std_header);
	else $std_header= str_replace('!!home_on_top!!','',$std_header);
	// mise à jour du contenu opac_biblio_main_header
	$std_header= str_replace('!!main_header!!',$opac_biblio_main_header,$std_header);
	$std_header= str_replace('!!liens_rss!!',genere_link_rss(),$std_header);
	//Enrichissement OPAC
	if($opac_notice_enrichment){
		require_once($base_path."/classes/enrichment.class.php");
		$enrichment = new enrichment();
		$std_header = str_replace("!!enrichment_headers!!",$enrichment->getHeaders(),$std_header);
	} else {
		$std_header = str_replace("!!enrichment_headers!!","",$std_header);
	}
	print $std_header ;
	include($base_path.'/includes/navigator.inc.php');
}
$popup_resa = 1 ;

if ($log_ok) {

	switch($lvl) {
		case 'make_sugg' :
			if ($allow_sugg || $opac_show_suggest==2) {
				include($base_path.'/includes/make_sugg.inc.php');
			} else {
				print $msg['empr_no_allow_sugg'];
			}
			break;
		case 'valid_sugg' :
			if ($allow_sugg || $opac_show_suggest==2) {
				include($base_path.'/includes/valid_sugg.inc.php');
			} else {
				print $msg['empr_no_allow_sugg'];
			}
			break;
		case 'resa_planning' :
			if ($allow_book && $opac_resa && $opac_resa_planning==1) {
				include($base_path.'/includes/resa_planning.inc.php');
			} else {
				print $msg['empr_no_allow_book'];
			}
			break;
		case 'resa_cart':
			if($pmb_logs_activate) {
				notice::recup_notice_infos($id_notice);
			}
			if ($allow_book && $opac_resa){
				include($base_path.'/includes/resa_cart.inc.php');
			}else{
				print $msg['empr_no_allow_book'];
			}
			break;
		default:
		case 'resa':
			if($pmb_logs_activate) {
			    if (!empty($id_bulletin)) {
			        
			    } elseif (!empty($id_notice)) {
				    notice::recup_notice_infos($id_notice);
			    }
			}
			if ($allow_book && $opac_resa) {
				include($base_path.'/includes/resa.inc.php');
			} else {
				print $msg['empr_no_allow_book'];
			}
			break;
	}

} else {

	if (!$time_expired) {
		$erreur_session = '' ;
		if ($login) {
			print '<br />'.$msg['empr_bad_login'].'<br /><br /><br />';
			print do_formulaire_connexion();
		} else {
			print do_formulaire_connexion();
		}
	} elseif ($time_expired==1) {
		print '<br />'.sprintf($msg['session_expired'],round($opac_duration_session_auth/60)).'<br /><br /><br />';
		print do_formulaire_connexion() ;
	} else { //$time_expired == 2
		print '<br />'.sprintf($msg['anonymous_session_expired'],round($opac_duration_session_auth/60)).'<br /><br /><br />';
		print do_formulaire_connexion() ;
	}

}

if ($erreur_session) {
	print $erreur_session ;
}

if ($opac_resa_popup) {
	print $popup_footer;
} else {
	//insertions des liens du bas dans le $footer si $opac_show_liensbas
	if ($opac_show_liensbas==1) {
		$footer = str_replace('!!div_liens_bas!!',$liens_bas,$footer);
	} else {
		$footer = str_replace('!!div_liens_bas!!','',$footer);
	}
	if ($opac_show_bandeau_2==0) {
		$bandeau_2_contains= '';
	} else {
		$bandeau_2_contains= '<div id="bandeau_2">!!contenu_bandeau_2!!</div>';
	}
	//affichage du bandeau de gauche si $opac_show_bandeaugauche = 1
	if ($opac_show_bandeaugauche==0) {
		$footer= str_replace('!!contenu_bandeau!!',$bandeau_2_contains,$footer);
		$footer= str_replace('!!contenu_bandeau_2!!',$opac_facette_in_bandeau_2?$lvl1.$facette:'',$footer);
	} else {
		$footer = str_replace('!!contenu_bandeau!!','<div id="bandeau">!!contenu_bandeau!!</div>'.$bandeau_2_contains,$footer);
		$home_on_left=str_replace('!!welcome_page!!',$msg['welcome_page'],$home_on_left);
		$adresse=str_replace('!!common_tpl_address!!',$msg['common_tpl_address'],$adresse);
		$adresse=str_replace('!!common_tpl_contact!!',$msg['common_tpl_contact'],$adresse);

		// loading the languages avaiable in OPAC - martizva >> Eric
		require_once($base_path.'/includes/languages.inc.php');
		$home_on_left = str_replace('!!common_tpl_lang_select!!', show_select_languages('empr.php'), $home_on_left);

		if (!$_SESSION['user_code']) {
			$loginform=str_replace('<!-- common_tpl_login_invite -->','<h3 class="login_invite">'.$msg['common_tpl_login_invite'].'</h3>',$loginform);
			$loginform__ = genere_form_connexion_empr();
		} else {
			$loginform=str_replace('<!-- common_tpl_login_invite -->','',$loginform);
			$loginform__.= '<b class="logged_user_name">'.$empr_prenom." ".$empr_nom.'</b><br />';
			if($opac_quick_access) {
				$loginform__.= quick_access::get_selector();
				$loginform__.= '<br />';
			} else {
				$loginform__.="<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />";
			}
			if(!$opac_quick_access_logout || !$opac_quick_access){	
				$loginform__.= '<a href="index.php?logout=1" id="empr_logout_lnk">'.$msg['empr_logout'].'</a>';
			}
		}
		$loginform = str_replace('!!login_form!!',$loginform__,$loginform);
		$footer= str_replace('!!contenu_bandeau!!',($opac_accessibility ? $accessibility : '').$home_on_left.$loginform.$meteo.$adresse,$footer);
		$footer= str_replace('!!contenu_bandeau_2!!',$opac_facette_in_bandeau_2?$lvl1.$facette:'',$footer);

		$footer=str_replace('!!cms_build_info!!','',$footer);

	}
	print $footer ;
}

global $pmb_logs_activate;
if($pmb_logs_activate){
	//Enregistrement du log
	global $log, $infos_expl, $infos_notice;

	if($_SESSION['user_code']) {
		$res=pmb_mysql_query($log->get_empr_query());
		if($res){
			$empr_carac = pmb_mysql_fetch_array($res);
			$log->add_log('empr',$empr_carac);
		}
	}
	$log->add_log('num_session',session_id());
	$log->add_log('expl',$infos_expl);
	$log->add_log('docs',$infos_notice);
	$log->save();
}

if($opac_parse_html || $cms_active){
	if($opac_parse_html){
		$htmltoparse= parseHTML(ob_get_contents());
	}else{
		$htmltoparse= ob_get_contents();
	}

	ob_end_clean();
	if ($cms_active) {
		require_once($base_path.'/classes/cms/cms_build.class.php');
		$cms=new cms_build();
		$htmltoparse = $cms->transform_html($htmltoparse);
	}
	print $htmltoparse;
}
/* Fermeture de la connexion */
pmb_mysql_close($dbh);