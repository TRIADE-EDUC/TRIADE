<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_serialcirc_copy_ui.class.php,v 1.2 2023/12/21 13:43:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc/serialcirc_copy.class.php");

class list_opac_serialcirc_copy_ui extends list_opac_serialcirc_ui {
	
    protected $analysis_headers = [];
    
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
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
	    $this->filters = array(
	        'id_empr' => 0,
	    );
	    parent::init_filters($filters);
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
						'date' => 'serialcirc_ask_copy_date',
						'issue' => 'serialcirc_ask_copy_issue',
						'analysis' => 'serialcirc_ask_copy_analysis',
						'empr' => 'serialcirc_ask_copy_empr',
						'comment' => 'serialcirc_ask_copy_msg',
						'state' => 'serialcirc_ask_statut',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
	    $this->add_column('date');
	    $this->add_column('issue');
	    $this->add_column('analysis');
	    $this->add_column('empr');
	    $this->add_column('comment');
	    $this->add_column('state');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('date', 'datatype', 'date');
	}
	
	protected function _add_query_filters() {
	    $this->_add_query_filter_simple_restriction('id_empr', 'num_serialcirc_copy_empr');
	}
	
	protected function _get_object_property_issue($object) {
	    return bulletin_header($object->get_num_bulletin());
	}
	
	protected function _get_object_property_analysis($object) {
	    $analysis = $this->get_analysis_headers($object);
	    if(count($analysis)==0){
	        return "n/a";
	    }else{
	        $content = '';
	        foreach($analysis as $analysis_value) {
	            if($content) {
	                $content.=" / ";
	            }
	            $content .= $analysis_value;
	        }
	        return $content;
	    }
	}
	
	protected function _get_object_property_empr($object) {
	}
	
	protected function _get_object_property_state($object) {
	    global $msg;
	    
	    return $msg['serialcirc_copy_statut_'.$object->get_state()];
	}
	
	protected function get_cell_content($object, $property) {
	    global $opac_url_base;
	    
		$content = '';
		switch($property) {
			case 'issue':
				$issue = $this->_get_object_property_issue($object);
				$content .= "<a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$object->get_num_bulletin()."'>".$issue."</a>";
				break;
			case 'analysis':
			    $analysis = $this->get_analysis_headers($object);
			    if(count($analysis)==0){
			        $content .= "n/a";
			    }else{
			        foreach($analysis as $analysis_id=>$analysis_value) {
			            if($content) {
			                $content.="<br />";
			            }
			            $content .= "<a href='".$opac_url_base."/index.php?lvl=notice_display&id=".$analysis_id."'>".$analysis_value."</a>";
			        }
			    }
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_analysis_headers($object) {
	    global $opac_notice_affichage_class;
	    
	    if(!isset($this->analysis_headers[$object->get_id()])) {
	        $this->analysis_headers[$object->get_id()] = [];
	        if(!empty($object->get_analysis())) {
    	        $analysis_ids = unserialize($object->get_analysis());
    	        if(count($analysis_ids)){
    	            for($j=0 ; $j<count($analysis_ids) ; $j++){
    	                $notice = new $opac_notice_affichage_class($analysis_ids[$j]);
    	                $notice->do_header();
    	                $this->analysis_headers[$object->get_id()][$analysis_ids[$j]] = $notice->notice_header;
    	            }
    	        }
	        }
	    }
	    return $this->analysis_headers[$object->get_id()];
	}
}