<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form.class.php,v 1.10.2.1 2024/09/26 11:48:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once($class_path."/contact_forms/contact_form_parameters.class.php");
require_once($class_path."/contact_forms/contact_form_objects.class.php");
require_once($class_path."/contact_forms/contact_form_object.class.php");
require_once($class_path."/contact_forms/contact_form_recipients.class.php");
require_once($base_path."/includes/securimage/securimage.php");

require_once($include_path."/templates/contact_forms/contact_form.tpl.php");
require_once($include_path."/h2o/h2o.php");
require_once($include_path."/mail.inc.php");

class contact_form {
	
	protected $id;
	
	protected $label;
	
	protected $desc;
	
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
	 * Tableau des messages d'erreur par champ
	 */
	protected $fields_errors;
	
	/**
	 * Envoyé (Oui / Non)
	 * @var Boolean
	 */
	protected $sended;
	
	/**
	 * Constructeur
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->label = '';
		$this->desc = '';
		$query = "SELECT * FROM contact_forms WHERE id_contact_form = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->contact_form_label;
			$this->desc = $row->contact_form_desc;
		}
		$contact_form_parameters = new contact_form_parameters($this->id);
		$this->parameters = $contact_form_parameters->get_parameters();
		$this->form_fields = new stdClass();
		$this->messages = array();
		$this->fields_errors = array();
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
			$nb_mandatory_fields = 0;
			$nb_visible_fields = 0;
			foreach($this->parameters['fields'] as $name=>$field) {
				
				if($field['display']) {
					$nb_visible_fields++;
					$display_fields .= "
					<div class='contact_form_parameter_".$name."'>
						<div class='colonne2'>
							<label for='contact_form_parameter_".$name."'>".htmlentities($msg['contact_form_parameter_'.$name], ENT_QUOTES, $charset)."</label>";
					if($field['mandatory']) {
						$display_fields .= ' ' . htmlentities($msg['contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset);
						$nb_mandatory_fields++;
					}
					$autocomplete_fields = [
						'firstname' => 'given-name',
						'name' => 'family-name',
						'tel' => 'tel',
						'group' => 'off'
					];

					$display_fields .= "
						</div>
						<div class='colonne2'>";
						switch ($field['type']) {
							case 'email':
								$display_fields .= "<input type='email' id='contact_form_parameter_".$name."' name='contact_form_parameter_".$name."' autocomplete='email' value='".$this->_get_global_field($name)."' ".($field['mandatory'] ? "required='true'" : "")." />";
								break;
							case 'file':
							    $display_fields .=  static::get_attachments_field();
							    break;
							case 'text':
							default:
								$display_fields .= "<input type='text' id='contact_form_parameter_".$name."' name='contact_form_parameter_".$name."' autocomplete='".$autocomplete_fields[$name]."' data-dojo-type='dijit/form/TextBox' value='".$this->_get_global_field($name)."' ".($field['mandatory'] ? "required='true'" : "")." />";
								break;
						}
							
					$display_fields .= "</div>
					</div>
					<div class='contact_form_separator'>&nbsp;</div>";
				}
			}
			// ajout de message indiquant si tous les champs ou seulement quelques-un sont obligatoires
			$msg_mandatory = '';
			if($nb_mandatory_fields === $nb_visible_fields){
				$msg_mandatory = "<p id='contact_form_mandatory'>".htmlentities($msg['contact_form_mandatory_fields_all'], ENT_QUOTES, $charset)."</p>";
			}else{
				$msg_mandatory = "<p id='contact_form_mandatory'>".htmlentities($msg['contact_form_mandatory_fields_not_all'], ENT_QUOTES, $charset)."</p>";
			}
			
		}
		$display_fields = $msg_mandatory . $display_fields;
		return $display_fields;
	}
	
	/**
	 * Formulaire
	 */
	public function get_form() {
		global $msg, $charset;
		global $contact_form_form_tpl;
		global $contact_form_show_errors;
		global $contact_form_show_fields_errors;
		
		$form = $contact_form_show_errors;
		if(!empty($this->parameters['display_fields_errors'])) {
		    $form = $contact_form_show_fields_errors;
		}
		$form .= $contact_form_form_tpl;
		$contact_form_recipients = new contact_form_recipients($this->id, $this->parameters['recipients_mode']);
		$form = str_replace("!!id!!", $this->id, $form);
		$form = str_replace("!!recipients!!", $contact_form_recipients->get_form(), $form);
		
		$form = str_replace("!!title!!", common::format_title($msg['contact_form_title']), $form);
		$form = str_replace("!!fields!!", $this->_get_display_fields(), $form);
		
		$contact_form_objects = new contact_form_objects($this->id);
		$form = str_replace("!!objects_label!!", htmlentities($msg['contact_form_object'], ENT_QUOTES, $charset), $form);
		$email_object_free_entry = 0;
		if(isset($this->parameters['email_object_free_entry'])) {
		    $email_object_free_entry = $this->parameters['email_object_free_entry'];
		}
		if(count($contact_form_objects->get_objects())) {
		    $form = str_replace("!!objects_selector!!", $contact_form_objects->gen_selector($email_object_free_entry), $form);
		} else {
		    $form = str_replace("!!objects_selector!!", 
		                          htmlentities($msg['contact_form_object_other'], ENT_QUOTES, $charset)."
                                <input type='hidden' name='contact_form_objects' value='0' />
                                <script>
									addLoadEvent(function() {
										contact_form_object_free_entry_only();
									});
								</script>"
            , $form);
		}
		
		$contact_form_object = $contact_form_objects->get_selected_object();
		$form = str_replace("!!message!!", (is_object($contact_form_object) ? $contact_form_object->get_translated_message() : ''), $form);
		$form = str_replace("!!captcha!!", emprunteur_display::get_captcha('contact_form_verifcode'), $form);
		if(!empty($this->parameters['consent_message'])) {
		    $form = str_replace("!!consent_message!!", static::get_consent_message_field(), $form);
		} else {
		    $form = str_replace("!!consent_message!!", '', $form);
		}
		return $form;
	}
	
	/**
	 * Vérification des données soumises
	 */
	public function check_form() {
	    global $msg;
		
		//captcha
		$securimage = new Securimage();
		if (!$securimage->check($this->form_fields->contact_form_verifcode)) {
			$this->messages[] = $msg['contact_form_verifcode_mandatory'];
			$this->fields_errors["contact_form_verifcode"] = $msg['contact_form_verifcode_mandatory'];
		}
		//Remove random value
		$_SESSION['image_random_value'] = '';
		//spécifique au mode par objets 
		if(empty($this->form_fields->contact_form_recipients) && ($this->parameters['recipients_mode'] == 'by_objects')) {
			if($this->form_fields->contact_form_objects) {
				$this->form_fields->contact_form_recipients = $this->form_fields->contact_form_objects; 
			} elseif(!empty($this->form_fields->contact_form_object_free_entry)) {
			    $this->form_fields->contact_form_recipients = 0;
			}
		}
		if(!isset($this->form_fields->contact_form_recipients) || ($this->form_fields->contact_form_recipients === '')) {
			$this->messages[] = $msg['contact_form_recipient_mandatory'];
			$this->fields_errors["contact_form_recipients"] = $msg['contact_form_recipient_mandatory'];
		}
		if(is_array($this->parameters['fields'])) {
			foreach ($this->parameters['fields'] as $name=>$field) {
				$property = 'contact_form_parameter_'.$name;
				if($field['mandatory']) {
					$fieldValue = is_string($this->form_fields->{$property}) ? trim($this->form_fields->{$property}) : $this->form_fields->{$property};
					if(empty($fieldValue)) {
						$this->messages[] = $msg[$property.'_mandatory'];
						$this->fields_errors[$property] = $msg[$property.'_mandatory'];
					}
				}
			}
		}
		if(!$this->form_fields->contact_form_objects && empty($this->form_fields->contact_form_object_free_entry)) {
		    $this->messages[] = $msg['contact_form_object_free_mandatory'];
		    $this->fields_errors["contact_form_object_free_entry"] = $msg['contact_form_object_free_mandatory'];
		}
		if(!trim($this->form_fields->contact_form_text)) {
			$this->messages[] = $msg['contact_form_text_mandatory'];
			$this->fields_errors["contact_form_text"] = $msg['contact_form_text_mandatory'];
		}

		if(!is_valid_mail($this->form_fields->contact_form_parameter_email)){
			$this->messages[] = $msg['contact_form_error_email_adress'];
			$this->fields_errors["contact_form_parameter_email"] = $msg['contact_form_error_email_adress'];
		}
		if(!empty($this->parameters['consent_message'])) {
		    if(empty($this->form_fields->contact_form_consent_message)) {
		        $this->messages[] = $msg['contact_form_consent_message_mandatory'];
		        $this->fields_errors["contact_form_consent_message"] = $msg['contact_form_consent_message_mandatory'];
		    }
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
		global $msg;
		
		$mail_opac_contact_form = new mail_opac_contact_form();
		$mail_opac_contact_form->set_contact_form($this);

		$this->sended = $mail_opac_contact_form->send_mail();
		if($this->sended) {
			$this->messages[] = $msg['contact_form_send_success_msg'];
			if($this->parameters['confirm_email']) {
			    $mail_opac_contact_form_confirm = new mail_opac_contact_form_confirm();
			    $mail_opac_contact_form_confirm->set_contact_form($this);
			    $sended_copy = $mail_opac_contact_form_confirm->send_mail();
			    if($sended_copy) {
					$this->messages[] = $msg['contact_form_send_copy_success_msg'];
				} else {
					$this->messages[] = $msg['contact_form_send_copy_error_msg'];
				}
			}
		} else {
			$this->messages[] = $msg['contact_form_send_error_msg'];
		}
		//Mails envoyés - Suppression des pièces jointes dans le répertoire temporaire
		if(!empty($this->form_fields->contact_form_parameter_attachments)) {
		    foreach ($this->form_fields->contact_form_parameter_attachments as $parameter_attachment) {
		        if(empty($parameter_attachment->has_error)) {
		            if(file_exists($parameter_attachment->location)) {
		                unlink($parameter_attachment->location);
		            }
		        }
		    }
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_desc() {
		return $this->desc;
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
	
	public function get_fields_errors() {
		return $this->fields_errors;
	}
	
	public function set_messages($messages) {
		$this->messages = $messages;
	}
	
	public function is_sended() {
		return $this->sended;
	}
	
	public static function get_attachments_field() {
	    global $contact_form_attachments_field_tpl;
	    
	    $form = $contact_form_attachments_field_tpl;
	    return $form;
	}
	
	public static function get_consent_message_field() {
	    global $contact_form_consent_message_field_tpl;
	    
	    $form = $contact_form_consent_message_field_tpl;
	    return $form;
	}
}