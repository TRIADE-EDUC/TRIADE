<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_ui.class.php,v 1.22 2023/12/18 15:17:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_ui extends list_ui {
	
	protected static $module;
	
	protected static $categ;
	
	protected static $sub;
	
	protected $separator_parameter;
	
	protected function add_separator_parameter($label_code) {
		global $msg;
		$this->separator_parameter = $msg[$label_code];
	}
	
	protected function get_parameter_id($type_param, $sstype_param) {
		$query = "SELECT id_param FROM parametres WHERE type_param='".addslashes($type_param)."' AND sstype_param='".addslashes($sstype_param)."'";
		return pmb_mysql_result(pmb_mysql_query($query), 0, 'id_param');
	}
	
	protected function get_parameter($type_param, $sstype_param, $label_code='', $values=array()) {
		global $msg;
		
		$parameter = array (
				"id" => $this->get_parameter_id($type_param, $sstype_param),
				"type_param" => $type_param,
				"sstype_param" => $sstype_param,
				"name" => $type_param."_".$sstype_param,
				"label" => (!empty($label_code) ? $msg[$label_code] : ''),
				"valeur_param" => $this->get_parameter_value($type_param."_".$sstype_param),
				"values" => $values,
				"section" => (!empty($this->separator_parameter) ? $this->separator_parameter : '')
		);
		return (object) $parameter;
	}
	
	protected function add_parameter($type_param, $sstype_param, $label_code='', $values=array()) {
		$this->add_object($this->get_parameter($type_param, $sstype_param, $label_code, $values));
	}
	
	protected function get_name_cell_edition($object, $property) {
		if($property == 'valeur_param') {
			return $this->objects_type."_".$object->name;
		} else {
			return parent::get_name_cell_edition($object, $property);
		}
	}
	protected function get_options_editable_column($object, $property) {
		//on est sur un objet type paramètre
		if($property == 'valeur_param' && !empty($object->values)) {
			return $object->values;
		}
	}
	
	public function get_parameter_value($name) {
		global ${$name};
		return ${$name};
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['nb_per_page'] = 100;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	
		$this->available_columns =
		array('main_fields' =>
				$this->get_main_fields_from_sub()
		);
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function get_cell_visible_flag($object, $property) {
		$method_name = "get_".$property;
		if (is_object($object) && !empty($object->{$property}) || (method_exists($object, $method_name) && !empty($object->{$method_name}()))) {
			return "<center>X</center>";
		} else {
			return "&nbsp;";
		}
	}
	
	protected function get_cell_img_class_html($object, $property) {
	    return "
        <span class='".$object->{$property}."' style='margin-right: 3px;'>
            <img src='".get_url_icon('spacer.gif')."' style='width:10px; height:10px' alt='' />
        </span>";
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		if(!isset($this->is_editable_object_list)) {
			$this->is_editable_object_list = true;
		}
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function get_button_add() {
		return $this->get_button('add', $this->get_label_button_add());
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
	
	protected function save_parameter($object, $property) {
		$value = $this->get_value_from_cell_form($object, $property);
		
		$varGlobal = $object->name;
		global ${$varGlobal};
		//on modifie la valeur de l'objet
		$object->valeur_param = $value;
		//on enregistre dans la variable globale
		${$varGlobal} = $value;
		//puis dans la base
		$query = "UPDATE parametres SET valeur_param='".addslashes($value)."'
					WHERE type_param='".$object->type_param."' AND sstype_param='".$object->sstype_param."'";
		pmb_mysql_query($query);
	}
	
	protected function save_object_property($object, $property) {
		switch ($property) {
			case 'valeur_param':
				$this->save_parameter($object, $property);
				break;
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub='.static::$sub;
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ='.static::$categ.(static::$sub ? '&sub='.static::$sub : '');
	}
}