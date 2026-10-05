<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etageres.class.php,v 1.7.10.1 2025/03/14 13:27:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/admin/opac/opac_view/filters/opac_view_filters.class.php");
require_once($class_path."/etagere.class.php");

class etageres extends opac_view_filters { 
	
	protected function _init_path() {
		$this->path="etageres";
	}
    
    public function fetch_data() {
		parent::fetch_data();	
		$this->liste_item=array();
		$liste_item=etagere::get_etagere_list();
		$i=0;
		foreach($liste_item as $valeur){
			$this->liste_item[$i]=new stdClass();
			$this->liste_item[$i]->id=$valeur['idetagere'];
			$this->liste_item[$i]->name=$valeur['name'];
			$this->liste_item[$i]->comment=$valeur['comment'];
			if(in_array($valeur['idetagere'],$this->selected_list))	{
			    $this->liste_item[$i]->selected=1;
			} else {
			    $this->liste_item[$i]->selected=0;	
			}
			$i++;						
		}
    }
    
    public function get_available_columns() {
        global $msg;
        return array( 
            'selection' => $this->msg["selection_opac"],
            'name' => $msg["etagere_name"],
            'comment' => ''
        );
    }
}