<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontology.inc.php,v 1.24 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("./selectors/classes/selector_ontology.class.php");

/* $caller = Nom du formulaire appelant
 * $objs = type d'objet demandé
 * $element = id de l'element à modifier
 * $order = numéro du champ à modifier
 * $range = id du range à afficher
 * $deb_rech = texte à rechercher 
 */

if (!isset($range)) $range = 0;
if (!isset($page)) $page = 1;

if(isset($parent_id) && $parent_id){
	$deb_rech= "";
}
global $concept_scheme;
if(!is_array($concept_scheme) && $concept_scheme != ''){
    $concept_scheme = explode(",",$concept_scheme);
}else{
    $concept_scheme = [];
}
$base_url = selector_ontology::get_base_url();

$selector_ontology = new selector_ontology(stripslashes($deb_rech));
$selector_ontology->proceed();
