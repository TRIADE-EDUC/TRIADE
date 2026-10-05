<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_planning.class.php,v 1.6.2.1 2024/09/18 07:32:27 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/netbase/netbase.class.php");
		
class clean_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		$netbase = new netbase();
			
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label for='bannette'>".$this->msg["planificateur_clean"]."</label>
			</div>
			<div class='colonne_suite'><div>";
		$form_task .= $netbase->get_form_proceedings((isset($param["clean"]) ? $param["clean"] : array()));
		$form_task .= "</div>
			</div></div>";	
								
		return $form_task;
	}
		    
	public function make_serialized_task_params() {
    	global $index_global, $index_notices, $clean_authors, $clean_editeurs;
    	global $clean_collections, $clean_subcollections, $clean_categories;
    	global $clean_series, $clean_titres_uniformes, $clean_indexint;
    	global $clean_relations, $clean_notices, $index_acquisitions;
    	global $gen_signature_notice, $gen_phonetique, $nettoyage_clean_tags, $clean_categories_path;
    	global $gen_date_publication_article, $gen_date_tri, $reindex_docnum, $gen_ark, $gen_docnum_thumbnail;
    	global $clean_opac_search_cache, $clean_cache_amende, $clean_cache_temporary_files, $clean_cache_apcu;
    	global $index_rdfstore, $index_synchrordfstore;
    	global $index_faq, $index_cms, $index_concept, $hash_empr_password;
    	global $index_authorities;
    	global $gen_signature_docnum, $clean_records_thumbnail, $index_date_flot, $clean_entities_data, $clean_docnum_thumbnail;
    	global $index_sphinx_records, $index_sphinx_authorities, $index_sphinx_concepts;
    	
		$t = parent::make_serialized_task_params();
		
		$t_clean = array();
		//Ordre d'exécution
		if($clean_notices) $t_clean["clean_notices"] = $clean_notices;
		if($clean_subcollections) $t_clean["clean_subcollections"] = $clean_subcollections;
		if($clean_collections) $t_clean["clean_collections"] = $clean_collections;
		if($clean_editeurs) $t_clean["clean_editeurs"] = $clean_editeurs;
		if($clean_authors) $t_clean["clean_authors"] = $clean_authors;
		if($clean_categories) $t_clean["clean_categories"] = $clean_categories;
		if($clean_series) $t_clean["clean_series"] = $clean_series;
		if($clean_titres_uniformes) $t_clean["clean_titres_uniformes"] = $clean_titres_uniformes;
		if($clean_indexint) $t_clean["clean_indexint"] = $clean_indexint;
		if($clean_relations) $t_clean["clean_relations"] = $clean_relations;
		if($index_acquisitions) $t_clean["index_acquisitions"] = $index_acquisitions;
		if($gen_signature_notice) $t_clean["gen_signature_notice"] = $gen_signature_notice;
		if($gen_phonetique) $t_clean["gen_phonetique"] = $gen_phonetique;
		if($nettoyage_clean_tags) $t_clean["nettoyage_clean_tags"] = $nettoyage_clean_tags;
		if($clean_categories_path) $t_clean["clean_categories_path"] = $clean_categories_path;
		if($gen_date_publication_article) $t_clean["gen_date_publication_article"] = $gen_date_publication_article;
		if($gen_date_tri) $t_clean["gen_date_tri"] = $gen_date_tri;
		if($index_notices) $t_clean["index_notices"] = $index_notices;
		if ($index_global) $t_clean["index_global"] = $index_global;
		if($reindex_docnum) $t_clean["reindex_docnum"] = $reindex_docnum;
		if($clean_opac_search_cache) $t_clean["clean_opac_search_cache"] = $clean_opac_search_cache;
		if($clean_cache_amende) $t_clean["clean_cache_amende"] = $clean_cache_amende;
		if($clean_cache_temporary_files) $t_clean["clean_cache_temporary_files"] = $clean_cache_temporary_files;
		if($clean_cache_apcu) $t_clean["clean_cache_apcu"] = $clean_cache_apcu;
		if($index_rdfstore) $t_clean["index_rdfstore"] = $index_rdfstore;
		if($index_synchrordfstore) $t_clean["index_synchrordfstore"] = $index_synchrordfstore;
		if($index_faq) $t_clean["index_faq"] = $index_faq;
		if($index_cms) $t_clean["index_cms"] = $index_cms;
		if($index_concept) $t_clean["index_concept"] = $index_concept;
		if($hash_empr_password) $t_clean["hash_empr_password"] = $hash_empr_password;
		if($index_authorities) $t_clean["index_authorities"] = $index_authorities;
		if($gen_signature_docnum) $t_clean["gen_signature_docnum"] = $gen_signature_docnum;
		if($clean_records_thumbnail) $t_clean["clean_records_thumbnail"] = $clean_records_thumbnail;
		if($index_date_flot) $t_clean["index_date_flot"] = $index_date_flot;
		if($clean_entities_data) $t_clean["clean_entities_data"] = $clean_entities_data;
		if($clean_docnum_thumbnail) $t_clean["clean_docnum_thumbnail"] = $clean_docnum_thumbnail;
		if($gen_ark) $t_clean["gen_ark"] = $gen_ark;
		if($gen_docnum_thumbnail) $t_clean["gen_docnum_thumbnail"] = $gen_docnum_thumbnail;
		if($index_sphinx_records) $t_clean["index_sphinx_records"] = $index_sphinx_records;
		if($index_sphinx_authorities) $t_clean["index_sphinx_authorities"] = $index_sphinx_authorities;
		if($index_sphinx_concepts) $t_clean["index_sphinx_concepts"] = $index_sphinx_concepts;
		$t["clean"] = $t_clean;

    	return serialize($t);
	}
}


