<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_state_ui.class.php,v 1.5 2023/09/29 07:22:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc_diff.class.php");

class list_serialcirc_state_ui extends list_serialcirc_ui {
	
	protected $serialcirc_diff;
	
	/**
	 * Tableau des libellés de localisations
	 * @var array
	 */
	protected $locations_labels;
	
	protected function _get_query_base() {
		$query = 'select SQL_CALC_FOUND_ROWS id_serialcirc as id, if(serialcirc_diff.serialcirc_diff_empr_type = '.SERIALCIRC_EMPR_TYPE_empr.', serialcirc_diff.num_serialcirc_diff_empr, serialcirc_group.num_serialcirc_group_empr) as empr_id, notices.notice_id, abts_abts.abt_id from serialcirc
				join abts_abts on serialcirc.num_serialcirc_abt = abts_abts.abt_id
				join notices on abts_abts.num_notice = notices.notice_id
				join serialcirc_diff on serialcirc.id_serialcirc = serialcirc_diff.num_serialcirc_diff_serialcirc
				left join serialcirc_group on serialcirc_diff.serialcirc_diff_empr_type = '.SERIALCIRC_EMPR_TYPE_group.' and serialcirc_diff.id_serialcirc_diff = serialcirc_group.num_serialcirc_group_diff';
		return $query;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('serial');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'serial':
	            return 'notices.index_sew, abts_abts.abt_name, serialcirc_diff.serialcirc_diff_order, serialcirc_group.serialcirc_group_order';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'serial' => '1150',
						'abt' => 'serialcirc_circ_list_bull_circulation_abonnement',
						'empr_name' => '379',
						'empr_adr1' => 'adresse_empr',
						'empr_ville' => 'ville_empr',
						'end_location' => 'serial_circ_state_end_location',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
		
	protected function init_default_columns() {
		$this->add_column('serial');
		$this->add_column('abt');
		$this->add_column('empr_name');
		$this->add_column('empr_adr1');
		$this->add_column('empr_ville');
		$this->add_column('end_location');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'export_icons', true);
		$this->set_setting_filter('serials', 'selection_type', 'completion');
		$this->set_setting_column('default', 'align', 'left');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'location' => '298',
						'caddies' => '396',
						'serials' => 'serials_query',
						'abts_date_fin' => 'serialcirc_state_date_echeance_filter',
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
				'caddies' => array(),
				'serials' => array(),
				'abts_date_fin_start' => date("Y-m-d"),
				'abts_date_fin_end' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('location');
		$this->add_selected_filter('caddies');
		$this->add_selected_filter('serials');
		$this->add_selected_filter('abts_date_fin');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('location', 'integer');
		$this->set_filter_from_form('caddies', 'integer');
		$this->set_filter_from_form('serials', 'integer');
		$this->set_filter_from_form('abts_date_fin_start');
		$this->set_filter_from_form('abts_date_fin_end');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		global $PMBuserid;
		
		$query = '';
		switch ($type) {
			case 'locations':
				$query = 'select idlocation as id, location_libelle as label from docs_location order by label';
				break;
			case 'caddies':
				$query = 'select idcaddie as id, name as label from caddie where type = "NOTI" and (autorisations="'.$PMBuserid.'" or autorisations like "'.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.'") order by name';
				break;
			case 'serials':
				$query = 'SELECT distinct notice_id as id, tit1 as label FROM notices WHERE niveau_biblio="s" and niveau_hierar="1" ORDER BY label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_location() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('locations'), 'location', $msg["all_location"]);
	}
	
	protected function get_search_filter_caddies() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('caddies'), 'caddies', $msg["serialcirc_diff_no_selection_caddie"]);
	}
	
	protected function get_search_filter_serials() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('serials'), 'serials', $msg["all"]);
	}
	
	protected function get_search_filter_abts_date_fin() {
		return $this->get_search_filter_interval_date('abts_date_fin');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('location', 'abts_abts.location_id', 'integer');
		if(!empty($this->filters['caddies'])) {
			foreach ($this->filters['caddies'] as $caddie_id) {
				$query = 'select group_concat(object_id separator ",") from caddie_content where caddie_id = '.$caddie_id;
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$notices_ids = pmb_mysql_result($result, 0, 0);
					if(empty($notices_ids)) $notices_ids = '0';
					$this->query_filters [] = 'notices.notice_id IN ('.$notices_ids.')';
				}
			}
		}
		$this->_add_query_filter_multiple_restriction('serials', 'notices.notice_id', 'integer');
		$this->_add_query_filter_interval_restriction('abts_date_fin', 'abts_abts.date_fin', 'date');
	}
	
	protected function _get_object_property_serial($object) {
		$serialcirc_diff = $this->get_serialcirc_diff($object->id);
		return $serialcirc_diff->serial_info['serial_name'];
	}
	
	protected function _get_object_property_abt($object) {
		$serialcirc_diff = $this->get_serialcirc_diff($object->id);
		return $serialcirc_diff->abt_name;
	}
	
	protected function _get_object_property_empr_name($object) {
		$serialcirc_diff = $this->get_serialcirc_diff($object->id);
		$empr = $serialcirc_diff->empr_info[$object->empr_id];
		return $empr['nom'].' '.$empr['prenom'];
	}
	
	protected function _get_object_property_empr_adr1($object) {
		$serialcirc_diff = $this->get_serialcirc_diff($object->id);
		$empr = $serialcirc_diff->empr_info[$object->empr_id];
		return $empr['adr1'];
	}
	
	protected function _get_object_property_empr_ville($object) {
		$serialcirc_diff = $this->get_serialcirc_diff($object->id);
		$empr = $serialcirc_diff->empr_info[$object->empr_id];
		return $empr['ville'];
	}
	
	protected function _get_object_property_end_location($object) {
		$serialcirc_diff = $this->get_serialcirc_diff($object->id);
		if ($serialcirc_diff->no_ret_circ) {
			// Le dernier lecteur garde le bulletin
			$last_empr = end($serialcirc_diff->empr_info);
			return $this->get_location_label($last_empr['location']);
		} else {
			// Le bulletin revient à la localisation de l'abonnement
			return $this->get_location_label($serialcirc_diff->serial_info['abt_location']);
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $charset, $base_path;
		
		$content = '';
		switch($property) {
			case 'serial':
				$link = serial::get_permalink($object->notice_id);
				$content .= "<a href='".$link."' title='".htmlentities($this->_get_object_property_serial($object),ENT_QUOTES,$charset)."'>".htmlentities($this->_get_object_property_serial($object),ENT_QUOTES,$charset)."</a>";
				break;
			case 'abt':
				$link = $base_path."/catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$object->abt_id;
				$content .= "<a href='".$link."' title='".htmlentities($this->_get_object_property_abt($object),ENT_QUOTES,$charset)."'>".htmlentities($this->_get_object_property_abt($object),ENT_QUOTES,$charset)."</a>";
				break;
			case 'empr_name':
				$serialcirc_diff = $this->get_serialcirc_diff($object->id);
				$empr = $serialcirc_diff->empr_info[$object->empr_id];
				$link = $base_path.'/circ.php?categ=pret&form_cb='.$empr['cb'];
				$content .= "<a href='".$link."' title='".htmlentities($this->_get_object_property_empr_name($object),ENT_QUOTES,$charset)."'>".htmlentities($this->_get_object_property_empr_name($object),ENT_QUOTES,$charset)."</a>";
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
	
	protected function _get_query_human_caddies() {
		if(!empty($this->filters['caddies'])) {
			$labels = array();
			foreach ($this->filters['caddies'] as $caddie_id) {
				$caddie = caddie::get_instance_from_object_type('NOTI', $caddie_id);
				$labels[] = $caddie->name;
			}
			return implode(', ', $labels);
		}
		return '';
	}
	
	protected function _get_query_human_serials() {
		if(!empty($this->filters['serials'])) {
			$labels = array();
			foreach ($this->filters['serials'] as $serial_id) {
				$labels[] = notice::get_notice_title($serial_id);
			}
			return implode(', ', $labels);
		}
		return '';
	}
	
	protected function _get_query_human_abts_date_fin() {
		return $this->_get_query_human_interval_date('abts_date_fin');
	}
	
	protected function get_serialcirc_diff($id) {
		if(!isset($this->serialcirc_diff[$id])) {
			$this->serialcirc_diff[$id] = new serialcirc_diff($id);
		}
		return $this->serialcirc_diff[$id];
	}
	
	protected function get_location_label($location_id) {
		if (!$this->locations_labels) {
			$this->locations_labels = array();
			$query = 'select idlocation, location_libelle from docs_location';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->locations_labels[$row->idlocation] = $row->location_libelle;
				}
			}
		}
		return $this->locations_labels[$location_id];
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg["1150"].' : '.$msg['serial_circ_state_edit']);
	}
	
	protected function get_spreadsheet_title() {
		return "Circulations.xls";
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg["1150"].' : '.$msg['serial_circ_state_edit']."</h1>";
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/edit.php?categ=serials&sub=circ_state';
	}
}