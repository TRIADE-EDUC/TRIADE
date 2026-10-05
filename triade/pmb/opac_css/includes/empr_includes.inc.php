<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_includes.inc.php,v 1.206.2.2.2.5 2025/05/20 14:00:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $msg, $charset, $lvl, $sub;
global $allow_loan_hist, $allow_book, $opac_resa, $opac_resa_planning, $opac_allow_self_checkout, $allow_self_checkout;
global $opac_opac_view_activate, $opac_view, $current_opac_view, $opac_search_other_function;
global $opac_show_login_form_next, $opac_notice_enrichment, $opac_parse_html, $cms_active;
global $allow_loan, $opac_show_suggest, $allow_sugg, $opac_allow_multiple_sugg, $opac_shared_lists, $allow_liste_lecture, $demandes_active, $opac_demandes_active, $allow_dema, $opac_serialcirc_active, $allow_serialcirc;
global $opac_scan_request_activate, $allow_scan_request, $opac_contribution_area_activate, $allow_contribution, $allow_pnb, $animations_active;
global $opac_dsi_active, $allow_dsi, $allow_dsi_priv, $opac_allow_bannette_priv, $opac_show_categ_bannette, $opac_allow_resiliation;
global $opac_quick_access, $opac_quick_access_logout;
global $empr_ldap, $allow_pwd, $empr_active_opac_renewal, $empr_date_expiration, $opac_duration_session_auth;
global $opac_show_liensbas, $opac_show_bandeau_2, $opac_show_bandeaugauche, $opac_facette_in_bandeau_2, $opac_accessibility, $opac_show_homeontop, $opac_biblio_main_header;
global $opac_rgaa_active, $security_mfa_active;

use Pmb\Animations\Opac\Controller\RegistrationController;
use Pmb\MFA\Opac\Controller\MFAManagementController;
use Pmb\Payments\Opac\Controller\PaymentsController;
use Pmb\MFA\Controller\MFAServicesController;
use Pmb\DSI\Opac\Controller\DiffusionsPrivateController;
use Pmb\DSI\Opac\Controller\DiffusionsController;
use Pmb\DSI\Controller\SubscribersController;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\Security\Library\Auth;

require_once ($base_path . '/includes/init.inc.php');

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

//si les vues sont activées (à laisser après le calcul des mots vides)
if ($opac_opac_view_activate) {
    $opac_view = intval($opac_view);
	if ($opac_view) {
		if ($current_opac_view != $opac_view) {
			// on change de vue donc :
			// on stocke le tri en cours pour la vue en cours
			$_SESSION['last_sortnotices_view_' . $current_opac_view] = $_SESSION['last_sortnotices'];
			if (isset($_SESSION['last_sortnotices_view_' . $opac_view])) {
				// on a déjà un tri pour la nouvelle vue, on l'applique
				$_SESSION['last_sortnotices'] = $_SESSION['last_sortnotices_view_' . $opac_view];
			} else {
				unset($_SESSION['last_sortnotices']);
			}
			// comparateur de facettes : on ré-initialise
			require_once ($base_path . '/classes/facette_search_compare.class.php');
			facette_search_compare::session_facette_compare(null, true);
			// comparateur de facettes externes : on ré-initialise
			require_once ($base_path . '/classes/facettes_external_search_compare.class.php');
			facettes_external_search_compare::session_facette_compare(null, true);
		}
	}
}

if ($opac_search_other_function) {
	require_once ($include_path . "/" . $opac_search_other_function);
}

require_once ($base_path . '/includes/templates/common.tpl.php');
require_once($include_path.'/plugins.inc.php');

// classe de gestion des catégories
require_once ($base_path . '/classes/categorie.class.php');
require_once ($base_path . '/classes/notice.class.php');
require_once ($base_path . '/classes/notice_display.class.php');

// classe indexation interne
require_once ($base_path . '/classes/indexint.class.php');

// classe d'affichage des tags
require_once ($base_path . '/classes/tags.class.php');

// classe de gestion des réservations
require_once ($base_path . '/classes/resa.class.php');

require_once($base_path.'/classes/quick_access.class.php');

// pour l'affichage correct des notices
require_once ($base_path . '/includes/templates/notice.tpl.php');
require_once ($base_path . '/includes/navbar.inc.php');
require_once ($base_path . '/includes/explnum.inc.php');
require_once ($base_path . '/includes/notice_affichage.inc.php');
require_once ($base_path . '/includes/bulletin_affichage.inc.php');

// autenticazione LDAP - by MaxMan
require_once ($base_path . '/includes/ldap_auth.inc.php');

// RSS
require_once ($base_path . '/includes/includes_rss.inc.php');

// pour fonction de formulaire de connexion
require_once ($base_path . '/includes/empr.inc.php');
// pour fonction de vérification de connexion
require_once ($base_path . '/includes/empr_func.inc.php');

// pour la gestion des tris
require_once ($base_path . '/classes/sort.class.php');

require_once ($base_path . '/classes/suggestions.class.php');

require_once ($base_path . '/classes/pnb/dilicom.class.php');

if (file_exists($base_path . '/includes/empr_extended.inc.php'))
	require_once ($base_path . '/includes/empr_extended.inc.php');

	// si paramétrage authentification particulière
$empty_pwd = true;
$ext_auth = false;
if (file_exists($base_path . '/includes/ext_auth.inc.php')) {
	$file_orig = "empr.php";
	require_once ($base_path . '/includes/ext_auth.inc.php');
}

// Vérification de la session
$log_ok = connexion_empr();
if ($first_log && empty($direct_access) && isset($_SESSION['opac_view']) && $_SESSION['opac_view']) {
	if ($opac_show_login_form_next) {
		print "<script>document.location='$opac_show_login_form_next';</script>";
	} else {
		print "<script>document.location='$base_path/empr.php';</script>";
	}
	exit();
}

// connexion en cours et paramètre de rebond ailleurs que sur le compte emprunteur
if (($opac_show_login_form_next) && ($login) && ($first_log) && empty($direct_access) && ($lvl != 'change_password') && ($lvl != 'change_profil'))
	die("<script>document.location='$opac_show_login_form_next';</script>");

if ($is_opac_included) {
	$std_header = $inclus_header;
	$footer = $inclus_footer;
}
// Enrichissement OPAC
if ($opac_notice_enrichment) {
	require_once ($base_path . '/classes/enrichment.class.php');
	$enrichment = new enrichment();
	$std_header = str_replace('!!enrichment_headers!!', $enrichment->getHeaders() ?? "", $std_header);
} else {
	$std_header = str_replace('!!enrichment_headers!!', "", $std_header);
}

// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque dans la fiche empr
if ($opac_show_homeontop == 1 && isset($home_on_top)) {
	$std_header = str_replace('!!home_on_top!!', $home_on_top, $std_header);
} else {
	$std_header = str_replace('!!home_on_top!!', '', $std_header);
}

// mise à jour du contenu opac_biblio_main_header
$std_header = str_replace('!!main_header!!', $opac_biblio_main_header, $std_header);

// RSS
$std_header = str_replace('!!liens_rss!!', genere_link_rss(), $std_header);
// l'image $logo_rss_si_rss est calculée par genere_link_rss() en global
$liens_bas = str_replace('<!-- rss -->', $logo_rss_si_rss ?? "", $liens_bas);

if ($opac_parse_html || $cms_active || $opac_rgaa_active) {
	ob_start();
}

if(!isset($dest)) $dest = '';
if (! $dest) {
	print $std_header;

	require_once ($base_path . '/includes/nav_history.inc.php');
	require_once ($base_path . '/includes/navigator.inc.php');

	require_once ($class_path . '/serialcirc_empr.class.php');

	if ($opac_empr_code_info && $log_ok)
		print $opac_empr_code_info;
}

if(!isset($tab)) $tab = '';
if (! $tab) {
	switch ($lvl) {
		case 'change_password' :
		case 'valid_change_password' :
		case 'message' :
		case 'change_profil' :
		case 'renewal' :
		case 'delete_account' :
			$tab = 'account';
			break;
		case 'all' :
		case 'old' :
		case 'pret' :
		case 'retour' :
			$tab = 'loan';
			break;
		case 'bannette' :
		case 'bannette_gerer' :
		case 'bannette_creer' :
		case 'bannette_edit' :
		case 'bannette_unsubscribe' :
			$tab = 'dsi';
			break;
		case 'make_sugg' :
		case 'make_multi_sugg' :
		case 'transform_to_sugg' :
		case 'valid_sugg' :
		case 'view_sugg' :
		case 'suppr_sugg' :
			$tab = 'sugg';
			break;
		case 'private_list' :
		case 'public_list' :
			$tab = 'lecture';
			break;
		case 'demande_list' :
		case 'do_dmde' :
		case 'list_dmde' :
			$tab = 'request';
			break;
		case 'scan_requests_list' :
			$tab = 'scan_requests';
			break;
		case 'pnb' :
			$tab = 'pnb';
			break;
		case 'animations_list' :
			$tab = 'animations';
			break;
		default :
			$tab = 'account';
			break;
	}
}

if ($log_ok) {
	require_once ($base_path . '/empr/empr.inc.php');
	if (! $dest) {
		/* Affichage du bandeau action en bas de la page. A externaliser dans le template */
		$empr_onglet_menu = "
		 <div id='empr_onglet'>
			<ul class='empr_tabs'>
				<li " . (($tab == 'account' || ! $tab) ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=account'>" . htmlentities($msg['empr_menu_account'], ENT_QUOTES, $charset) . "</a></li>";
		if ($allow_loan || $allow_loan_hist || ($allow_book && $opac_resa)) {
			$onglet_lib = array ();
			if ($allow_loan || $allow_loan_hist) {
				$onglet_lib[] = $msg['empr_menu_loan'];
			}
			if ($allow_book && $opac_resa) {
				$onglet_lib[] = $msg['empr_menu_resa'];
			}
			$empr_onglet_menu .= "<li " . (($tab == "loan_reza") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=loan_reza&lvl=all'>";
			$empr_onglet_menu .= htmlentities(implode(" / ", $onglet_lib), ENT_QUOTES, $charset);
			$empr_onglet_menu .= "</a></li>";
		}
		if (($opac_dsi_active) && ($allow_dsi || $allow_dsi_priv)) {
			$empr_onglet_menu .= "<li " . (($tab == "dsi") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=dsi&lvl=bannette'>" . htmlentities($msg['empr_menu_dsi'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_show_suggest && $allow_sugg) {
			$empr_onglet_menu .= "<li " . (($tab == "sugg") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=sugg&lvl=view_sugg'>" . htmlentities($msg['empr_menu_sugg'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_shared_lists && $allow_liste_lecture) {
			$empr_onglet_menu .= "<li " . (($tab == "lecture") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=lecture&lvl=private_list'>" . htmlentities($msg['empr_menu_lecture'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_demandes_active && $allow_dema) {
			$empr_onglet_menu .= "<li " . (($tab == "request") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=request&lvl=list_dmde'>" . htmlentities($msg['empr_menu_dmde'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_serialcirc_active && $allow_serialcirc) {
			$empr_onglet_menu .= "<li " . (($tab == "serialcirc") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=list_abo'>" . htmlentities($msg['empr_menu_serialcirc'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_scan_request_activate && $allow_scan_request) {
			$empr_onglet_menu .= "<li " . (($tab == "scan_requests") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=scan_requests&lvl=scan_requests_list'>" . htmlentities($msg['empr_menu_scan_requests'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_contribution_area_activate && $allow_contribution) {
			$empr_onglet_menu .= "<li " . (($tab == "contribution_area") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=contribution_area&lvl=contribution_area_list'>" . htmlentities($msg['empr_menu_contribution_area'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($allow_pnb) {
			$empr_onglet_menu .= "<li " . (($tab == "pnb") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=pnb&lvl=pnb_devices'>" . htmlentities($msg['empr_menu_pnb'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($animations_active) {
		    $empr_onglet_menu .= "<li " . (($tab == "animations") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=animations&lvl=animations_list'>" . htmlentities($msg['empr_menu_animations'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($security_mfa_active) {
			$mfa_service = (new MFAServicesController())->getData("OPAC");
			if($mfa_service->application) {
				$empr_onglet_menu .= "<li " . (($tab == "mfa") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=mfa&lvl=mfa_initialization'>" . htmlentities($msg['empr_menu_mfa'], ENT_QUOTES, $charset) . "</a></li>";
			}
		}

		// En cour de Dev
	    // $empr_onglet_menu .= "<li " . (($tab == "payments_list") ? "class=\"subTabCurrent\" aria-current='page'" : "") . "><a href='./empr.php?tab=payments&lvl=payments_list'>" . htmlentities($msg['empr_menu_payment'], ENT_QUOTES, $charset) . "</a></li>";

		if (function_exists('empr_extended_bandeau')) {
			empr_extended_bandeau($tab);
		}
		$empr_onglet_menu .= '</ul>';

		print $empr_onglet_menu;

		$subitems ='';
		switch ($tab) {
			case 'loan' :
			case 'reza' :
			case 'loan_reza' :
				// Prêts - Réservations
				$loan_reza_item = '<ul class="empr_subtabs empr_loan_reza_subtabs">';
				if ($allow_loan) {
					$loan_reza_item .= "
						<li " . (($lvl == 'all') ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=loan_reza&lvl=all#empr-loan'>" . htmlentities($msg['empr_bt_show_all'], ENT_QUOTES, $charset) . "</a></li>
					";
				}
				if ($allow_loan_hist) {
					$loan_reza_item .= "
						<li " . (($lvl == 'old') ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=loan_reza&lvl=old'>" . htmlentities($msg['empr_bt_show_old'], ENT_QUOTES, $charset) . "</a></li>
					";
				}
				if ($allow_book) {
					if ($opac_resa) {
						$loan_reza_item .= "<li " . (($lvl == "all") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=loan_reza&lvl=all#empr-resa'>" . htmlentities($msg['empr_bt_show_resa'], ENT_QUOTES, $charset) . "</a></li>";
					}
					if ($opac_resa_planning) {
						$loan_reza_item .= '<li ' . (($lvl == 'all') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=loan_reza&lvl=all#empr-resa_planning">' . htmlentities($msg['empr_bt_show_resa_planning'], ENT_QUOTES, $charset) . '</a></li>';
					}
				}
				if ($opac_allow_self_checkout) {
					if (($opac_allow_self_checkout == 1 || $opac_allow_self_checkout == 3) && ($allow_self_checkout)) {
						$loan_reza_item .= "<li " . (($lvl == "pret") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=loan&lvl=pret'>" . htmlentities($msg['empr_bt_checkout'], ENT_QUOTES, $charset) . "</a></li>";
					}
					if (($opac_allow_self_checkout == 2 || $opac_allow_self_checkout == 3) && ($allow_self_checkout)) {
						$loan_reza_item .= "<li " . (($lvl == "retour") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=loan&lvl=retour'>" . htmlentities($msg['empr_bt_checkin'], ENT_QUOTES, $charset) . "</a></li>";
					}
				}

				$loan_reza_item .= "</ul>";

				$subitems .= '<div class="row">';
				if ($opac_rgaa_active) {

				    $onglet_lib = array ();
				    if ($allow_loan || $allow_loan_hist) {
				        $onglet_lib[] = $msg['empr_menu_loan'];
				    }
				    if ($allow_book && $opac_resa) {
				        $onglet_lib[] = $msg['empr_menu_resa'];
				    }
				    if( ('loan_reza' == $tab) && ('old' == $lvl) )  {
				        $subitems .= '<h1>' . htmlentities($msg['empr_loans_old'], ENT_QUOTES, $charset) . '</h1>';
				    } elseif ( ('loan' == $tab) && ('pret' == $lvl) ){
				        $subitems .= '<h1>' . htmlentities($msg['empr_checkout_title'], ENT_QUOTES, $charset) . '</h1>';
				    } elseif ( ('loan' == $tab) && ('retour' == $lvl) ){
				        $subitems .= '<h1>' . htmlentities($msg['empr_checkin_title'], ENT_QUOTES, $charset) . '</h1>';
				    } else {
				        $subitems .= '<h1>' . htmlentities(implode(" / ", $onglet_lib), ENT_QUOTES, $charset) . '</h1>';
				    }
				}
				$subitems .= $loan_reza_item .'</div>';
				break;
			case 'dsi' :
				global $dsi_active;
				if($dsi_active == 2) {
					//Nouvelle DSI
					$controller_data = new \stdClass();
					$controller_data->id = (int) $_SESSION['id_empr_session'];
					//On ne gère que les listes d'emprunteurs
					//Pour le moment ...
					$controller_data->emprType = "pmb";
					$controller = new DiffusionsController($controller_data);
					$controller->proceed($lvl);
					break;
				}
				// Mes abonnements
				$abo_item = "<ul class='empr_subtabs empr_dsi_subtabs'>";
				if (($opac_dsi_active) && ($allow_dsi || $allow_dsi_priv)) {
					$abo_item .= "<li " . (($lvl == "bannette") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=dsi&lvl=bannette'>" . htmlentities($msg['dsi_bannette_acceder'], ENT_QUOTES, $charset) . "</a></li>";
				}
				if ((($opac_show_categ_bannette && $opac_allow_resiliation) || $opac_allow_bannette_priv) && ($allow_dsi || $allow_dsi_priv)) {
					$abo_item .= "<li " . (($lvl == "bannette_gerer") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=dsi&lvl=bannette_gerer'>" . htmlentities($msg['dsi_bannette_gerer'], ENT_QUOTES, $charset) . "</a></li>";
				}
				if ($opac_allow_bannette_priv && $allow_dsi_priv) {
					$link_alert = './index.php?tab=dsi&bt_cree_bannette_priv=1&search_type_asked=extended_search';
					if(!isset($bt_cree_bannette_priv)) $bt_cree_bannette_priv = 0;
					$abo_item .= "<li id='cree_bannette_priv_li' " . (($bt_cree_bannette_priv == "1") ? "class=\"subTabCurrent\"" : "") . ">
						<a href='" . $link_alert . "'>" . htmlentities($msg['dsi_bt_bannette_priv_empr'], ENT_QUOTES, $charset) . "<span class='visually-hidden'>" . htmlentities($msg['dsi_bt_bannette_priv_empr_rgaa'], ENT_QUOTES, $charset) ."</span>
						</a></li>";
				}
				$abo_item .= "</ul>";
				if ($opac_dsi_active && $lvl == "bannette" && empty($id_bannette)) {
					if ($opac_allow_bannette_priv){
						$abo_item .= "
								<ul class='empr_subtabs empr_dsi_subtabs'>
                                    <li><a href='#title_bannette_pub'>".$msg['dsi_bannette_pub']."</a></li>
									<li><a href='#title_bannette_priv'>".$msg['dsi_bannette_priv']."</a></li>
								</ul>";
				    }
				}
				$subitems .= '<div class="row">'. $abo_item .'</div>';
				break;
			case 'sugg' :
				// Mes suggestions
				if ($opac_show_suggest && $allow_sugg) {
					$sugg_onglet = "
							<ul class='empr_subtabs empr_sugg_subtabs'>";
					if ($allow_sugg) {
						$sugg_onglet .= "<li " . (($lvl == "make_sugg") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=sugg&lvl=make_sugg' title='" . $msg['empr_bt_make_sugg'] . "'>" . htmlentities($msg['empr_bt_make_sugg'], ENT_QUOTES, $charset) . "</a></li>";
						if ($opac_allow_multiple_sugg)
							$sugg_onglet .= "<li " . (($lvl == "make_multi_sugg") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=sugg&lvl=make_multi_sugg'>" . htmlentities($msg['empr_bt_make_mul_sugg'], ENT_QUOTES, $charset) . "</a></li>";
					}
					$sugg_onglet .= "<li " . (($lvl == "view_sugg") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=sugg&lvl=view_sugg'>" . htmlentities($msg['empr_bt_view_sugg'], ENT_QUOTES, $charset) . "</a></li>";
					$sugg_onglet .= "</ul>";
				}
				$subitems .= '<div class="row">'. $sugg_onglet .'</div>';
				break;
			case 'lecture' :
				// Mes listes de lecture
				if ($opac_shared_lists && $allow_liste_lecture) {
					$liste_onglet = "
						<ul class='empr_subtabs empr_lecture_subtabs'>
							<li " . (($lvl == "private_list") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=lecture&lvl=private_list'>" . htmlentities($msg['list_lecture_show_my_list'], ENT_QUOTES, $charset) . "</a></li>
							<li " . (($lvl == "public_list") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=lecture&lvl=public_list'>" . htmlentities($msg['list_lecture_show_public_list'], ENT_QUOTES, $charset) . "</a></li>
							<li " . (($lvl == "demande_list") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=lecture&lvl=demande_list'>" . htmlentities($msg['list_lecture_show_my_requests'], ENT_QUOTES, $charset) . "</a></li>
							<li><a href='./empr.php?tab=lecture&lvl=private_list&act=add_list'>" . htmlentities($msg['list_lecture_add_list'], ENT_QUOTES, $charset) . "</a></li>
						</ul>
					";
				}
				$subitems .= '<div class="row">'. $liste_onglet .'</div>';
				break;
			case 'request' :
				// Mes demandes de recherche
			    $demandes_onglet = "";
				if ($demandes_active && $opac_demandes_active && $allow_dema) {
					$demandes_onglet = "
						<ul class='empr_subtabs empr_request_subtabs'>";
					$demandes_onglet .= "<li " . (($lvl == "list_dmde" && isset($sub)) ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=request&lvl=list_dmde&sub=add_demande'>" . htmlentities($msg['demandes_add'], ENT_QUOTES, $charset) . "</a></li>";
					$demandes_onglet .= "<li " . (($lvl == "list_dmde" && ! isset($sub)) ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=request&lvl=list_dmde&view=all'>" . htmlentities($msg['demandes_list'], ENT_QUOTES, $charset) . "</a></li>
						</ul>
					";
				}
				$subitems .= '<div class="row">'. $demandes_onglet .'</div>';
				break;
			case "serialcirc" :
				if ($opac_serialcirc_active) {
					$nb_virtual = count(serialcirc_empr::get_virtual_abo());
					$serialcirc_submenu = "
							<ul class='empr_subtabs empr_serialcirc_subtabs'>
								<li id='empr_menu_serialcirc_list_abo' " . (($lvl == "list_abo" && ! isset($sub)) ? "class='subTabCurrent'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=list_abo'>" . htmlentities($msg['serialcirc_list_abo'], ENT_QUOTES, $charset) . "</a></li>
								<li id='empr_menu_serialcirc_list_asked_abo' " . (($lvl == "list_virtual_abo" && ! isset($sub)) ? "class='subTabCurrent'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=list_virtual_abo'>" . htmlentities($msg['serialcirc_list_asked_abo'] . "(" . $nb_virtual . ")", ENT_QUOTES, $charset) . "</a></li>
								<li id='empr_menu_serialcirc_pointer' " . (($lvl == "point" && ! isset($sub)) ? "class='subTabCurrent'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=point'>" . htmlentities($msg['serialcirc_pointer'], ENT_QUOTES, $charset) . "</a></li>
								<li id='empr_menu_serialcirc_add_resa' " . (($lvl == "add_resa" && ! isset($sub)) ? "class='subTabCurrent'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=add_resa'>" . htmlentities($msg['serialcirc_add_resa'], ENT_QUOTES, $charset) . "</a></li>
								<li id='empr_menu_serialcirc_ask_copy' " . (($lvl == "copy" && ! isset($sub)) ? "class='subTabCurrent'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=copy'>" . htmlentities($msg['serialcirc_ask_copy'], ENT_QUOTES, $charset) . "</a></li>
								<li id='empr_menu_serialcirc_ask_menu' " . (($lvl == "ask" && ! isset($sub)) ? "class='subTabCurrent'" : "") . "><a href='./empr.php?tab=serialcirc&lvl=ask'>" . htmlentities($msg['serialcirc_ask_menu'], ENT_QUOTES, $charset) . "</a></li>
							</ul>";
					$subitems .= '<div class="row">'. $serialcirc_submenu .'</div>';
					break;
				}
			case 'scan_requests' :
				// Mes demandes de numérisation
			    $subitems .= '<div class="row"></div>';
			    break;
			case 'contribution_area' :
			    global $opac_contribution_area_activate, $allow_contribution;
			    global $lvl, $msg, $charset;

				$contribution_area_submenu = '';
				if ($opac_contribution_area_activate && $allow_contribution) {
					$contribution_area_submenu = '
					<ul class="empr_subtabs empr_contribution_area_subtabs">
						<li id="empr_menu_contribution_area_new" ' . (($lvl == 'contribution_area_new') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_new">' . htmlentities($msg['empr_menu_contribution_area_new'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_contribution_area_list_draft" ' . (($lvl == 'contribution_area_list_draft') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_list_draft">' . htmlentities($msg['empr_menu_contribution_area_list_draft'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_contribution_area_list" ' . (($lvl == 'contribution_area_list') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_list">' . htmlentities($msg['empr_menu_contribution_area_list'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_contribution_area_done" ' . (($lvl == 'contribution_area_done') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_done">' . htmlentities($msg['empr_menu_contribution_area_done'], ENT_QUOTES, $charset) . '</a></li>';

					global $gestion_acces_active, $gestion_acces_contribution_moderator_empr;
					if (($gestion_acces_active == 1) && ($gestion_acces_contribution_moderator_empr == 1)) {
    					$contribution_area_submenu .= '<li id="empr_menu_contribution_area_moderation" ' . (($lvl == 'contribution_area_moderation') ? 'class="subTabCurrent"' : '') . '>
                                <a href="./empr.php?tab=contribution_area&lvl=contribution_area_moderation">' . htmlentities($msg['empr_menu_contribution_area_moderation'], ENT_QUOTES, $charset) . '</a>
                            </li>';
					}

					$contribution_area_submenu .= "</ul>";
				}
				$subitems .= '<div class="row">'. $contribution_area_submenu .'</div>';
				break;
			case 'pnb' :
					//Mon prêt numérique
				if($allow_pnb) {
					$pnb_submenu = '
						<ul class="empr_subtabs empr_pnb_subtabs">
							<li id="empr_menu_pnb_devices" ' . (($lvl == 'pnb_devices') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=pnb&lvl=pnb_devices">' . htmlentities($msg['empr_menu_pnb_devices'], ENT_QUOTES, $charset) . '</a></li>
							<li id="empr_menu_pnb_parameters" ' . (($lvl == 'pnb_parameters') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=pnb&lvl=pnb_parameters">' . htmlentities($msg['empr_menu_pnb_parameters'], ENT_QUOTES, $charset) . '</a></li>
						</ul>';
				}
				$subitems = '<div class="row">'.$pnb_submenu.'</div>';
				break;
			case 'animations' :
			    $subitems .= '<div class="row"></div>';
			    break;
			case 'mfa' :
				$subitems .= '<div class="row"></div>';
				break;
			// En cour
// 			case 'payments' :
// 			    $subitems .= '<div class="row"></div>';
// 			    break;
			default :
				if (function_exists('empr_extended_tab_default')) {
					if (empr_extended_tab_default($tab))
						break;
				}
				break;
		}
		$subitems .= '</div>';
		print $subitems;
	}
	switch ($lvl) {
		case 'change_password' :
			$change_password_checked = " checked";
			require_once ($base_path . '/empr/change_password.inc.php');
			break;
		case 'change_profil' :
			require_once ($base_path . '/empr/change_profil.inc.php');
			break;
		case 'renewal' :
			require_once ($base_path . '/empr/renewal.inc.php');
			break;
		case 'delete_account' :
		    require_once ($base_path . '/empr/delete_account.inc.php');
		    break;
		case 'message' :
			$message_checked = " checked";
			require_once ($base_path . '/empr/message.inc.php');
			break;
		case 'all' :
		case 'resa_planning' :
			$all_checked = " checked";
			if (! $dest) {
				print "<div id='empr-all'>\n";
			}
			$critere_requete = " AND empr.empr_login='" . addslashes($login) . "' order by location_libelle, pret_retour ";
			require_once ($base_path . '/empr/all.inc.php');
			if (! $dest) {
			    print "</div>";
			}
			if (! $dest) {
    			if(dilicom::is_pnb_active()){
    			    print "<div id='empr-pnb_loan'>\n";
    			    require_once ($base_path . '/empr/pnb_loan.inc.php');
    			    print "</div>";
    			}
    			print '<div id="empr-resa">';
    			if ($allow_book) {
    				include ($base_path . '/includes/resa.inc.php');
    				print '<div id="empr-resa_planning">';
    				include ($base_path . '/includes/resa_planning.inc.php');
    				print '</div>';
    			} else {
    				print $msg['empr_no_allow_book'];
    			}
    			print '</div>';
			}
			break;
		case 'old' :
			if (! $dest) {
				print "<div id='empr-old'>\n";
			}
			require_once ($base_path . '/empr/old.inc.php');
			if (! $dest) {
                print "</div>\n";
			}
			break;
		case 'bannette' :
			if($dsi_active == 2) {
				//On ne passe plus par ce systeme avec la nouvelle dsi
				//Donc ciao
				break;
			}
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv || $allow_dsi) {
				require_once ($base_path . '/includes/bannette.inc.php');
			} else {
				print $msg['empr_no_allow_dsi'];
			}
			print "</div>";
			break;
		case 'bannette_gerer' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv || $allow_dsi) {
				require_once ($base_path . '/includes/bannette_gerer.inc.php');
			} else {
				print $msg['empr_no_allow_dsi'];
			}
			print "</div>";
			break;
		case 'diffusion_gerer' :
			global $diffusion_filtered_list, $diffusion_abon;

			//Nouvelle DSI
			$controller_data = new \stdClass();
			$controller_data->id = (int) $_SESSION['id_empr_session'];
			//On ne gère que les listes d'emprunteurs
			//Pour le moment ...
			$controller_data->emprType = "pmb";
			$diffusion_controller = new DiffusionsController($controller_data);

			// On gère l'inscription aux diffusions sélectionnées
			if(is_array($diffusion_filtered_list) && is_array($diffusion_abon)) {
				foreach($diffusion_filtered_list as $diffusion_id) {
					$diffusion_id = intval($diffusion_id);
					if(isset($diffusion_abon[$diffusion_id])) {
						$subscriber = $diffusion_controller->getSubscriberFromEntity("diffusions", $diffusion_id);
						if(is_null($subscriber)) {
							$subscriber = $diffusion_controller->getSubscriberFromType();
						}

						$subscriber_controller = new SubscribersController($subscriber);
						$subscriber_controller->subscribeFromOpac("diffusions", $diffusion_id, false);
					}
				}
			}

			print "<div id='empr-dsi'>\n";
				$controller->proceed("bannette");
			print "</div>";
			break;

		case 'bannette_creer' :
			print "<div id='empr-dsi'>\n";
			if($dsi_active == 2) {
				$controller_data = new \stdClass();
				$controller_data->id = (int) $_SESSION['id_empr_session'];
				$controller = new DiffusionsPrivateController($controller_data);
				$controller->proceed($lvl);
			} else {
				if ($allow_dsi_priv) {
					require_once ($base_path . '/includes/bannette_creer.inc.php');
				} else {
					print $msg['empr_no_allow_dsi_priv'];
				}
			}
			print "</div>";
			break;
		case 'bannette_edit' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv) {
				require_once ($base_path . '/includes/bannette_edit.inc.php');
			} else {
				print $msg['empr_no_allow_dsi_priv'];
			}
			print "</div>";
			break;
		case 'bannette_unsubscribe' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv) {
				require_once ($base_path . '/includes/bannette_unsubscribe.inc.php');
			} else {
				print $msg['empr_no_allow_dsi_priv'];
			}
			print "</div>";
			break;
		case 'make_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/make_sugg.inc.php');
			} else {
				print $msg['empr_no_allow_sugg'];
			}
			print "</div>";
			break;
		case 'make_multi_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/make_multi_sugg.inc.php');
			} else {
				print $msg['empr_no_allow_sugg'];
			}
			print "</div>";
			break;
		case 'transform_to_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/make_multi_sugg.inc.php');
			} else {
				print $msg['empr_no_allow_sugg'];
			}
			print "</div>";
			break;
		case 'valid_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/valid_sugg.inc.php');
			} else {
				print $msg['empr_no_allow_sugg'];
			}
			print "</div>";
			break;
		case 'view_sugg' :
			print "<div id='empr-sugg'>\n";
			require_once ($base_path . '/empr/view_sugg.inc.php');
			print "</div>";
			break;
		case 'suppr_sugg' :
			if ($allow_sugg && $id_sug) {
				suggestions::delete($id_sug);
			}
			print "<div id='empr-sugg'>\n";
			require_once ($base_path . '/empr/view_sugg.inc.php');
			print "</div>";
			break;
		case 'private_list' :
		case 'public_list' :
		case 'demande_list' :
			print "<div id='empr-list'>";
			require_once ($base_path . '/empr/liste_lecture.inc.php');
			print "</div>";
			break;
		case 'list_dmde' :
			print "<div id='empr-dema'>\n";
			if ($allow_dema) {
				$nb_themes = demandes_themes::get_qty();
				$nb_types = demandes_types::get_qty();
				if ($nb_themes && $nb_types) {
					require_once ($class_path . '/demandes.class.php');
					$tmp = demandes::get_first_tab();
					if ($tmp && ! $sub) {
						$sub = $tmp;
					}
					require_once ($base_path . '/empr/liste_demande.inc.php');
				} else {
					print $msg['empr_dema_not_configured'];
				}
			} else
				print $msg['empr_no_allow_dema'];
			print "</div>";
			break;
		case 'pret' :
			print "<div id='empr-sugg'>";
			if( !$opac_rgaa_active ) {
			    print "<h3><span>" . $msg['empr_checkout_title'] . "</span></h3>";
			}
			require_once ($base_path . '/empr/self_checkout.inc.php');
			print "</div>";
			break;
		case 'retour' :
			print "<div id='empr-sugg'>";
			if( !$opac_rgaa_active ) {
			 print "<h3><span>" . $msg['empr_checkin_title'] . "</span></h3>";
			}
			require_once ($base_path . '/empr/self_checkin.inc.php');
			print "</div>";
			break;
		// circulation des périos
		case "list_abo" :
		case "list_virtual_abo" :
		case "add_resa" :
		case "copy" :
		case "point" :
		case "ask" :
			if ($opac_serialcirc_active) {
				print "<div id='empr-abo' class='empr_tab_content'>";
				require_once ($base_path . '/empr/serialcirc.inc.php');
				print "</div>";
				break;
			}
		case "scan_requests_list" :
		case "scan_request" :
			print "<div id='empr-scan-request'>\n";
			if ($allow_scan_request) {
    			print common::format_title($msg['empr_menu_scan_requests']);
				require_once ($base_path . '/empr/scan_requests.inc.php');
			} else {
				print $msg['empr_no_allow_scan_requests'];
			}
			print "</div>";
			break;
		case "contribution_area_new" :
		case "contribution_area_list" :
		case "contribution_area_list_draft" :
		case "contribution_area_done" :
		case "contribution_area_moderation" :
		    if(!empty($msg[$lvl])) {
    		    print common::format_title($msg[$lvl]);
    		}
			print "<div id='empr_contribution_area'>";
			if ($opac_contribution_area_activate && $allow_contribution) {
				require_once ($base_path . '/empr/contribution_area.inc.php');
			} else {
				print $msg['empr_contribution_area_not_activate'];
			}
			print "</div>";
			break;
		case "pnb_devices" :
		case "pnb_parameters" :
		    if(!empty($msg[$lvl])) {
		        print common::format_title($msg[$lvl]);
		    }
			print "<div id='empr_pnb'>";
			if ($allow_pnb) {
				$pnb_controller = new pnb_controller();
				$pnb_controller->proceed();
			} else {
				print $msg['pnb_not_allowed'];
			}
			print "</div>";
			break;
		case "animations_list" :
		    print "<div id='empr_animations'>\n";
		    if ($animations_active) {
		        $controller_data = new stdClass();
		        $controller_data->empr_id = (int) $_SESSION['id_empr_session'];
		        $animation_controller = new RegistrationController($controller_data);
		        $animation_controller->proceed('list');
		    } else {
		        print $msg['empr_animations_not_activate'];
		    }
		    print "</div>";
		    break;
		case "mfa_initialization" :
			print "<div id='empr_mfa'>\n";
			if ($security_mfa_active && $mfa_service->application) {
		        $controller_data = new stdClass();
		        $controller_data->empr_id = (int) $_SESSION['id_empr_session'];

		        $mfa_controller = new MFAManagementController($controller_data);
		        $mfa_controller->proceed();
		    } else {
		        print $msg['empr_mfa_not_activate'];
		    }
			print "</div>";
			break;
		    // En cour de Dev
// 		case "payments_list" :
// 		    print "<div id='empr_payments'>\n";
// 		        $controller_data = new stdClass();
// 		        $controller_data->empr_id = (int) $_SESSION['id_empr_session'];
// 		        $payments_controller = new PaymentsController($controller_data);
// 		        $payments_controller->proceed('list');
// 		    print "</div>";
// 		    break;
		default :
			if (function_exists('empr_extended_lvl_default')) {
				if (empr_extended_lvl_default($lvl))
					break;
			}
			// Avant s'il n'y avait pas de lvl on affichait forcement mon compte
			if ("account" == $tab) {
    			print pmb_bidi($empr_identite);
			}
			break;
	}

} else {

	print "<div id='error_connection' class='error'>";

	require_once $base_path.'/includes/connexion_empr.inc.php';

	// Si la connexion n'a pas pu etre etablie
	switch (intval($erreur_connexion)) {

	    // Aucune erreur
	    case CONNEXION_ERROR_NONE :
	        print get_default_connexion_form();
	        break;

	    // L'abonnement du lecteur est expire
		case CONNEXION_ERROR_EXPIRED_SUBSCRIPTION :
			print $msg['empr_expire'];
			break;

		// Le statut du lecteur ne l'autorise pas a se connecter
		case CONNEXION_ERROR_FORBIDDEN_STATUS :
			print $msg['empr_connexion_interdite'];
			break;

		// Erreur de saisie des identifiants
		case CONNEXION_ERROR_INVALID_CREDENTIALS :
			print get_default_connexion_form();
			print $msg['empr_bad_login'];

			if (isset($opac_active_log_login_attempts) && (1 == $opac_active_log_login_attempts)) {
			    $auth_instance = Auth::getInstance($login);
			    if ($auth_instance->isRejected()) {
			        $remainingAttemptsMessage = '<p class="login_rejected erreur">' . $auth_instance->getRejectCause() . '</p>';
			    } elseif ($auth_instance->getRemainingAttempts() != $opac_block_after_failures) {
			        $remainingAttemptsMessage = '<p class="login_remaining_attempts">' . $auth_instance->getRemainingAttemptsMessage() . '</p>';
			    }
			    print $remainingAttemptsMessage;
			}
			break;

		//  En attente du code OTP
		case CONNEXION_ERROR_WAITING_FOR_OTP_CODE :
			break;

		// Code OTP invalide
		case CONNEXION_ERROR_INVALID_OTP_CODE :
			print $msg['mfa_login_error'];
			break;

		// Le lien de changement de mdp est expire
		case CONNEXION_ERROR_INVALID_LINK :
			print $msg["reset_password_link_expired"];
			break;

		// Nb d'erreur de connexion trop important
		case CONNEXION_ERROR_TOO_MANY_ATTEMPTS :
		    $auth_instance = Auth::getInstance($login);
		    print $auth_instance->getRejectCause();
		    break;

		// IP Interdite
		case CONNEXION_ERROR_FORBIDDEN_IP :
		    print $msg["login_rejected_blacklist"];
		    break;


		// La session est expiree
		case CONNEXION_ERROR_EXPIRED_SESSION :
			print sprintf($msg['session_expired'], round($opac_duration_session_auth / 60));
			break;

		case CONNEXION_ERROR_UNKNOWN :
		default :
		    print get_default_connexion_form();
		    break;
	}
	print "</div>";
}

if ($erreur_session) {
	print "<div class='error'>" . $erreur_session . "</div>";
}

// insertion des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas == 1) {
	$footer = str_replace('!!div_liens_bas!!', $liens_bas, $footer);
} else {
    $footer = str_replace('!!div_liens_bas!!', $liens_bas_disabled, $footer);
}

// affichage du bandeau_2 si $opac_show_bandeau_2 = 1
if ($opac_show_bandeau_2 == 0) {
	$bandeau_2_contains = "";
} else {
	$bandeau_2_contains = '<div id="bandeau_2">!!contenu_bandeau_2!!</div>';
}

// affichage du bandeau de gauche si $opac_show_bandeaugauche = 1
if ($opac_show_bandeaugauche == 0) {
	$footer = str_replace('!!contenu_bandeau!!', $bandeau_2_contains, $footer);
	$footer = str_replace('!!contenu_bandeau_2!!', $opac_facette_in_bandeau_2 ? $lvl1 . $facette : "", $footer);
} else {
	$footer = str_replace('!!contenu_bandeau!!', '<div id="bandeau">!!contenu_bandeau!!</div>' . $bandeau_2_contains, $footer);
	$home_on_left = str_replace('!!welcome_page!!', $msg['welcome_page'], $home_on_left);
	$adresse = str_replace('!!common_tpl_address!!', $msg['common_tpl_address'], $adresse);
	$adresse = str_replace('!!common_tpl_contact!!', $msg['common_tpl_contact'], $adresse);

	// loading the languages available in OPAC - martizva >> Eric
	require_once ($base_path . '/includes/languages.inc.php');
	$home_on_left = str_replace('!!common_tpl_lang_select!!', show_select_languages('empr.php'), $home_on_left);

	$external_authentication_form = '';
	if (! $_SESSION['user_code']) {
		$common_tpl_login_invite = $opac_rgaa_active ? '<h2 class="login_invite">%s</h2>' : '<h3 class="login_invite">%s</h3>';
		$loginform = str_replace(
			'<!-- common_tpl_login_invite -->',
			sprintf($common_tpl_login_invite, $msg['common_tpl_login_invite']),
			$loginform
		);
		$loginform__ = genere_form_connexion_empr();
		$external_authentication_form = generate_external_authentication_form();
	} else {
		$loginform = str_replace('<!-- common_tpl_login_invite -->', '', $loginform);
		$loginform__ = '<b class="logged_user_name">' . $empr_prenom . ' ' . $empr_nom . '</b><br />';
		if ($opac_quick_access) {
			$loginform__ .= quick_access::get_selector();
			$loginform__ .= '<br />';
		} else {
			$loginform__ .= "<a href=\"empr.php\" id=\"empr_my_account\">" . $msg["empr_my_account"] . "</a><br />";
		}
		if (! $opac_quick_access_logout || ! $opac_quick_access) {
			$loginform__ .= '<a href="index.php?logout=1" id="empr_logout_lnk">' . $msg['empr_logout'] . '</a>';
		}
	}
	$loginform = str_replace('!!login_form!!', $loginform__, $loginform);
	$loginform = str_replace('<!-- external_authentication -->', $external_authentication_form ,$loginform);
	$footer = str_replace('!!contenu_bandeau!!', ($opac_accessibility ? $accessibility : '') . $home_on_left . $loginform . $meteo . $adresse, $footer);
	$footer = str_replace('!!contenu_bandeau_2!!', $opac_facette_in_bandeau_2 ? $lvl1 . $facette : '', $footer);
}

// Alerte mot de passe non securise
printPasswordNoCompliant();

// LOG OPAC
$record_log = generate_log('empr');
if ($record_log) {
    print $record_log->validation_script();
}

//Alerte avant la fin de session pour la prolongation
$instance_session = new session();
print $instance_session->monitoring_endsession();

cms_build_info(array(
    'input' => 'empr.php',
));

/* Fermeture de la connexion */
pmb_mysql_close();
