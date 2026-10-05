<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authpersos.class.php,v 1.13 2023/05/05 12:34:46 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_authpersos extends searcher_autorities {

    protected $id_authperso;

	public function __construct($user_query, $id_authperso = 0) {
		$this->authority_type = AUT_TABLE_AUTHPERSO;
		parent::__construct($user_query);
		$this->object_table = "authperso_authorities";
		$this->object_table_key = "id_authperso_authority";
		$this->id_authperso = $id_authperso;
	}
	
	public function _get_search_type() {
		return parent::_get_search_type()."_authpersos";
	}
	
	protected function _get_authorities_filters() {
		global $id_authperso;
		
		$filters = parent::_get_authorities_filters();
		$id_authperso = (int) $id_authperso;
		if (empty($id_authperso)) {
		    $id_authperso = $this->id_authperso;
		}
		if (!empty($id_authperso)) {
			$filters[] = $this->object_table . ".authperso_authority_authperso_num = $id_authperso";
		}
		return $filters;
	}
	
	public function get_authority_tri() {
		return 'authperso_index_infos_global';
	}
	
	public function add_fields_restrict($fields_restrict = array()) {
	    if (!empty($this->var_table)) {
	        $nb_fields_restrict = count($fields_restrict);
	        for ($i = 0; $i < $nb_fields_restrict; $i++) {
	            $nb_values = count($fields_restrict[$i]["values"]);
	            for ($j = 0; $j < $nb_values; $j++) {
	                if (!empty($fields_restrict[$i]["values"][$j])) {
	                    $fields_restrict[$i]["values"][$j] = str_replace("!!id_authperso!!", $this->var_table['authperso_num'], $fields_restrict[$i]["values"][$j]);
    	            }
	            }
	        }
	    }
	    parent::add_fields_restrict($fields_restrict);
	}

		protected function get_full_results_query() {
		global $mode;
		
		$id_authperso = $this->id_authperso;
		if (empty($id_authperso) && !empty($mode)) {
		    $id_authperso = $mode - 1000;
		}
		
		$query = "SELECT id_authority FROM authorities JOIN $this->object_table ON authorities.num_object = $this->object_table_key";
		if (!empty($id_authperso)) {
			$query .= " AND authperso_authority_authperso_num = $id_authperso";
		}
		return $query;
	}
}