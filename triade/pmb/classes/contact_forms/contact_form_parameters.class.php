<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_parameters.class.php,v 1.6.6.1 2024/09/26 08:02:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class contact_form_parameters {
	
	protected $id;
	
	/**
	 * Liste des paramètres
	 */
	protected $parameters;
	
	protected $updated_in_database = true;
	
	protected $message = "";
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->_init_parameters();
		$this->fetch_data();
	}
	
	protected function _get_field($type='text', $display=0, $mandatory=0, $readonly=0) {
		return array(
			'type' => $type,
			'display' => $display,
			'mandatory' => $mandatory,
			'readonly' => $readonly
		);
	}
	
	protected function _init_parameters() {
		$this->parameters = array(
				'fields' => array(
					'name' => $this->_get_field('text', 1, 1),
					'firstname' => $this->_get_field('text', 1, 1),
					'group' => $this->_get_field(),
					'email' => $this->_get_field('email', 1, 1, 1),
					'tel' => $this->_get_field(),
				    'attachments' => $this->_get_field('file')
				),
				'recipients_mode' => 'by_persons',
                'email_object_free_entry' => 0,
                'email_content' => $this->_get_email_content_template(),
				'confirm_email' => 1,
				'display_fields_errors' => 0,
                'consent_message' => 1
		);
	}
	
	protected function fetch_data() {
		$query = 'select contact_form_parameters from contact_forms where id_contact_form='.$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->contact_form_parameters) {
				$parameters = encoding_normalize::json_decode($row->contact_form_parameters, true);
				if(is_array($parameters)) {
    				foreach ($this->parameters['fields'] as $name=>$field) {
    				    if(!empty($parameters['fields'][$name]) && is_array($parameters['fields'][$name])) {
    						if(!$this->parameters['fields'][$name]['readonly']) {
    							$this->parameters['fields'][$name]['display'] = $parameters['fields'][$name]['display'];
    							$this->parameters['fields'][$name]['mandatory'] = $parameters['fields'][$name]['mandatory'];
    						}
    					} else {
    						$this->updated_in_database = false;
    					}
    				}
    				if($parameters['recipients_mode']) {
    					$this->parameters['recipients_mode'] = $parameters['recipients_mode'];
    				}
    				if($parameters['email_object_free_entry']) {
    				    $this->parameters['email_object_free_entry'] = $parameters['email_object_free_entry'];
    				}
    				if($parameters['email_content']) {
    					$this->parameters['email_content'] = $parameters['email_content'];
    				}
    				if($parameters['confirm_email'] == 0) {
    					$this->parameters['confirm_email'] = 0;
    				}
    				if($parameters['display_fields_errors']) {
    				    $this->parameters['display_fields_errors'] = $parameters['display_fields_errors'];
    				}
				} else {
				    $this->updated_in_database = false;
				}
				if(!$this->updated_in_database) {
					$this->save();
					$this->updated_in_database = true;
				}
			}
		}
	}
	
	public static function gen_recipients_mode_selector($selected='', $onchange='') {
		global $msg, $charset;
		return "
			<select name='parameter_recipients_mode' onchange=\"".$onchange."\">
				<option value='by_persons' ".($selected == 'by_persons' ? "selected='selected'" : "").">".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode_by_persons'], ENT_QUOTES, $charset)."</option>
				<option value='by_objects' ".($selected == 'by_objects' ? "selected='selected'" : "").">".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode_by_objects'], ENT_QUOTES, $charset)."</option>
				<option value='by_locations' ".($selected == 'by_locations' ? "selected='selected'" : "").">".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode_by_locations'], ENT_QUOTES, $charset)."</option>
			</select>
		";
	}
	
	/**
	 * Affiche la recherche + la liste des décomptes
	 */
	public function get_display_list() {
		global $msg, $charset, $base_path;
		global $current_module;
		
		list_contact_forms_parameters_ui::set_num_contact_form($this->id);
		list_contact_forms_parameters_ui::set_contact_form_parameters($this->parameters);
		
		$display = "
		<form class='form-".$current_module."' action='./admin.php?categ=contact_forms&sub=parameters&action=save&id=".$this->id."' method='post'>
			<div class='form-contenu'>";
		if($this->message != "") {
			$display .= "<span class='erreur'>".htmlentities($this->message, ENT_QUOTES, $charset)."</span>";
		}
		//Affichage de la liste des paramètres
		$display .= list_contact_forms_parameters_ui::get_instance()->get_display_list();
		$display .= "
			</div>
			<div class='row'>
				<input type='button' class='bouton' value='".$msg['76']."' onclick=\"document.location='".$base_path."/admin.php?categ=contact_forms'\" />
				<input type='submit' class='bouton' value='".$msg['admin_opac_contact_form_parameters_save']."' />
			</div>
		</form>";
		return $display;
	}
	
	public function set_properties_from_form() {
		global $parameter_fields;
		global $parameter_recipients_mode;
		global $parameter_email_object_free_entry;
		global $parameter_email_content;
		global $parameter_confirm_email;
		global $parameter_display_fields_errors;
		global $parameter_consent_message;
		
		if(is_array($parameter_fields)) {
			foreach ($this->parameters['fields'] as $name=>$field) {
				if(isset($parameter_fields[$name]['display'])) {
					$this->parameters['fields'][$name]['display'] = $parameter_fields[$name]['display'];
				} else {
					$this->parameters['fields'][$name]['display'] = 0;
				}
				if(isset($parameter_fields[$name]['mandatory'])) {
					$this->parameters['fields'][$name]['mandatory'] = $parameter_fields[$name]['mandatory'];
				} else {
					$this->parameters['fields'][$name]['mandatory'] = 0;
				}
			}
		}
		$this->parameters['recipients_mode'] = $parameter_recipients_mode;
		$this->parameters['email_object_free_entry'] = ($parameter_email_object_free_entry ? 1 : 0);
		$this->parameters['display_fields_errors'] = ($parameter_display_fields_errors ? 1 : 0);
		if(trim($parameter_email_content)) {
			$this->parameters['email_content'] = stripslashes($parameter_email_content);
		} else {
			$this->parameters['email_content'] = $this->_get_email_content_template();
		}
		$this->parameters['confirm_email'] = ($parameter_confirm_email ? 1 : 0);
		$this->parameters['consent_message'] = ($parameter_consent_message ? 1 : 0);
	}
	
	public function save() {
		global $msg;
		
		$query = "update contact_forms set
				contact_form_parameters = '".addslashes(encoding_normalize::json_encode($this->parameters))."'
				where id_contact_form='".$this->id."'";
		$result = pmb_mysql_query($query);
		if($result) {
			$this->message = $msg['admin_opac_contact_form_parameters_save_success'];
			return true;
		} else {
			$this->message = $msg['admin_opac_contact_form_parameters_save_error'];
			return false;
		}
	}
	
	protected function _get_email_content_template() {
		global $include_path;
		
		$email_content = '';
		if (file_exists($include_path.'/templates/contact_forms/email_content_subst.html')) {
			$template_path =  $include_path.'/templates/contact_forms/email_content_subst.html';
		} else {
			$template_path =  $include_path.'/templates/contact_forms/email_content.html';
		}
		if (file_exists($template_path)) {
			$email_content = file_get_contents($template_path);
		}
		return $email_content;
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
	
	public function get_message() {
		return $this->message;
	}
	
	public function set_message($message) {
		$this->message = $message;
	}
}