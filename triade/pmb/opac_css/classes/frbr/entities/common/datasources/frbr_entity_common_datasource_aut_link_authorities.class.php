<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_authorities.class.php,v 1.13 2021/03/01 11:04:05 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link_authorities extends frbr_entity_common_datasource {
	
	protected $link_type;
	
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	protected function get_query($datas = array()) {
		$sub_query = "SELECT DISTINCT aut_link_to_num as id, aut_link_from_num as parent, aut_link_type as group_key
		              FROM aut_link WHERE aut_link_to = ".$this->authority_type;
		$this->get_link_type();
		if (!empty($this->link_type) && $this->link_type[0] != "0") {
		    // Si le link_type = 0, alors il ne faut spécifier aucun link_type
		    if (!is_array($this->link_type)) {
		        $this->link_type = array($this->link_type);
		    }
		    foreach ($this->link_type as $key => $link_type) {
		        if (empty($key)) {
		            $sub_query .= " AND (aut_link_type = '$link_type'";
		        } else {
		            $sub_query .= " OR aut_link_type = '$link_type'";
		        }
		    }
		    $sub_query .= ")";
		}
		if($this->get_parent_type() !== 'authperso'){
            $aut_link_from = authority::get_const_type_object($this->get_parent_type());
        } else {
		    $query = "SELECT authperso_authority_authperso_num FROM authperso_authorities WHERE id_authperso_authority = ".$datas[0];
		    $result = pmb_mysql_query($query);
		    $aut_link_from = pmb_mysql_result($result,0,0);
		    $aut_link_from = 1000 + intval($aut_link_from);
        }
		$sub_query .= " AND aut_link_from = ".$aut_link_from;
		$sub_query .= " AND aut_link_from_num IN (".implode(',', $datas).")";
		return $sub_query;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas = array()) {
		$query = $this->get_query($datas);
		$datas = $this->get_datas_from_query($query);
		if (isset($datas[0])) {
		    $datas[0] = parent::get_datas($datas[0]);
		}
		if (!empty($this->used_external_filter)) {
			foreach($datas as $parent => $data) {
				if (!empty($parent)) {
					$datas[$parent] = $this->external_filter->filter_datas($data);
				}
			}
		}
		return $datas;
	}
	
	public function get_link_type() {
	    if (isset($this->link_type)) {
    		return $this->link_type;
	    }
	    if (!empty($this->parameters->link_type)) {
	        $this->link_type = $this->parameters->link_type;
	    }
	    return $this->link_type;
	}
	
	public function set_link_type($link_type) {
		$this->link_type = $link_type;
		return $this;
	}
	
	protected function get_label_from_group($group) {
	    $autlink= marc_list_collection::get_instance('aut_link');
	    foreach($autlink->table as $key => $types) {
	        if (isset($types[$group])) {
	            return $types[$group];
	        }
	    }
	    return "";
	}

}