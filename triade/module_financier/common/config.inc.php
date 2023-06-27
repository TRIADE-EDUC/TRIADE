<?php
// **************** MODULE FINANCIER **********************
// Repertoire ou se trouve le module
define("FIN_REP_MODULE", "module_financier");

// Script vers lequel l'utilisateur est redirige si pas autorisation d'acces
define("FIN_SCRIPT_PAS_AUTORISATION", "acces_refuse.php");

// Tables utilisees
define("FIN_TAB_ELEVES", PREFIXE . "eleves");
define("FIN_TAB_RIB", PREFIXE . "fin_rib");
define("FIN_TAB_CLASSES", PREFIXE . "classes");
define("FIN_TAB_BAREME", PREFIXE . "fin_bareme");
define("FIN_TAB_FRAIS_BAREME", PREFIXE . "fin_frais_bareme");
define("FIN_TAB_TYPE_FRAIS", PREFIXE . "fin_type_frais");
define("FIN_TAB_GROUPE_FRAIS", PREFIXE . "fin_groupe_frais");
define("FIN_TAB_ECHEANCIER_GROUPE", PREFIXE . "fin_echeancier_groupe");
define("FIN_TAB_INSCRIPTIONS", PREFIXE . "fin_inscriptions");
define("FIN_TAB_FRAIS_INSCRIPTION", PREFIXE . "fin_frais_inscription");
define("FIN_TAB_TYPE_ECHEANCIER", PREFIXE . "fin_type_echeancier");
define("FIN_TAB_ECHEANCIER", PREFIXE . "fin_echeancier");
define("FIN_TAB_REGLEMENT", PREFIXE . "fin_reglement");
define("FIN_TAB_TYPE_REGLEMENT", PREFIXE . "fin_type_reglement");

define("FIN_TAB_CONFIG_ECOLE", PREFIXE . "fin_config_ecole");

// Liste des fichiers Javascript qui sont inclus dans toutes le pages
$g_tab_scripts_js_toutes_pages = array();
$g_tab_scripts_js_toutes_pages[0] = 'librairie_js/validation_formulaire.js';
$g_tab_scripts_js_toutes_pages[1] = 'librairie_js/outils.js';
$g_tab_scripts_js_toutes_pages[2] = 'librairie_js/attente_serveur.js';
$g_tab_scripts_js_toutes_pages[3] = 'librairie_js/info_bulle.js';
$g_tab_scripts_js_toutes_pages[4] = 'librairie_js/select.js';
$g_tab_scripts_js_toutes_pages[5] = 'librairie_js/radio.js';
$g_tab_scripts_js_toutes_pages[6] = 'librairie_js/ajax.js';
$g_tab_scripts_js_toutes_pages[7] = 'librairie_js/finance.js';
$g_tab_scripts_js_toutes_pages[8] = 'librairie_js/ajax_recherche.js';

// Liste des types de reglements non modifiable
$g_tab_type_reglement_id = array();
$g_tab_type_reglement_id['cheque'] = 2;
$g_tab_type_reglement_id['espece'] = 3;
$g_tab_type_reglement_id['prelevement'] = 4;

// Liste des champs utilises dans le fichier de prelevement
$g_tab_fichier_prelevement_champs = array();
$g_tab_fichier_prelevement_champs[0] = array('nom_champ' => 'CODE', 'type' => 'chaine', 'dim' => 2);
$g_tab_fichier_prelevement_champs[1] = array('nom_champ' => 'CODOPE', 'type' => 'chaine', 'dim' => 2);
$g_tab_fichier_prelevement_champs[2] = array('nom_champ' => 'B', 'type' => 'chaine', 'dim' => 8);
$g_tab_fichier_prelevement_champs[3] = array('nom_champ' => 'NUMEMET', 'type' => 'chaine', 'dim' => 6);
$g_tab_fichier_prelevement_champs[4] = array('nom_champ' => 'REF', 'type' => 'chaine', 'dim' => 7);
$g_tab_fichier_prelevement_champs[5] = array('nom_champ' => 'DATE', 'type' => 'chaine', 'dim' => 5);
$g_tab_fichier_prelevement_champs[6] = array('nom_champ' => 'ICB', 'type' => 'chaine', 'dim' => 24);
$g_tab_fichier_prelevement_champs[7] = array('nom_champ' => 'DOM', 'type' => 'chaine', 'dim' => 24);
$g_tab_fichier_prelevement_champs[8] = array('nom_champ' => 'B2', 'type' => 'chaine', 'dim' => 8);
$g_tab_fichier_prelevement_champs[9] = array('nom_champ' => 'CG', 'type' => 'entier', 'dim' => 5);
$g_tab_fichier_prelevement_champs[10] = array('nom_champ' => 'COMPT', 'type' => 'entier', 'dim' => 11);
$g_tab_fichier_prelevement_champs[11] = array('nom_champ' => 'MT1', 'type' => 'montant', 'dim' => 16);
$g_tab_fichier_prelevement_champs[12] = array('nom_champ' => 'LIBELLE', 'type' => 'chaine', 'dim' => 31);
$g_tab_fichier_prelevement_champs[13] = array('nom_champ' => 'CB', 'type' => 'entier', 'dim' => 5);
$g_tab_fichier_prelevement_champs[14] = array('nom_champ' => 'B1', 'type' => 'chaine', 'dim' => 6);
$g_tab_fichier_prelevement_champs[15] = array('nom_champ' => 'CG_DERNIERE_LIGNE', 'type' => 'chaine', 'dim' => 5);
$g_tab_fichier_prelevement_champs[16] = array('nom_champ' => 'COMPT_DERNIERE_LIGNE', 'type' => 'chaine', 'dim' => 11);
$g_tab_fichier_prelevement_champs[17] = array('nom_champ' => 'CB_DERNIERE_LIGNE', 'type' => 'chaine', 'dim' => 5);
?>
