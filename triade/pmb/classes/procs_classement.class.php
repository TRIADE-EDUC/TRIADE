<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs_classement.class.php,v 1.4 2023/07/04 15:30:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class procs_classement {

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
	
		$requete = 'SELECT * FROM procs_classements WHERE idproc_classement='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->libproc_classement;
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libproc_classement', 'proc_clas_lib')
		->add_input_node('text', $this->libelle);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('proc_clas_form');
		if($this->id){
			$interface_form->set_label($msg['proc_clas_modif']);
		}else{
			$interface_form->set_label($msg['proc_clas_bt_add']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('procs_classements')
		->set_field_focus('form_libproc_classement');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libproc_classement;
		
		$this->libelle = stripslashes($form_libproc_classement);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM procs_classements WHERE (libproc_classement='".addslashes($this->libelle)."' AND idproc_classement!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.  if item already exists UPDATE else INSERT
		if ($this->id) {
			$requete = "UPDATE procs_classements SET libproc_classement='".addslashes($this->libelle)."' WHERE idproc_classement='".$this->id."' ";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO procs_classements SET libproc_classement='".addslashes($this->libelle)."' ";
			pmb_mysql_query($requete);
		}
	}

	public static function check_data_from_form() {
		global $form_libproc_classement;
		
		if(empty($form_libproc_classement)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from procs where num_classement='".$id."' "), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM procs_classements WHERE idproc_classement='$id' ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('proc_clas', 'proc_clas_used');
				return false;
			}
		}
		return true;
	}
	
	public static function get_id_from_libelle($libelle) {
		$query = "SELECT idproc_classement FROM procs_classements WHERE libproc_classement='".addslashes($libelle)."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'idproc_classement');
		}
		return 0;
	}
} /* fin de définition de la classe */