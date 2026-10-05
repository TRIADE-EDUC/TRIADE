<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: temps.class.php,v 1.5.8.1 2025/05/06 15:04:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class temps extends demande_dynamic_field {
	
    protected function get_query_display() {
        return "select temps_passe from demandes_actions where id_action='".$this->idobjet."'";
    }
    
    protected function get_value($row) {
        return $row->temps_passe;
    }
    
	public function update(){
		global $temps, $msg;		
		
		$req = "update demandes_actions set temps_passe='".$temps."' where id_action='".$this->idobjet."'";
		pmb_mysql_query($req);
		switch($this->champ_sortie){
			default :
				if(strpos($temps,$msg['demandes_action_time_unit']) !== false)
					$this->display = $temps;
				else $this->display = $temps.$msg['demandes_action_time_unit'];
			break;
		}
		
	}
}