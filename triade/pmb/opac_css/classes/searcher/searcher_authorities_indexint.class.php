<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_indexint.class.php,v 1.2 2021/04/06 12:46:05 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_indexint extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_INDEXINT;
		parent::__construct($user_query);
		$this->object_table = "indexint";
		$this->object_table_key = "indexint_id";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_indexint";
	}
	
	protected function _get_authorities_filters(){
		global $thesaurus_classement_mode_pmb, $thesaurus_classement_defaut, $id_pclass;
		
		$filters = parent::_get_authorities_filters();
		if ($thesaurus_classement_mode_pmb) {
			if ($id_pclass){
				$filters[] = 'num_pclass = "'.$id_pclass.'"';
			}else{
				// dans tous les classements
				return $filters;
			}
		} else {
			$filters[] = 'num_pclass = "'.$thesaurus_classement_defaut.'"';
		}
		return $filters;
	}
	
	protected function _get_sign_elements($sorted=false) {
		global $thesaurus_classement_mode_pmb, $thesaurus_classement_defaut, $id_pclass;
		$str_to_hash = parent::_get_sign_elements($sorted);
		if ($thesaurus_classement_mode_pmb) {
			$str_to_hash .= "&num_pclass=".$id_pclass;
		} else {
			$str_to_hash .= "&num_pclass=".$thesaurus_classement_defaut;
		}
		return $str_to_hash;
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