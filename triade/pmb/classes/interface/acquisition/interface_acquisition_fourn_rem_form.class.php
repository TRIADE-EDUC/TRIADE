<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_acquisition_fourn_rem_form.class.php,v 1.1.4.2 2025/05/28 06:20:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/acquisition/interface_acquisition_form.class.php');

class interface_acquisition_fourn_rem_form extends interface_acquisition_form {
	
    protected $num_fournisseur;
    
	protected function get_cancel_action() {
	    return $this->get_url_base()."&action=cond&id=".$this->num_fournisseur;
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=updaterem&id=".$this->num_fournisseur;
	}
	
	protected function get_delete_action() {
	    return $this->get_url_base()."&action=deleterem&id=".$this->num_fournisseur."& id_prod=".$this->object_id;
	}
	
	public function set_num_fournisseur($num_fournisseur) {
	    $this->num_fournisseur = intval($num_fournisseur);
	    return $this;
	}
}