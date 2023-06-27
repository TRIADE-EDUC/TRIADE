<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_provider.class.php,v 1.3 2015-06-09 09:42:07 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_provider {
	private static $classes = array();

	public static function get_instance($type,$id){
		if (!isset(self::$classes[$type])){
			self::$classes[$type] = array();
		}
		if(!isset(self::$classes[$type][$id])){
			switch($type){
				case "article" :
					self::$classes[$type][$id] = new cms_article($id);
					break;
				case "section" :
					self::$classes[$type][$id] = new cms_section($id);
					break;
			}
		}
		return self::$classes[$type][$id];
	}
}
