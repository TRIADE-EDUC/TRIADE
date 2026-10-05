<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_usage.class.php,v 1.5 2023/06/23 07:21:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

class notice_usage {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';

	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
	
		$requete = 'SELECT * FROM notice_usage WHERE id_usage='.$this->id;
		$result = pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->usage_libelle;
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('usage_libelle', 'notice_usage_libelle')
		->add_input_node('text', $this->libelle)
		->set_attributes(array('data-translation-fieldname' => 'usage_libelle'));
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('notice_usageform');
		if(!$this->id){
			$interface_form->set_label($msg['notice_usage_ajout']);
		}else{
			$interface_form->set_label($msg['notice_usage_modification']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('notice_usage')
		->set_field_focus('usage_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $usage_libelle;
		
		$this->libelle = stripslashes($usage_libelle);
	}
	
	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE notice_usage SET usage_libelle='".addslashes($this->libelle)."' WHERE id_usage='".$this->id."' ";
			$res = pmb_mysql_query($requete);
		} else {
			$requete = "SELECT count(1) FROM notice_usage WHERE usage_libelle='".addslashes($this->libelle)."' LIMIT 1 ";
			$res = pmb_mysql_query($requete);
			$nbr = pmb_mysql_result($res, 0, 0);
			if($nbr == 0){
				$requete = "INSERT INTO notice_usage (usage_libelle) VALUES ('".addslashes($this->libelle)."') ";
				$res = pmb_mysql_query($requete);
				$this->id = pmb_mysql_insert_id();
			}
		}
		$translation = new translation($this->id, "notice_usage");
		$translation->update("usage_libelle");
	}

	public static function check_data_from_form() {
		global $usage_libelle;
		
		if(empty($usage_libelle)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$total = 0;
			$total = pmb_mysql_num_rows(pmb_mysql_query("select num_notice_usage from notices where num_notice_usage ='".$id."' "));
			if ($total==0) {
				$requete = "DELETE FROM notice_usage WHERE id_usage='".$id."' ";
				pmb_mysql_query($requete);
				$requete = "OPTIMIZE TABLE notice_usage ";
				pmb_mysql_query($requete);
				translation::delete($id, "notice_usage");
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message("", 'notice_usage_used');
				return false;
			}
		}
		return true;
	}
	
	public function get_translated_libelle() {
		return translation::get_translated_text($this->id, 'notice_usage', 'libelle', $this->libelle);
	}
} /* fin de définition de la classe */


