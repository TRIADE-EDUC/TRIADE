<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_facettes_ui.class.php,v 1.2 2021/04/13 14:59:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_opac_facettes_ui extends list_configuration_opac_facettes_root_ui {
	
	protected function _get_query_base() {
		return "SELECT id_facette as id, facettes.* FROM facettes";
	}
}