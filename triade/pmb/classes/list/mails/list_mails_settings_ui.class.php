<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_mails_settings_ui.class.php,v 1.9.2.1.2.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/mails/mail_setting.class.php');

class list_mails_settings_ui extends list_ui {
	
	public function add_mail_setting($folder_path, $filename) {
		global $class_path;
		
		$classname = str_replace('.class.php', '', $filename);
		$reflectionClass = new ReflectionClass($classname);
		if($reflectionClass->isInstantiable()) {
			$id = mail_setting::get_id_from_classname($classname);
			if($id) {
				$mail_setting = new mail_setting($id);
				$mail_setting->set_folder_path($folder_path);
			} else {
				$mail_setting = new mail_setting();
				$mail_setting->set_folder_path($folder_path);
				$mail_setting->set_classname($classname);
				$mail_setting->set_properties_from_folder($folder_path);
			}
			
			//Application des filtres ici..
			
			if(!empty($this->filters['group'])) {
			    if($this->filters['group'] == 'OPAC' && strpos($mail_setting->get_folder_path(), '/opac/') === false) {
			        return;
			    } elseif($this->filters['group'] == 'Gestion' && strpos($mail_setting->get_folder_path(), '/opac/') !== false) {
			        return;
			    }
			}
			if(!empty($this->filters['sender']) && $this->filters['sender'] != $mail_setting->get_sender()) {
			    return;
			}
			$this->add_object($mail_setting);
		}
	}
	
	protected function _parsed_recursive_folder($folder_path){
		$folder_files = array();
		if(file_exists($folder_path)){
			$dh = opendir($folder_path);
			while(($file = readdir($dh)) !== false){
				if($file != "." && $file != ".." && $file != "CVS"){
					if(is_dir($folder_path.'/'.$file)){
						$folder_files[$file] = $this->_parsed_recursive_folder($folder_path.'/'.$file);
						if(!count($folder_files[$file])) {
							unset($folder_files[$file]);
						}
						ksort($folder_files);
						//C'est une classe PMB
					} elseif((strpos($file,".class.php") !== false && (strlen($file) - strlen(".class.php") == strrpos($file,".class.php")) && $file != 'mail_root.class.php')){
						$this->add_mail_setting($folder_path, $file);
					}
				}
			}
		}
		return $folder_files;
	}
	
	protected function _init_mails_settings() {
		global $class_path;
		
		$this->_parsed_recursive_folder($class_path.'/mail');
	}
	
	protected function fetch_data() {
	    $this->set_filters_from_form();
	    $this->set_applied_sort_from_form();
		$this->objects = array();
		$this->_init_mails_settings();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
	    $this->available_filters =
	    array('main_fields' =>
	        array(
	            'group' => 'mail_setting_group',
	            'sender' => 'mail_setting_sender',
	            'copy_bcc' => 'mail_setting_copy_bcc',
	        )
	    );
	    $this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
	    
	    $this->filters = array(
	        'group' => '',
	        'sender' => '',
	        'copy_bcc' => '',
	    );
	    parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('group');
	    $this->add_selected_filter('sender');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'group' => 'mail_setting_group',
					'classname' => 'mail_setting_classname',
					'sender' => 'mail_setting_sender',
					'copy_cc' => 'mail_setting_copy_cc',
					'copy_bcc' => 'mail_setting_copy_bcc',
					'reply' => 'mail_setting_reply',
					'associated_campaign' => 'mail_setting_associated_campaign',
					'actions' => 'mail_setting_actions'
			)
		);
	}
	
	/**
	 * Initialisation des colonnes éditables disponibles
	 */
	protected function init_available_editable_columns() {
	    $this->available_editable_columns = array(
	        'sender',
	        'copy_bcc',
	        'reply',
	    );
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('classname', 'asc');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'group');
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('classname');
		$this->add_column('sender');
		$this->add_column('copy_cc');
		$this->add_column('copy_bcc');
		$this->add_column('reply');
// 		$this->add_column('associated_campaign');
		$this->add_column('actions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('sender', 'edition_type', 'select');
		$this->set_setting_column('copy_bcc', 'datatype', 'boolean');
		$this->set_setting_column('copy_bcc', 'edition_type', 'radio');
		$this->set_setting_column('reply', 'edition_type', 'select');
		$this->set_setting_column('associated_campaign', 'datatype', 'boolean');
		$this->set_setting_column('associated_campaign', 'edition_type', 'radio');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'actions'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function get_search_filter_group() {
	    global $msg;
	    $options = array(
	        'Gestion' => 'Gestion',
	        'OPAC' => 'OPAC',
	    );
	    return $this->get_search_filter_simple_selection('', 'group', $msg["all"], $options);
	}
	
	protected function get_search_filter_sender() {
	    global $msg;
	    
	    $options = array(
	        'reader' => $msg['379'],
	        'user' => $msg['86'],
	        'docs_location' => $msg['location'],
	        'parameter' => $msg['opac_view_form_parameters'].' : biblio_name / biblio_email'
	    );
	    asort($options);
	    return $this->get_search_filter_simple_selection('', 'sender', $msg["all"], $options);
	}
	
	protected function get_search_filter_copy_bcc() {
	    global $msg;

	    return $this->get_search_filter_boolean_selection('copy_bcc', $msg["all"]);
	}
	
	public function set_filters_from_form() {
	    $this->set_filter_from_form('group');
	    $this->set_filter_from_form('sender');
	    $this->set_filter_from_form('copy_bcc');
	    parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
	    if($this->filters['group']) {
	        switch ($this->filters['group']) {
	            case 'OPAC':
	                $this->query_filters [] = 'mail_setting_classname LIKE "%opac%"';
	                break;
	            case 'Gestion':
	                $this->query_filters [] = 'mail_setting_classname NOT LIKE "%opac%"';
	                break;
	        }
	    }
	    $this->_add_query_filter_simple_restriction('send', 'mail_setting_sender');
	}
	
	protected function _get_query_human_group() {
	    if($this->filters['group']) {
	        return $this->filters['group'];
	    }
	    return '';
	}
	
	protected function _get_query_human_sender() {
	    global $msg;
	    
	    if($this->filters['sender']) {
	        switch ($this->filters['sender']) {
	            case 'reader':
	                return $msg['379'];
	            case 'user':
	                return $msg['86'];
	            case 'docs_location':
	                return $msg['location'];
	            case 'parameter':
	                return $msg['opac_view_form_parameters'].' : biblio_name / biblio_email';
	        }
	        return $this->filters['sender'];
	    }
	    return '';
	}
	
	protected function _get_object_property_group($object) {
		$display = '';
		if(strpos($object->get_folder_path(), '/opac/') !== false) {
			$display .= '[OPAC] ';
		} else {
			$display .= '[Gestion] ';
		}
		if(strpos($object->get_label(), ':') !== false) {
			$display .= trim(substr($object->get_label(), 0, strpos($object->get_label(), ':')));
		} else {
			$display .= $object->get_label();
		}
		return $display;
	}
	
	protected function _get_object_property_classname($object) {
		if(strpos($object->get_label(), ':') !== false) {
			return ucfirst(trim(substr($object->get_label(), strpos($object->get_label(), ':')+1)));
		}
		return $object->get_label();
	}
	
	protected function _get_object_property_sender($object) {
		global $msg;
		
		switch ($object->get_sender()) {
			case 'docs_location':
				return $msg['location'];
			case 'user':
				return $msg['86'];
			case 'reader':
				return $msg['379'];
			case 'parameter':
				return $msg['opac_view_form_parameters'].' : biblio_name / biblio_email';
			case 'accounting_bib_coords':
				return $msg['acquisition_coord_lib'];
		}
	}
	
	protected function _get_object_property_copy_cc($object) {
	    return $object->format_copy_cc();
	}
	
	protected function _get_object_property_copy_bcc($object) {
		global $msg;
		
		if($object->get_copy_bcc()) {
			return $msg['40'];
		} else {
			return $msg['39'];
		}
	}
	
	protected function _get_object_property_reply($object) {
		global $msg;
		
		switch ($object->get_reply()) {
			case 'user':
				return $msg['86'];
			case 'reader':
			    return $msg['379'];
			case 'docs_location':
			    return $msg['location'];
			default:
			    return '';
// 				return $msg['mail_setting_reply_unspecified'];
		}
	}
	
	protected function _get_object_property_associated_campaign($object) {
		global $msg;
		
		if($object->is_associated_campaign()) {
			return $msg['40'];
		} else {
			return $msg['39'];
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'actions':
				if($object->is_confidential()) {
					$content .= "<img src='".get_url_icon('lock.png')."' title='".htmlentities($msg["mail_setting_action_edit_locked"], ENT_QUOTES, $charset)."' />";
				} else {
				    $content .= $this->get_interface_button_small($msg['mail_setting_action_edit'], ['location' => static::get_controller_url_base()."&action=edit&classname=".$object->get_classname()]);
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$edit_link = array(
		    'showConfiguration' => static::get_controller_url_base()."&action=list_save"
		);
		$this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete"
		);
		$this->add_selection_action('delete', $msg['initialize'], '', $delete_link);
	}
	
	protected function get_options_editable_column($object, $property) {
	    global $msg;
	    
	    switch ($property) {
	        case 'sender':
	        case 'reply':
	            $options = array(
        	            array('value' => 'docs_location', 'label' => $msg['location']),
        	            array('value' => 'user', 'label' => $msg['86']),
        	            array('value' => 'reader', 'label' => $msg['379']),
        	            array('value' => 'parameter', 'label' => $msg['opac_view_form_parameters'].' : biblio_name / biblio_email'),
	            );
	            return $options;
	        default:
	            return parent::get_options_editable_column($object, $property);
	    }
	}
	
	protected function is_selected_object($object, $selected_objects = []) {
	    $is_selected = parent::is_selected_object($object, $selected_objects);
	    if(!$is_selected) {
	        if(method_exists($object, 'get_classname')) {
	            if(in_array($object->get_classname(), $selected_objects)) {
	                $is_selected = true;
	            }
	        }
	    }
	    return $is_selected;
	}
	
	public static function delete_object($type) {
		mail_setting::delete($type);
	}
}