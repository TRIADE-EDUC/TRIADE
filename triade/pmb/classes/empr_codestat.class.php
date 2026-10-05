<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_codestat.class.php,v 1.4 2023/07/04 12:00:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

class empr_codestat {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';
	
	/* ---------------------------------------------------------------
			empr_codestat($id) : constructeur
	   --------------------------------------------------------------- */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
	
		$query = 'SELECT * FROM empr_codestat WHERE idcode='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->libelle;
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', '103')
		->add_input_node('text', $this->libelle);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('lenderform');
		if(!$this->id){
			$interface_form->set_label($msg['101']);
		}else{
			$interface_form->set_label($msg['102']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('empr_codestat')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle;
		
		$this->libelle = stripslashes($form_libelle);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM empr_codestat WHERE (libelle='".addslashes($this->libelle)."' AND idcode!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE empr_codestat SET libelle='".addslashes($this->libelle)."' WHERE idcode=".$this->id;
			$res = pmb_mysql_query($requete);
		} else {
			$requete = "SELECT count(1) FROM empr_codestat WHERE libelle='".addslashes($this->libelle)."' LIMIT 1 ";
			$res = pmb_mysql_query($requete);
			$nbr = pmb_mysql_result($res, 0, 0);
			if($nbr == 0) {
				$requete = "INSERT INTO empr_codestat (idcode,libelle) VALUES ('', '".addslashes($this->libelle)."') ";
				$res = pmb_mysql_query($requete);
				$this->id = pmb_mysql_insert_id();
			}
		}
		$translation = new translation($this->id, "empr_codestat");
		$translation->update("libelle", "form_libelle");
	}
	
	public static function check_data_from_form() {
		global $form_libelle;
		
		if(empty($form_libelle)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_codestat ='".$id."' "), 0, 0);
			if ($total==0) {
				translation::delete($id, "empr_codestat");
				$requete = "DELETE FROM empr_codestat WHERE idcode=$id ;";
				pmb_mysql_query($requete);
				$requete = "OPTIMIZE TABLE empr_codestat ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('294', '1707');
				return false;
			}
		}
		return true;
	}
	
	public function get_translated_libelle() {
		return translation::get_translated_text($this->id, 'empr_codestat', 'libelle', $this->libelle);
	}
	
} /* fin de définition de la classe */