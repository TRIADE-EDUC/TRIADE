<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: askmdp.php,v 1.78.2.1.2.1 2025/02/07 13:49:04 qvarin Exp $

$base_path=".";
$is_opac_included = false;

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

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

require_once($base_path.'/classes/quick_access.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");
require_once($base_path."/includes/notice_affichage.inc.php");
require_once($base_path."/includes/bulletin_affichage.inc.php");

// pour l'envoi de mails
require_once($base_path."/includes/mail.inc.php");

// autenticazione LDAP - by MaxMan
require_once($base_path."/includes/ldap_auth.inc.php");

// RSS
require_once($base_path."/includes/includes_rss.inc.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");
// pour fonction de vérification de connexion
require_once($base_path.'/includes/empr_func.inc.php');


// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

//Vérification de la session
$log_ok=connexion_empr();

if ($is_opac_included) {
	$std_header = $inclus_header ;
	$footer = $inclus_footer ;
}

// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque dans la fiche empr
if ($opac_show_homeontop==1) $std_header= str_replace("!!home_on_top!!",$home_on_top,$std_header);
else $std_header= str_replace("!!home_on_top!!","",$std_header);

// mise à jour du contenu opac_biblio_main_header
$std_header= str_replace("!!main_header!!",$opac_biblio_main_header,$std_header);

// RSS
$std_header= str_replace("!!liens_rss!!",genere_link_rss(),$std_header);

//Enrichissement OPAC
$std_header = str_replace("!!enrichment_headers!!","",$std_header);

global $opac_parse_html, $cms_active, $opac_rgaa_active;
if($opac_parse_html || $cms_active || $opac_rgaa_active){
	ob_start();
}

print $std_header;

require_once ($base_path.'/includes/navigator.inc.php');

global $msg, $charset, $include_path;
global $email, $demande, $empr_firstname_name;

if (!empty($email)) {
    $email = trim($email);
}
$query = "SELECT valeur_param FROM parametres WHERE type_param='opac' AND sstype_param = 'biblio_name'";
$result = pmb_mysql_query($query) or die ("*** Erreur dans la requ&ecirc;te <br />*** $query<br />\n");
$row = pmb_mysql_fetch_array($result);

$send_email = false;
$email_unavailable = (!empty($email) && !is_valid_mail($email));

if (!empty($email) && is_valid_mail($email) && $demande == "ok") {
	$query = "SELECT id_empr, empr_login, empr_password, empr_location,empr_mail,concat(empr_prenom,' ',empr_nom) as nom_prenom FROM empr WHERE empr_mail like '%".$email."%'";
	if($empr_firstname_name) {
		$query .= " AND concat(empr_prenom,' ',empr_nom) = '".addslashes($empr_firstname_name)."'";
	}
	$result = pmb_mysql_query($query) or die ("*** Erreur dans la requ&ecirc;te <br />*** $query<br />\n");
	if (pmb_mysql_num_rows($result)) {
		while ($row = pmb_mysql_fetch_object($result)) {
		    if (!empty($row->empr_mail)) {
		        $emails_empr = explode(";", trim($row->empr_mail));
		        $emails_empr = array_map("strtolower", $emails_empr);
		        if (in_array(strtolower($email), $emails_empr)) {
		            $emprunteur = new emprunteur($row->id_empr);
		            $emprunteur->forgotten_password_email($email);
		        }
		    }
		}
	}

	// Ticket #127259
	// On indique que le mail à été envoyé, mais on de dit pas si un compte correspond ou non.
	$send_email = true;
} else {
	$email = "";
}

try{
	$template_path = $include_path.'/templates/askmdp.tpl.html';
	$H2o = H2o_collection::get_instance($template_path);
	print $H2o->render([
		'email' => $email,
		'send_email' => $send_email,
		'email_unavailable' => $email_unavailable,
		'success_msg' => $send_email ? sprintf($msg['mdp_sent_succesfully'], $email) : ""
	]);
} catch(Exception $e) {
	print '<blockquote id="askmdp" role="presentation">';
	print '<!-- '.$e->getMessage().' -->';
	print '<div class="error_on_template" ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">';
	print $msg["error_template"];
	print '</div>';
	print '</blockquote>';
}

//insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas==1) $footer = str_replace("!!div_liens_bas!!",$liens_bas,$footer);
else $footer = str_replace("!!div_liens_bas!!",$liens_bas_disabled,$footer);

if ($opac_show_bandeau_2==0) {
	$bandeau_2_contains= "";
} else {
	$bandeau_2_contains= "<div id=\"bandeau_2\">!!contenu_bandeau_2!!</div>";
}
//affichage du bandeau de gauche si $opac_show_bandeaugauche = 1
if ($opac_show_bandeaugauche==0) {
	$footer= str_replace("!!contenu_bandeau!!",$bandeau_2_contains,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
} else {
	$footer = str_replace("!!contenu_bandeau!!","<div id=\"bandeau\">!!contenu_bandeau!!</div>".$bandeau_2_contains,$footer);
	$home_on_left=str_replace("!!welcome_page!!",$msg["welcome_page"],$home_on_left);
	$adresse=str_replace("!!common_tpl_address!!",$msg["common_tpl_address"],$adresse);
	$adresse=str_replace("!!common_tpl_contact!!",$msg["common_tpl_contact"],$adresse);

	// loading the languages avaiable in OPAC - martizva >> Eric
	require_once($base_path.'/includes/languages.inc.php');
	$home_on_left = str_replace("!!common_tpl_lang_select!!", show_select_languages("empr.php"), $home_on_left);

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

// Enregistrement du log
$record_log = generate_log('askmdp');
if ($record_log) {
    print $record_log->validation_script();
}

// Portail - Mise en forme de la page
cms_build_info(['input' => 'askmdp.php']);
// Fermeture de la connexion
pmb_mysql_close($dbh);
