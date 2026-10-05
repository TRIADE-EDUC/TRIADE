<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_demandes_content_form.class.php,v 1.1.4.2 2025/05/13 15:23:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_demandes_content_form extends interface_content_form {
	
	public function add_element($name, $label_code='', $display_type='row') {
	    $interface_element = parent::add_element($name, $label_code, $display_type);
	    $interface_element->set_class('colonne3');
	    if (!empty($interface_element->get_label())) {
	       $interface_element->set_label($interface_element->get_label().' :');
	    }
		return $interface_element;
	}
}