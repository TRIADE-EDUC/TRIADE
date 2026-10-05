<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_records_selectors_list_ui.class.php,v 1.5 2018/10/18 10:15:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
require_once($class_path.'/record_display.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste de notices dans un sélecteur
 * @author dgoron
 *
 */
class elements_records_selectors_list_ui extends elements_records_list_ui {
	
	protected function generate_elements_list(){
		$this->add_context_parameter('in_selector', true);
		return parent::generate_elements_list();
	}
}