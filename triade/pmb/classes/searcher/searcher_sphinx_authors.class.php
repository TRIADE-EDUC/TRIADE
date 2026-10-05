<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authors.class.php,v 1.7 2018/08/17 10:33:02 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_authors extends searcher_sphinx_authorities {
	protected $index_name = 'authors';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/authors/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'authors';
		$this->authority_type = AUT_TABLE_AUTHORS;
		$this->object_table = "authors";
		$this->object_table_key = "author_id";
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $type_autorite;
		// Ca ne devrait pas, mais pour le bon fonctionnement, on a besoin d'exclure cette valeur
		if($type_autorite && $type_autorite != 7){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			$filters[] = array(
					'name'=> 'author_type',
					'values' => $type_autorite
			);
		}
		return $filters;
	}
	
	public function get_authority_tri() {
		return 'index_author';
	}
	
	protected function _get_human_queries() {
		global $msg;
		global $type_autorite;
		
		$human_queries = parent::_get_human_queries();
		if ($type_autorite && ($type_autorite != '7')) {
			switch ($type_autorite) {
				case '70' :
					$type_autorite_label = $msg['203'];
					break;
				case '71' :
					$type_autorite_label = $msg['204'];
					break;
				case '72' :
					$type_autorite_label = $msg['congres_libelle'];
					break;
			}
			$human_queries[] = array(
					'name' => $msg['search_extended_author_type'],
					'value' => $type_autorite_label
			);
		}
		
		return $human_queries;
	}
}