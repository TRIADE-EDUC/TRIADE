<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_concept.inc.php,v 1.10 2018-06-29 12:50:41 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/classes/autoloader.class.php');
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE skos_words_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE skos_words_global_index DISABLE KEYS",$dbh);
	
	pmb_mysql_query("TRUNCATE skos_fields_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE skos_fields_global_index DISABLE KEYS",$dbh);
}

$v_state=urldecode($v_state);

$onto_store_config = array(
		/* db */
		'db_name' => DATA_BASE,
		'db_user' => USER_NAME,
		'db_pwd' => USER_PASS,
		'db_host' => SQL_SERVER,
		/* store */
		'store_name' => 'ontology',
		/* stop after 100 errors */
		'max_errors' => 100,
		'store_strip_mb_comp_str' => 0
);
$data_store_config = array(
		/* db */
		'db_name' => DATA_BASE,
		'db_user' => USER_NAME,
		'db_pwd' => USER_PASS,
		'db_host' => SQL_SERVER,
		/* store */
		'store_name' => 'rdfstore',
		/* stop after 100 errors */
		'max_errors' => 100,
		'store_strip_mb_comp_str' => 0
);

$tab_namespaces=array(
		"skos"	=> "http://www.w3.org/2004/02/skos/core#",
		"dc"	=> "http://purl.org/dc/elements/1.1",
		"dct"	=> "http://purl.org/dc/terms/",
		"owl"	=> "http://www.w3.org/2002/07/owl#",
		"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
		"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
		"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
		"pmb"	=> "http://www.pmbservices.fr/ontology#"
);

$onto_index = onto_index::get_instance('skos');
$onto_index->load_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
$onto_index->init();
$onto_index->set_netbase(true);

$elem_query = "";
//la requete de base...
$query = "select * where {
		?item <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
		?item rdf:type ?type .
		filter(";
$i=0;
foreach($onto_index->infos as $uri => $infos){
	if($i) $query.=" || ";
	$query.= "?type=<".$uri.">";
	$i++;
}
$query.=")
	}";
if (empty($count)) {
	$onto_index->handler->data_query($query);
	$count = $onto_index->handler->data_num_rows();
}
	
print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_concept"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;

// $query = pmb_mysql_query("select id_faq_question from faq_questions order by id_faq_question LIMIT $start, $lot");
$query.= " order by asc(?label) limit ".$lot." offset ".$start;
$onto_index->handler->data_query($query);	
if($onto_index->handler->data_num_rows()) {
	$onto_index->set_deleted_index(true);
	print netbase::get_display_progress($start, $count);
	$results = $onto_index->handler->data_result();
	foreach($results as $row){
	    $info=$onto_index->maj(0,$row->item);
	}
	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_CONCEPT;
	$not = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_concept"], ENT_QUOTES, $charset)." :";
	$v_state .= $compte." ".htmlentities($msg["nettoyage_res_reindex_concept"], ENT_QUOTES, $charset);

	print netbase::get_process_state_form($v_state, $spec);
	pmb_mysql_query("ALTER TABLE skos_words_global_index ENABLE KEYS",$dbh);
	pmb_mysql_query("ALTER TABLE skos_fields_global_index ENABLE KEYS",$dbh);
}