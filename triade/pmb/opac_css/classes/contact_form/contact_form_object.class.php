<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_object.class.php,v 1.1 2016-05-26 13:52:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
		$this->id = $id+0;
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