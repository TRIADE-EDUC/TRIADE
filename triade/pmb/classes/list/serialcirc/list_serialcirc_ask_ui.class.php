<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_ask_ui.class.php,v 1.15 2024/03/13 13:38:36 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc.class.php");

class list_serialcirc_ask_ui extends list_serialcirc_ui {

	protected function _get_query_base() {
		$query = 'select * from serialcirc_ask ';
		return $query;
	}

	protected function get_object_instance($row) {
		return new serialcirc_ask($row->id_serialcirc_ask);
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date', 'desc');
	}

	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'date' => 'serialcirc_asklist_date',
						'empr' => 'serialcirc_asklist_empr',
						'type' => 'serialcirc_asklist_type',
						'serial' => 'serialcirc_asklist_perio',
						'status' => 'serialcirc_asklist_statut',
						'comment' => 'serialcirc_asklist_comment',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}

	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['serialcirc_asklist_title_form'], ENT_QUOTES, $charset);
	}

	protected function init_default_selection_actions() {
		global $msg;

		parent::init_default_selection_actions();
		$this->add_selection_action('accept', $msg['serialcirc_asklist_accept_bt'], '', $this->get_link_action('accept'));
		$this->add_selection_action('refus', $msg['serialcirc_asklist_refus_bt'], '', $this->get_link_action('refus'));
		$this->add_selection_action('delete', $msg['serialcirc_asklist_delete_bt'], '', $this->get_link_action('delete'));
	}

	protected function get_name_selected_objects() {
		return "asklist_id";
	}

	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('date');
		$this->add_column('empr');
		$this->add_column('type');
		$this->add_column('serial');
		$this->add_column('status');
		$this->add_column('comment');
	}

	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('date', 'datatype', 'date');
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'location' => 'serialcirc_asklist_location_title',
						'type' => 'serialcirc_asklist_type_title',
						'status' => 'serialcirc_asklist_statut_title',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}

	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'location' => '',
				'type' => -1,
				'status' => -1,
		);
		parent::init_filters($filters);
	}

	protected function init_default_selected_filters() {
		$this->add_selected_filter('location');
		$this->add_selected_filter('type');
		$this->add_selected_filter('status');
	}

	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$location = $this->objects_type.'_location';
		global ${$location};
		if(isset(${$location}) && ${$location} != '') {
			$this->filters['location'] = ${$location};
		}
		$this->set_filter_from_form('type', 'integer');
		$this->set_filter_from_form('status', 'integer');
		parent::set_filters_from_form();
	}

	protected function get_search_filter_location() {
		global $msg;

		return gen_liste("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", $this->objects_type.'_location', "calcule_section(this);", $this->filters['location'], "", "",0,$msg["serialcirc_asklist_location_all"],0);
	}

	protected function get_search_filter_type() {
		global $msg;

		return $this->gen_selector($this->objects_type.'_type',
				array(
						-1=>$msg['serialcirc_asklist_type_all'],
						0=>$msg['serialcirc_asklist_type_0'],
						1=>$msg['serialcirc_asklist_type_1']
				),	$this->filters['type']);
	}

	protected function get_search_filter_status() {
		global $msg;

		return $this->gen_selector($this->objects_type.'_status',
				array(
						-1=>$msg['serialcirc_asklist_statut_all'],
						0=>$msg['serialcirc_asklist_statut_0'],
						1=>$msg['serialcirc_asklist_statut_1'],
						2=>$msg['serialcirc_asklist_statut_2'],
						3=>$msg['serialcirc_asklist_statut_3']
				),$this->filters['status']);
	}

	protected function gen_selector($name,$field_list,$value=0){
		global $charset;
		$selector="<select name='$name' id='$name'>";
		foreach($field_list as $val =>$field) {
			$selector.= "<option value='".$val."'";
			$val == $value ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
			$selector.= htmlentities($field,ENT_QUOTES, $charset).'</option>';
		}
		return $selector.'</select>';
	}

	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
		$filter_join_query = '';
		if($this->filters['location']) {
			$filter_join_query .= " JOIN empr ON num_serialcirc_ask_empr=id_empr";
		}
		return $filter_join_query;
	}

	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('location', 'empr_location', 'integer');
		if($this->filters['type'] !== -1) {
			$this->query_filters [] = 'serialcirc_ask_type = "'.$this->filters['type'].'"';
		}
		if($this->filters['status'] !== -1) {
			$this->query_filters [] = 'serialcirc_ask_statut = "'.$this->filters['status'].'"';
		}
	}

	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["serialcirc_asklist_no"], ENT_QUOTES, $charset);
	}

	protected function _get_object_property_empr($object) {
		return $object->ask_info['empr']["empr_libelle"];
	}

	protected function _get_object_property_date($object) {
		return $object->ask_info['date'];
	}

	protected function _get_object_property_comment($object) {
		return $object->ask_info['comment'];
	}

	protected function _get_object_property_type($object) {
		global $msg;
		return $msg['serialcirc_asklist_type_'.$object->ask_info['type']];
	}

	protected function _get_object_property_serial($object) {
	    return $object->ask_info['perio']['header'];
	}

	protected function _get_object_property_status($object) {
		global $msg;
		return $msg['serialcirc_asklist_statut_'.$object->ask_info['statut']];
	}

	protected function get_cell_content($object, $property) {
	    global $charset;

		$content = '';
		switch($property) {
			case 'empr':
				$content .= "<a href='".$object->ask_info['empr']['view_link']."'>".htmlentities($this->_get_object_property_empr($object),ENT_QUOTES,$charset)."</a>";
				break;
			case 'serial':
				$abt_list="";
				if($object->ask_info['type']==0){
					foreach($object->ask_info['abts'] as $abt){
						$abt_list.="<br /><a href='". $abt['link_diff'] ."' >".$abt['name']." </a>";
					}
				}

				if (!empty($object->ask_info['perio'])) {
					$content .= "<a href='".$object->ask_info['perio']['view_link']."'>".$object->ask_info['perio']['header'].$abt_list;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function _get_query_human_location() {
		if($this->filters['location']) {
			$docs_location = new docs_location($this->filters['location']);
			return $docs_location->libelle;
		}
		return '';
	}

	protected function _get_query_human_type() {
		global $msg;
		if($this->filters['type'] !== -1) {
			return $msg['serialcirc_asklist_type_'.$this->filters['type']];
		}
		return '';
	}

	protected function _get_query_human_status() {
		global $msg;
		if($this->filters['status'] !== -1) {
			return $msg['serialcirc_asklist_statut_'.$this->filters['status']];
		}
		return '';
	}

	public static function get_controller_url_base() {
		global $base_path;

		return $base_path.'/catalog.php?categ=serials&sub=circ_ask';
	}
}