<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_theme.class.php,v 1.2 2023/06/23 07:21:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/liste_simple.tpl.php");

class demandes_theme {

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
	
		$requete = 'SELECT * FROM demandes_theme WHERE id_theme='.$this->id;
		$result = pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->libelle_theme;
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('simple_list_form');
		if(!$this->id){
			$interface_form->set_label($msg['demandes_ajout_theme']);
		}else{
			$interface_form->set_label($msg['demandes_modif_theme']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['demandes_del_theme'])
		->set_content_form($this->get_content_form())
		->set_table_name('demandes_theme')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle;
		
		$this->libelle = stripslashes($libelle);
	}
	
	public function save() {
		if($this->id) {
			$requete = "UPDATE demandes_theme set libelle_theme='".addslashes($this->libelle)."' where id_theme='".$this->id."'";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO demandes_theme set libelle_theme='".addslashes($this->libelle)."'";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
	}

	public static function check_data_from_form() {
		global $libelle;
		
		if(empty($libelle)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$total = pmb_mysql_num_rows(pmb_mysql_query("select * from demandes where theme_demande = '".$id."'"));
			if ($total==0) {
				$requete = "DELETE FROM demandes_theme where id_theme='".$id."'";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message("321", 'demandes_used_theme');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */