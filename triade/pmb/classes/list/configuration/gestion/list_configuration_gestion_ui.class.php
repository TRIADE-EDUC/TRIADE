<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_gestion_ui.class.php,v 1.2 2024/01/31 13:06:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_gestion_ui extends list_configuration_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
	    global $module, $current_module, $categ;
	    static::$module = (!empty($module) ? $module : $current_module);
		static::$categ = $categ;
		static::$sub = str_replace(array('list_configuration_gestion_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_cell_visible_flag($object, $property) {
		if ($object->{$property}) {
			return "<center><img src='".get_url_icon('tick.gif')."' style='border:0px; margin:0px 0px' class='bouton-nav align_middle' value='=' /></center>";
		} else {
			return "";
		}
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		return list_ui::get_display_content_object_list($object, $indice);
	}
}