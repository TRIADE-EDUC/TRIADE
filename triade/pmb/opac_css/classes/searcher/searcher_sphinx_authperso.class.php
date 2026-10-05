<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authperso.class.php,v 1.4 2019/05/27 12:55:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_authperso extends searcher_sphinx_authorities {
	
    protected $index_name = 'authperso';
    
    protected $id_authperso;

    public function __construct($user_query,  $id_authperso = 0){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/authperso/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'authperso';
		$this->authority_type = AUT_TABLE_AUTHPERSO;
		$this->id_authperso = $id_authperso;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		return $filters;
	}
	
	protected function get_search_indexes(){
		global $lang, $id_authperso;
		global $sphinx_indexes_prefix;
		
		if ($this->id_authperso){
		    return $sphinx_indexes_prefix.$this->index_name.'_'.$this->id_authperso.'_'.$lang.','.$sphinx_indexes_prefix.$this->index_name.'_'.$this->id_authperso;
		} elseif ($id_authperso) {
		    return $sphinx_indexes_prefix.$this->index_name.'_'.$id_authperso.'_'.$lang.','.$sphinx_indexes_prefix.$this->index_name.'_'.$id_authperso;
		}
		// On cherche dans toutes les autorités persos
		$indexes = '';
		$result = pmb_mysql_query('select id_authperso from authperso');
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($indexes) {
					$indexes.= ',';
				}
				$indexes.= $this->index_name.'_'.$row->id_authperso.'_'.$lang.','.$this->index_name.'_'.$row->id_authperso;
			}
		}
		return $indexes;
	}
	
	protected function get_full_raw_query(){
	    global $id_authperso;
	    global $authperso_id;
	    $current_authperso_id = ($id_authperso ? $id_authperso : $authperso_id);
	    
	    
	    if ($this->id_authperso){
	        return 'select id_authority as id, 100 as weight from authorities join authperso_authorities on num_object = id_authperso_authority where type_object = '.$this->authority_type.' and authperso_authority_authperso_num = '.$this->id_authperso;
	    } elseif ($current_authperso_id) {
	        return 'select id_authority as id, 100 as weight from authorities join authperso_authorities on num_object = id_authperso_authority where type_object = '.$this->authority_type.' and authperso_authority_authperso_num = '.$current_authperso_id;
		}
		return 'select id_authority as id, 100 as weight from authorities where type_object = '.$this->authority_type;
	}
}