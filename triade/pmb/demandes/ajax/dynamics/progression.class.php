<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: progression.class.php,v 1.6.8.1 2025/05/06 15:04:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class progression extends demande_dynamic_field {
	
    protected function get_query_display() {
        return "select progression_action as prog from demandes_actions where id_action='".$this->idobjet."'";
    }
    
    protected function get_value($row) {
        return $row->prog;
    }
	
	public function update(){
		global $progression;		
		
		$req = "update demandes_actions set progression_action='".$progression."' where id_action='".$this->idobjet."'";
		pmb_mysql_query($req);
		switch($this->champ_sortie){
			default:
				$this->display = "<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$progression."%\" title='".$progression."%' />";
			break;
		}
				
	}
}