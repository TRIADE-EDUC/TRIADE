<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_serialcirc_ask_ui.class.php,v 1.2 2023/12/21 13:43:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc.class.php");

class list_opac_serialcirc_ask_ui extends list_opac_serialcirc_ui {
	
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
		$this->add_applied_sort('type');
		$this->add_applied_sort('status');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'date' => 'serialcirc_ask_date',
						'empr' => 'serialcirc_ask_empr',
						'type' => 'serialcirc_ask_type',
						'serial' => 'serialcirc_serial_name',
						'status' => 'serialcirc_ask_statut',
						'comment' => 'serialcirc_ask_msg',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
	    $this->add_column('type');
	    $this->add_column('serial');
		$this->add_column('empr');
		$this->add_column('date');
		$this->add_column('status');
		$this->add_column('comment');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('date', 'datatype', 'date');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
                'id_empr' => 0,
				'location' => '',
				'type' => -1,
				'status' => -1,
		);
		parent::init_filters($filters);
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
	    $this->_add_query_filter_simple_restriction('id_empr', 'num_serialcirc_ask_empr', 'integer');
	    $this->_add_query_filter_simple_restriction('location', 'empr_location', 'integer');
		if($this->filters['type'] !== -1) {
			$this->query_filters [] = 'serialcirc_ask_type = "'.$this->filters['type'].'"';
		}
		if($this->filters['status'] !== -1) {
			$this->query_filters [] = 'serialcirc_ask_statut = "'.$this->filters['status'].'"';
		}
	}
			
	protected function _get_object_property_empr($object) {
	}
	
	protected function _get_object_property_type($object) {
		global $msg;
		return $msg['serialcirc_ask_type_'.$object->type];
	}
	
	protected function _get_object_property_serial($object) {
	    if ($object->num_serial) {
	        $query = "select tit1 from notices where notice_id = ".$object->num_serial;
	        $res= pmb_mysql_query($query);
	        if(pmb_mysql_num_rows($res)){
	            return pmb_mysql_result($res,0,0);
	        }
	    }else{
	        $serialcirc = new serialcirc($object->num_serialcirc);
	        return $serialcirc->get_serial_title();
	    }
	    return '';
	}
	
	protected function _get_object_property_status($object) {
		global $msg;
		return $msg['serialcirc_ask_statut_'.$object->status];
	}
	
	protected function get_cell_content($object, $property) {
	    global $charset;
	    global $opac_url_base;
	    
		$content = '';
		switch($property) {
			case 'serial':
			    $serial = $this->_get_object_property_serial($object);
				$content .= "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$object->num_serial."'>".htmlentities($serial,ENT_QUOTES,$charset)."</a>";
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
			return $msg['serialcirc_ask_type_'.$this->filters['type']];
		}
		return '';
	}
	
	protected function _get_query_human_status() {
		global $msg;
		if($this->filters['status'] !== -1) {
			return $msg['serialcirc_ask_statut_'.$this->filters['status']];
		}
		return '';
	}
}