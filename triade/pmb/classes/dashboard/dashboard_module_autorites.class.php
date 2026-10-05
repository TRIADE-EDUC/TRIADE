<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard_module_autorites.class.php,v 1.5 2023/07/25 12:22:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/dashboard/dashboard_module.class.php");

class dashboard_module_autorites extends dashboard_module {

	
	public function __construct(){
		global $msg;
		$this->template = "template";
		$this->module = "autorites";
		$this->module_name = $msg[132];
		parent::__construct();
	}
	
	public function get_informations_from_authority_query($query){
		$return = array();
		if($query) {
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$return = pmb_mysql_fetch_assoc($result);
			}
		}
		return $return;
	}
	
	public function get_categories_informations(){
		$query = "select count(id_noeud) as nb from noeuds";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_authors_informations(){
		$query = "select count(author_id) as nb from authors";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_publishers_informations(){
		$query = "select count(ed_id) as nb from publishers";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_collections_informations(){
		$query = "select count(collection_id) as nb from collections";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_sub_collections_informations(){
		$query = "select count(sub_coll_id) as nb from sub_collections";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_series_informations(){
		$query = "select count(serie_id) as nb from series";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_titres_uniformes_informations(){
		$query = "select count(tu_id) as nb from titres_uniformes";
		return $this->get_informations_from_authority_query($query);
	}
	
	public function get_indexint_informations(){
		$query = "select count(indexint_id) as nb from indexint";
		return $this->get_informations_from_authority_query($query);
	}
}