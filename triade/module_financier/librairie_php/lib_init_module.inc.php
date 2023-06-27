<?php
// Inclure le fichier de config de l'application
include_once("../common/config.inc.php");

// Inclure le fichier de config de l'application
include_once("../common/config2.inc.php");

// Inclure le fichier de config du module
include("./common/config.inc.php");
 
// Inclure la classe de gestion des acces a la base de donnees
include_once('../librairie_php/db_triade.php');

// Inclure la librairie des outils du module
include("./librairie_php/lib_outils.inc.php");

// Inclure la librairie de gestion des autorisations du module
include("./librairie_php/lib_autorisations.inc.php");


if ($_SESSION["langue"] == "") $_SESSION["langue"]="fr";
// Inclure la librairie PHP de traduction des textes du module
include("./librairie_php/langue-text-" . $_SESSION["langue"] . ".php");


// Changer le repertoire de travail => le positioner sur la racine du site (pour que les includes de
// l'application fonctionnent)
chdir(site_repertoire_racine(FIN_REP_MODULE));

// Connexion a la base de donnees
$cnx=cnx();


// Definition des variables globales
// Chemin relatif vers le module (par rapport a la racine du site)
$g_chemin_relatif_module;
if(FIN_REP_MODULE != "") {
	$g_chemin_relatif_module = FIN_REP_MODULE . "/";
} else {
	$g_chemin_relatif_module = "";
}

if(!isset($_SESSION[FIN_REP_MODULE])) {
	$_SESSION[FIN_REP_MODULE] = array();
}

// Pour guarder les parametres pour l'inscription (recherche eleve et inscription)
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_rechercher'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_rechercher'] = array();
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_rechercher']['code_class'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['code_class'] = '';
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_rechercher']['nom_eleve'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['nom_eleve'] = '';
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_rechercher']['inscrits_pas_inscrits'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['inscrits_pas_inscrits'] = 'tous';
}

// Pour guarder les parametres pour la duplication de l'echeancier d'un eleve existant
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier'] = array();
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['type_creation'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['type_creation'] = 'nouvelle';
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['code_class'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['code_class'] = '0';
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['annee_scolaire'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['annee_scolaire'] = '';
}
if(!isset($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['inscription_id_a_dupliquer'])) {
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['inscription_id_a_dupliquer'] = '0';
}


?>