<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: askmdp.php,v 1.64 2019-06-04 08:58:11 ngantier Exp $

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

if($opac_parse_html || $cms_active){
	ob_start();
}

print $std_header;

require_once ($base_path.'/includes/navigator.inc.php');
	
$query = "SELECT valeur_param FROM parametres WHERE type_param='opac' AND sstype_param = 'biblio_name'";
$result = pmb_mysql_query($query) or die ("*** Erreur dans la requ&ecirc;te <br />*** $query<br />\n");
$row = pmb_mysql_fetch_array($result);
$demandeemail= "<hr /><p class='texte'>".$msg['mdp_txt_intro_demande']."</p>
	<form action=\"askmdp.php\" method=\"post\" ><br />
	<input type=\"text\" name=\"email\" size=\"20\" border=\"0\" value=\"email@\" onFocus=\"this.value='';\">&nbsp;&nbsp;
	<input type=\"hidden\" name=\"demande\" value=\"ok\" >
	<input type='submit' name='ok' value='".$msg['mdp_bt_send']."' class='bouton'>
	</form>"; 

print "<blockquote>";
if(!isset($email)) $email = '';
if(!isset($demande)) $demande = '';
$email = str_replace("%", "", $email);
if ($demande!="ok" || $email=='') {

	// Mettre ici le formulaire de saisie de l'email
	print $demandeemail ;
	
} elseif ($email) {
	$query = "SELECT empr_login, empr_password, empr_location,empr_mail,concat(empr_prenom,' ',empr_nom) as nom_prenom FROM empr WHERE empr_mail like '%".$email."%'";
	$result = pmb_mysql_query($query) or die ("*** Erreur dans la requ&ecirc;te <br />*** $query<br />\n");
	if (pmb_mysql_num_rows($result)!=0) {
		$res_envoi = false;
		while ($row = pmb_mysql_fetch_object($result)) {
			$emails_empr = explode(";",$row->empr_mail);
			for ($i=0; $i<count($emails_empr); $i++) {
				if (strtolower($email) == strtolower($emails_empr[$i])) {
					if (!$opac_biblio_name) {
						$query_loc = "SELECT name, email FROM docs_location WHERE idlocation='$row->empr_location'";
						$result_loc = pmb_mysql_query($query_loc) or die ("*** Erreur dans la requ&ecirc;te <br />*** $query_loc<br />\n");
						$info_loc = pmb_mysql_fetch_object($result_loc) ;
						$biblio_name_temp=$info_loc->name ;
						$biblio_email_temp=$info_loc->email ;
					} else {
						$biblio_name_temp=$opac_biblio_name;
						$biblio_email_temp=$opac_biblio_email;
					}
					$headers  = "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		
					// clé pour autoriser une seule connexion auto :
					$alphanum  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
					$password_key = substr(str_shuffle($alphanum), 0, 20);
					$rqt = "update empr set cle_validation='".$password_key."' where empr_login='".$row->empr_login."' ";
					$res = pmb_mysql_query($rqt,$dbh);
					
					// Bonjour,<br /><br />Pour faire suite à votre demande de réinitialisation de mot de passe à <b>!!biblioname!!</b>, veuillez trouver ci-dessous le lien qui vous permettra d'effectuer ce changement : <br /><br />!!lien_mdp!!<br /><br /> - Pour rappel, votre identifiant est : !!login!!<br /><br />Si vous rencontrez des difficultés, adressez un mail à !!biblioemail!!.<br /><br />
					$messagemail = $msg['mdp_mail_body'] ;
					$messagemail = str_replace("!!login!!",$row->empr_login,$messagemail);
					$messagemail = str_replace("!!biblioname!!","<a href=\"$opac_url_base\">".$biblio_name_temp."</a>",$messagemail);	
					$lien_mdp = "<a href='".$opac_url_base."empr.php?lvl=change_password&emprlogin=".$row->empr_login."&password_key=".$password_key."'>".$opac_url_base."empr.php?lvl=change_password&emprlogin=".$row->empr_login."&password_key=".$password_key."</a>";
					$messagemail = str_replace("!!lien_mdp!!",$lien_mdp,$messagemail);
					$messagemail = str_replace("!!biblioemail!!","<a href=mailto:$opac_biblio_email>$biblio_email_temp</a>",$messagemail);
		
					$objetemail = str_replace("!!biblioname!!",$biblio_name_temp,$msg['mdp_mail_obj']);
					print "<hr />";
					
					if($opac_parse_html){
						$objetemail = parseHTML($objetemail);
						$messagemail = parseHTML($messagemail);
						$biblio_name_temp = parseHTML($biblio_name_temp);
						$biblio_email_temp = parseHTML($biblio_email_temp); 
					}	
		
					$res_envoi=@mailpmb(trim($row->nom_prenom), $emails_empr[$i],$objetemail,$messagemail,$biblio_name_temp, $biblio_email_temp, $headers);
					if (!$res_envoi) {
						print "<p class='texte'>Could not send information to $emails_empr[$i].</p><br />" ;
					} else {
						print "<p class='texte'>".$msg['mdp_sent_ok']." $emails_empr[$i].</p><br />" ;
					}
				}
			}
		}
		if (!$res_envoi) {
			print "<hr /><p class='texte'>".str_replace("!!biblioemail!!","<a href=mailto:$opac_biblio_email>$opac_biblio_email</a>",$msg['mdp_no_email'])."</p>" ;
			print $demandeemail ;
		}
	} else {
		print "<hr /><p class='texte'>".str_replace("!!biblioemail!!","<a href=mailto:$opac_biblio_email>$opac_biblio_email</a>",$msg['mdp_no_email'])."</p>" ;
		print $demandeemail ;
	}
}

print "</blockquote>";

//insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas==1) $footer = str_replace("!!div_liens_bas!!",$liens_bas,$footer);
else $footer = str_replace("!!div_liens_bas!!","",$footer);

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
	
	if (!$_SESSION["user_code"]) {
		$loginform=str_replace('<!-- common_tpl_login_invite -->','<h3 class="login_invite">'.$msg['common_tpl_login_invite'].'</h3>',$loginform);
		$loginform__ = genere_form_connexion_empr();
	} else {
		$loginform=str_replace('<!-- common_tpl_login_invite -->','',$loginform);
		$loginform__ ="<b class='logged_user_name'>".$empr_prenom." ".$empr_nom."</b><br />\n";
		if($opac_quick_access) {
			$loginform__.= quick_access::get_selector();
			$loginform__.="<br />";
		} else {
			$loginform__.="<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />";
		}
		$loginform__.="<a href=\"index.php?logout=1\" id=\"empr_logout_lnk\">".$msg["empr_logout"]."</a>";
	}
	if(!$opac_quick_access_logout || !$opac_quick_access){
		$loginform = str_replace("!!login_form!!",$loginform__,$loginform);
	}
	$footer= str_replace("!!contenu_bandeau!!",($opac_accessibility ? $accessibility : "").$home_on_left.$loginform.$meteo.$adresse,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
}

//Enregistrement du log
global $pmb_logs_activate;
if($pmb_logs_activate){	
	global $log;
	$log->add_log('num_session',session_id());
	$log->save();
}

cms_build_info(array(
    'input' => 'askmdp.php',
));

/* Fermeture de la connexion */
pmb_mysql_close($dbh);
