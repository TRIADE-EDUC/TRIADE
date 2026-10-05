<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.20 2019/06/03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $authority_statut, $skos_concept_search_form_submitted, $deflt_concept_scheme, $params, $nb_per_page_gestion, $thesaurus_concepts_autopostage;

if(!isset($authority_statut))  $authority_statut = 0;


if(empty($skos_concept_search_form_submitted)){
    if(isset($_SESSION['onto_skos_concept_last_concept_scheme']) && ($_SESSION['onto_skos_concept_last_concept_scheme'] !== "")){
        $concept_scheme = $_SESSION['onto_skos_concept_last_concept_scheme'];
    }else{
        $concept_scheme = $deflt_concept_scheme;
    }
}
if(!is_array($concept_scheme)){
    if($concept_scheme != ""){
        $concept_scheme = explode(",",$concept_scheme);
    }else{
        $concept_scheme = [];
    }
}


$params = new onto_param(array(
	'categ'=>'concepts',
	'sub'=> 'concept',
	'action'=>'list',
	'page'=>'1',
	'nb_per_page'=> $nb_per_page_gestion,
	'id'=>'',
	'parent_id'=>'',
	'user_input'=>'',
    'concept_scheme'=>$concept_scheme,
    'item_uri' => "",
	'only_top_concepts' => ((empty($skos_concept_search_form_submitted) && isset($_SESSION['onto_skos_concept_only_top_concepts'])) ? $_SESSION['onto_skos_concept_only_top_concepts'] : 0),
	'base_resource'=> "autorites.php",
	/* Pour le replace */
	'by' => '',
	'aut_link_save' => '',
	'authority_statut' => $authority_statut,
	'thesaurus_concepts_autopostage' => (!empty($thesaurus_concepts_autopostage) ? $thesaurus_concepts_autopostage : 0)
));

$onto_ui = new onto_ui("", skos_onto::get_store(), array(), skos_datastore::get_store(), array(), array(), 'http://www.w3.org/2004/02/skos/core#prefLabel', $params);
$onto_ui->proceed();