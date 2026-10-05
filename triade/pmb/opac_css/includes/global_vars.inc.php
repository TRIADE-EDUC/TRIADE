<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: global_vars.inc.php,v 1.18.2.1.2.1 2025/04/18 08:28:01 jparis Exp $

// fichier de configuration générale

// prevents direct script access
pt_register("SERVER", "REQUEST_URI");

if(preg_match('/global_vars\.inc\.php/', $REQUEST_URI)) {
    require_once './forbidden.inc.php';
    forbidden();
}

// Liste des variables qui ne doivent pas etre declarees
$forbidden_overload = [
    'base_path',
    'include_path',
    'class_path',
    'overload_global_parameters',
    'forbidden_overload',
    'charset',
    'javascript_path',
    'SQL_MOTOR_TYPE',
    'SQL_VARIABLES',
    '_SERVER',
    '_SESSION',
    '_GET',
    '_POST',
    '_FILES',
    '_COOKIE',
    '_REQUEST',
    '_ENV',
    'known_int_variables',
    'func_format',
    'footer_suite',
    'facettes_lvl1',
];

// Liste des variables qui sont des entiers
$known_int_variables = [
    "nbr_lignes",
    "page",
    "nb_per_page_custom",
    "nb_per_page",
    "ai_session",
];

/**
 * Quote string with slashes
 *
 * @param array|string $var
 * @return void
 */
function add_sl(&$var)
{
    if (is_array($var)) {
        reset($var);
        foreach ($var as $k => $v) {
            add_sl($var[$k]);
        }
    } else {
        $var = addslashes($var);
    }
}

/**
 * Format a variable
 *
 * @param string $name
 * @param array|string $val
 * @return void
 */
function format_global($name, $val)
{
    global $known_int_variables;
    switch (true) {
        case in_array($name, $known_int_variables):
            // cas particuliers de certaines variables censees etre des nombres mais qu'on passe en chaine
            return ($val === "") ? '' : intval($val);

        default:
            add_sl($val);
            return $val;
    }
}

/* on récupère tout sans se poser de question, attention à la sécurité ! */
foreach ($_GET as $__key__PMB => $val) {
    if (!in_array($__key__PMB, $forbidden_overload)) {
        $GLOBALS[$__key__PMB] = format_global($__key__PMB, $val);
    }
}
foreach ($_POST as $__key__PMB => $val) {
    if (!in_array($__key__PMB, $forbidden_overload)) {
        $GLOBALS[$__key__PMB] = format_global($__key__PMB, $val);
    }
}

//Post de fichiers
foreach ($_FILES as $__key__PMB => $val) {
    if (!in_array($__key__PMB, $forbidden_overload)) {
        $GLOBALS[$__key__PMB] = format_global($__key__PMB, $val);
    }
}

function pt_register()
{
	$num_args = func_num_args();
	$vars = [];
    if ($num_args >= 2) {
        $method = strtoupper(func_get_arg(0));

        if (
            ($method != 'SESSION') &&
            ($method != 'GET') &&
            ($method != 'POST') &&
            ($method != 'SERVER') &&
            ($method != 'COOKIE') &&
            ($method != 'FILES') &&
            ($method != 'REQUEST') &&
            ($method != 'ENV')
        ) {
            http_response_code(500);
            die('The first argument of pt_register must be one of the following: SESSION, GET, POST, SERVER, COOKIE, FILES, REQUEST or ENV');
        }

        $varname = "_{$method}";
        global ${$varname};

        for ($i = 1; $i < $num_args; $i++) {
            $parameter = func_get_arg($i);
            if (isset(${$varname}[$parameter])) {
                global ${$parameter};
                ${$parameter} = ${$varname}[$parameter];
            }
        }

    } else {
        http_response_code(500);
        die('You must specify at least two arguments');
    }
}

/* quand register_globals sera à off il faudra récupérer en automatique le strict minum : */
pt_register("COOKIE", "PhpMyBibli-SESSID", "PhpMyBibli-LOGIN", "PhpMyBibli-SESSNAME", "PhpMyBibli-LOGIN", "PhpMyBibli-LANG");
pt_register("SERVER", "REMOTE_ADDR", "HTTP_USER_AGENT", "PHP_SELF", "REQUEST_URI", "SCRIPT_NAME");
