<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_lists_datasources_ui.class.php,v 1.1 2023/02/22 17:07:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_lists_datasources_ui extends list_lists_ui {
	
	protected function get_xmlfile_lists() {
		if (file_exists($this->get_list_directory()."datasources_subst.xml")) {
			return $this->get_list_directory()."datasources_subst.xml";
		} else {
			return $this->get_list_directory()."datasources.xml";
		}
	}
}