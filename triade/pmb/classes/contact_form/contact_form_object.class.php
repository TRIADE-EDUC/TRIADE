<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_object.class.php,v 1.4 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_recipients.class.php");
require_once($include_path."/templates/contact_form/contact_form.tpl.php");

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
	
	public function __construct($id=0) {
	    $this->id = (int) $id;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		
		if($this->id) {
			$query = 'select object_label from contact_form_objects where id_object ='.$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->object_label;
		}
	}
	
	public function get_form() {
		global $msg, $charset;
		global $contact_form_object_form_tpl;
		
		$form = $contact_form_object_form_tpl;
		if($this->id) {
			$form = str_replace('!!title!!', htmlentities($msg['admin_opac_contact_form_object_form_edit'], ENT_QUOTES, $charset), $form);
  			$button_delete = "<input type='button' class='bouton' value='$msg[63]' ";
  			$button_delete .= "onClick=\"if(confirm('".addslashes($msg['admin_opac_contact_form_object_confirm_delete'])."')) { document.location='./admin.php?categ=contact_form&sub=objects&action=delete&id=".$this->id."'}\">";
			$form = str_replace('!!delete!!', $button_delete, $form);
		} else {
			$form = str_replace('!!title!!', htmlentities($msg['admin_opac_contact_form_object_form_add'], ENT_QUOTES, $charset), $form);
			$form = str_replace('!!delete!!', '', $form);
		}
		$form = str_replace('!!label!!', htmlentities($this->label, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!id!!', $this->id, $form);
		return $form;
	}
	
	/**
	 * Données provenant d'un formulaire
	 */
	public function set_properties_from_form() {
		global $object_label;
		
		$this->label = stripslashes($object_label);
	}
	
	/**
	 * Sauvegarde
	 */
	public function save(){
	
		if($this->id) {
			$query = 'update contact_form_objects set ';
			$where = 'where id_object= '.$this->id;
		} else {
			$query = 'insert into contact_form_objects set ';
			$where = '';
		}
		$query .= '
				object_label = "'.addslashes($this->label).'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Suppression
	 */
	public function delete(){
		global $msg;
	
		if($this->id) {
			$contact_form_recipients = new contact_form_recipients('by_objects');
			$contact_form_recipients->unset_recipient($this->id);
			$contact_form_recipients->save();
			$query = "delete from contact_form_objects where id_object = ".$this->id;
			$result = pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
}