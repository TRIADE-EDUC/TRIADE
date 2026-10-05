<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_contact_forms_parameters_ui.class.php,v 1.1.2.2 2024/09/26 08:02:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_contact_forms_parameters_ui extends list_ui {
	
    protected static $num_contact_form;
    
    protected static $contact_form_parameters;
    
    public function add_contact_form_field_parameter($name) {
        global $msg;
        $parameter = array(
            'name' => $name,
            'label' => $msg['admin_opac_contact_form_parameter_'.$name],
            'parameters' => static::$contact_form_parameters['fields'][$name],
            'value' => ''
        );
        $this->add_object((object) $parameter);
    }
    
    public function add_contact_form_simple_parameter($name) {
        global $msg;
        $parameter = array(
            'name' => $name,
            'label' => $msg['admin_opac_contact_form_parameter_'.$name],
            'parameter' => array(),
            'value' => (isset(static::$contact_form_parameters[$name]) ? static::$contact_form_parameters[$name] : '')
        );
        $this->add_object((object) $parameter);
    }
    
    
	protected function fetch_data() {
		$this->objects = array();
		$this->add_contact_form_field_parameter('name');
		$this->add_contact_form_field_parameter('firstname');
		$this->add_contact_form_field_parameter('group');
		$this->add_contact_form_field_parameter('email');
		$this->add_contact_form_field_parameter('tel');
		$this->add_contact_form_field_parameter('attachments');
		$this->add_contact_form_simple_parameter('recipients_mode');
		$this->add_contact_form_simple_parameter('email_object_free_entry');
		$this->add_contact_form_simple_parameter('email_content');
		$this->add_contact_form_simple_parameter('confirm_email');
		$this->add_contact_form_simple_parameter('permalink');
		$this->add_contact_form_simple_parameter('display_fields_errors');
		$this->add_contact_form_simple_parameter('consent_message');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
				        'label' => 'admin_opac_contact_form_parameter_label',
				        'value' => 'admin_opac_contact_form_parameter_value',
				)
		);
		
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('label');
		$this->add_column('value');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('label', 'text', array('italic' => true));
		$this->set_setting_column('value', 'display_mode', 'edition');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label', 'value'
		);
	}
	
	public function get_display_search_form() {
	    return '';
	}
	
	protected function get_cell_edition_content($object, $property) {
	    global $msg, $charset;
	    global $opac_url_base;
	    
	    $content = '';
		switch($property) {
		    case 'value':
		        switch ($object->name) {
		            case 'name':
		            case 'firstname':
		            case 'group':
		            case 'email':
		            case 'tel':
		            case 'attachments':
		                $name = $object->name;
		                $content .= htmlentities($msg['admin_opac_contact_form_parameter_display_field'], ENT_QUOTES, $charset)."
                			<input type='checkbox' id='parameter_display_field_".$name."' name='parameter_fields[".$name."][display]' value='1' ".($object->parameters['display'] ? "checked='checked'" : "")." ".($object->parameters['readonly'] ? "disabled='disabled'" : "")." />
                			".($object->parameters['readonly'] ? "<input type='hidden' id='parameter_display_field_".$name."' name='parameter_fields[".$name."][display]' value='1' />" : "")."
                			".htmlentities($msg['admin_opac_contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset)."
                			<input type='checkbox' id='parameter_mandatory_field_".$name."' name='parameter_fields[".$name."][mandatory]' value='1' ".($object->parameters['mandatory'] ? "checked='checked'" : "")." ".($object->parameters['readonly'] ? "disabled='disabled'" : "")." />
                			".($object->parameters['readonly'] ? "<input type='hidden' id='parameter_mandatory_field_".$name."' name='parameter_fields[".$name."][mandatory]' value='1' />" : "")."
                		";
		                break;
		            case 'recipients_mode':
		                $content .= contact_form_parameters::gen_recipients_mode_selector($object->value);
		                break;
		            case 'email_object_free_entry':
		            case 'confirm_email':
		            case 'display_fields_errors':
		            case 'consent_message':
		                $name = $object->name;
		                $content .= "
                            <input type='checkbox' id='parameter_".$name."' name='parameter_".$name."' class='switch' value='1' ".($object->value ? "checked='checked'" : "")." />
                    		<label for='parameter_".$name."'>".htmlentities($msg['admin_opac_contact_form_parameter_confirm_email_active'], ENT_QUOTES, $charset)."</label>";
		                break;
		            case 'email_content':
		                $content .= "<textarea id='parameter_email_content' name='parameter_email_content' class='saisie-50em' rows='15' cols='55'>".$object->value."</textarea>";
		                break;
		            case 'permalink':
		                $content .= "<a href = '".$opac_url_base."index.php?lvl=contact_form&id=".static::$num_contact_form."' target='_blank'>".$opac_url_base."index.php?lvl=contact_form&id=".static::$num_contact_form."</a>";
	                    break;
		        }
			default :
				break;
		}
		return $content;
	}
	
	protected function get_button_add() {
		return '';
	}
	
	public static function get_controller_url_base() {
	    return parent::get_controller_url_base().'&id='.static::$num_contact_form;
	}
	
	public static function set_num_contact_form($num_contact_form) {
	    static::$num_contact_form = intval($num_contact_form);
	}
	
	public static function set_contact_form_parameters($contact_form_parameters) {
	    static::$contact_form_parameters = $contact_form_parameters;
	}
}