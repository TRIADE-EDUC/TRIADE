<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: progressiondemande.class.php,v 1.6.8.1 2025/05/06 15:04:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class progressiondemande extends demande_dynamic_field {
	
	public $id_element = 0;
	public $champ_entree = "";
	public $champ_sortie = "";
	public $display="";
	public $idobjet = 0;
	
	public function __construct($id_elt,$fieldElt){
		$this->id_element = $id_elt;
		$format_affichage = explode('/',$fieldElt);
		$this->champ_entree = $format_affichage[0];
		if($format_affichage[1]) $this->champ_sortie = $format_affichage[1];		
		$ids = explode("_",$id_elt);
		$this->idobjet = $ids[1];
		
	}
	
	protected function get_query_display() {
	    return "select progression from demandes where id_demande='".$this->idobjet."'";
	}
	
	protected function get_value($row) {
	    return $row->progression;
	}
	
	public function update(){
		global $progressiondemande;		
		
		$req = "update demandes set progression='".$progressiondemande."' where id_demande='".$this->idobjet."'";
		pmb_mysql_query($req);
		
		switch($this->champ_sortie){
			case 'img':
				$this->display = "<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$progressiondemande."%\" title='".$progressiondemande."%' />";
				break;
			default:
				$this->display = $progressiondemande."%";
				break;
		}
	}
}