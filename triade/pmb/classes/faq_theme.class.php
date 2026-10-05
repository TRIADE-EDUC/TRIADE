<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_theme.class.php,v 1.4 2023/06/23 07:21:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/indexation.class.php");
require_once($include_path."/templates/liste_simple.tpl.php");

class faq_theme {

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
	
		$requete = 'SELECT * FROM faq_themes WHERE id_theme='.$this->id;
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
			$interface_form->set_label($msg['faq_ajout_theme']);
		}else{
			$interface_form->set_label($msg['faq_modif_theme']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['faq_del_theme'])
		->set_content_form($this->get_content_form())
		->set_table_name('faq_themes')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle;
		
		$this->libelle = stripslashes($libelle);
	}
	
	public function save() {
		if($this->id) {
			$requete = "UPDATE faq_themes set libelle_theme='".addslashes($this->libelle)."' where id_theme='".$this->id."'";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO faq_themes set libelle_theme='".addslashes($this->libelle)."'";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		$this->update_index();
	}

	public function update_index(){
		global $include_path;
		$query = "select id_faq_question from faq_questions where faq_question_num_theme = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
		    $index = indexations_collection::get_indexation(AUT_TABLE_FAQ);
			while($row = pmb_mysql_fetch_object($result)){
				$index->maj($row->id_faq_question,"theme");
			}
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
			$total = pmb_mysql_num_rows(pmb_mysql_query("select * from faq_questions where faq_question_num_theme = '".$id."'"));
			if ($total==0) {
				$requete = "DELETE FROM faq_themes where id_theme='".$id."'";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message("321", 'faq_used_theme');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */