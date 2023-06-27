<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authpersos.class.php,v 1.3 2018-10-08 13:59:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_authpersos extends searcher_autorities {
    
    protected $id_authperso;
    
	public function __construct($user_query, $id_authperso = 0){
		$this->authority_type = AUT_TABLE_AUTHPERSO;
		parent::__construct($user_query);
		$this->object_table = "authperso_authorities";
		$this->object_table_key = "id_authperso_authority";
		$this->id_authperso = $id_authperso;
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_authpersos_".$this->id_authperso;
	}
	
	protected function _get_authorities_filters(){
		global $id_authperso;
		$filters = parent::_get_authorities_filters();
		if($this->id_authperso){
		    $filters[] = $this->object_table.'.authperso_authority_authperso_num='.($this->id_authperso);
		} elseif ($id_authperso*1) {
		    $filters[] = $this->object_table.'.authperso_authority_authperso_num='.($id_authperso*1);
		}
		return $filters;
	}
	
	public function get_authority_tri() {
		return ' authperso_index_infos_global';
	}
	
	protected function get_full_results_query(){
	    global $id_authperso;
	    
	    $query = 'select id_authority from authorities join '.$this->object_table.' on authorities.num_object = '.$this->object_table_key;
	    if($this->id_authperso) {
	        $query .= ' and authperso_authority_authperso_num ='.($this->id_authperso);
	    } elseif ($id_authperso) {
	        $query .= ' and authperso_authority_authperso_num ='.($id_authperso);
	    }
	    return $query;
	}
	
}