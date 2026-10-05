<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_concepts_list_ui.class.php,v 1.1 2019/06/13 15:33:03 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_authorities_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'autorité
 * @author dgoron
 *
 */
class elements_concepts_list_ui extends elements_authorities_list_ui {

	protected function get_authority_instance($element_id) {
	    return authorities_collection::get_authority('authority', 0, array('type_object' => 10, 'num_object' => $element_id));
	}
}