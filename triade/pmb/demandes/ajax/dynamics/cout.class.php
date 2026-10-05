<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cout.class.php,v 1.5.8.1 2025/05/06 15:04:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cout extends demande_dynamic_field {
	
    protected function get_query_display() {
        return "select cout from demandes_actions where id_action='".$this->idobjet."'";
    }
    
    protected function get_value($row) {
        return $row->cout;
    }
    
	public function update(){
		global $cout, $pmb_gestion_devise;		
		
		$req = "update demandes_actions set cout='".$cout."' where id_action='".$this->idobjet."'";
		pmb_mysql_query($req);
		
		switch($this->champ_sortie){
			default :
				if(strpos($cout,$pmb_gestion_devise) !== false)
					$this->display = $cout;
				else $this->display = $cout.$pmb_gestion_devise;
			break;
		}
	}
}