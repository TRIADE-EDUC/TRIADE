<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav_group_scan_request.class.php,v 1.1 2016/02/05 10:06:14 vtouchard Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path.'/admin/connecteurs/out/webdav/groups/webdav_group.class.php');

class webdav_group_scan_request extends webdav_group {
	
	public function get_config_form(){
		return $this->get_collections_tree();
	}
	
	public function get_config_form_script() {
		return $this->get_collections_tree_script();
	}
	
}