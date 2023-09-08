<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_includes.inc.php,v 1.155.2.1 2019-06-17 10:25:56 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($base_path . '/includes/init.inc.php');

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

//si les vues sont activées (à laisser après le calcul des mots vides)
if ($opac_opac_view_activate) {
	if ($opac_view) {
		if ($current_opac_view != $opac_view * 1) {
			// on change de vue donc :
			// on stocke le tri en cours pour la vue en cours
			$_SESSION['last_sortnotices_view_' . $current_opac_view] = $_SESSION['last_sortnotices'];
			if (isset($_SESSION['last_sortnotices_view_' . ($opac_view * 1)])) {
				// on a déjà un tri pour la nouvelle vue, on l'applique
				$_SESSION['last_sortnotices'] = $_SESSION['last_sortnotices_view_' . ($opac_view * 1)];
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
		print "<script type='text/javascript'>document.location='$opac_show_login_form_next';</script>";
	} else {
		print "<script type='text/javascript'>document.location='$base_path/empr.php';</script>";
	}
	exit();
}

// connexion en cours et paramètre de rebond ailleurs que sur le compte emprunteur
if (($opac_show_login_form_next) && ($login) && ($first_log) && empty($direct_access) && ($lvl != 'change_password') && ($lvl != 'change_profil'))
	die("<script type='text/javascript'>document.location='$opac_show_login_form_next';</script>");

if ($is_opac_included) {
	$std_header = $inclus_header;
	$footer = $inclus_footer;
}
// Enrichissement OPAC
if ($opac_notice_enrichment) {
	require_once ($base_path . '/classes/enrichment.class.php');
	$enrichment = new enrichment();
	$std_header = str_replace('!!enrichment_headers!!', $enrichment->getHeaders(), $std_header);
} else
	$std_header = str_replace('!!enrichment_headers!!', "", $std_header);
	
	// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque dans la fiche empr
if ($opac_show_homeontop == 1)
	$std_header = str_replace('!!home_on_top!!', $home_on_top, $std_header);
else
	$std_header = str_replace('!!home_on_top!!', '', $std_header);
	
	// mise à jour du contenu opac_biblio_main_header
$std_header = str_replace('!!main_header!!', $opac_biblio_main_header, $std_header);

// RSS
$std_header = str_replace('!!liens_rss!!', genere_link_rss(), $std_header);
// l'image $logo_rss_si_rss est calculée par genere_link_rss() en global
$liens_bas = str_replace('<!-- rss -->', $logo_rss_si_rss, $liens_bas);

if ($opac_parse_html || $cms_active) {
	ob_start();
}

if(!isset($dest)) $dest = '';
if (! $dest) {
	print $std_header;
	
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
		case 'import_sugg' :
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
				<li " . (($tab == 'account' || ! $tab) ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=account'>" . htmlentities($msg['empr_menu_account'], ENT_QUOTES, $charset) . "</a></li>";
		if ($allow_loan || $allow_loan_hist || ($allow_book && $opac_resa)) {
			$onglet_lib = array ();
			if ($allow_loan || $allow_loan_hist) {
				$onglet_lib[] = $msg['empr_menu_loan'];
			}
			if ($allow_book && $opac_resa) {
				$onglet_lib[] = $msg['empr_menu_resa'];
			}
			$empr_onglet_menu .= "<li " . (($tab == "loan_reza") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=loan_reza&lvl=all'>";
			$empr_onglet_menu .= htmlentities(implode(" / ", $onglet_lib), ENT_QUOTES, $charset);
			$empr_onglet_menu .= "</a></li>";
		}
		if (($opac_dsi_active) && ($allow_dsi || $allow_dsi_priv))
			$empr_onglet_menu .= "<li " . (($tab == "dsi") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=dsi&lvl=bannette'>" . htmlentities($msg['empr_menu_dsi'], ENT_QUOTES, $charset) . "</a></li>";
		if ($opac_show_suggest && $allow_sugg)
			$empr_onglet_menu .= "<li " . (($tab == "sugg") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=sugg&lvl=view_sugg'>" . htmlentities($msg['empr_menu_sugg'], ENT_QUOTES, $charset) . "</a></li>";
		if ($opac_shared_lists && $allow_liste_lecture)
			$empr_onglet_menu .= "<li " . (($tab == "lecture") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=lecture&lvl=private_list'>" . htmlentities($msg['empr_menu_lecture'], ENT_QUOTES, $charset) . "</a></li>";
		if ($opac_demandes_active && $allow_dema) {
			$empr_onglet_menu .= "<li " . (($tab == "request") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=request&lvl=list_dmde'>" . htmlentities($msg['empr_menu_dmde'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_serialcirc_active && $allow_serialcirc) {
			$empr_onglet_menu .= "<li " . (($tab == "serialcirc") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=serialcirc&lvl=list_abo'>" . htmlentities($msg['empr_menu_serialcirc'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_scan_request_activate && $allow_scan_request) {
			$empr_onglet_menu .= "<li " . (($tab == "scan_requests") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=scan_requests&lvl=scan_requests_list'>" . htmlentities($msg['empr_menu_scan_requests'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if ($opac_contribution_area_activate && $allow_contribution) {
			$empr_onglet_menu .= "<li " . (($tab == "contribution_area") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=contribution_area&lvl=contribution_area_list'>" . htmlentities($msg['empr_menu_contribution_area'], ENT_QUOTES, $charset) . "</a></li>";
		}
		/**
		 * TODO tester si le pnb est configuré !
		 */
		if (dilicom::is_pnb_active()) {
			$empr_onglet_menu .= "<li " . (($tab == "pnb_loan") ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=pnb_loan&lvl=pnb_loan_list'>" . htmlentities($msg['empr_menu_pnb_loan'], ENT_QUOTES, $charset) . "</a></li>";
		}
		if (function_exists('empr_extended_bandeau')) {
			empr_extended_bandeau($tab);
		}
		$empr_onglet_menu .= '</ul>';
		
		print $empr_onglet_menu;
		$subitems = '
			<div class="row">
				!!subonglet!!
			</div>
		</div>';
		
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
				$subitems = str_replace('!!subonglet!!', $loan_reza_item, $subitems);
				break;
			case 'dsi' :
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
					$abo_item .= "<li id='cree_bannette_priv_li' " . (($bt_cree_bannette_priv == "1") ? "class=\"subTabCurrent\"" : "") . "><a href='" . $link_alert . "'>" . htmlentities($msg['dsi_bt_bannette_priv_empr'], ENT_QUOTES, $charset) . "</a></li>";
				}
				$abo_item .= "</ul>";
				$subitems = str_replace('!!subonglet!!', $abo_item, $subitems);
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
				$subitems = str_replace('!!subonglet!!', $sugg_onglet, $subitems);
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
				$subitems = str_replace('!!subonglet!!', $liste_onglet, $subitems);
				break;
			case 'request' :
				// Mes demandes de recherche
				if ($demandes_active && $opac_demandes_active && $allow_dema) {
					$demandes_onglet = "
						<ul class='empr_subtabs empr_request_subtabs'>";
					$demandes_onglet .= "<li " . (($lvl == "list_dmde" && isset($sub)) ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=request&lvl=list_dmde&sub=add_demande'>" . htmlentities($msg['demandes_add'], ENT_QUOTES, $charset) . "</a></li>";
					$demandes_onglet .= "<li " . (($lvl == "list_dmde" && ! isset($sub)) ? "class=\"subTabCurrent\"" : "") . "><a href='./empr.php?tab=request&lvl=list_dmde&view=all'>" . htmlentities($msg['demandes_list'], ENT_QUOTES, $charset) . "</a></li>
						</ul>
					";
				}
				$subitems = str_replace('!!subonglet!!', $demandes_onglet, $subitems);
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
					$subitems = str_replace('!!subonglet!!', $serialcirc_submenu, $subitems);
					break;
				}
			case 'scan_requests' :
				// Mes demandes de numérisation
				$subitems = str_replace("!!subonglet!!", '', $subitems);
				break;
			case 'contribution_area' :
				$contribution_area_submenu = '';
				if ($opac_contribution_area_activate && $allow_contribution) {
					$contribution_area_submenu = '
					<ul class="empr_subtabs empr_contribution_area_subtabs">
						<li id="empr_menu_contribution_area_new" ' . (($lvl == 'contribution_area_new') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_new">' . htmlentities($msg['empr_menu_contribution_area_new'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_contribution_area_list" ' . (($lvl == 'contribution_area_list') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_list">' . htmlentities($msg['empr_menu_contribution_area_list'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_contribution_area_done" ' . (($lvl == 'contribution_area_done') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_done">' . htmlentities($msg['empr_menu_contribution_area_done'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_contribution_area_moderation" ' . (($lvl == 'contribution_area_moderation') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=contribution_area&lvl=contribution_area_moderation">' . htmlentities($msg['empr_menu_contribution_area_moderation'], ENT_QUOTES, $charset) . '</a></li>
					</ul>';
				}
				$subitems = str_replace('!!subonglet!!', $contribution_area_submenu, $subitems);
				break;
			case 'pnb_loan':
				/**
				 * TODO: tester le parametrage du PNB
				 */
				$pnb_loan_submenu = '';
				if(dilicom::is_pnb_active()){
					$pnb_loan_submenu = '
					<ul class="empr_subtabs empr_pnb_loan_subtabs">
						<li id="empr_menu_pnb_loan_list" ' . (($lvl == 'empr_menu_pnb_loan_list') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=pnb_loan&lvl=pnb_loan_list">' . htmlentities($msg['empr_menu_pnb_loan_list'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_pnb_devices" ' . (($lvl == 'empr_menu_pnb_devices') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=pnb_loan&lvl=pnb_devices">' . htmlentities($msg['empr_menu_pnb_devices'], ENT_QUOTES, $charset) . '</a></li>
						<li id="empr_menu_pnb_parameters" ' . (($lvl == 'empr_menu_pnb_parameters') ? 'class="subTabCurrent"' : '') . '><a href="./empr.php?tab=pnb_loan&lvl=pnb_parameters">' . htmlentities($msg['empr_menu_pnb_parameters'], ENT_QUOTES, $charset) . '</a></li>
					</ul>';
				}
				$subitems = str_replace('!!subonglet!!', $pnb_loan_submenu, $subitems);
				break;
			default :
				if (function_exists('empr_extended_tab_default')) {
					if (empr_extended_tab_default($tab))
						break;
				}
				// Mon Compte
				$my_account_item = "<ul class='empr_subtabs'>";
				if (! $empr_ldap && $allow_pwd) {
					$my_account_item .= "<li " . (($lvl == "change_password") ? "class=\"subTabCurrent\"" : "") . "><a id='change_password' href='./empr.php?lvl=change_password'>" . htmlentities($msg['empr_modify_password'], ENT_QUOTES, $charset) . "</a></li>";
				}
				if(emprunteur_display::is_renewal_form_set() ){
					$my_account_item .= "<li " . (($lvl == "change_profil" ) ? "class=\"subTabCurrent\"" : "") . "><a id='change_profil' href='./empr.php?lvl=change_profil'>" . htmlentities($msg['empr_change_profil'], ENT_QUOTES, $charset) . "</a></li>";
				}
				global $pmb_relance_adhesion;
				if ($empr_active_opac_renewal && strtotime($empr_date_expiration) <= (time() + ($pmb_relance_adhesion* 86400))) {
					$my_account_item .= "<li " . (($lvl == "renewal" ) ? "class=\"subTabCurrent\"" : "") . "><a id='renewal' href='./empr.php?lvl=renewal'>" . htmlentities($msg['empr_renewal'], ENT_QUOTES, $charset) . "</a></li>";
				}
				$my_account_item .= "</ul>";
				
				$subitems = str_replace('!!subonglet!!', $my_account_item, $subitems);
				break;
		}
		print $subitems;
	}
	switch ($lvl) {
		case 'change_password' :
			$change_password_checked = " checked";
			require_once ($base_path . '/empr/change_password.inc.php');
			break;
		case 'valid_change_password' :
			$change_password_checked = " checked";
			require_once ($base_path . '/empr/valid_change_password.inc.php');
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
				print '<h3><span>' . $msg['empr_loans'] . '</span><span id="empr_loans_number"></span></h3>';
			}
			$critere_requete = " AND empr.empr_login='$login' order by location_libelle, pret_retour ";
			require_once ($base_path . '/empr/all.inc.php');
			print "</div>";
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
			break;
		case 'old' :
			if (! $dest) {
				print "<div id='empr-old'>\n";
				print '<h3><span>' . $msg['empr_loans_old'] . '</span><span id="empr_loans_old_number"></span></h3>';
			}
			require_once ($base_path . '/empr/old.inc.php');
			print "</div>\n";
			break;
		case 'bannette' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv || $allow_dsi)
				require_once ($base_path . '/includes/bannette.inc.php');
			else
				print $msg['empr_no_allow_dsi'];
			print "</div>";
			break;
		case 'bannette_gerer' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv || $allow_dsi)
				require_once ($base_path . '/includes/bannette_gerer.inc.php');
			else
				print $msg['empr_no_allow_dsi'];
			print "</div>";
			break;
		case 'bannette_creer' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv)
				require_once ($base_path . '/includes/bannette_creer.inc.php');
			else
				print $msg['empr_no_allow_dsi_priv'];
			print "</div>";
			break;
		case 'bannette_edit' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv)
				require_once ($base_path . '/includes/bannette_edit.inc.php');
			else
				print $msg['empr_no_allow_dsi_priv'];
			print "</div>";
			break;
		case 'bannette_unsubscribe' :
			print "<div id='empr-dsi'>\n";
			if ($allow_dsi_priv)
				require_once ($base_path . '/includes/bannette_unsubscribe.inc.php');
			else
				print $msg['empr_no_allow_dsi_priv'];
			print "</div>";
			break;
		case 'make_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg)
				require_once ($base_path . '/empr/make_sugg.inc.php');
			else
				print $msg['empr_no_allow_sugg'];
			print "</div>";
			break;
		case 'make_multi_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/make_multi_sugg.inc.php');
			} else
				print $msg['empr_no_allow_sugg'];
			print "</div>";
			break;
		case 'import_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/import_sugg.inc.php');
			} else
				print $msg['empr_no_allow_sugg'];
			print "</div>";
			break;
		case 'transform_to_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg) {
				require_once ($base_path . '/empr/make_multi_sugg.inc.php');
			} else
				print $msg['empr_no_allow_sugg'];
			print "</div>";
			break;
		case 'valid_sugg' :
			print "<div id='empr-sugg'>\n";
			if ($allow_sugg)
				require_once ($base_path . '/empr/valid_sugg.inc.php');
			else
				print $msg['empr_no_allow_sugg'];
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
			print "<div id='empr-list'>\n";
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
			print "<div id='empr-sugg'>\n";
			print "<h3><span>" . $msg['empr_checkout_title'] . "</span></h3>";
			require_once ($base_path . '/empr/self_checkout.inc.php');
			print "</div>";
			break;
		case 'retour' :
			print "<div id='empr-sugg'>\n";
			print "<h3><span>" . $msg['empr_checkin_title'] . "</span></h3>";
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
				require_once ($base_path . '/empr/scan_requests.inc.php');
			} else {
				print $msg['empr_no_allow_scan_requests'];
			}
			print "</div>";
			break;
		case "contribution_area_new" :
		case "contribution_area_list" :
		case "contribution_area_done" :
		case "contribution_area_moderation" :
			print "<div id='empr_contribution_area'>\n";
			if ($opac_contribution_area_activate && $allow_contribution) {
				require_once ($base_path . '/empr/contribution_area.inc.php');
			} else {
				print $msg['empr_contribution_area_not_activate'];
			}
			print "</div>";
			break;
		case "pnb_devices" :
		case "pnb_loan_list" :
		case "pnb_parameters" :
			/**
			 * TODO tester le paramétrage du PNB avant d'afficher tout ça
			 */
			print "<div id='empr_pnb_loan'>\n";
			if (true) {
				require_once ($base_path . '/empr/pnb_loan.inc.php');
			} else {
				print $msg['empr_pnb_loan_not_activate'];
			}
			print "</div>";
			break;
		default :
			if (function_exists('empr_extended_lvl_default')) {
				if (empr_extended_lvl_default($lvl))
					break;
			}
			print pmb_bidi($empr_identite);
			break;
	}
} else {
	print "<div class='error'>";
	// Si la connexion n'a pas pu être établie
	switch ($erreur_connexion) {
		case "1" :
			// L'abonnement du lecteur est expiré
			print $msg['empr_expire'];
			break;
		case "2" :
			// Le statut de l'abonné ne l'autorise pas à se connecter
			print $msg['empr_connexion_interdite'];
			break;
		case "3" :
			if(empty($_POST['login'])) {
				//Accès direct par l'URL
				require_once($base_path.'/includes/connexion_empr.inc.php');
				print get_default_connexion_form();
			} else {
				// Erreur de saisie du mot de passe ou du login ou de connexion avec le ldap
				print $msg['empr_bad_login'];
			}
			break;
		default :
			// La session est expirée
			print sprintf($msg['session_expired'], round($opac_duration_session_auth / 60));
			break;
	}
	print "</div>";
}

if ($erreur_session)
	print "<div class='error'>" . $erreur_session . "</div>";
	
	// insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas == 1)
	$footer = str_replace('!!div_liens_bas!!', $liens_bas, $footer);
else
	$footer = str_replace('!!div_liens_bas!!', '', $footer);
	
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
	
	// loading the languages avaiable in OPAC - martizva >> Eric
	require_once ($base_path . '/includes/languages.inc.php');
	$home_on_left = str_replace('!!common_tpl_lang_select!!', show_select_languages('empr.php'), $home_on_left);
	
	if (! $_SESSION['user_code']) {
		$loginform = str_replace('<!-- common_tpl_login_invite -->', '<h3 class="login_invite">' . $msg['common_tpl_login_invite'] . '</h3>', $loginform);
		$loginform__ = genere_form_connexion_empr();
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
	$footer = str_replace('!!contenu_bandeau!!', ($opac_accessibility ? $accessibility : '') . $home_on_left . $loginform . $meteo . $adresse, $footer);
	$footer = str_replace('!!contenu_bandeau_2!!', $opac_facette_in_bandeau_2 ? $lvl1 . $facette : '', $footer);
}

cms_build_info(array(
    'input' => 'empr.php',
));

// LOG OPAC
global $pmb_logs_activate;
if ($pmb_logs_activate) {
	global $log, $infos_notice, $infos_expl;
	
	if ($_SESSION['user_code']) {
		$res = pmb_mysql_query($log->get_empr_query());
		if ($res) {
			$empr_carac = pmb_mysql_fetch_array($res);
			$log->add_log('empr', $empr_carac);
		}
	}
	$log->add_log('num_session', session_id());
	$log->add_log('expl', $infos_expl);
	$log->add_log('docs', $infos_notice);
	
	// Enregistrement multicritere
	global $search;
	if ($search) {
		$search_stat = new search();
		$log->add_log('multi_search', $search_stat->serialize_search());
		$log->add_log('multi_human_query', $search_stat->make_human_query());
	}
	
	$log->save();
}

/* Fermeture de la connexion */
pmb_mysql_close($dbh);
?>