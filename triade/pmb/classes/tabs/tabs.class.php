<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tabs.class.php,v 1.2 2021/11/17 16:36:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class tabs {
	
	protected static $tabs_module;
	
	public static function get_tabs_module($module_name) {
		if(!isset(static::$tabs_module[$module_name])) {
			static::$tabs_module[$module_name] = array();
			$query = "SELECT * FROM tabs WHERE tab_module = '".addslashes($module_name)."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					static::$tabs_module[$module_name][$row->tab_categ.($row->tab_sub ? "_".$row->tab_sub : '')] = array(
							'visible' => $row->tab_visible,
							'autorisations' => explode(" ", $row->tab_autorisations),
							'autorisations_all' => $row->tab_autorisations_all
					);
				}
			}
		}
		return static::$tabs_module[$module_name];
	}
	
	public static function get_shortcuts() {
		$shortcuts = array();
		$query = "SELECT * FROM tabs WHERE tab_shortcut <> ''";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$shortcuts[] = array(
						$row->tab_shortcut,
						"./".$row->tab_module.".php?categ=".$row->tab_categ.($row->tab_sub ? "&sub=".$row->tab_sub : "")
				);
			}
		}
		return $shortcuts;
	}
}