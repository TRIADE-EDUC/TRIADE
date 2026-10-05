<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_dashboard_ui.class.php,v 1.1 2021/06/07 09:07:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_dashboard_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		//Aucun menu
	}
}