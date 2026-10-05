<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_serials_controller.class.php,v 1.1 2018/10/08 13:59:39 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_records_controller.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path."/serial_display.class.php");

class entities_serials_controller extends entities_records_controller {

	protected $url_base = './catalog.php?categ=serials';
	
	protected $model_class_name = 'serial';
	
	public function get_display_object_instance($id=0, $niveau_biblio='') {
		return new serial_display($id,1, $this->get_permalink($id));
	}
	
	/**
	 * 8 = droits de modification
	 */
	protected function get_acces_m() {
		global $PMBuserid;
	
		$acces_m = 1;
		if($this->id) $acces_m = $this->dom_1->getRights($PMBuserid,$this->id,8);
		if($acces_m == 0) {
			$this->error_message = 'mod_seri_error';
		}
		return $acces_m;
	}
	
	public function proceed_form() {
		global $msg;
		global $serial_header;
		
		// affichage d'un form pour création, modification d'un périodique
		if(!$this->id) {
			// pas d'id, c'est une création
			print str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4003], $serial_header);
		} else {
			print str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4004], $serial_header);
		}
		$mySerial = $this->get_object_instance();
		print $mySerial->do_form();
	}
	
	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=view&serial_id=".$id;
	}
	
	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=view&serial_id=".$id;
	}
}
