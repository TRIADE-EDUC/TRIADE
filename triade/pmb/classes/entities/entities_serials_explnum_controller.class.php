<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_serials_explnum_controller.class.php,v 1.4.8.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/entities/entities_serials_controller.class.php");

class entities_serials_explnum_controller extends entities_serials_controller {

	protected $serial_id;

	protected $action_link;

	protected $delete_link;

	/**
	 * 8 = droits de modification
	 */
	protected function get_acces_m() {
		global $PMBuserid;

		$acces_m = $this->dom_1->getRights($PMBuserid,$this->serial_id,8);
		if($acces_m == 0) {
			if(!$this->id) {
				$this->error_message = 'mod_seri_error';
			} else {
				$this->error_message = 'mod_enum_error';
			}
		}
		return $acces_m;
	}

	public function proceed_explnum_form() {
		$this->action_link = $this->url_base."&sub=explnum_update";
		if($this->id) {
			$this->delete_link = $this->url_base."&sub=explnum_delete&serial_id=".$this->serial_id."&explnum_id=".$this->id;
		} else {
			$this->delete_link = "";
		}
		// affichage des infos du bulletinage pour rappel
		$perio = new serial_display($this->serial_id, 0);
		print "<div class='row'><h2>".$perio->result.'</h2></div>';

		$explnum = new explnum($this->id,$this->serial_id);
		print $explnum->explnum_form($this->action_link,$this->get_permalink(), $this->delete_link);
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->serial_id;
		return $this->url_base."&sub=view&serial_id=".$this->serial_id;
	}

	public function set_serial_id($serial_id=0) {
	    $this->serial_id = (int) $serial_id;
	}
}
