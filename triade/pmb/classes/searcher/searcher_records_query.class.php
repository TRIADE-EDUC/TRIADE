<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_query.class.php,v 1.1 2019-04-23 09:19:56 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/searcher/searcher_generic.class.php");

class searcher_records_query extends searcher_records {
    protected $query="";	
	
	public function __construct($user_query){
		parent::__construct($user_query);
	}
	
	public function set_query($query){
	    $this->query = $query;
	}

	protected function _get_search_type(){
		return "records_query";
	}
	public function get_raw_query()
	{
	    return $this->_get_search_query();
	}
	protected function _get_search_query(){
		$query = $this->query;
		if($this->user_query !== "*"){
		    $query = str_replace('!!p!!',pmb_mysql_escape_string($this->user_query),$this->query);
			$query.= self::_get_typdoc_filter(true);
			return $query;
		}
        return "select 0 as id_notice";	
	}
	
	protected function _get_pert($with_explnum=false, $query=false){
	    $this->table_tempo = $this->get_temporary_table_name("_pert");
	    pmb_mysql_query("create temporary table $this->table_tempo (notice_id int(11) not null primary key, pert int(11) not null default 0)");
	    if($this->objects_ids != ""){
	       pmb_mysql_query("insert into $this->table_tempo (notice_id) values (".implode("),(",explode(",",$this->objects_ids)).")");
	    }
	    if($query){
	        return "select * from $this->table_tempo";
	    }
	    return $this->table_tempo;
	}
	
	protected function _get_sign($sorted=false){
		$sign = parent::_get_sign($sorted);
		$sign.= md5('&query='.$this->query);
		return $sign;
	}
}