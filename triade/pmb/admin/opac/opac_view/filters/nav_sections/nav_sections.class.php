<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nav_sections.class.php,v 1.7.10.1 2025/03/14 13:27:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/opac/opac_view/filters/opac_view_filters.class.php");

class nav_sections extends opac_view_filters {
	
	protected function _init_path() {
		$this->path="nav_sections";
	}
    
    public function fetch_data() {
		parent::fetch_data();	
		$myQuery = pmb_mysql_query("SELECT * FROM docs_location order by location_libelle ");
		$this->liste_item=array();
		$i=0;
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->liste_item[$i]=new stdClass();
				$this->liste_item[$i]->id=$r->idlocation;
				$this->liste_item[$i]->name=$r->location_libelle ;
				if(in_array($r->idlocation,$this->selected_list)) {
				    $this->liste_item[$i]->selected=1;
				} else {
				    $this->liste_item[$i]->selected=0;				
				}
				$i++;			
			}	
		}
		return true;
 	}
 	
 	public function get_available_columns() {
 	    global $msg;
 	    return array(
 	        'selection' => $this->msg["selection_opac"],
 	        'name' => $msg['21'],
 	    );
 	}
}