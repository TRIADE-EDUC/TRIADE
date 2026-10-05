<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_tpl_bannette_ui.class.php,v 1.1 2021/02/01 08:45:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_tpl_bannette_ui extends list_configuration_tpl_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM bannette_tpl';
	}
	
	protected function get_object_instance($row) {
		return new bannette_tpl($row->bannettetpl_id);
	}	
}