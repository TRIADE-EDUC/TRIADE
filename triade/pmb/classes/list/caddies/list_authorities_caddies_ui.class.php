<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_caddies_ui.class.php,v 1.4 2020/11/05 12:26:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/authorities_caddie.class.php");

class list_authorities_caddies_ui extends list_caddies_root_ui {
	
	protected static $model_class_name = 'authorities_caddie';
	
	protected static $field_name = 'idcaddie';
	
	public static function get_controller_url_base() {
		global $base_path, $sub;
		
		return $base_path.'/autorites.php?categ=caddie&sub='.$sub;
	}
}