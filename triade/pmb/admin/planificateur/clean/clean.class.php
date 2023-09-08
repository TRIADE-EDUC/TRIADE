<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean.class.php,v 1.16 2019-04-29 11:04:20 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/netbase/netbase.class.php");
require_once($class_path."/netbase/netbase_cache.class.php");

class clean extends scheduler_task {
	
	protected function execution_element($title, $method_name) {
		$this->add_section_report($title);
		if (method_exists($this->proxy, 'pmbesClean_'.$method_name)) {
			$ws_method_name = "pmbesClean_".$method_name;
			$this->add_content_report($this->proxy->{$ws_method_name}());
			return true;
		} else {
			$this->add_function_rights_report($method_name,"pmbesClean");
			return false;
		}
	}
	
	public function execution() {
		global $dbh, $msg, $charset, $PMBusername;
		global $acquisition_active,$pmb_indexation_docnum;
		global $base_path;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			//progression
			$p_value = (int) 100/count($parameters["clean"]);
			$this->add_section_report($this->msg["planificateur_clean"]);
			foreach ($parameters["clean"] as $clean) {
				$response = false;
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == WAITING) {
					$this->send_command(RUNNING);
				}
				if ($this->statut == RUNNING) {
					switch ($clean) {
						case INDEX_GLOBAL:
							$response = $this->execution_element($msg["nettoyage_index_global"], 'indexGlobal');
							break;
						case INDEX_NOTICES:
							$response = $this->execution_element($msg["nettoyage_index_notices"], 'indexNotices');
							break;
						case CLEAN_AUTHORS:
							$response = $this->execution_element($msg["nettoyage_clean_authors"], 'cleanAuthors');
							break;
						case CLEAN_PUBLISHERS:
							$response = $this->execution_element($msg["nettoyage_clean_editeurs"], 'cleanPublishers');
							break;
						case CLEAN_COLLECTIONS:
							$response = $this->execution_element($msg["nettoyage_clean_collections"], 'cleanCollections');
							break;
						case CLEAN_SUBCOLLECTIONS:
							$response = $this->execution_element($msg["nettoyage_clean_subcollections"], 'cleanSubcollections');
							break;
						case CLEAN_CATEGORIES:
							$response = $this->execution_element($msg["nettoyage_clean_categories"], 'cleanCategories');
							break;
						case CLEAN_SERIES:
							$response = $this->execution_element($msg["nettoyage_clean_series"], 'cleanSeries');
							break;
						case CLEAN_TITRES_UNIFORMES:
							$response = $this->execution_element($msg["nettoyage_clean_titres_uniformes"], 'cleanTitresUniformes');
							break;
						case CLEAN_INDEXINT:
							$response = $this->execution_element($msg["nettoyage_clean_indexint"], 'cleanIndexint');
							break;
						case CLEAN_RELATIONS:
							$response = $this->execution_element($msg["nettoyage_clean_relations"], 'cleanRelations');
							break;
						case CLEAN_NOTICES:
							$response = $this->execution_element($msg["nettoyage_clean_expl"], 'cleanNotices');
							break;
						case INDEX_ACQUISITIONS:
							if ($acquisition_active) {
								$response = $this->execution_element($msg["nettoyage_reindex_acq"], 'indexAcquisitions');
							} else {
								$this->add_section_report($msg["nettoyage_reindex_acq"]);
								$this->add_content_report($this->msg["clean_acquisition"]);
							}
							break;
						case GEN_SIGNATURE_NOTICE:
							$response = $this->execution_element($msg["gen_signature_notice"], 'genSignatureNotice');
							break;
						case GEN_PHONETIQUE:
							$response = $this->execution_element($msg["gen_phonetique"], 'genPhonetique');
							break;
						case NETTOYAGE_CLEAN_TAGS:
							$response = $this->execution_element($msg["nettoyage_clean_tags"], 'nettoyageCleanTags');
							break;
						case CLEAN_CATEGORIES_PATH:
							$response = $this->execution_element($msg["clean_categories_path"], 'cleanCategoriesPath');
							break;
						case GEN_DATE_PUBLICATION_ARTICLE:
							$response = $this->execution_element($msg["gen_date_publication_article"], 'genDatePublicationArticle');
							break;
						case GEN_DATE_TRI:
							$response = $this->execution_element($msg["gen_date_tri"], 'genDateTri');
							break;
						case INDEX_DOCNUM:
							if ($pmb_indexation_docnum) {
								$response = $this->execution_element($msg["docnum_reindexer"], 'indexDocnum');
							} else {
								$this->add_section_report($msg["docnum_reindexer"]);
								$this->add_content_report($this->msg["clean_indexation_docnum"]);
							}
							break;
						case CLEAN_OPAC_SEARCH_CACHE:
							$this->add_section_report($msg["cleaning_opac_search_cache"]);
							$query = "truncate table search_cache";
							if(pmb_mysql_query($query,$dbh)){
								$query = "optimize table search_cache";
								pmb_mysql_query($query);
								$this->add_content_report('OK');
							}else{
								$this->add_content_report('KO');
							}
							break;
						case CLEAN_CACHE_AMENDE:
							$this->add_section_report($msg["cleaning_cache_amende"]);
							$query = "truncate table cache_amendes";
							if(pmb_mysql_query($query,$dbh)){
								$query = "optimize table cache_amendes";
								pmb_mysql_query($query);
								$this->add_content_report('OK');
							}else{
								$this->add_content_report('KO');
							}
							break;
						case CLEAN_CACHE_TEMPORARY_FILES:
							$this->add_section_report($msg["cleaning_cache_temporary_files"]);
							$cleaned = netbase_cache::clean_files($base_path."/temp");
							if($cleaned) {
								//Correctement réalisé en gestion, on nettoye à l'OPAC
								$cleaned = netbase_cache::clean_files($base_path."/opac_css/temp");
							}
							if($cleaned) {
								$this->add_content_report('OK');
							} else {
								$this->add_content_report('KO');
							}
							break;
						case INDEX_RDFSTORE:
							$response = $this->execution_element($msg["nettoyage_rdfstore_reindexation"], 'cleanRdfStore');
							break;
						case INDEX_SYNCHRORDFSTORE:
							$response = $this->execution_element($msg["nettoyage_synchrordfstore_reindexation"], 'cleanSynchroRdfStore');
							break;
						case INDEX_FAQ:
							$response = $this->execution_element($msg["nettoyage_reindex_faq"], 'cleanFAQ');
							break;
						case INDEX_CMS:
							$response = $this->execution_element($msg["nettoyage_reindex_cms"], 'cleanCMS');
							break;
						case INDEX_CONCEPT:
							$response = $this->execution_element($msg["nettoyage_reindex_concept"], 'cleanConcept');
							break;
						case HASH_EMPR_PASSWORD:
							$response = $this->execution_element($msg["hash_empr_password"], 'hashEmprPassword');
							break;
						case INDEX_AUTHORITIES:
							$response = $this->execution_element($msg["nettoyage_index_authorities"], 'indexAuthorities');
							break;
					}
// 					if($response) {
						$percent += $p_value;
						$this->update_progression($percent);
// 					}
				}
			}
		} else {
			$this->add_rights_bad_user_report();
		}
		
	}
	
	protected function add_section_report($content='', $css_class='scheduler_report_section') {
		global $charset;
		$this->report[] = "<tr><th class='".$css_class."'>".htmlentities($content, ENT_QUOTES, $charset)."</th></tr>";
	}
}


