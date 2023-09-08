<?php
// **************** MODULE CHAMBRES **********************
// Repertoire ou se trouve le module
define("CHA_REP_MODULE", "module_chambres");

// Script vers lequel l'utilisateur est redirige si pas autorisation d'acces
define("CHA_SCRIPT_PAS_AUTORISATION", "acces_refuse.php");

// Tables utilisees
define("CHA_TAB_ELEVES", PREFIXE . "eleves");
define("CHA_TAB_CLASSES", PREFIXE . "classes");
define("CHA_TAB_BATIMENT", PREFIXE . "cha_batiment");
define("CHA_TAB_TYPE_CHAMBRE", PREFIXE . "cha_type_chambre");
define("CHA_TAB_ETAGE", PREFIXE . "cha_etage");
define("CHA_TAB_CHAMBRE", PREFIXE . "cha_chambre");
define("CHA_TAB_RESERVATION", PREFIXE . "cha_reservation");


// Liste des fichiers Javascript qui sont inclus dans toutes le pages
$g_tab_scripts_js_toutes_pages = array();
$g_tab_scripts_js_toutes_pages[0] = 'librairie_js/validation_formulaire.js';
$g_tab_scripts_js_toutes_pages[1] = 'librairie_js/outils.js';
$g_tab_scripts_js_toutes_pages[2] = 'librairie_js/attente_serveur.js';
$g_tab_scripts_js_toutes_pages[3] = 'librairie_js/info_bulle.js';
$g_tab_scripts_js_toutes_pages[4] = 'librairie_js/select.js';
$g_tab_scripts_js_toutes_pages[5] = 'librairie_js/radio.js';
$g_tab_scripts_js_toutes_pages[6] = 'librairie_js/ajax.js';
$g_tab_scripts_js_toutes_pages[7] = 'librairie_js/popup_modules.js';

?>