<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_objects.class.php,v 1.3 2016-09-27 12:39:09 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_object.class.php");

class contact_form_objects {
	
	/**
	 * Liste des objets
	 */
	protected $objects;
	
	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->fetch_data();
	}
	
	/**
	 * Données
	 */
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_object from contact_form_objects order by object_label';
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
		global $contact_form_objects_id;

		$selector = "<select name='contact_form_objects' data-dojo-type='dijit/form/Select' ";
		if (isset($contact_form_objects_id)) {
			$selector .= " data-dojo-props='value:".$contact_form_objects_id."'";
		}
		$selector .= " maxHeight='80'>";
		foreach ($this->objects as $object) {
			$selector .= "<option value='".$object->get_id()."'>".$object->get_label()."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public function get_objects() {
		return $this->objects;
	}
}