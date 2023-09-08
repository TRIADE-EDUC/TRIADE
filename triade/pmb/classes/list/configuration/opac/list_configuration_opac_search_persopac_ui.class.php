<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_search_persopac_ui.class.php,v 1.1 2018-10-12 12:18:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/opac/list_configuration_opac_ui.class.php");

class list_configuration_opac_search_persopac_ui extends list_configuration_opac_ui {
	
	protected $entities;
	
	protected function _get_query_base() {
		return "SELECT search_id as id, search_persopac.* FROM search_persopac";
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'search_order',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'search_order' => 'search_persopac_table_order',
				'search_directlink' => 'search_persopac_table_preflink',
				'search_name' => 'search_persopac_table_name',
				'search_shortname' => 'search_persopac_table_shortname',
				'search_human' => 'search_persopac_table_humanquery',
				'search__type' => 'search_persopac_type'		);
	}
	
	protected function add_column_edit() {
		global $msg, $charset;
		
		$this->columns[] = array(
				'property' => '',
				'label' => $msg['search_persopac_table_edit'],
				'html' => "<input class='bouton_small' value='".$msg["search_persopac_modifier"]."' type='button'  onClick=\"document.location='".static::get_controller_url_base()."&action=form&id=!!id!!'\" />"
		);
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
		$this->add_column_edit();
	}
	
	protected function get_entities($entitie = '') {
		global $msg, $charset;
	
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
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'search_order':
				$content .= "
					<input type='button' class='bouton_small' value='-' onClick=\"document.location='".static::get_controller_url_base()."&action=up&id=".$object->search_id."'\"/></a>
					<input type='button' class='bouton_small' value='+' onClick=\"document.location='".static::get_controller_url_base()."&action=down&id=".$object->search_id."'\"/>
				";
				break;
			case 'search_directlink':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			case 'search_type':
				$entities = $this->get_entities();
				$content .= $entities[$object->search_type];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
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