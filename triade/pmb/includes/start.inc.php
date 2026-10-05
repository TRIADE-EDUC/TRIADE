<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: start.inc.php,v 1.24 2023/11/13 13:55:07 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}
// ce système crée des variables de nom type_param_sstype_param et de contenu valeur_param à partir de la table parametres

global $class_path, $include_path, $pmb_indexation_lang, $lang, $pmb_display_errors;

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
        if (count($nxtweb_params)) {
            $cache->setInCache($db_parameters_datetime, pmb_sql_value("select now()"));
            $cache->setInCache($db_parameters_name, $nxtweb_params);
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
    }
}

if (! $pmb_indexation_lang) {
    $pmb_indexation_lang = $lang;
}

require_once ($include_path . "/marc_tables/" . $pmb_indexation_lang . "/empty_words");
require_once ($class_path . "/semantique.class.php");
// ajout des mots vides calcules
$add_empty_words = semantique::add_empty_words();
if ($add_empty_words) {
    eval($add_empty_words);
}
// Affichage des erreurs PHP
if (! empty($pmb_display_errors)) {
    $pmb_display_errors = intval($pmb_display_errors);
    if ($pmb_display_errors == 1 || $pmb_display_errors == 2) {
        ini_set('display_errors', 1);
    }
}


// Definition du moteur temporaire
$result = pmb_mysql_query("SHOW VARIABLES LIKE 'default_storage_engine'");
$default_storage_engine = "MyISAM";
if (pmb_mysql_num_rows($result)) {
    $default_storage_engine = pmb_mysql_result($result, 0, 1);
}

global $default_tmp_storage_engine;
$default_tmp_storage_engine = $default_storage_engine;

