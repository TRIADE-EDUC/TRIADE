<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_copy_ui.class.php,v 1.5 2022/09/30 12:30:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc/serialcirc_copy.class.php");

class list_serialcirc_copy_ui extends list_serialcirc_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT * FROM serialcirc_copy 
				JOIN bulletins ON serialcirc_copy.num_serialcirc_copy_bulletin = bulletins.bulletin_id
				JOIN notices ON bulletins.bulletin_notice = notices.notice_id';
		return $query;
	}
		
	protected function get_object_instance($row) {
		return new serialcirc_copy($row->id_serialcirc_copy);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date', 'asc');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'date' => 'serialcirc_circ_list_bull_circulation_periode',
						'serial' => 'serialcirc_circ_list_bull_circulation_perodique',
						'bulletin_numero' => 'serialcirc_circ_list_bull_circulation_numero',
						'empr' => 'serialcirc_circ_list_reproduction_empr',
						'comment' => 'serialcirc_circ_list_reproduction_empr_message',
						'state' => 'serialcirc_circ_list_reproduction_state',
						'actions' => 'serialcirc_circ_list_bull_circulation_actions',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('date');
		$this->add_column('serial');
		$this->add_column('bulletin_numero');
		$this->add_column('empr');
		$this->add_column('comment');
		$this->add_column('state');
		$this->add_column('actions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('grouped_objects', 'display_counter', true);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('date', 'datatype', 'date');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'date', 'serial', 'bulletin_numero', 'empr', 'comment', 'state',
				'actions'
		);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'classement');
	}
	
	protected function _get_object_property_serial($object) {
		return serial::get_notice_title($object->get_bulletin_notice());
	}
	
	protected function _get_object_property_empr($object) {
		$empr_info = serialcirc::empr_info($object->get_num_empr());
		return $empr_info['empr_libelle'];
	}
	
	protected function _get_object_property_classement($object) {
		global $msg;
		return $msg["serialcirc_circ_list_bull_reproduction"];
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'serial':
				$serial = $this->_get_object_property_serial($object);
				$content .= "<a href='".serial::get_permalink($object->get_bulletin_notice())."'>". htmlentities($serial,ENT_QUOTES,$charset)."</a>";
				break;
			case 'bulletin_numero':
				$bulletin_numero = $object->get_bulletin_numero();
				$content .= "<a href='".bulletinage::get_permalink($object->get_bulletin_id())."'>". htmlentities($bulletin_numero,ENT_QUOTES,$charset)." ".$object->get_bulletin_mention_date()."</a>";
				break;
			case 'state':
				if($object->get_state()) {
					$content .= $msg["serialcirc_circ_list_reproduction_state_ok"];
				}
				break;
			case 'empr':
				$empr_info = serialcirc::empr_info($object->get_num_empr());
				$content .= "<a href='".$empr_info['view_link']."'>".htmlentities($empr_info['empr_libelle'],ENT_QUOTES,$charset)."</a>";
				break;
			case 'actions':
				if(!$object->get_state()) {
					$content .= "
						<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_reproduction_ok_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_copy_accept('copy','".$object->get_id()."'); return false;\"/>&nbsp;
						<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_reproduction_none_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_copy_none('copy','".$object->get_id()."'); return false;\"/>&nbsp;
						<div id='circ_actions_copy_".$object->get_id()."' class='erreur'></div>
					";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_js_sort_expandable_list() {
		return '';
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/circ.php?categ=serialcirc';
	}
}