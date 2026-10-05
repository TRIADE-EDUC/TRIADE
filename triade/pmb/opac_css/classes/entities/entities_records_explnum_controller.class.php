<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_records_explnum_controller.class.php,v 1.2 2022/02/16 12:38:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/entities/entities_records_controller.class.php");

class entities_records_explnum_controller extends entities_records_controller {
		
	protected $record_id;
	
	protected $bulletin_id;
	
	/**
	 * 8 = droits de modification
	 */
	protected function get_acces_m() {
		global $PMBuserid;
		
		$acces_m = $this->dom_1->getRights($PMBuserid,$this->record_id,8);
		if($acces_m == 0) {
			if(!$this->id) {
				$this->error_message = 'mod_noti_error';
			} else {
				$this->error_message = 'mod_enum_error';
			}
		}
		return $acces_m;
	}
		
	public function proceed_explnum_form() {
		global $msg;
		global $base_path;
		
		if($this->id) {
			$this->action_link = "./catalog.php?categ=explnum_update&sub=update&id=".$this->id;
			$this->delete_link = "./catalog.php?categ=del_explnum&id=".$this->record_id."&explnum_id=".$this->id;
			print "<h1>".$msg['explnum_doc_associe']."</h1>";
		} else {
			$this->action_link = "./catalog.php?categ=explnum_update&sub=create";
			$this->delete_link = "";
			if (file_exists($base_path.'/temp/explnum_doublon_'.$this->record_id)) {
				// On supprime les doublons stockés inutilement
				unlink($base_path.'/temp/explnum_doublon_'.$this->record_id);
			}
			print "<h1>".$msg['explnum_ajouter_doc']."</h1>";
		}
		$notice = new mono_display($this->record_id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE);
		print pmb_bidi("<div class=\"row\"><b>".$notice->header.'</b><br />');
		print pmb_bidi($notice->isbd.'</div>');
		print "<div class=\"row\">";
		
		$explnum = new explnum($this->id,$this->record_id, $this->bulletin_id);
		print $explnum->explnum_form($this->action_link,$this->get_permalink(), $this->delete_link);
		print '</div>';
	}
	
	protected function get_permalink($id=0) {
		if(!$id) $id = $this->record_id;
		return notice::get_permalink($id);
	}
	
	public function set_record_id($record_id=0) {
	    $this->record_id = (int) $record_id;
	}
	
	public function set_bulletin_id($bulletin_id=0) {
	    $this->bulletin_id = (int) $bulletin_id;
	}
}
