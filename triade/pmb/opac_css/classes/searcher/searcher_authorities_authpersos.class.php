<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authpersos.class.php,v 1.6 2021/01/14 13:58:49 jlaurent Exp $

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
	
	public function add_fields_restrict($fields_restrict = array()) {
	    if (!empty($this->var_table)) {
	        for ($i = 0; $i < count($fields_restrict) ; $i++) {
	            for ($j = 0; $j < count($fields_restrict[$i]["values"]); $j++) {
	                if ($fields_restrict[$i]["values"][$j]) {
	                    $fields_restrict[$i]["values"][$j] = str_replace("!!id_authperso!!", $this->var_table['authperso_num'], $fields_restrict[$i]["values"][$j]);
	                }
	            }
	        }
	    }
	    parent::add_fields_restrict($fields_restrict);
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
	
	public function init_fields_restrict($mode) {
	    $mode = intval($mode);
	    if ($mode > 1000){
	        $mode -= 1000;
	        $this->id_authperso = $mode;
	    }
	    return true;
	}
	
}