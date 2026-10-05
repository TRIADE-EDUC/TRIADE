<?php
// ----------------------------------------------------------------------------
// Compatibilité get_magic_quotes_gpc() pour PHP 8
// ----------------------------------------------------------------------------
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc(): bool {
        return false;
    }
}

function globalizeRequestVars() {
    // Combine GET and POST
    $inputs = array_merge($_GET, $_POST);

    foreach ($inputs as $key => $value) {
        // On s’assure que la clé est un nom de variable valide
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {

            // Nettoyage minimal (optionnel, améliorable)
            if (is_string($value)) {
                $value = trim($value);
            }

            // Création dynamique de variable
            $GLOBALS[$key] = $value;
        }
    }
}

globalizeRequestVars();

function countTriade($val) {
        if (is_countable($val)) {
                return(count($val));
        }else{
                return(0);
        }
}

// ----------------------------------------------------------------------------
// Inclusion configuration
// ----------------------------------------------------------------------------
include("conf.inc.php");

// ----------------------------------------------------------------------------
// Récupération des paramètres depuis la BDD
// ----------------------------------------------------------------------------
if (empty($_GET['msg']) || $_GET['msg'] != "6") {
    $DB_CX->DbQuery("SELECT param, valeur FROM {$PREFIX_TABLE}configuration WHERE groupe=0");
    while ($enr = $DB_CX->DbNextRow()) {
        $val = $enr['valeur'];
        if ($val === "OUI") {
            ${$enr['param']} = true;
        } elseif ($val === "NON") {
            ${$enr['param']} = false;
        } else {
            ${$enr['param']} = $val;
        }
    }
}

// ----------------------------------------------------------------------------
// Fonction de nettoyage des inputs
// ----------------------------------------------------------------------------
function sanitizeInput($str) {
    global $AUTORISE_HTML;
    $clean = $AUTORISE_HTML ? $str : strip_tags($str);
    return trim($clean);
}

// ----------------------------------------------------------------------------
// Extraction sécurisée des données
// ----------------------------------------------------------------------------
function pxExtract(array $array, array &$target): bool {
    $is_magic_quotes = get_magic_quotes_gpc();

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            if (!isset($target[$key]) || !is_array($target[$key])) {
                $target[$key] = [];
            }
            pxExtract($value, $target[$key]);
        } else {
            $cleanValue = sanitizeInput($value);
            if (!$is_magic_quotes) {
                $cleanValue = addslashes($cleanValue);
            }
            $target[$key] = $cleanValue;
        }
    }
    return true;
}

// ----------------------------------------------------------------------------
// Récupération sécurisée des données POST, GET et FILES
// ----------------------------------------------------------------------------
$cleanInputs = [];
if (!empty($_GET)) {
    pxExtract($_GET, $cleanInputs);
}
if (!empty($_POST)) {
    pxExtract($_POST, $cleanInputs);
}

// Fichiers
if (!empty($_FILES)) {
    foreach ($_FILES as $name => $file) {
        $cleanInputs[$name] = $file['tmp_name'];
        $cleanInputs[$name.'_name'] = $file['name'];
    }
}

// ----------------------------------------------------------------------------
// Gestion des erreurs BDD
// ----------------------------------------------------------------------------
function serveurDown() {
    echo "<html>
    <head><link rel=\"stylesheet\" type=\"text/css\" href=\"css/agenda_css.php\"></head>
    <body onload=\"window.location.href='index.php?msg=6';\"></body>
    </html>";
    exit;
}

// ----------------------------------------------------------------------------
// Constantes pour la gestion des droits et menus (inchangées)
// ----------------------------------------------------------------------------

// profil
define("_DROIT_PROFIL_RIEN", 0);
define("_DROIT_PROFIL_PARAM_BASE", 10);
define("_DROIT_PROFIL_PARAM_PARTAGE", 20);
define("_DROIT_PROFIL_AUTRE_PARAM_BASE", 30);
define("_DROIT_PROFIL_AUTRE_PARAM_PARTAGE", 40);
define("_DROIT_PROFIL_COMPLET", 50);

// agenda
define("_DROIT_AGENDA_SEUL", 0);
define("_DROIT_AGENDA_PARTAGE", 10);
define("_DROIT_AGENDA_TOUS", 20);

// note
define("_DROIT_NOTE_CONSULT_SEUL", 0);
define("_DROIT_NOTE_CONSULT_RECHERCHE", 5);
define("_DROIT_NOTE_STANDARD_SANS_APPR", 10);
define("_DROIT_NOTE_STANDARD", 15);
define("_DROIT_NOTE_MODIF_STATUT", 20);
define("_DROIT_NOTE_MODIF_CREATION", 30);
define("_DROIT_NOTE_COMPLET", 40);

// menu
define("_MENU_PLG_QUOT", 0);
define("_MENU_PLG_HEBDO", 1);
define("_MENU_PLG_MENSUEL", 2);
define("_MENU_PLG_ANNUEL", 3);
define("_MENU_PLG_MENS_GBL", 4);
define("_MENU_PLG_HEBDO_GBL", 5);
define("_MENU_PLG_QUOT_GBL", 6);
define("_MENU_DISP_HEBDO", 8);
define("_MENU_DISP_QUOT", 9);
define("_MENU_RECHERCHE", 10);
define("_MENU_CONTACT", 11);
define("_MENU_PROFIL", 13);
define("_MENU_NOTE_IMPORT", 16);
define("_MENU_NOTE_EXPORT", 17);
define("_MENU_ADMIN", 20);

// types
define("_TYPE_ANNIV", 1);
define("_TYPE_NOTE", 2);
define("_TYPE_CONTACT", 3);
define("_TYPE_IMPORT_CONTACT", 4);
define("_TYPE_EVENEMENT", 5);
define("_TYPE_MEMO", 8);
define("_TYPE_LIBELLE", 9);
define("_TYPE_FAVORIS", 10);
define("_TYPE_RSS_READER", 101);
define("_TYPE_EMPL", 11);

// rappels
define('_RAPPEL_NOTE', 1);
define('_RAPPEL_ANNIV', 2);
define('_RAPPEL_ANNIV_CONTACT', 3);
?>

