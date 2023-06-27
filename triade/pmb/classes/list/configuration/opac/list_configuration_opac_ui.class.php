<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_ui.class.php,v 1.2 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/list_configuration_ui.class.php");

class list_configuration_opac_ui extends list_configuration_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'admin';
		static::$categ = 'opac';
		static::$sub = str_replace(array('list_configuration_opac_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_cell_visible_flag($object, $property) {
		if ($object->{$property}) {
			return "<img src='".get_url_icon('tick.gif')."' style='border:0px; margin:0px 0px' class='bouton-nav align_middle' value='=' />";
		} else {
			return "";
		}
	}
	
	protected function get_button_order() {
		global $msg, $charset;
	
		return "<input class='bouton' type='button' value='".htmlentities($msg['list_ui_save_order'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=save_order';\" />";
	}
	
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= $this->get_button_order();
		return $display;
	}
		
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		return list_ui::get_display_content_object_list($object, $indice);
	}
}