<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: start.inc.php,v 1.52 2024/04/02 10:59:38 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

// ce système crée des variables de nom type_param_sstype_param et de contenu valeur_param à partir de la table parametres

global $base_path, $class_path, $charset;
global $opac_default_style, $opac_authorized_styles, $opac_default_lang, $cms_active;
global $opac_categories_categ_path_sep, $opac_display_errors;
global $pmb_indexation_lang, $opac_search_results_per_page, $opac_max_results_on_a_page;

// header charset
if(!isset($base_is_http_request) || !$base_is_http_request) {
    if ($charset) {
        header("Content-Type: text/html; charset=$charset");
    }
}

require_once ($base_path . "/includes/misc.inc.php");
require_once ($class_path . "/cache_factory.class.php");

// Tableau de surcharge par le config_local !
global $overload_global_parameters;

/* param par défaut */
$nxtweb_params = [];
$cache = cache_factory::getCache();
if ($cache) {
    $db_parameters_name = SQL_SERVER . DATA_BASE . "_parameters";
    $db_parameters_datetime = SQL_SERVER . DATA_BASE . "_parameters_datetime";
    $tmp_parameters_datetime = $cache->getFromCache($db_parameters_datetime);
    $cache_up_to_date = pmb_sql_value("select if ((SELECT IF(UPDATE_TIME IS NULL,'3000-01-01 01:01:01',UPDATE_TIME) from information_schema.tables where table_schema='" . DATA_BASE . "' and table_name='parametres' ) >= '" . $tmp_parameters_datetime . "', 0, 1)");

    if ($tmp_parameters_datetime && $cache_up_to_date) {
        $nxtweb_params = $cache->getFromCache($db_parameters_name);
        foreach ($nxtweb_params as $param_name => $param_value) {
            global ${$param_name};
            // Les fichiers config_local et opac_config_local ne sont par définition jamais lus en même temps,
            // donc on doit revérifier ici pour être sur de ne pas avoir une surcharge manquante
            if (isset($overload_global_parameters[$param_name])) {
                ${$param_name} = $overload_global_parameters[$param_name];
            } else {
                ${$param_name} = $param_value;
            }
        }
    } else {
        $requete_param = "SELECT type_param, sstype_param, valeur_param FROM parametres ";
        $res_param = pmb_mysql_query($requete_param);
        while ($field_values = pmb_mysql_fetch_row($res_param)) {
            $field = $field_values[0] . "_" . $field_values[1];
            global ${$field};
            if (isset($overload_global_parameters[$field])) {
                ${$field} = $overload_global_parameters[$field];
            } else {
                ${$field} = $field_values[2];
            }
            $nxtweb_params[$field] = ${$field};
        }
        if (is_countable($nxtweb_params) && count($nxtweb_params)) {
            $cache->setInCache($db_parameters_datetime, pmb_sql_value("select now()"));
            $cache->setInCache($db_parameters_name, $nxtweb_params);
        }
    }
} else {
    $requete_param = "SELECT type_param, sstype_param, valeur_param FROM parametres  ";
    $res_param = pmb_mysql_query($requete_param);
    while ($field_values = pmb_mysql_fetch_row($res_param)) {
        $field = $field_values[0] . "_" . $field_values[1];
        global ${$field};
        if (isset($overload_global_parameters[$field])) {
            ${$field} = $overload_global_parameters[$field];
        } else {
            ${$field} = $field_values[2];
        }
    }
}

// On a une petite histoire avec les URLs dans le portail !
if (! isset($_SESSION['cms_build_activate'])) {
    $_SESSION['cms_build_activate'] = '';
}
if (! isset($cms_build_activate)) {
    $cms_build_activate = 0;
}
if ($cms_active && $_SESSION['cms_build_activate']) {
    $opac_url_base = $cms_url_base_cms_build;
}

// if there isn't a custom class stored in the notice_affichage_class parameter
// it's selected the default
if (! $opac_notice_affichage_class) {
    $opac_notice_affichage_class = "notice_affichage";
}
// afin que le séparateur de catégories soit correct partout mais visible à l'oeil nu en paramétrage :
$opac_categories_categ_path_sep = ' ' . htmlentities($opac_categories_categ_path_sep, ENT_QUOTES, $charset) . ' ';

// chargement de la feuille de style
if (isset($opac_css)) {
    $_SESSION["css"] = $opac_css;
    $css = $opac_css;
} else if (isset($_SESSION["css"]) && $_SESSION["css"] != "") {
    $css = $_SESSION["css"];
} else {
    $css = $opac_default_style;
}

// vérification que le style demandé (éventuellement par l'url) est bien autorisé:
$tab_opac_authorized_styles = explode(',', $opac_authorized_styles);
$style_is_authorized = array_search($css, $tab_opac_authorized_styles);
if (! ($style_is_authorized !== FALSE && $style_is_authorized !== NULL))
    $css = $opac_default_style;

// si aucune feuille de style n'est précisée,
// chargement de la feuille 1/1.css
if (! $css) {
    $css = "1";
}

$_SESSION["css"] = $css; // Je mets en session le bon style Opac pour le cas ou le style demandé ne soit pas autorisé

// a language was selected so refresh cookie and set lang
if (!empty($lang_sel)) {
    $rqtveriflang = "select 1 from parametres where type_param='opac' and sstype_param='show_languages' and valeur_param like '%" . addslashes($lang_sel) . "%'";
    $reqveriflang = pmb_mysql_query($rqtveriflang);
    if (! pmb_mysql_num_rows($reqveriflang)) {
        $lang_sel = $opac_default_lang;
    }
    $expiration = time() + 30000000; /* 1 year */
    pmb_setcookie('PhpMyBibli-LANG', $lang_sel, $expiration);
    $lang = $lang_sel;
    // if there is a user session we also change the language in PMB database for this user
    if ($_SESSION["user_code"]) {
        $query = "UPDATE empr SET empr_lang='$lang' WHERE empr_login='" . $_SESSION['user_code'] . "' limit 1";
        pmb_mysql_query($query);
        $_SESSION["lang"] = $lang;
    }
} else {
    // there is a user session so we use his params
    if (isset($_SESSION["lang"])) {
        $lang = $_SESSION["lang"];
    } else {
        // no changement,no session, we use the cookie to set the lang
        // cookies must be enabled to remember the lang...this must be changed ?
        if (isset($_COOKIE['PhpMyBibli-LANG'])) {
            $rqtveriflang = "select 1 from parametres where type_param='opac' and sstype_param='show_languages' and valeur_param like '%" . pmb_mysql_real_escape_string(stripslashes($_COOKIE['PhpMyBibli-LANG'])) . "%'";
            $reqveriflang = pmb_mysql_query($rqtveriflang);
            if (! pmb_mysql_num_rows($reqveriflang)) {
                $lang = $opac_default_lang;
            } else {
                $lang = $_COOKIE['PhpMyBibli-LANG'];
            }
        }
        if (! isset($lang)) {
            if ($opac_default_lang) {
                $lang = $opac_default_lang;
            } else {
                $lang = "fr_FR";
            }
        }
    }
}

if (! $pmb_indexation_lang) {
    $pmb_indexation_lang = $lang;
}
if ($opac_search_results_per_page > $opac_max_results_on_a_page) {
    $opac_search_results_per_page = $opac_max_results_on_a_page;
}
// Affichage des erreurs PHP
if (! empty($opac_display_errors)) {
    $opac_display_errors = intval($opac_display_errors);
    if ($opac_display_errors == 1 || $opac_display_errors == 2) {
        ini_set('display_errors', 1);
    }
}

require_once ($base_path . "/includes/logs.inc.php");

// Definition du moteur temporaire
$result = pmb_mysql_query("SHOW VARIABLES LIKE 'default_storage_engine'");
$default_storage_engine = "MyISAM";
if (pmb_mysql_num_rows($result)) {
    $default_storage_engine = pmb_mysql_result($result, 0, 1);
}

global $default_tmp_storage_engine;
$default_tmp_storage_engine = $default_storage_engine;
