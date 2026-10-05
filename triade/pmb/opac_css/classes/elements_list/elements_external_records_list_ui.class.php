<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_external_records_list_ui.class.php,v 1.2 2020/03/10 10:05:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
// require_once($class_path.'/serial_display.class.php');
// require_once($class_path.'/mono_display.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste de notices
 * @author vtouchard
 *
 */
class elements_external_records_list_ui extends elements_records_list_ui {
	
	protected function generate_element($element_id, $recherche_ajax_mode=0){
		return record_display::get_display_unimarc_in_result(intval($element_id));
	}
}