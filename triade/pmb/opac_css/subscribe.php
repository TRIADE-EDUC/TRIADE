<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subscribe.php,v 1.67.2.1 2024/10/21 12:16:10 qvarin Exp $

global $msg;
global $opac_websubscribe_show, $opac_show_homeontop, $opac_biblio_main_header;
global $opac_parse_html, $cms_active, $opac_cart_allow, $opac_cart_only_for_subscriber;
global $opac_duration_session_auth, $opac_show_liensbas, $opac_show_bandeau_2, $opac_show_bandeaugauche;
global $time_expired, $opac_rgaa_active, $charset;

$base_path=".";
$is_opac_included = false;

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if (!$opac_websubscribe_show) die("");
if(!isset($subsact)) $subsact = '';
if ($subsact=="validation" && (!$login || !$cle_validation)) die("");

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

require_once($base_path."/includes/rec_history.inc.php");

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/common.tpl.php");
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");

require_once($base_path."/includes/notice_affichage.inc.php");

require_once($base_path."/classes/analyse_query.class.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");

//pour la gestion des tris
require_once($base_path."/classes/sort.class.php");

//pour la localisation du lecteur
require_once($base_path."/classes/docs_location.class.php");

require_once($base_path.'/classes/quick_access.class.php');

// si paramétrage authentification particulière
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

// pour les étagères et les nouveaux affichages
require_once($base_path."/includes/isbn.inc.php");
require_once($base_path."/classes/notice_affichage.class.php");
require_once($base_path."/includes/etagere_func.inc.php");

require_once($base_path."/includes/websubscribe.inc.php");
require_once($base_path."/includes/mail.inc.php");


// RSS
require_once($base_path."/includes/includes_rss.inc.php");
require_once($base_path."/includes/securimage/securimage.php");

if ($is_opac_included) {
	$std_header = $inclus_header ;
	$footer = $inclus_footer ;
}

// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque
if ($opac_show_homeontop==1) {
    $std_header = str_replace("!!home_on_top!!", $home_on_top ?? "", $std_header);
} else {
    $std_header= str_replace("!!home_on_top!!", "", $std_header);
}

// mise à jour du contenu opac_biblio_main_header
$std_header= str_replace("!!main_header!!", $opac_biblio_main_header ?? "", $std_header);

// RSS
$std_header= str_replace("!!liens_rss!!", genere_link_rss() ?? "", $std_header);
// l'image $logo_rss_si_rss est calculée par genere_link_rss() en global
$liens_bas = str_replace("<!-- rss -->", $logo_rss_si_rss ?? "", $liens_bas);

$std_header = str_replace("!!enrichment_headers!!","",$std_header);
if($opac_parse_html || $cms_active || $opac_rgaa_active){
	ob_start();
}


print $std_header;

if ($time_expired ==1) {
	echo "<script>alert(reverse_html_entities(\"".sprintf($msg["session_expired"],round($opac_duration_session_auth/60))."\"));</script>";
} elseif ($time_expired==2) {
	echo "<script>alert(reverse_html_entities(\"".sprintf($msg["anonymous_session_expired"],round($opac_duration_session_auth/60))."\"));</script>";
}

if (((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"])))&&($lvl!="show_cart")) {
	print "<div id='resume_panier'>
		<iframe
	        title='". htmlentities($msg['iframe_cart_info'],ENT_QUOTES,$charset) ."'
	        src=''
	        recept='yes'
	        recepttype='cart'
	        id='iframe_resume_panier'
	        name='cart_info'
	        allowtransparency='true'
	        scrolling='no'
	        scrollbar='0'>
	    </iframe>
		<script>
			addLoadEvent(function() {
	        	let iframe = document.getElementById('iframe_resume_panier');
	           	if (iframe) {
					iframe.src = 'cart_info.php'
				}
			});
		</script>

	</div>";
} else {
	print "<div id='resume_panier' class='empty'></div>";
}
echo "<div id='websubscribe'>";

switch($subsact) {
	case 'validation':
		$verif=verif_validation_compte();
		echo $verif[1];
		break;
	case 'inscrire':
	    if ($captcha_code) {
		    $securimage = new Securimage();
		    if ($securimage->check($captcha_code)) {
		        // set the session
		        $_SESSION['image_is_logged_in'] = true;
				// remove the random value from session
				$_SESSION['image_random_value'] = '';
				$verif=verif_validite_compte();
				echo $verif[1];
		    } else {
		        // set the session
		        $_SESSION['image_is_logged_in'] = false;
				// remove the random value from session
				$_SESSION['image_random_value'] = '';
				// Raté on repart...
				echo $msg['subs_pb_wrongcode'] ;
				echo generate_form_inscription() ;
			}
		} else {
			// vide
			echo $msg['subs_pb_wrongcode'] ;
			echo generate_form_inscription() ;
		}
		break;
	case '':
	default:
		$subsact='';
		echo generate_form_inscription() ;
		break;
	}

echo "</div>";

//insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas==1) $footer = str_replace("!!div_liens_bas!!",$liens_bas,$footer);
	else $footer = str_replace("!!div_liens_bas!!",$liens_bas_disabled,$footer);

//affichage du bandeau_2 si $opac_show_bandeau_2 = 1
if ($opac_show_bandeau_2==0) {
	$bandeau_2_contains= "";
} else {
	$bandeau_2_contains= "<div id=\"bandeau_2\">!!contenu_bandeau_2!!</div>";
}
//si ce n'est pas un popup qui est affiché, alors on affiche $footer
if ($opac_show_bandeaugauche==0) {
	$footer= str_replace("!!contenu_bandeau!!",$bandeau_2_contains,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
} else {
	$footer = str_replace("!!contenu_bandeau!!","<div id=\"bandeau\">!!contenu_bandeau!!</div>".$bandeau_2_contains,$footer);
	$home_on_left=str_replace("!!welcome_page!!",$msg["welcome_page"],$home_on_left);
	$adresse=str_replace("!!common_tpl_address!!",$msg["common_tpl_address"],$adresse);
	$adresse=str_replace("!!common_tpl_contact!!",$msg["common_tpl_contact"],$adresse);

	// loading the languages available in OPAC - martizva >> Eric
	require_once($base_path.'/includes/languages.inc.php');
	$home_on_left = str_replace("!!common_tpl_lang_select!!", show_select_languages("index.php"), $home_on_left);

	$external_authentication_form = '';
	if (!$_SESSION["user_code"]) {
		$common_tpl_login_invite = $opac_rgaa_active ? '<h2 class="login_invite">%s</h2>' : '<h3 class="login_invite">%s</h3>';
		$loginform = str_replace(
			'<!-- common_tpl_login_invite -->',
			sprintf($common_tpl_login_invite, $msg['common_tpl_login_invite']),
			$loginform
		);
		$loginform__ = genere_form_connexion_empr();
		$external_authentication_form = generate_external_authentication_form();
	} else {
		$loginform=str_replace('<!-- common_tpl_login_invite -->','',$loginform);
		$loginform__ ="<b class='logged_user_name'>".$empr_prenom." ".$empr_nom."</b><br />\n";
		if($opac_quick_access) {
			$loginform__.= quick_access::get_selector();
			$loginform__.="<br />";
		} else {
			$loginform__.="<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />";
		}
		if(!$opac_quick_access_logout || !$opac_quick_access){
			$loginform__.="<a href=\"index.php?logout=1\" id=\"empr_logout_lnk\">".$msg["empr_logout"]."</a>";
		}
	}
	$loginform = str_replace("!!login_form!!",$loginform__,$loginform);
	$loginform = str_replace('<!-- external_authentication -->', $external_authentication_form ,$loginform);
	$footer= str_replace("!!contenu_bandeau!!",($opac_accessibility ? $accessibility : "").$home_on_left.$loginform.$meteo.$adresse,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
}


cms_build_info(array(
    'input' => 'subscribe.php',
));

pmb_mysql_close();