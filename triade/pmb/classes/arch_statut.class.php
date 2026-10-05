<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: arch_statut.class.php,v 1.3 2023/11/17 09:34:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class arch_statut {
	
	/* ---------------------------------------------------------------
	 propriétés de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $gestion_libelle='';
	public $opac_libelle='';
	public $visible_opac=1;
	public $visible_opac_abon=0;
	public $visible_gestion=1;
	public $class_html='';
	
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
		
		$query = 'SELECT * FROM arch_statut WHERE archstatut_id='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->gestion_libelle = $data->archstatut_gestion_libelle;
		$this->opac_libelle = $data->archstatut_opac_libelle;
		$this->visible_opac = $data->archstatut_visible_opac;
		$this->visible_opac_abon = $data->archstatut_visible_opac_abon;
		$this->visible_gestion = $data->archstatut_visible_gestion;
		$this->class_html = $data->archstatut_class_html;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		
		$interface_content_form->add_element('form_gestion_libelle', 'collstate_statut_libelle')
		->add_input_node('text', $this->gestion_libelle);
		$interface_content_form->add_inherited_element('display_colors', 'form_class_html', 'collstate_statut_class_html')
		->init_nodes([$this->class_html]);
		
		$interface_content_form->add_element('form_opac_libelle', 'collstate_statut_libelle')
		->add_input_node('text', $this->opac_libelle)
		->set_attributes(array('data-translation-fieldname' => 'archstatut_opac_libelle'));
		$interface_content_form->add_element('form_visible_opac', 'collstate_statut_visu_opac_form', 'flat')
		->add_input_node('boolean', $this->visible_opac);
		$interface_content_form->add_element('form_visu_abon', 'collstate_statut_visible_opac_abon', 'flat')
		->add_input_node('boolean', $this->visible_opac_abon);
		
		$interface_content_form->add_zone('gestion', 'collstate_statut_gestion', ['form_gestion_libelle', 'form_class_html']);
		$interface_content_form->add_zone('opac', 'collstate_statut_opac', ['form_opac_libelle']);
		$interface_content_form->add_zone('visibilite_generale', 'collstate_statut_visibilite_generale', ['form_visible_opac'])
		->set_class('colonne2');
		$interface_content_form->add_zone('visibilite_restrict', 'collstate_statut_visibilite_restrict', ['form_visu_abon'])
		->set_class('colonne_suite');
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('statutform');
		if(!$this->id){
			$interface_form->set_label($msg['115']);
		}else{
			$interface_form->set_label($msg['118']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->gestion_libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('arch_statut')
		->set_field_focus('form_gestion_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_gestion_libelle, $form_opac_libelle;
		global $form_visible_gestion, $form_visible_opac, $form_visu_abon, $form_class_html;
		
		$this->gestion_libelle = stripslashes($form_gestion_libelle);
		$this->opac_libelle = stripslashes($form_opac_libelle);
		$this->visible_opac = intval($form_visible_opac);
		$this->visible_opac_abon = intval($form_visu_abon);
		$this->visible_gestion = intval($form_visible_gestion);
		$this->class_html = stripslashes($form_class_html);
	}
	
	public function save() {
		if ($this->id) {
			if ($this->id==1) $visu=", archstatut_visible_gestion=1, archstatut_visible_opac='".$this->visible_opac."', archstatut_visible_opac_abon='".$this->visible_opac_abon."' ";
			else $visu=", archstatut_visible_gestion='".$this->visible_gestion."', archstatut_visible_opac='".$this->visible_opac."', archstatut_visible_opac_abon='".$this->visible_opac_abon."' ";
			$requete = "UPDATE arch_statut SET archstatut_gestion_libelle='".addslashes($this->gestion_libelle)."', archstatut_opac_libelle='".addslashes($this->opac_libelle)."', archstatut_class_html='".addslashes($this->class_html)."' $visu WHERE archstatut_id='".$this->id."' ";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO arch_statut SET archstatut_gestion_libelle='".addslashes($this->gestion_libelle)."',archstatut_visible_gestion='".$this->visible_gestion."',archstatut_opac_libelle='".addslashes($this->opac_libelle)."', archstatut_visible_opac='".$this->visible_opac."', archstatut_class_html='".addslashes($this->class_html)."', archstatut_visible_opac_abon='".$this->visible_opac_abon."' ";
			pmb_mysql_query($requete);
		}
		$translation = new translation($this->id, "arch_statut");
		$translation->update("archstatut_opac_libelle", "form_opac_libelle");
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from collections_state where collstate_statut ='".$id."' "), 0, 0);
			if ($total==0) {
			    translation::delete($id, "arch_statut");
				$requete = "DELETE FROM arch_statut WHERE archstatut_id='$id' ";
				pmb_mysql_query($requete);
				$requete = "OPTIMIZE TABLE arch_statut ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('294', 'collstate_statut_used');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */