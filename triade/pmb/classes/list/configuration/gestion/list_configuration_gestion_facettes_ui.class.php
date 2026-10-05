<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_gestion_facettes_ui.class.php,v 1.1 2024/01/31 07:35:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_gestion_facettes_ui extends list_configuration_gestion_facettes_root_ui {
	
	protected function _get_query_base() {
		return "SELECT id_facette as id, facettes.* FROM facettes";
	}
}