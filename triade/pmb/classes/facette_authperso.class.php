<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_authperso.class.php,v 1.3.10.1 2025/02/24 15:36:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion d'une facette d'autorite perso pour la recherche Gestion et OPAC
require_once $class_path."/facette.class.php";
require_once $class_path."/authperso.class.php";

class facette_authperso extends facette {
    private $authperso_id = 0;
    
    public function set_type($type) {
        if (strpos($this->type, "authperso_") === false) {
            $this->type = $type;
        }
        $this->init_authperso_id();
    }
    
	public function set_properties_from_form() {
	    global $authperso_id;
	    
	    parent::set_properties_from_form();
	    if (!empty($authperso_id)) {
	        $this->type = "authperso_".$authperso_id;
	    }
	}
	
	protected function get_authperso_selector() {
	    global $tpl_form_facette_authperso_selector, $charset;
	    
	    $authpersos = authpersos::get_authpersos();
	    $options = "";
	    foreach($authpersos as $authperso) {
	        $options .= "<option value='".$authperso['id']."' ".($this->authperso_id == $authperso['id'] ? " selected" : "").">".htmlentities($authperso['name'],ENT_QUOTES,$charset)."</option>";
	    }
	    $html = str_replace("!!authperso_options!!", $options, $tpl_form_facette_authperso_selector);
	    return $html;
	}
	
	private function init_authperso_id() {	    
        $authperso =  preg_split("#_([\d]+)#", $this->type, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (!empty($authperso[1]) && intval($authperso[1])) {
            $this->authperso_id = $authperso[1];
        }
	}
}

