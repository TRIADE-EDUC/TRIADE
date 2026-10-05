<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_search_persopac_ui.class.php,v 1.13 2023/03/24 09:26:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_opac_search_persopac_ui extends list_configuration_opac_ui {
	
	protected $entities;
	
	protected function _get_query_base() {
		return "SELECT search_id as id, search_persopac.* FROM search_persopac";
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('search_order');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'search_order' => 'search_persopac_table_order',
				'search_directlink' => 'search_persopac_table_preflink',
				'search_name' => 'search_persopac_table_name',
				'search_shortname' => 'search_persopac_table_shortname',
				'search_human' => 'search_persopac_table_humanquery',
				'search_type' => 'search_persopac_type'		
		);
	}
	
	protected function add_column_edit() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['search_persopac_modifier'],
				'link' => static::get_controller_url_base()."&action=form&id=!!id!!"
		);
		$this->add_column_simple_action('', $msg['search_persopac_table_edit'], $html_properties);
	}
	
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['id'] = '1601';
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			if($name != 'id') {
				$this->add_column($name);
			}
		}
		$this->add_column_edit();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('id', 'datatype', 'integer');
		$this->set_setting_column('id', 'align', 'center');
		$this->set_setting_column('id', 'text', array('bold' => true));
		$this->set_setting_column('search_order', 'datatype', 'integer');
		$this->set_setting_column('search_order', 'align', 'center');
	}
	
	protected function get_entities($entitie = '') {
		global $msg;
	
		if(!isset($this->entities)) {
			$authpersos=authpersos::get_instance();
			$authperso_infos = $authpersos->get_data();
			$authperso_values = array();
			if(count($authperso_infos)){
				foreach($authperso_infos as $authperso_info){
					$authperso_values[$authperso_info['id']] =  $authperso_info['name'];
				}
			}
			$entities = array(
					'notices' => $msg['288'],
					'authors' => $msg['isbd_author'],
					'categories' => $msg['isbd_categories'],
					'concepts' => $msg['search_concept_title'],
					'collections' => $msg['isbd_collection'],
					'indexint' => $msg['isbd_indexint'],
					'publishers' => $msg['isbd_editeur'],
					'series' => $msg['isbd_serie'],
					'subcollections' => $msg['isbd_subcollection'],
					'titres_uniformes' => $msg['isbd_titre_uniforme'],
			);
			$this->entities = $entities + $authperso_values;
		}
		return $this->entities;
	}
	
	protected function _get_object_property_search_type($object) {
		$entities = $this->get_entities();
		return $entities[$object->search_type];
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'search_order':
				$content .= "
					<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=down&id=".$object->search_id."'\" style='cursor:pointer;'/>
					<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=up&id=".$object->search_id."'\" style='cursor:pointer;'/>
				";
				break;
			case 'search_directlink':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			case 'search_human':
				$content .= $object->search_human; // on conserve l'interprétation du HTML
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_button_order() {
		global $msg;
		
		return $this->get_button('save_order', $msg['list_ui_save_order']);
	}
	
	protected function get_display_left_actions() {
		$display = parent::get_display_left_actions();
		$display .= $this->get_button_order();
		return $display;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['search_persopac_add'];
	}
	
	public static function get_controller_url_base() {
		return parent::get_controller_url_base()."&section=liste";
	}
	
	public function run_action_save_order($action='') {
		foreach ($this->objects as $order=>$object) {
			$query = "update search_persopac set search_order = '".$order."' where search_id = ".$object->id;
			pmb_mysql_query($query);
		}
	}
}