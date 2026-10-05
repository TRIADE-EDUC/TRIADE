<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_concepts_search.class.php,v 1.1 2018/04/17 12:48:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/search/level1/level1_authorities_search.class.php");

class level1_concepts_search extends level1_authorities_search {

	protected $searcher;
	
	protected function get_searcher() {
		if(!isset($this->searcher)) {
			$this->searcher = new opac_searcher_autorities_skos_concepts($this->user_query);
		}
		return $this->searcher;
	}
	
	public function get_nb_results() {
		if(!isset($this->nb_results)) {
			$searcher = $this->get_searcher();
			$searcher->get_result();
			$this->nb_results = $searcher->get_nb_results();
			if($this->nb_results) {
				$this->add_in_session();
			}
		}
		return $this->nb_results;
	}
	
	protected function get_affiliate_label() {
		global $msg;
		
		return $msg['skos_view_concepts_concepts'];
	}
}
?>