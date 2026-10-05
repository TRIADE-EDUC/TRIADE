<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_perio.class.php,v 1.4 2022/02/11 15:50:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes d'info sur bulletinage de périodique
		
class abts_perio {

	public $serial_id       = 0;         // id du périodique 
	
	// constructeur
	public function __construct($serial_id=0) {
		$this->serial_id = intval($serial_id);
		$this->fetch_data();
	}
	    
	// récupération des infos en base
	public function fetch_data() {
		
	}

} // fin définition classe
