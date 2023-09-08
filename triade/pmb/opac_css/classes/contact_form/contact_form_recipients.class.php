<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_recipients.class.php,v 1.2 2018-07-06 14:28:28 plmrozowski Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_objects.class.php");
require_once($class_path."/contact_form/contact_form_parameters.class.php");
require_once($include_path."/templates/contact_form/contact_form.tpl.php");

class contact_form_recipients {
		
	/**
	 * Liste des destinataires par mode
	 */
	protected $recipients;
	
	/**
	 * Mode
	 * @var string
	 */
	protected $mode;
	
	/**
	 * Constructeur
	 * @param string $mode
	 */
	public function __construct($mode) {
		$this->set_mode($mode);
		$this->_init_recipients();
		$this->fetch_data();
	}
	
	/**
	 * Initialisation
	 */
	protected function _init_recipients() {
		$this->recipients = array(
				'by_persons' => array(),
				'by_objects' => array(),
				'by_locations' => array()
		);
	}
	
	/**
	 *  Données provenant de la base de données
	 */
	protected function fetch_data() {
		
		$query = 'select valeur_param from parametres where type_param="pmb" and sstype_param="contact_form_recipients_lists"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->valeur_param) {
				$this->recipients = unserialize($row->valeur_param);
			}
		}
	}
	
	/**
	 * Sélecteur de destinataires
	 * @return string
	 */
	public function gen_selector() {
		global $empr_location;
		$options = array();
		switch ($this->mode) {
			case 'by_persons' :
				foreach ($this->recipients[$this->mode] as $id=>$recipient) {
					if($recipient['name'] != '') {
					    $option = array('value'=>$id, 'label'=>$recipient['name']);
					    $options[] = $option;
					}
				}
				break;
			case 'by_locations' :
				foreach ($this->recipients[$this->mode] as $id=>$recipient) {
					$query = 'SELECT * FROM docs_location WHERE idlocation='.$id.' and location_visible_opac = 1';
					$result = pmb_mysql_query($query);
			        if(pmb_mysql_num_rows($result)) {
                        $row = pmb_mysql_fetch_object($result);
                        $option = array('value'=>$id, 'label'=>$row->location_libelle, ($empr_location == $id ? "selected" : '')=>($empr_location == $id ? "selected" : ''));
                        $options[] = $option;
					}
				}
				break;
		}
		$selector = "<select
        	name='contact_form_recipients'
        	id='contact_form_recipients'
        	data-dojo-type='dijit/form/Select'
        	data-dojo-props='options:".encoding_normalize::json_encode($options)."'/>";
		return $selector;
	}
	
	/**
	 * Bloc de formulaire de destinataires
	 */
	public function get_form() {
		global $msg, $charset;
		global $contact_form_recipients_tpl;
		
		$form = "";
		switch ($this->mode) {
			case 'by_persons' :
				$form = $contact_form_recipients_tpl;
				$form = str_replace("!!recipients_label!!", htmlentities($msg['contact_form_recipient_by_person'], ENT_QUOTES, $charset), $form);
				$form = str_replace("!!recipients_selector!!", $this->gen_selector(), $form);
				break;
			case 'by_locations' :
				$form = $contact_form_recipients_tpl;
				$form = str_replace("!!recipients_label!!", htmlentities($msg['contact_form_recipient_by_location'], ENT_QUOTES, $charset), $form);
				$form = str_replace("!!recipients_selector!!", $this->gen_selector(), $form);
				break;
		}
		return $form;
	}
		
	public function get_recipients() {
		return $this->recipients;
	}
	
	public function get_mode() {
		return $this->mode;
	}
	
	public function set_mode($mode) {
		if(!$mode) {
			$contact_form_parameters = new contact_form_parameters();
			$parameters = $contact_form_parameters->get_parameters();
			$mode = $parameters['recipients_mode']; 
		}
		$this->mode = $mode;
	}
}