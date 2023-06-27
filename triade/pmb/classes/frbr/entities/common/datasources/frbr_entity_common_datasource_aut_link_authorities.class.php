<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_authorities.class.php,v 1.5 2019-04-19 12:23:43 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_aut_link_authorities extends frbr_entity_common_datasource {
	
	protected $link_type;
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	protected function get_sub_query($datas=array()) {
		
		$sub_query = "
			SELECT aut_link_from_num as id, aut_link_to_num as parent FROM aut_link 
			WHERE aut_link_from = ".$this->authority_type;
		if(!empty($this->link_type)) {
			$sub_query .= " AND aut_link_type = '".$this->link_type."'";
		}
		$sub_query .= " AND aut_link_to_num IN (".implode(',', $datas).")";
		return $sub_query;
	}
	
	protected function get_sub_reverse_query($datas=array()) {
		$sub_query = "
			SELECT aut_link_to_num as id, aut_link_from_num as parent FROM aut_link
			WHERE aut_link_to = ".$this->authority_type;
		if(!empty($this->link_type)) {
			$sub_query .= " AND aut_link_type = '".$this->link_type."'";
		}
		$sub_query .= " AND aut_link_from_num IN (".implode(',', $datas).")";
		return $sub_query;
	}	
	
// 	public function get_query() {
// 		$query = "select distinct ".$this->table_name.".".$this->key_name." FROM ".$this->table_name."
// 			WHERE (".$this->key_name." IN (".$this->get_sub_query().")
// 			OR ".$this->key_name." IN (".$this->get_sub_reverse_query().")
// 			";
// 		return $query;
// 	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
// 		$query = "select distinct ".$this->table_name.".".$this->key_name." as id, ".$this->key_name." as parent FROM ".$this->table_name."
// 			WHERE ".$this->key_name." IN (".$this->get_sub_query($datas).")
// 			OR ".$this->key_name." IN (".$this->get_sub_reverse_query($datas).")
// 			";

		$query = $this->get_sub_query($datas) . " UNION " . $this->get_sub_reverse_query($datas);
		$result = pmb_mysql_query($query);
		$datas = array();
		while ($row = pmb_mysql_fetch_object($result)) {
			$datas[$row->parent][] = $row->id;
			$datas[0][] = $row->id;
		}
		$datas[0]= parent::get_datas($datas[0]);
		if ($this->used_external_filter){
			foreach($datas as $parent => $data) {
				if ($parent) {
					$datas[$parent] = $this->external_filter->filter_datas($data);
				}
			}
		}
		return $datas;
	}
	
	public function get_link_type() {
		return $this->link_type;
	}
	
	public function set_link_type($link_type) {
		$this->link_type = $link_type;
		return $this;
	}

}