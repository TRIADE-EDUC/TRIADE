<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_indexint.class.php,v 1.5 2018/08/17 10:33:02 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_indexint extends searcher_sphinx_authorities {
	protected $index_name = 'indexint';
	
	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/indexint/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'indexint';
		$this->authority_type = AUT_TABLE_INDEXINT;
		$this->object_table = "indexint";
		$this->object_table_key = "indexint_id";
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $thesaurus_classement_mode_pmb, $thesaurus_classement_defaut, $id_pclass;
		if ($thesaurus_classement_mode_pmb) {
			if ($id_pclass){
				//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
				$filters[] = array(
						'name'=> 'num_pclass',
						'values' => $id_pclass
				);
			}
		} else {
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			$filters[] = array(
					'name'=> 'num_pclass',
					'values' => $thesaurus_classement_defaut
			);
		}
		return $filters;
	}
	
	public function get_authority_tri() {
		return 'indexint_name';
	}
	
	protected function _get_human_queries() {
		global $msg;
		global $thesaurus_classement_mode_pmb, $thesaurus_classement_defaut, $id_pclass;;
		
		$human_queries = parent::_get_human_queries();
		if ($thesaurus_classement_mode_pmb) {
			if ($id_pclass){
				$human_queries[] = array(
						'name' => $msg['search_extended_indexint_pclassement'],
						'value' => pmb_mysql_result(pmb_mysql_query('select name_pclass from pclassement where id_pclass = '.$id_pclass), 0, 0)
				);
			}else{
				// dans tous les classements
				return $human_queries;
			}
		} else {
			$human_queries[] = array(
					'name' => $msg['search_extended_indexint_pclassement'],
					'value' => pmb_mysql_result(pmb_mysql_query('select name_pclass from pclassement where id_pclass = '.$thesaurus_classement_defaut), 0, 0)
			);
		}
		
		return $human_queries;
	}
}