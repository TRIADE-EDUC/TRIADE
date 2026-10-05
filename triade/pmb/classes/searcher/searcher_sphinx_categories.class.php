<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_categories.class.php,v 1.8 2024/02/28 08:30:27 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_categories extends searcher_sphinx_authorities {
	protected $index_name = 'categories';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/categories/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'categories';
		$this->authority_type = AUT_TABLE_CATEG;
		$this->object_table = "noeuds";
		$this->object_table_key = "id_noeud";
	}

	protected function get_filters(){
		$filters = parent::get_filters();
		global $id_thes;
		if($id_thes && ($id_thes != -1)){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			$filters[] = array(
					'name'=> 'num_thesaurus',
					'values' => intval($id_thes)
			);
		}
		return $filters;
	}

	protected function get_search_indexes(){
		global $lang, $lg_search;
		global $sphinx_indexes_prefix;
		if ($lg_search) {
			$indexes = '';
			foreach ($this->get_available_languages() as $language) {
				if ($indexes) {
					$indexes.= ',';
				}
				$indexes.= $sphinx_indexes_prefix.$this->index_name.($language ? '_'.$language : '');
			}
			return $indexes;
		}
		return $sphinx_indexes_prefix.$this->index_name.'_'.$lang.','.$sphinx_indexes_prefix.$this->index_name;
	}

	protected function _get_human_queries() {
		global $msg;
		global $id_thes;

		$human_queries = parent::_get_human_queries();
		if ($id_thes && ($id_thes != '-1')) {
			$thes_label = pmb_mysql_result(pmb_mysql_query('select libelle_thesaurus from thesaurus where id_thesaurus = '.$id_thes), 0, 0);
			$human_queries[] = array(
					'name' => $msg['search_extended_category_thesaurus'],
					'value' => $thes_label
			);
		}

		return $human_queries;
	}
}