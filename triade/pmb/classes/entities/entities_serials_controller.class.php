<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_serials_controller.class.php,v 1.5 2023/04/07 09:12:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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
	
	protected function is_deletable() {
		global $msg;
		
		$query = "select 1 from pret, exemplaires, bulletins, notices where notice_id='".$this->id."' and expl_notice=0 ";
		$query .="and pret_idexpl=expl_id and expl_bulletin=bulletin_id and bulletin_notice=notice_id";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			// gestion erreur pret en cours
			error_message($msg[416], $msg['impossible_perio_del_pret'], 1, serial::get_permalink($this->id));
			return false;
		} else {
			return true;
		}
	}
	
	public function proceed_delete() {
		global $msg;
		global $pmb_archive_warehouse;
		global $current_module;
		
		print "<div class=\"row\"><div class=\"msg-perio\">".$msg['catalog_notices_suppression']."</div></div>";
		
		//suppression du périodique
		$serial = new serial($this->id);
		if ($pmb_archive_warehouse) {
			serial::save_to_agnostic_warehouse(array(0=>$this->id),$pmb_archive_warehouse);
		}
		$serial->serial_delete();
		print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"./catalog.php?categ=serials\" style=\"display:none\">
				<input type=\"hidden\" name=\"id_form\" value=\"".md5(microtime())."\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>
			";
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
