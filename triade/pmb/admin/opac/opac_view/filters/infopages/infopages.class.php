<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: infopages.class.php,v 1.8.10.1 2025/03/14 13:27:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/opac/opac_view/filters/opac_view_filters.class.php");

class infopages extends opac_view_filters {  
	
	protected function _init_path() {
		$this->path="infopages";
	}
    
    public function fetch_data() {
		parent::fetch_data();
		$myQuery = pmb_mysql_query("SELECT * FROM infopages order by title_infopage ");
		$this->liste_item=array();
		$i=0;
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->liste_item[$i]= new stdClass();
				$this->liste_item[$i]->id=$r->id_infopage;
				$this->liste_item[$i]->name=$r->title_infopage ;
				if(in_array($r->id_infopage,$this->selected_list)) {
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
 	        'name' => $msg['infopage_title_infopage'],
 	    );
 	}
}