<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: do_resa.php,v 1.93.4.1 2025/02/07 13:49:04 qvarin Exp $

use Pmb\Common\Library\CSRF\ParserCSRF;
use Pmb\Common\Library\RGAA\RGAABuilder;

global $erreur_session, $lvl, $opac_show_suggest, $empty_pwd, $ext_auth, $opac_parse_html, $cms_active, $opac_resa_popup, $popup_header, $msg;
global $opac_show_homeontop, $home_on_top, $std_header, $opac_biblio_main_header, $opac_notice_enrichment, $popup_resa, $allow_sugg, $allow_book;
global $opac_resa, $opac_resa_planning, $id_notice, $pmb_logs_activate, $id_bulletin, $time_expired, $login, $opac_duration_session_auth;
global $popup_footer, $opac_show_liensbas, $liens_bas, $liens_bas_disabled, $footer, $opac_show_bandeau_2, $opac_show_bandeaugauche, $opac_facette_in_bandeau_2;
global $lvl1, $facette, $home_on_left, $adresse, $loginform, $empr_prenom, $empr_nom, $opac_quick_access, $opac_quick_access_logout;
global $opac_accessibility, $accessibility, $meteo, $opac_rgaa_active;

$base_path = '.';

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

if (($lvl == 'make_sugg' || $lvl == 'valid_sugg') && $opac_show_suggest == 2) {
    //Suggestion possible sans authentification
    $log_ok = 1;
} else {
    //Vérification de la session
    // si paramétrage authentification particulière et pour la re-authentification ntlm
    $empty_pwd = true;
    $ext_auth = false;
    if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
        require_once($base_path.'/includes/ext_auth.inc.php');
    }
    $log_ok = connexion_empr();
}

ob_start();

if ($opac_resa_popup) {
    print $popup_header;
} else {
    if ($opac_show_homeontop == 1) {
        $std_header = str_replace('!!home_on_top!!', $home_on_top, $std_header);
    } else {
        $std_header = str_replace('!!home_on_top!!', '', $std_header);
    }
    // mise à jour du contenu opac_biblio_main_header
    $std_header = str_replace('!!main_header!!', $opac_biblio_main_header, $std_header);
    $std_header = str_replace('!!liens_rss!!', genere_link_rss(), $std_header);
    //Enrichissement OPAC
    if($opac_notice_enrichment) {
        require_once($base_path."/classes/enrichment.class.php");
        $enrichment = new enrichment();
        $std_header = str_replace("!!enrichment_headers!!", $enrichment->getHeaders(), $std_header);
    } else {
        $std_header = str_replace("!!enrichment_headers!!", "", $std_header);
    }
    print $std_header ;
    include($base_path.'/includes/navigator.inc.php');
}
$popup_resa = 1 ;

if ($log_ok) {

    switch($lvl) {
        case 'make_sugg' :
            if ($allow_sugg || $opac_show_suggest == 2) {
                include($base_path.'/includes/make_sugg.inc.php');
            } else {
                print $msg['empr_no_allow_sugg'];
            }
            break;
        case 'valid_sugg' :
            if ($allow_sugg || $opac_show_suggest == 2) {
                include($base_path.'/includes/valid_sugg.inc.php');
            } else {
                print $msg['empr_no_allow_sugg'];
            }
            break;
        case 'resa_planning' :
            if ($allow_book && $opac_resa && $opac_resa_planning == 1) {
                include($base_path.'/includes/resa_planning.inc.php');
            } else {
                print $msg['empr_no_allow_book'];
            }
            break;
        case 'resa_cart':
            if(!empty($id_notice) && $pmb_logs_activate) {
                notice::recup_notice_infos($id_notice);
            }
            if ($allow_book && $opac_resa) {
                include($base_path.'/includes/resa_cart.inc.php');
            } else {
                print $msg['empr_no_allow_book'];
            }
            break;
        default:
        case 'resa':
            if ($pmb_logs_activate) {
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
    } elseif ($time_expired == 1) {
        print '<br />'.sprintf($msg['session_expired'], round($opac_duration_session_auth / 60)).'<br /><br /><br />';
        print do_formulaire_connexion() ;
    } else { //$time_expired == 2
        print '<br />'.sprintf($msg['anonymous_session_expired'], round($opac_duration_session_auth / 60)).'<br /><br /><br />';
        print do_formulaire_connexion() ;
    }

}

if ($erreur_session) {
    print htmlentities($erreur_session, ENT_QUOTES, $charset);
}

if ($opac_resa_popup) {
    print $popup_footer;
} else {
    //insertions des liens du bas dans le $footer si $opac_show_liensbas
    if ($opac_show_liensbas == 1) {
        $footer = str_replace('!!div_liens_bas!!', $liens_bas, $footer);
    } else {
        $footer = str_replace('!!div_liens_bas!!', $liens_bas_disabled, $footer);
    }
    if ($opac_show_bandeau_2 == 0) {
        $bandeau_2_contains = '';
    } else {
        $bandeau_2_contains = '<div id="bandeau_2">!!contenu_bandeau_2!!</div>';
    }
    //affichage du bandeau de gauche si $opac_show_bandeaugauche = 1
    if ($opac_show_bandeaugauche == 0) {
        $footer = str_replace('!!contenu_bandeau!!', $bandeau_2_contains, $footer);
        $footer = str_replace('!!contenu_bandeau_2!!', $opac_facette_in_bandeau_2 ? $lvl1.$facette : '', $footer);
    } else {
        $footer = str_replace('!!contenu_bandeau!!', '<div id="bandeau">!!contenu_bandeau!!</div>'.$bandeau_2_contains, $footer);
        $home_on_left = str_replace('!!welcome_page!!', $msg['welcome_page'], $home_on_left);
        $adresse = str_replace('!!common_tpl_address!!', $msg['common_tpl_address'], $adresse);
        $adresse = str_replace('!!common_tpl_contact!!', $msg['common_tpl_contact'], $adresse);

        // loading the languages avaiable in OPAC - martizva >> Eric
        require_once($base_path.'/includes/languages.inc.php');
        $home_on_left = str_replace('!!common_tpl_lang_select!!', show_select_languages('empr.php'), $home_on_left);

        $external_authentication_form = '';
        if (!$_SESSION['user_code']) {
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
            $loginform__ = '<b class="logged_user_name">'.$empr_prenom." ".$empr_nom.'</b><br />';
            if($opac_quick_access) {
                $loginform__ .= quick_access::get_selector();
                $loginform__ .= '<br />';
            } else {
                $loginform__ .= "<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />";
            }
            if(!$opac_quick_access_logout || !$opac_quick_access) {
                $loginform__ .= '<a href="index.php?logout=1" id="empr_logout_lnk">'.$msg['empr_logout'].'</a>';
            }
        }
        $loginform = str_replace('!!login_form!!', $loginform__, $loginform);
        $loginform = str_replace('<!-- external_authentication -->', $external_authentication_form, $loginform);
        $footer = str_replace('!!contenu_bandeau!!', ($opac_accessibility ? $accessibility : '').$home_on_left.$loginform.$meteo.$adresse, $footer);
        $footer = str_replace('!!contenu_bandeau_2!!', $opac_facette_in_bandeau_2 ? $lvl1.$facette : '', $footer);

        $footer = str_replace('!!cms_build_info!!', '', $footer);

    }
    print $footer ;
}

$record_log = generate_log('do_resa');
if ($record_log) {
    print $record_log->validation_script();
}

if($opac_parse_html || $cms_active) {
    if($opac_parse_html) {
        $htmltoparse = parseHTML(ob_get_contents());
    } else {
        $htmltoparse = ob_get_contents();
    }
    ob_end_clean();

    // On conditionne aussi avec resa_popup car il manque des div container, et surment d'autre dans la popup
    if ($cms_active == 1 || $opac_resa_popup == 1) {

        require_once($base_path . "/classes/cms/cms_build.class.php");
        $cms = new cms_build();
        $htmltoparse = $cms->transform_html($htmltoparse);

    } elseif ($cms_active == 2) {

        $data = new \stdClass();
        $data->html = $htmltoparse;

        $cmsController = new \Pmb\CMS\Opac\Controller\CmsController($data);
        $htmltoparse = $cmsController->proceed();
    }

    $parserCSRF = new ParserCSRF();
    print $parserCSRF->parseHTML($htmltoparse);
} else {
    $htmltoparse = ob_get_contents();
    ob_end_clean();

    if ($opac_rgaa_active) {
        $htmltoparse = RGAABuilder::transform($htmltoparse);
    }

    $parserCSRF = new ParserCSRF();
    print $parserCSRF->parseHTML($htmltoparse);
}
/* Fermeture de la connexion */
pmb_mysql_close();
