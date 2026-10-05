<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: admin_session.class.php,v 1.1.2.2 2025/01/30 10:36:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class admin_session {
	
    protected $session = [];
    
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct() {
        $this->fetch_data();
	}
	
	protected function fetch_data() {
	    global $PMBuserid;
	    
	    $this->session = [
	        "session_history" => [],
	        "facette_default" => [],
	        "facette_external_default" => [],
	    ];
	    // Récupération de l'historique
	    $query = "select session from admin_session where userid=" . $PMBuserid;
	    $resultat = pmb_mysql_query($query);
	    if ($resultat) {
	        if (pmb_mysql_num_rows($resultat)) {
	            $session_history = pmb_mysql_result($resultat, 0, 0);
	            $decoded_session_history = encoding_normalize::json_decode($session_history, true);
	            if (empty($decoded_session_history)) {
	                $decoded_session_history = @unserialize($session_history);
	            }
	            $this->session["session_history"] = $decoded_session_history['session_history'] ?? $decoded_session_history;
	            $this->session["facette_default"] = $decoded_session_history['facette_default'] ?? [];
	            $this->session["facette_external_default"] = $decoded_session_history['facette_external_default'] ?? [];
	        }
	    }
	}
	
	public function load() {
	    // Récupération de l'historique
	    $_SESSION["session_history"] = $this->session['session_history'];
	    $_SESSION["facette_default"] = $this->session['facette_default'];
	    $_SESSION["facette_external_default"] = $this->session['facette_external_default'];
	}
	
	public function save_property($property) {
	    $this->session[$property] = (isset($_SESSION[$property]) ? $_SESSION[$property] : []);
	    $this->save();
	}
	
	public function save() {
	    $query = "replace into admin_session values(".SESSuserid.",'".addslashes(encoding_normalize::json_encode($this->session))."')";
	    return pmb_mysql_query($query);
	}
	
}


