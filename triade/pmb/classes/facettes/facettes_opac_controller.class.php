<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_opac_controller.class.php,v 1.2 2024/02/02 08:03:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// Controleur de facettes
global $class_path;
require_once($class_path."/facette_search_opac.class.php");

class facettes_opac_controller extends facettes_controller {
	
	protected static function init_list_ui_class_name() {
		global $sub;
		switch ($sub) {
			case 'facettes_authorities':
				static::$list_ui_class_name = 'list_configuration_opac_facettes_authorities_ui';
				break;
			case 'facettes_external':
				static::$list_ui_class_name = 'list_configuration_opac_facettes_external_ui';
				break;
			default:
				static::$list_ui_class_name = 'list_configuration_opac_facettes_ui';
				break;
		}
	}
}

