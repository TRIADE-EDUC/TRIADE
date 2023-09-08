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
chdir(site_repertoire_racine(CHA_REP_MODULE));

// Connexion a la base de donnees
$cnx=cnx();


// Definition des variables globales
// Chemin relatif vers le module (par rapport a la racine du site)
$g_chemin_relatif_module;
if(CHA_REP_MODULE != "") {
	$g_chemin_relatif_module = CHA_REP_MODULE . "/";
} else {
	$g_chemin_relatif_module = "";
}

if(!isset($_SESSION[CHA_REP_MODULE])) {
	$_SESSION[CHA_REP_MODULE] = array();
}

// Pour guarder les parametres pour la liste des chambres
if(!isset($_SESSION[CHA_REP_MODULE]['chambre_liste'])) {
	$_SESSION[CHA_REP_MODULE]['chambre_liste'] = array();
}
if(!isset($_SESSION[CHA_REP_MODULE]['chambre_liste']['batiment_id'])) {
	$_SESSION[CHA_REP_MODULE]['chambre_liste']['batiment_id'] = 0;
}

// Pour guarder les parametres pour la liste des reservations
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste'] = array();
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = 0;
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['prenom'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['prenom'] = '';
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['nom'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['nom'] = '';
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'] = 0;
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'] = 0;
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = 0;
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['date_debut'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['date_debut'] = '';
}
if(!isset($_SESSION[CHA_REP_MODULE]['reservation_liste']['date_fin'])) {
	$_SESSION[CHA_REP_MODULE]['reservation_liste']['date_fin'] = '';
}

// Pour guarder les parametres pour le planning
if(!isset($_SESSION[CHA_REP_MODULE]['planning_liste'])) {
	$_SESSION[CHA_REP_MODULE]['planning_liste'] = array();
}
if(!isset($_SESSION[CHA_REP_MODULE]['planning_liste']['date_debut'])) {
	$_SESSION[CHA_REP_MODULE]['planning_liste']['date_debut'] = '';
}
if(!isset($_SESSION[CHA_REP_MODULE]['planning_liste']['date_fin'])) {
	$_SESSION[CHA_REP_MODULE]['planning_liste']['date_fin'] = '';
}

if(!isset($_SESSION[CHA_REP_MODULE]['planning_liste']['batiment_id'])) {
	$_SESSION[CHA_REP_MODULE]['planning_liste']['batiment_id'] = 0;
}
if(!isset($_SESSION[CHA_REP_MODULE]['planning_liste']['etage_id'])) {
	$_SESSION[CHA_REP_MODULE]['planning_liste']['etage_id'] = 0;
}

if(!isset($_SESSION[CHA_REP_MODULE]['planning_liste']['occupation_chambre'])) {
	$_SESSION[CHA_REP_MODULE]['planning_liste']['occupation_chambre'] = 1;
}

// Planning + Reservation
if(!isset($_SESSION[CHA_REP_MODULE]['planning_reservation']['date_debut'])) {
	$_SESSION[CHA_REP_MODULE]['planning_reservation']['date_debut'] = '';
}
if(!isset($_SESSION[CHA_REP_MODULE]['planning_reservation']['date_fin'])) {
	$_SESSION[CHA_REP_MODULE]['planning_reservation']['date_fin'] = '';
}

if(!isset($_SESSION[CHA_REP_MODULE]['planning_reservation']['chambre_id'])) {
	$_SESSION[CHA_REP_MODULE]['planning_reservation']['chambre_id'] = 0;
}
?>