<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categories.inc.php,v 1.13 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $parent, $id_thes, $class_path, $msg, $id;

if(!isset($parent)) {
	$parent = 0;
} else {
	$parent+= 0;
}
if (!isset($id_thes)) {
	$id_thes = thesaurus::getSessionThesaurusId();
} else if ($id_thes) {
	thesaurus::setSessionThesaurusId($id_thes);
}

require_once("$class_path/categ_browser.class.php");
require_once($class_path."/entities/entities_categories_controller.class.php");

//Spécifique à l'affichage du formulaire de modif d'une catégorie
function usort_categs_array_by_libelle($a, $b)	{
	if ($a->libelle == $b->libelle) {
		return 0;
	}
	return (strtolower(convert_diacrit($a->libelle)) < strtolower(convert_diacrit($b->libelle))) ? -1 : 1;
}

// gestion des catégories
print "<h1>".$msg[140]."&nbsp;: ". $msg[134]."</h1>";

$entities_categories_controller = new entities_categories_controller($id);
$entities_categories_controller->set_url_base('autorites.php?categ=categories');
$entities_categories_controller->set_parent($parent);
$entities_categories_controller->set_id_thes($id_thes);
$entities_categories_controller->proceed();