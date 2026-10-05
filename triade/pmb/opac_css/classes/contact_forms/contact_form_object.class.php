<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_object.class.php,v 1.1 2020/08/11 08:57:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/translation.class.php");

class contact_form_object {
	
	/**
	 * identifiant de l'objet
	 */
	protected $id;
	
	/**
	 * Libellé de l'objet
	 * @var string
	 */
	protected $label;
	
	/**
	 * Votre message
	 * @var string
	 */
	protected $message;
	
	protected $num_contact_form;
	
	public function __construct($id=0) {
	    $this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		
		if($this->id) {
			$query = 'select object_label, object_message, num_contact_form from contact_form_objects where id_object ='.$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->object_label;
			$this->message = $row->object_message;
			$this->num_contact_form = $row->num_contact_form;
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_translated_label() {
	    return translation::get_text($this->id, 'contact_form_objects', 'object_label', $this->label);
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function get_message() {
	    return $this->message;
	}
	
	public function get_translated_message() {
	    return translation::get_text($this->id, 'contact_form_objects', 'object_message', $this->message);
	}
	
	public function set_message($message) {
	    $this->message = $message;
	}
	
	public function get_num_contact_form() {
		return $this->num_contact_form;
	}
	
	public function set_num_contact_form($num_contact_form) {
		$this->num_contact_form = intval($num_contact_form);
	}
}