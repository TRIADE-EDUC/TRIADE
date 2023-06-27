<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_concepts.class.php,v 1.4 2019-01-14 15:34:20 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_concepts extends searcher_sphinx_authorities {
	protected $index_name = 'concepts';

	public function __construct($user_query){
		global $include_path;
		parent::__construct($user_query);
		$this->index_name = 'concepts';
		$this->authority_type = AUT_TABLE_CONCEPT;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $concept_scheme;
		if(isset($concept_scheme) && (is_string($concept_scheme) && $concept_scheme != -1) || (is_array($concept_scheme) && $concept_scheme[0] != -1) ){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			$filters[] = array(
					'name'=> 'scheme',
					'values' => $concept_scheme
			);
		}
		return $filters;
	}
}