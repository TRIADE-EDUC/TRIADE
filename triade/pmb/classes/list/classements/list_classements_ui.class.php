<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_classements_ui.class.php,v 1.6.4.1 2025/02/20 15:31:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/classements.class.php");

class list_classements_ui extends list_ui {
	
	protected static $type;
	
	public static function set_type($type) {
		static::$type = $type;
	}
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(empty($this->objects_type)) {
			$this->objects_type = str_replace('list_', '', get_class($this)).'_'.static::$type;
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'SELECT id_classement FROM classements ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new classement($row->id_classement);
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'order':
	            return 'classement_order, nom_classement';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'order' => '',
						'nom_classement' => '103',
						'nom_classement_opac' => 'dsi_clas_form_nom_opac',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('order');
		$this->add_column('nom_classement');
		$this->add_column('nom_classement_opac');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('nom_classement', 'align', 'left');
		$this->set_setting_column('nom_classement_opac', 'align', 'left');
		$this->set_setting_column('nom_classement', 'text', array('bold' => true));
	}
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_interface_button($msg['dsi_clas_ajouter'], ['location' => static::get_controller_url_base().'&suite=add']);
	}
	
	protected function _add_query_filters() {
		if(static::$type == 'EQU') {
			$this->query_filters [] = "(type_classement='EQU')";
		} elseif(static::$type !== '') {
			$this->query_filters [] = "(type_classement='' or type_classement='".static::$type."')";
		}
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		return "<h3>".htmlentities($msg['dsi_clas_type_class_'.static::$type], ENT_QUOTES, $charset)." (".$this->pager['nb_results'].")</h3>";
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'order':
				$content .= "
						<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&id_classement=".$object->id_classement."&suite=down'\" style='cursor:pointer;'/>
						<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&id_classement=".$object->id_classement."&suite=up'\" style='cursor:pointer;'/>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_default_attributes_format_cell($object, $property) {
		switch ($property) {
			case 'order':
				return array();
			default:
				return array(
						'onclick' => "document.location=\"".static::get_controller_url_base()."&id_classement=".$object->id_classement."&suite=acces\""
				);
				break;
		}
	}
	
	protected function get_display_left_actions() {
		if(static::$type == 'EQU') {
			return $this->get_button_add();
		}
		return "";
	}
	
	public static function get_button_add_empty_lists() {
		global $msg;
		
		return "<br /><input class='bouton' type='button' value='".$msg['dsi_clas_ajouter']."' onClick=\"document.location='".static::get_controller_url_base().'&suite=add'."';\" />";
	}
}