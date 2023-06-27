<?php
use Sabre\DAV\ObjectTree;
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form.class.php,v 1.5 2018-09-18 13:00:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_parameters.class.php");
require_once($class_path."/contact_form/contact_form_objects.class.php");
require_once($class_path."/contact_form/contact_form_object.class.php");
require_once($class_path."/contact_form/contact_form_recipients.class.php");

require_once($include_path."/templates/contact_form/contact_form.tpl.php");
require_once($include_path."/h2o/h2o.php");
require_once($include_path."/mail.inc.php");

class contact_form {
	
	/**
	 * Tableau des paramètres (administration > Formulaire de contact > Paramètres)
	 * @var contact_form_parameters
	 */
	protected $parameters;
	
	/**
	 * Elements du formulaire suite à la validation
	 * @var Object
	 */
	protected $form_fields;
	
	/**
	 * Tableau de messages à afficher
	 */
	protected $messages;
	
	/**
	 * Envoyé (Oui / Non)
	 * @var Boolean
	 */
	protected $sended;
	
	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$contact_form_parameters = new contact_form_parameters();
		$this->parameters = $contact_form_parameters->get_parameters();
		$this->form_fields = new stdClass();
		$this->messages = array();
		$this->sended = false;
	}
	
	/**
	 * Pré-remplissage du formulaire (avec la globale associée) 
	 */
	protected function _get_global_field($name) {
		
		$value = '';
		switch ($name) {
			case 'name':
				$value = 'empr_nom';
				break;
			case 'firstname':
				$value = 'empr_prenom';
				break;
			case 'email':
				$value = 'empr_mail';
				break;
		}
		if($value) {
			global ${$value};
			return ${$value};
		} else {
			return '';
		}
	}

	/**
	 * Parcours des champs à afficher
	 */
	protected function _get_display_fields() {
		global $msg, $charset;
		
		$display_fields = "";
		if(is_array($this->parameters['fields'])) {
			foreach($this->parameters['fields'] as $name=>$field) {
				if($field['display']) {
					$display_fields .= "
					<div class='contact_form_parameter_".$name."'>
						<div class='colonne2'>
							<label for='contact_form_parameter_".$name."'>".htmlentities($msg['contact_form_parameter_'.$name], ENT_QUOTES, $charset)."</label>";
					if($field['mandatory']) {
						$display_fields .= htmlentities($msg['contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset);
					}
					$display_fields .= "
						</div>
						<div class='colonne2'>";
						switch ($field['type']) {
							case 'email':
								$display_fields .= "<input type='email' id='contact_form_parameter_".$name."' name='contact_form_parameter_".$name."' value='".$this->_get_global_field($name)."' ".($field['mandatory'] ? "required='true'" : "")." />";
								break;
							case 'text':
							default:
								$display_fields .= "<input type='text' id='contact_form_parameter_".$name."' name='contact_form_parameter_".$name."' data-dojo-type='dijit/form/TextBox' value='".$this->_get_global_field($name)."' ".($field['mandatory'] ? "required='true'" : "")." />";
								break;
						}
							
					$display_fields .= "</div>
					</div>
					<div class='contact_form_separator'>&nbsp;</div>";
				}
			}
		}
		return $display_fields;
	}
	
	/**
	 * Formulaire
	 */
	public function get_form() {
		global $msg, $charset;
		global $contact_form_form_tpl;
		
		$form = $contact_form_form_tpl;
		$contact_form_recipients = new contact_form_recipients($this->parameters['recipients_mode']);
		$form = str_replace("!!recipients!!", $contact_form_recipients->get_form(), $form);
		
		$form = str_replace("!!title!!", htmlentities($msg['contact_form_title'], ENT_QUOTES, $charset), $form);
		$form = str_replace("!!fields!!", $this->_get_display_fields(), $form);
		
		$contact_form_objects = new contact_form_objects();
		$form = str_replace("!!objects_label!!", htmlentities($msg['contact_form_object'], ENT_QUOTES, $charset), $form);
		$form = str_replace("!!objects_selector!!", $contact_form_objects->gen_selector(), $form);
		
		return $form;
	}
	
	/**
	 * Vérification des données soumises
	 */
	public function check_form() {
		global $msg;
		
		if(md5($this->form_fields->contact_form_verifcode) != $_SESSION['image_random_value']) {
			$this->messages[] = $msg['contact_form_verifcode_mandatory'];
		}
		//Remove random value
		$_SESSION['image_random_value'] = '';
		//spécifique au mode par objets 
		if(empty($this->form_fields->contact_form_recipients) && ($this->parameters['recipients_mode'] == 'by_objects')) {
			if($this->form_fields->contact_form_objects) {
				$this->form_fields->contact_form_recipients = $this->form_fields->contact_form_objects; 
			}
		}
		if(!isset($this->form_fields->contact_form_recipients) || ($this->form_fields->contact_form_recipients === '')) {
			$this->messages[] = $msg['contact_form_recipient_mandatory'];
		}
		if(is_array($this->parameters['fields'])) {
			foreach ($this->parameters['fields'] as $name=>$field) {
				$property = 'contact_form_parameter_'.$name;
				if($field['mandatory'] && (empty($this->form_fields->{$property}) || (trim($this->form_fields->{$property}) == ''))) {
					$this->messages[] = $msg[$property.'_mandatory'];
				}
			}
		}
		if(!$this->form_fields->contact_form_objects) {
			$this->messages[] = $msg['contact_form_object_mandatory'];
		}
		if(!trim($this->form_fields->contact_form_text)) {
			$this->messages[] = $msg['contact_form_text_mandatory'];
		}
		if(count($this->messages)) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Envoi de mail
	 */
	public function send_mail() {
		global $msg, $charset;
		global $include_path;
		
		$contact_form_recipients = new contact_form_recipients($this->parameters['recipients_mode']);
		$recipients = $contact_form_recipients->get_recipients();
		$recipient_info = $recipients[$this->parameters['recipients_mode']][$this->form_fields->contact_form_recipients];
		$transmitter_info = array();
		if($this->form_fields->contact_form_parameter_name) {
			$transmitter_info['name'] = $this->form_fields->contact_form_parameter_name." ".$this->form_fields->contact_form_parameter_firstname;
		} else {
			$transmitter_info['name'] = $this->form_fields->contact_form_parameter_email;
		}
		$transmitter_info['email'] = $this->form_fields->contact_form_parameter_email;
		$contact_form_object = new contact_form_object($this->form_fields->contact_form_objects);
		$content = h2o($this->parameters['email_content'])->render(array('contact_form' => $this->form_fields));
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		$this->sended = mailpmb($recipient_info['name'], $recipient_info['email'], $contact_form_object->get_label(), $content, $transmitter_info['name'], $transmitter_info['email'], $headers, $recipient_info['copy_email']);
		if($this->sended) {
			$this->messages[] = $msg['contact_form_send_success_msg'];
			if($this->parameters['confirm_email']) {
				$sended_copy = mailpmb($transmitter_info['name'], $transmitter_info['email'], $contact_form_object->get_label()." ".$msg['contact_form_send_copy_suffix'], $content, $recipient_info['name'], ($recipient_info['transmitter_email'] ? $recipient_info['transmitter_email'] : $recipient_info['email']), $headers);
				if($sended_copy) {
					$this->messages[] = $msg['contact_form_send_copy_success_msg'];
				} else {
					$this->messages[] = $msg['contact_form_send_copy_error_msg'];
				}
			}
		} else {
			$this->messages[] = $msg['contact_form_send_error_msg'];
		}
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
	
	public function get_form_fields() {
		return $this->form_fields;
	}
	
	public function set_form_fields($form_fields) {
		$this->form_fields = $form_fields;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function set_messages($messages) {
		$this->messages = $messages;
	}
	
	public function is_sended() {
		return $this->sended;
	}
}