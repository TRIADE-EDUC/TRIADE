<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_rdfstore.inc.php,v 1.11 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $start, $v_state, $spec, $count;

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE rdfstore_index");
}

$v_state=urldecode($v_state);

if (!$count) {
	$q_count = "select count(1) from rdfstore_triple t, rdfstore_id2val l where t.o_type=2 and t.o_lang_dt=l.id and length(l.val)<3";
	$r_count = pmb_mysql_query($q_count);
	$count = pmb_mysql_result($r_count, 0, 0);
}

print netbase::get_display_progress_title($msg["nettoyage_rdfstore_reindexation"]);

$q_sel = "
select t.t as num_triple, s.val as subject_uri, p.val as predicat_uri, o.id as num_object, o.val as object_val, l.val as object_lang 
from rdfstore_triple t, rdfstore_s2val s, rdfstore_id2val p, rdfstore_o2val o, rdfstore_id2val l  
where t.o_type=2 and t.o_lang_dt=l.id and length(l.val)<3 and t.s=s.id and t.p=p.id and t.o=o.id 
order by t.t LIMIT $start, $lot";
$r_sel = pmb_mysql_query($q_sel);

if(pmb_mysql_num_rows($r_sel)) {
	print netbase::get_display_progress($start, $count);

	require_once("$class_path/rdf/ontology.class.php");
	$op = new ontology_parser("$class_path/rdf/skos_pmb.rdf");
	$sh = new skos_handler($op);

	while(($triple = pmb_mysql_fetch_object($r_sel))){
		$type=$sh->op->from_ns($sh->get_object_type($triple->subject_uri));
		$q_ins = "insert ignore into rdfstore_index ";
		$q_ins.= "set num_triple='".$triple->num_triple."', ";
		$q_ins.= "subject_uri='".addslashes($triple->subject_uri)."', ";
		$q_ins.= "subject_type='".addslashes($type)."', ";
		$q_ins.= "predicat_uri='".addslashes($triple->predicat_uri)."', ";
		$q_ins.= "num_object='".$triple->num_object."', "; 
		$q_ins.= "object_val ='".addslashes($triple->object_val)."', ";
		$q_ins.= "object_index=' ".strip_empty_chars($triple->object_val)." ', ";
		$q_ins.= "object_lang ='".addslashes($triple->object_lang)."' ";

		pmb_mysql_query($q_ins);
	}
	
	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - INDEX_RDFSTORE;
	$not = pmb_mysql_query("select count(1) from rdfstore_triple where o_type=2");
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_rdfstore_reindexation"], $compte." ".$msg["nettoyage_rdfstore_reindex_elt"]);
	print netbase::get_process_state_form($v_state, $spec);
}

?>