<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_statut.class.php,v 1.4 2023/08/30 07:18:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

// définition de la classe de gestion des status de documents numériques

class explnum_statut {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $gestion_libelle='';
	public $opac_libelle='';
	public $class_html="";
	public $visible_opac=1;
	public $consult_opac=1;
	public $download_opac=1;
	public $visible_opac_abon=0;
	public $consult_opac_abon=0;
	public $download_opac_abon=0;
	public $thumbnail_visible_opac_override=0;
	
	/* ---------------------------------------------------------------
			docs_statut($id) : constructeur
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
	
		/* récupération des informations du statut */
	
		$requete = 'SELECT * FROM explnum_statut WHERE id_explnum_statut='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->gestion_libelle = $data->gestion_libelle;
		$this->opac_libelle = $data->opac_libelle;
		$this->class_html = $data->class_html;
		$this->visible_opac = $data->explnum_visible_opac;
		$this->consult_opac = $data->explnum_consult_opac;
		$this->download_opac = $data->explnum_download_opac;
		$this->visible_opac_abon = $data->explnum_visible_opac_abon;
		$this->consult_opac_abon = $data->explnum_consult_opac_abon;
		$this->download_opac_abon = $data->explnum_download_opac_abon;
		$this->thumbnail_visible_opac_override = $data->explnum_thumbnail_visible_opac_override;
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_gestion_libelle', 'docnum_statut_libelle')
		->add_input_node('text', $this->gestion_libelle);
		$interface_content_form->add_inherited_element('display_colors', 'form_class_html', 'noti_statut_class_html')
		->init_nodes([$this->class_html]);
		
		$interface_content_form->add_element('form_opac_libelle', 'docnum_statut_libelle')
		->add_input_node('text', $this->opac_libelle);
		
		$interface_content_form->add_element('form_visible_opac', 'docnum_statut_visu_opac_form', 'flat')
		->add_input_node('boolean', $this->visible_opac);
		$interface_content_form->add_element('form_visible_opac_abon', 'docnum_statut_visu_opac_abon', 'flat')
		->add_input_node('boolean', $this->visible_opac_abon);
		$interface_content_form->add_element('form_consult_opac', 'docnum_statut_cons_opac_form', 'flat')
		->add_input_node('boolean', $this->consult_opac);
		$interface_content_form->add_element('form_consult_opac_abon', 'docnum_statut_cons_opac_abon', 'flat')
		->add_input_node('boolean', $this->consult_opac_abon);
		$interface_content_form->add_element('form_download_opac', 'docnum_statut_down_opac_form', 'flat')
		->add_input_node('boolean', $this->download_opac);
		$interface_content_form->add_element('form_download_opac_abon', 'docnum_statut_down_opac_abon', 'flat')
		->add_input_node('boolean', $this->download_opac_abon);
		$interface_content_form->add_element('form_thumbnail_visible_opac_override', 'docnum_statut_thumbnail_visible_opac_override', 'flat')
		->add_input_node('boolean', $this->thumbnail_visible_opac_override);
		
		$interface_content_form->add_zone('gestion', 'noti_statut_gestion',
				['form_gestion_libelle', 'form_class_html']
		);
		$interface_content_form->add_zone('opac', 'noti_statut_opac', ['form_opac_libelle']);
		$interface_content_form->add_zone('visibilite_generale', 'docnum_statut_visibilite_generale',
				['form_visible_opac', 'form_consult_opac', 'form_download_opac', 'form_thumbnail_visible_opac_override']
		)->set_class('colonne2');
		$interface_content_form->add_zone('visibilite_restrict', 'docnum_statut_visibilite_restrict',
				['form_visible_opac_abon', 'form_consult_opac_abon', 'form_download_opac_abon']
		)->set_class('colonne_suite');
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
		->set_table_name('explnum_statut')
		->set_field_focus('form_gestion_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_gestion_libelle, $form_opac_libelle, $form_class_html, $form_visible_opac, $form_consult_opac, $form_download_opac;
		global $form_visible_opac_abon, $form_consult_opac_abon, $form_download_opac_abon, $form_thumbnail_visible_opac_override;
		
		$this->gestion_libelle = stripslashes($form_gestion_libelle);
		$this->opac_libelle = stripslashes($form_opac_libelle);
		$this->class_html = stripslashes($form_class_html);
		$this->visible_opac = intval($form_visible_opac);
		$this->consult_opac = intval($form_consult_opac);
		$this->download_opac = intval($form_download_opac);
		$this->visible_opac_abon = intval($form_visible_opac_abon);
		$this->consult_opac_abon = intval($form_consult_opac_abon);
		$this->download_opac_abon = intval($form_download_opac_abon);
		$this->thumbnail_visible_opac_override = intval($form_thumbnail_visible_opac_override);
	}
	
	public function save() {
		if ($this->id) {
			$requete = 'UPDATE explnum_statut SET
						gestion_libelle="'.addslashes($this->gestion_libelle).'",
						opac_libelle="'.addslashes($this->opac_libelle).'",
						class_html="'.addslashes($this->class_html).'",
						explnum_visible_opac="'.$this->visible_opac.'",
						explnum_consult_opac="'.$this->consult_opac.'",
						explnum_download_opac="'.$this->download_opac.'",
						explnum_visible_opac_abon="'.$this->visible_opac_abon.'",
						explnum_consult_opac_abon="'.$this->consult_opac_abon.'",
						explnum_download_opac_abon="'.$this->download_opac_abon.'",
						explnum_thumbnail_visible_opac_override="'.$this->thumbnail_visible_opac_override.'"
			 			WHERE id_explnum_statut="'.$this->id.'" ';
			pmb_mysql_query($requete);
		} else {
			$requete = 'INSERT INTO explnum_statut SET
						gestion_libelle="'.addslashes($this->gestion_libelle).'",
						opac_libelle="'.addslashes($this->opac_libelle).'",
						class_html="'.addslashes($this->class_html).'",
						explnum_visible_opac="'.$this->visible_opac.'",
						explnum_consult_opac="'.$this->consult_opac.'",
						explnum_download_opac="'.$this->download_opac.'",
						explnum_visible_opac_abon="'.$this->visible_opac_abon.'",
						explnum_consult_opac_abon="'.$this->consult_opac_abon.'",
						explnum_download_opac_abon="'.$this->download_opac_abon.'",
						explnum_thumbnail_visible_opac_override="'.$this->thumbnail_visible_opac_override.'" ';
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, "explnum_statut");
		$translation->update("gestion_libelle", "form_gestion_libelle");
		$translation->update("opac_libelle", "form_opac_libelle");
	}

	public static function delete($id) {
		$id = intval($id);
		if ($id && $id!=1) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from explnum where explnum_docnum_statut ='".$id."' "), 0, 0);
			if ($total==0) {
				translation::delete($id, "explnum_statut");
				$requete = "DELETE FROM explnum_statut WHERE id_explnum_statut='$id' ";
				pmb_mysql_query($requete);
				$requete = "OPTIMIZE TABLE explnum_statut ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('docnum_statut_docnum', 'docnum_statut_used');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */


