<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_contact_form.class.php,v 1.1 2023/10/10 07:39:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docs_location.class.php");

class mail_opac_contact_form extends mail_opac {
	
    /**
     * 
     * @var contact_form
     */
    protected $contact_form;
    
    /**
     * La personne qui a soumis le formulaire de contact
     * @var array
     */
    protected $applicant;
    
    /**
     * La personne qui expédie le mail
     * @var array
     */
    protected $recipient;
    
	protected function _init_default_settings() {
	    global $opac_biblio_email;
	    
		parent::_init_default_settings();
		if(!empty($opac_biblio_email)) {
		    $this->_init_setting_value('sender', 'parameter');
		} else {
		    $this->_init_setting_value('sender', 'docs_location');
		}
		$this->_init_setting_value('reply', 'reader');
	}
	
	protected function get_applicant() {
	    if(empty($this->applicant)) {
	        $this->applicant = array();
	        $form_fields = $this->contact_form->get_form_fields();
	        if(!empty($form_fields->contact_form_parameter_name)) {
	            $this->applicant['name'] = $form_fields->contact_form_parameter_name." ".$form_fields->contact_form_parameter_firstname;
	        } else {
	            $this->applicant['name'] = $form_fields->contact_form_parameter_email;
	        }
	        $this->applicant['email'] = $form_fields->contact_form_parameter_email;
	    }
	    return $this->applicant;
	}
	
	protected function get_recipient() {
	    if(empty($this->recipient)) {
	        $mode = $this->contact_form->get_parameters()['recipients_mode'];
	        $num_recipient = $this->contact_form->get_form_fields()->contact_form_recipients;
	        $contact_form_recipients = new contact_form_recipients($this->contact_form->get_id(), $mode);
	        $recipients = $contact_form_recipients->get_recipients();
	        $this->recipient = $recipients[$mode][$num_recipient];
	    }
	    return $this->recipient;
	}
	
	protected function get_mail_to_name() {
	    return $this->get_recipient()['name'];
	}
	
	protected function get_mail_to_mail() {
	    return $this->get_recipient()['email'];
	}
	
	protected function get_mail_object() {
		if($this->contact_form->get_form_fields()->contact_form_objects) {
		    $contact_form_object = new contact_form_object($this->contact_form->get_form_fields()->contact_form_objects);
		    return $contact_form_object->get_translated_label();
		} else {
		    return $this->contact_form->get_form_fields()->contact_form_object_free_entry;
		}
	}
	
	protected function get_mail_content() {
		return h2o($this->contact_form->get_parameters()['email_content'])->render(array('contact_form' => $this->contact_form->get_form_fields()));
	}
	
	protected function get_mail_copy_cc() {
	    //Ne pas entrer dans cette condition sur le mail d'accusé de réception
	    if(static::class == 'mail_opac_contact_form' && !empty($this->get_recipient()['copy_email'])) {
	        return $this->get_recipient()['copy_email'];
	    }
	    return parent::get_mail_copy_cc();
	}
	
	protected function get_mail_attachments() {
	    if(empty($this->mail_attachments)) {
    	    $this->mail_attachments = [];
    	    if(!empty($this->contact_form->get_form_fields()->contact_form_parameter_attachments)) {
    	        $parameters_attachments = $this->contact_form->get_form_fields()->contact_form_parameter_attachments;
    	        foreach ($parameters_attachments as $parameter_attachment) {
    	            if(empty($parameter_attachment->has_error)) {
    	                $this->mail_attachments[] = array(
    	                    'contenu' => file_get_contents($parameter_attachment->location),
    	                    'nomfichier' => $parameter_attachment->name
    	                );
    	            }
    	        }
    	    }
	    }
	    return $this->mail_attachments;
	}
	
	protected function get_mail_reply_name() {
	    return $this->get_applicant()['name'];
	}
	
	protected function get_mail_reply_mail() {
	    return $this->get_applicant()['email'];
	}
	
	public function set_contact_form($contact_form) {
	    $this->contact_form = $contact_form;
		return $this;
	}
}