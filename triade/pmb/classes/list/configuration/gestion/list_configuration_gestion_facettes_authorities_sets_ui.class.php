<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_gestion_facettes_authorities_sets_ui.class.php,v 1.1 2024/01/31 07:35:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_gestion_facettes_authorities_sets_ui extends list_configuration_gestion_facettes_root_sets_ui {
	
	protected function _get_query_base() {
		return "SELECT id_set as id, facettes_sets.* FROM facettes_sets";
	}
	
	protected function get_cell_content($object, $property) {
	    $content = '';
	    switch($property) {
	        case 'facettes':
	            $objects = list_configuration_gestion_facettes_authorities_ui::get_instance(array('num_facettes_set' => $object->id))->get_objects();
	            if (!empty($objects)) {
	                $facettes = [];
	                foreach ($objects as $object) {
	                    $facettes[] = $object->facette_name;
	                }
	                $content .= '<ul><li>';
	                $content .= implode('</li><li>', $facettes);
	                $content .= '</li></ul>';
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	public static function get_controller_url_base() {
	    global $type;
		return parent::get_controller_url_base()."&type=".$type;
	}
}