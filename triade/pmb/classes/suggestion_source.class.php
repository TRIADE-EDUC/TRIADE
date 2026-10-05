<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestion_source.class.php,v 1.6 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/suggestion_source.tpl.php");

class suggestion_source{
	
	public $id_source=0;
	public $libelle_source='';
	
	/*
	 * Constructeur
	 */
	public function __construct($id=0){
		$this->id_source = intval($id);
		
		if(!$this->id_source){
			$this->libelle_source ='';
		} else {
			$req="select libelle_source from suggestions_source where id_source='".$this->id_source."'";
			$res = pmb_mysql_query($req);
			if(!pmb_mysql_num_rows($res)) {
				pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
				return;
			}
			$src = pmb_mysql_fetch_object($res);
			$this->libelle_source = $src->libelle_source;
		}
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle_source);
		return $interface_content_form->get_display();
	}
	
	/*
	 * Formulaire d'ajout/modification
	 */
	public function get_form(){
		global $msg;
		
		$interface_form = new interface_admin_form('srcform');
		if(!$this->id_source){
			$interface_form->set_label($msg['acquisition_ajout_src']);
		}else{
			$interface_form->set_label($msg['acquisition_modif_src']);
		}
		$interface_form->set_object_id($this->id_source)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle_source." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('suggestions_source')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
		
	public function set_properties_from_form() {
		global $libelle;
		
		$this->libelle_source = stripslashes($libelle);
	}
	
	/*
	 * Création/Modification
	 */
	public function save(){
		if(!$this->id_source){
			$req = "insert into suggestions_source set libelle_source='".addslashes($this->libelle_source)."'";
		} else {
			$req="update suggestions_source set libelle_source='".addslashes($this->libelle_source)."' where id_source='".$this->id_source."'";
		}		
		pmb_mysql_query($req);
	}
	
	//Suppression d'une source
	public static function delete($id){
		global $msg;		
		
		$id = intval($id);
		if(static::hasSuggestions($id)){
			pmb_error::get_instance(static::class)->add_message('321', $msg['acquisition_sugg_source_used']);
			return false;
		} else {		
			$req="delete from suggestions_source where id_source='".$id."'";
			pmb_mysql_query($req);
			return true;
		}
		return true;
	}
	
	//Vérifie si la source de suggestions est utilisee dans les suggestions	
	public static function hasSuggestions($id){
		$id = intval($id);
		$q = "select count(1) from suggestions where sugg_source = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}
}
?>