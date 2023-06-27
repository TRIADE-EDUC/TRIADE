<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_categories.class.php,v 1.1 2017-01-06 16:10:51 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_categories extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_CATEG;
		parent::__construct($user_query);
		$this->object_table = "noeuds";
		$this->object_table_key = "id_noeud";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_categories";
	}
	
	protected function _get_authorities_filters(){
		global $id_thes;
		
		$filters = parent::_get_authorities_filters();
		if ($id_thes && ($id_thes != '-1')) {
			$filters[] = $this->object_table.'.num_thesaurus = "'.$id_thes.'"';
		}
		return $filters;
	}
	
	protected function _get_sign_elements($sorted=false) {
		global $id_thes;
		$str_to_hash = parent::_get_sign_elements($sorted);
		$str_to_hash .= "&id_thes=".$id_thes;
		return $str_to_hash;
	}
	
	// à réécrire au besoin...
	protected function _sort($start,$number){
		global $dbh;
		global $last_param, $tri_param, $limit_param;
		global $lang;
		
		if($this->table_tempo != ""){
			$query = "select * from ".$this->table_tempo." order by pert desc limit ".$start.",".$number;
			$res = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($res)){
				$this->result=array();
				while($row = pmb_mysql_fetch_object($res)){
					$this->result[] = $row->{$this->object_key};
				}
			}
		} else {
			$query = $this->get_full_results_query();
			// On va chercher le thesaurus
			$query .= ' join thesaurus on '.$this->object_table.'.num_thesaurus = thesaurus.id_thesaurus';
			// On va chercher le libellé dans la langue par défaut du thesaurus
			$query .= ' left join categories as defcateg on '.$this->object_table.'.'.$this->object_key.' = defcateg.num_noeud and thesaurus.langue_defaut = defcateg.langue';
			// On va chercher le libellé dans la langue de l'interface
			$query .= ' left join categories as lgcateg on '.$this->object_table.'.'.$this->object_key.' = lgcateg.num_noeud and lgcateg.langue = "'.$lang.'"';
			// On va chercher les filtres
			$query .= ' where '.implode(' and ', $this->_get_authorities_filters());
			// On trie
			$query .= ' order by lgcateg.index_categorie, defcateg.index_categorie';
			// On limite
			$query .= ' limit '.$start.', '.$number;
			$res = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($res)){
				$this->result=array();
				while($row = pmb_mysql_fetch_object($res)){
					$this->result[] = $row->id_authority;
				}
			}
		}
	}
}