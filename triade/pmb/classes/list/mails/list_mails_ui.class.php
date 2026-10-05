<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_mails_ui.class.php,v 1.17 2023/09/29 06:46:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/mail.class.php');
require_once($class_path.'/mails/mails.class.php');
require_once($class_path.'/campaigns/campaign.class.php');

class list_mails_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'select id_mail from mails';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new mail($row->id_mail);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'types' => 'mail_types',
						'from_name' => 'mail_from_name',
						'date' => 'mail_date',
						'sended' => 'mail_sended',
						'campaigns' => 'mail_campaigns',
// 						'from_uri' => 'mail_from_uri',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'types' => array(),
				'from_name' => array(),
				'date_start' => '',
				'date_end' => '',
				'sended' => 'all',
				'campaigns' => array(),
				'from_uri' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('types');
		$this->add_selected_filter('from_name');
		$this->add_selected_filter('date');
		$this->add_selected_filter('sended');
		$this->add_selected_filter('campaigns');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'type' => 'mail_type',
					'to_name' => 'mail_to_name',
					'to_mail' => 'mail_to_mail',
					'object' => 'mail_object',
					'from_name' => 'mail_from_name',
					'from_mail' => 'mail_from_mail',
					'copy_cc' => 'mail_copy_cc',
					'copy_bcc' => 'mail_copy_bcc',
					'reply_name' => 'mail_reply_name',
					'reply_mail' => 'mail_reply_mail',
					'date' => 'mail_date',
					'sended' => 'mail_sended',
					'error' => 'mail_error',
					'from_uri' => 'mail_from_uri',
					'campaign' => 'mail_campaign'
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date', 'desc');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'type');
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'to_name' :
	        case 'to_mail' :
	        case 'object' :
	        case 'from_name' :
	        case 'from_mail' :
	        case 'copy_cc' :
	        case 'copy_bcc' :
	        case 'reply_name' :
	        case 'reply_mail' :
	        case 'date' :
	        case 'sended' :
	        case 'error' :
	        case 'from_uri' :
	            return 'mail_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('types');
		$this->set_filter_from_form('from_name');
		$this->set_filter_from_form('date_start');
		$this->set_filter_from_form('date_end');
		$this->set_filter_from_form('sended');
		$this->set_filter_from_form('campaigns');
		$this->set_filter_from_form('from_uri');
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('to_name');
		$this->add_column('to_mail');
		$this->add_column('object');
		$this->add_column('from_name');
		$this->add_column('from_mail');
		$this->add_column('copy_cc');
		$this->add_column('copy_bcc');
		$this->add_column('reply_name');
		$this->add_column('reply_mail');
		$this->add_column('date');
		$this->add_column('sended');
		$this->add_column('campaign');
		$this->add_column('error');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', true);
		$this->set_setting_column('date', 'datatype', 'datetime');
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'from_name':
				$query = 'select distinct mail_from_name as id, mail_from_name as label from mails order by label';
				break;
			case 'campaigns':
				$query = 'SELECT distinct id_campaign as id, concat(campaign_date, " ", campaign_label) as label  FROM campaigns JOIN mails ON mails.mail_num_campaign = campaigns.id_campaign ORDER BY campaign_label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_types() {
		global $msg;
		$types = mail::get_list_types();
		return $this->get_search_filter_multiple_selection('', 'types', $msg["all"], $types);
	}
	
	protected function get_search_filter_from_name() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('from_name'), 'from_name', $msg["all"]);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function get_search_filter_sended() {
		global $msg;
		return "
			<input type='radio' id='".$this->objects_type."_sended_all' name='".$this->objects_type."_sended' value='all' ".($this->filters['sended'] == 'all' ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_sended_all'>".$msg['all']."</label>
			<input type='radio' id='".$this->objects_type."_sended_no' name='".$this->objects_type."_sended' value='no' ".($this->filters['sended'] == 'no' ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_sended_no'>".$msg['mail_sended_no']."</label>
			<input type='radio' id='".$this->objects_type."_sended_yes' name='".$this->objects_type."_sended' value='yes' ".($this->filters['sended'] == 'yes' ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_sended_yes'>".$msg['mail_sended_yes']."</label>";
	}
	
	protected function get_search_filter_campaigns() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('campaigns'), 'campaigns', $msg['campaigns_all']);
	}
	
	protected function get_search_filter_from_uri() {
		global $msg;
		$types_uri = mail::get_list_types_uri();
		return $this->get_search_filter_multiple_selection('', 'from_uri', $msg["all"], $types_uri);
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('types', 'mail_type');
		$this->_add_query_filter_multiple_restriction('from_name', 'mail_from_name');
		$this->_add_query_filter_interval_restriction('date', 'mail_date', 'datetime');
		if($this->filters['sended'] == 'yes') {
			$this->query_filters [] = 'mail_sended = 1';
		} elseif($this->filters['sended'] == 'no') {
			$this->query_filters [] = 'mail_sended = 0';
		}
		$this->_add_query_filter_multiple_restriction('campaigns', 'mail_num_campaign');
		if(!empty($this->filters['from_uri'])) {
			foreach ($this->filters['from_uri'] as $uri) {
				$this->query_filters [] = 'mail_from_uri LIKE "%'.$uri.'%"';
			}
		}
	}
	
	protected function _get_object_property_type($object) {
		$message = mails::get_message('mail_'.$object->get_type());
		if($message) {
			return $message;
		}
		return $object->get_type();
	}
	
	protected function _get_object_property_to_mail($object) {
		return implode('; ', $object->get_to_mail());
	}
	
	protected function _get_object_property_copy_cc($object) {
		return implode('; ', $object->get_copy_cc());
	}
	
	protected function _get_object_property_copy_bcc($object) {
		return implode('; ', $object->get_copy_bcc());
	}
	
	protected function _get_object_property_sended($object) {
		global $msg;
		if($object->get_sended()) {
			return $msg['mail_sended_yes'];
		} else {
			return $msg['mail_sended_no'];
		}
	}
	
	protected function _get_object_property_campaign($object) {
		if($object->get_num_campaign()) {
			$campaign = new campaign($object->get_num_campaign());
			return $campaign->get_label();
		}
		return '';
	}
	
	protected function _get_object_property_from_uri($object) {
		$types_uri = mail::get_list_types_uri();
		foreach ($types_uri as $uri=>$label) {
			if(strpos($object->get_from_uri(), $uri) !== false) {
				return $label;
			}
		}
		return $object->get_from_uri();
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $base_path;
		
		$attributes = array();
		switch($property) {
			case 'campaign':
				if(SESSrights & EDIT_AUTH) {
					$attributes['href'] = $base_path."/edit.php?categ=opac&sub=campaigns&action=view&id=".$object->get_num_campaign();
				}
				break;
			default:
				break;
		}
		return $attributes;
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'campaigns':
				return "SELECT concat(campaign_date, ' ', campaign_label) as label FROM campaigns where id_campaign IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}
	
	protected function _get_query_human_date() {
		return $this->_get_query_human_interval_date('date');
	}
	
	protected function _get_query_human_sended() {
		global $msg;
		if($this->filters['sended'] == 'yes') {
			return $msg['mail_sended_yes'];
		} elseif($this->filters['sended'] == 'no') {
			return $msg['mail_sended_no'];
		}
		return '';
	}
	
	protected function _get_query_human_from_uri() {
		if(!empty($this->filters['from_uri'])) {
			$types_uri = mail::get_list_types_uri();
			foreach ($types_uri as $uri=>$label) {
				foreach ($this->filters['from_uri'] as $from_uri) {
					if(strpos($from_uri, $uri) !== false) {
						return $label;
					}
				}
			}
		}
		return '';
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete"
		);
		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
	}
	
	public static function delete_object($id) {
		$mail = new mail($id);
		$mail->delete();
	}
}