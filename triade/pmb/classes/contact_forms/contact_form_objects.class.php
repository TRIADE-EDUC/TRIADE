<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_objects.class.php,v 1.5 2024/03/13 14:57:42 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/contact_forms/contact_form_object.class.php");

class contact_form_objects {
	
	protected $num_contact_form;
	
	/**
	 * Liste des objets
	 */
	protected $objects;
	
	/**
	 * Constructeur
	 */
	public function __construct($num_contact_form=0) {
		$this->num_contact_form = intval($num_contact_form);
		$this->fetch_data();
	}
	
	/**
	 * Données
	 */
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_object from contact_form_objects where num_contact_form = '.$this->num_contact_form.' order by object_label';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new contact_form_object($row->id_object);
			}
		}
	}
	
	/**
	 * Sélecteur d'objets de mail
	 */
	public function gen_selector() {
		$selector = "<select aria-labelledby='contact_form_objects' name='contact_form_objects' data-dojo-type='dijit/form/Select'>";
		foreach ($this->objects as $object) {
			$selector .= "<option value='".$object->get_id()."'>".$object->get_label()."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public static function delete($num_contact_form=0) {
		$num_contact_form = intval($num_contact_form);
		if (!isset($num_contact_form)) {
			return;
		}
		$query = "delete from contact_form_objects where num_contact_form = ".$num_contact_form;
		pmb_mysql_query($query);
		return true;
	}
	
	public function get_objects() {
		return $this->objects;
	}
}