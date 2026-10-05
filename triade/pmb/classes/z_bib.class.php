<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: z_bib.class.php,v 1.4 2023/07/07 07:02:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class z_bib {
	
	/* ---------------------------------------------------------------
	 propriétés de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $nom='';
	public $search_type='CATALOG';
	public $url='';
	public $port='211';
	public $base='';
	public $format='UNIMARC';
	public $auth_user='';
	public $auth_pass='';
	public $sutrs_lang='';
	public $fichier_func='';
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
	 getData() : récupération des propriétés
	 --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		$requete = 'SELECT * FROM z_bib WHERE bib_id='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->nom = $data->bib_nom;
		$this->search_type = $data->search_type;
		$this->url = $data->url;
		$this->port = $data->port;
		$this->base = $data->base;
		$this->format = $data->format;
		$this->auth_user = $data->auth_user;
		$this->auth_pass = $data->auth_pass;
		$this->sutrs_lang = $data->sutrs_lang;
		$this->fichier_func = $data->fichier_func;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->set_grid_model('flat_column_4_right');
		$interface_content_form->add_element('form_nom', 'admin_Nom')
		->add_input_node('text', $this->nom);
		$interface_content_form->add_element('form_search_type', 'admin_Utilisation')
		->add_input_node('text', $this->search_type);
		$interface_content_form->add_element('form_base', 'admin_Base')
		->add_input_node('text', $this->base);
		$interface_content_form->add_element('form_url', 'admin_URL')
		->add_input_node('text', $this->url);
		$interface_content_form->add_element('form_port', 'admin_NumPort')
		->add_input_node('integer', $this->port);
		$interface_content_form->add_element('form_base', 'admin_Base')
		->add_input_node('text', $this->base);
		$interface_content_form->add_element('form_format', 'admin_Format')
		->add_input_node('text', $this->format);
		$interface_content_form->add_element('form_sutrs', 'z3950_sutrs')
		->add_input_node('text', $this->sutrs_lang);
		$interface_content_form->add_element('form_user', 'admin_user')
		->add_input_node('text', $this->auth_user);
		$interface_content_form->add_element('form_password', 'admin_password')
		->add_input_node('text', $this->auth_pass);
		$interface_content_form->add_element('form_zfunc', 'zbib_zfunc')
		->add_input_node('text', $this->fichier_func);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('zbibform');
		if(!$this->id){
			$interface_form->set_label($msg['zbib_ajouter_serveur']);
		}else{
			$interface_form->set_label($msg['zbib_modifier_serveur']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->nom." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('z_bib')
		->set_field_focus('form_nom');
		$interface_form->add_action_extension('attributs_button', $msg['admin_Attributs'], './admin.php?categ=z3950&sub=zattr&action=edit&bib_id='.$this->id);
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_nom, $form_search_type, $form_url, $form_port;
		global $form_base, $form_format, $form_user, $form_password, $form_sutrs, $form_zfunc;
		
		$this->nom = stripslashes($form_nom);
		$this->search_type = stripslashes($form_search_type);
		$this->url = stripslashes($form_url);
		$this->port = stripslashes($form_port);
		$this->base = stripslashes($form_base);
		$this->format = stripslashes($form_format);
		$this->auth_user = stripslashes($form_user);
		$this->auth_pass = stripslashes($form_password);
		$this->sutrs_lang = stripslashes($form_sutrs);
		$this->fichier_func = stripslashes($form_zfunc);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM z_bib WHERE (bib_nom='".addslashes($this->nom)."' AND bib_id!='".$this->id."' )";
	}
	
	public function save() {
		if($this->id) {
			$requete = "UPDATE z_bib SET bib_nom='".addslashes($this->nom)."', base='".addslashes($this->base)."',
					search_type='".addslashes($this->search_type)."', url='".addslashes($this->url)."', port='".addslashes($this->port)."',
					format='".addslashes($this->format)."', auth_user='".addslashes($this->auth_user)."',
					auth_pass='".addslashes($this->auth_pass)."', sutrs_lang='".addslashes($this->sutrs_lang)."', fichier_func='".addslashes($this->fichier_func)."' WHERE bib_id=".$this->id;
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO z_bib (bib_nom, search_type, url, port, base, format, auth_user, auth_pass, sutrs_lang, fichier_func) VALUES ('".addslashes($this->nom)."', '".addslashes($this->search_type)."', '".addslashes($this->url)."', '".addslashes($this->port)."', '".addslashes($this->base)."', '".addslashes($this->format)."', '".addslashes($this->auth_user)."', '".addslashes($this->auth_pass)."', '".addslashes($this->sutrs_lang)."', '".addslashes($this->fichier_func)."') ";
			pmb_mysql_query($requete);
			$this->id=pmb_mysql_insert_id();
			$requete = "INSERT INTO z_attr (attr_bib_id,  attr_libelle, attr_attr) VALUES ('".$this->id."', 'sujet', '21') ";
			pmb_mysql_query($requete);
			$requete = "INSERT INTO z_attr (attr_bib_id,  attr_libelle, attr_attr) VALUES ('".$this->id."', 'auteur', '1003') ";
			pmb_mysql_query($requete);
			$requete = "INSERT INTO z_attr (attr_bib_id,  attr_libelle, attr_attr) VALUES ('".$this->id."', 'isbn', '7') ";
			pmb_mysql_query($requete);
			$requete = "INSERT INTO z_attr (attr_bib_id,  attr_libelle, attr_attr) VALUES ('".$this->id."', 'titre', '4') ";
			pmb_mysql_query($requete);
		}
	}
	
	public static function check_data_from_form() {
		global $form_nom, $form_base, $form_search_type, $form_url, $form_port, $form_format;
		
		if(empty($form_nom) || empty($form_base) || empty($form_search_type) || empty($form_url) || empty($form_port) || empty($form_format)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$requete = "DELETE FROM z_bib WHERE bib_id=$id ";
			pmb_mysql_query($requete);
			$requete = "DELETE FROM z_attr WHERE attr_bib_id=$id ";
			pmb_mysql_query($requete);
			return true;
		}
		return true;
	}
} /* fin de définition de la classe */