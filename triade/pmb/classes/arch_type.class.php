<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: arch_type.class.php,v 1.4 2023/11/17 09:34:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class arch_type {
	
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
		
		$query = 'SELECT * FROM arch_type WHERE archtype_id='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->archtype_libelle;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', 'admin_collstate_support_nom')
		->add_input_node('text', $this->libelle)
		->set_attributes(array('data-translation-fieldname' => 'archtype_libelle'));
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('supportform');
		if(!$this->id){
			$interface_form->set_label($msg['admin_collstate_add_support']);
		}else{
			$interface_form->set_label($msg['admin_collstate_edit_support']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('arch_type')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle;
		
		$this->libelle = stripslashes($form_libelle);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM arch_type WHERE (archtype_libelle='".addslashes($this->libelle)."' AND archtype_id!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE arch_type SET archtype_libelle='".addslashes($this->libelle)."' WHERE archtype_id=".$this->id;
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO arch_type (archtype_id,archtype_libelle) VALUES (0, '".addslashes($this->libelle)."') ";
			pmb_mysql_query($requete);
		}
		$translation = new translation($this->id, "arch_type");
		$translation->update("archtype_libelle", "form_libelle");
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$total = pmb_mysql_num_rows(pmb_mysql_query("select 1 from collections_state where collstate_type='".$id."' limit 0,1"));
			if ($total==0) {
			    translation::delete($id, "arch_type");
				$requete = "DELETE FROM arch_type WHERE archtype_id=$id ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('294', 'collstate_support_used');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */