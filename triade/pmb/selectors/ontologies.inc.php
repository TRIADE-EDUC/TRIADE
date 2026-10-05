<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontologies.inc.php,v 1.6.6.1 2025/03/04 16:43:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

/* $caller = Nom du formulaire appelant
 * $objs = type d'objet demandé
 * $element = id de l'element à modifier
 * $order = numéro du champ à modifier
 * $range = id du range à afficher
 * $deb_rech = texte à rechercher
 */

if (!isset($range)) $range = "";
if (!isset($page)) $page = 1;
if($parent_id){
	$deb_rech= "";
}
$base_url = "./select.php?what=ontologies&ontology_id=$ontology_id&source=$source&caller=".rawurlencode($caller)."&objs=$objs&element=$element&order=$order&infield=$infield&callback=$callback&dyn=$dyn&deb_rech=$deb_rech&param1=$param1&param2=$param2&module_from=$module_from";

// contenu popup selection
require('./selectors/templates/sel_ontology.tpl.php');


$ontology = new ontology($ontology_id);

$params = new onto_param(array(
		'categ'=>'',
		'sub'=>'',
		'objs'=>$objs,
		'action'=>'list_selector',
		'page'=>'1',
		'nb_per_page'=>'20',
		'caller'=>$caller,
		'element'=>$element,
		'order'=>$order,
		'callback'=>$callback,
		'base_url'=>$base_url,
		'deb_rech'=>$deb_rech,
		'parent_id'=>'',
		'param1' => $param1,
		'param2' => $param2,
		'item_uri' => $item_uri,
        'range' => $range,
		'ontology_id' => $ontology_id,
		'base_resource'=> "modelling.php"
));
if($source != "onto"){
	$ontology->exec_data_selector_framework($params);
}else{
	$ontology->exec_onto_selector_framework($params);
}
// ?>