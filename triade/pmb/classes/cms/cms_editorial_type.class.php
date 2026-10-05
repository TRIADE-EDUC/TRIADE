<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_type.class.php,v 1.2 2021/05/06 13:01:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");
require_once($class_path.'/interface/admin/interface_admin_cms_form.class.php');
// require_once($include_path."/templates/cms/cms_editorial_type.tpl.php");

class cms_editorial_type {
	public $id;
	public $element='';
	public $label='';
	public $comment='';
	public $num_page=0;
	public $var_name='';
	public $fields;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}

	protected function fetch_data(){
		global $msg;
		if(!$this->id) return;
		
		$requete = 'SELECT * FROM cms_editorial_types WHERE id_editorial_type='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		
		$data = pmb_mysql_fetch_object($result);
		$this->element = $data->editorial_type_element;
		if(strpos($this->element, "_generic") !== false) {
			$this->label = $msg['editorial_content_type_fieldslist_'.$data->editorial_type_element.'_label'];
		} else {
			$this->label = $data->editorial_type_label;
		}
		$this->comment = $data->editorial_type_comment;
		$this->num_page = $data->editorial_type_permalink_num_page;
		$this->var_name = $data->editorial_type_permalink_var_name;
		$fields = new cms_editorial_parametres_perso($this->id);
		$this->fields = $fields->t_fields;
	}
	
	public function get_form(){
		global $msg,$charset;
		global $cms_editorial_type_content_form;
		global $cms_editorial_type_form_generic_label, $cms_editorial_type_form_std_label;
		
		$content_form = $cms_editorial_type_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_admin_cms_form('cms_editorial_type_form');
		$interface_form->set_elem($this->element);
		if(!$this->id){
			$interface_form->set_label($msg['editorial_content_type_add']);
		}else{
			$interface_form->set_label($msg['editorial_content_type_edit']);
		}
		if(strpos($this->element, "generic") != false){
			$content_form = str_replace("!!cms_editorial_label!!",$cms_editorial_type_form_generic_label, $content_form);
		}else{
			$content_form = str_replace("!!cms_editorial_label!!",$cms_editorial_type_form_std_label, $content_form);
			
		}
		$content_form = str_replace("!!label!!",htmlentities($this->label,ENT_QUOTES,$charset),$content_form);
		$content_form = str_replace("!!comment!!",htmlentities($this->comment,ENT_QUOTES,$charset),$content_form);
		$content_form = str_replace("!!cms_page_options!!",cms_editorial_types::get_pages_options($this->num_page),$content_form);
		$content_form = str_replace("!!cms_env_var_options!!",cms_editorial_types::get_env_var_options($this->num_page, $this->var_name), $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->label." ?")
		->set_content_form($content_form)
		->set_table_name('cms_editorial_types')
		->set_field_focus('cms_editorial_type_label');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $cms_editorial_type_label,$cms_editorial_type_comment;
		global $cms_editorial_type_page_var_selector, $cms_editorial_type_page_selector;
		
		$this->label = stripslashes($cms_editorial_type_label);
		$this->comment = stripslashes($cms_editorial_type_comment);
		$this->num_page = intval($cms_editorial_type_page_selector);
		$this->var_name = stripslashes($cms_editorial_type_page_var_selector);
	}
	
	public function save(){
		
		if($this->id){
			$query = "update cms_editorial_types set ";
			$clause = "where id_editorial_type = ".$this->id;
		}else{
			$query = "insert into cms_editorial_types set ";
			$clause = "";
		}
		$query.= "
			editorial_type_element = '".addslashes($this->element)."',
			editorial_type_label = '".addslashes($this->label)."',
			editorial_type_comment = '".addslashes($this->comment)."',
			editorial_type_permalink_num_page = ".$this->num_page.",
			editorial_type_permalink_var_name = '".addslashes($this->var_name)."'";
		$query.= " ".$clause;
		pmb_mysql_query($query);
		
		if(strpos($this->element, "generic") != false){
			//On repasse au fonctionnement normal, nous ne sommes plus en éditions d'un élément générique
			$this->element = str_replace('_generic', '' , $this->element);
		}
	}
	
	public function delete(){
		global $msg;
		
		if(strpos($this->element, "generic") != false){
			pmb_error::get_instance(static::class)->add_message('', $msg['cant_delete']);
			return false;
		}
		
		if($this->id){
			//on regarde si le type est utilisé
			$query = "select id_".$this->element." from cms_".$this->element."s where ".$this->element."_num_type = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$error = $msg['type_used'];
			}
			
			//on regarde si le type est utilisé dans un formulaire de contribution
			$query = "SELECT id_form, form_title FROM contribution_area_forms WHERE form_type = '".$this->element."_".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$error = $msg['cms_type_used_in_contribution'];
			}
		}
		if($error){
			pmb_error::get_instance(static::class)->add_message('', $msg['cant_delete'].". ".$error);
			return false;
		}else{
			$fields = new cms_editorial_parametres_perso($this->id);
			$fields->delete_all();
			$query = "delete from cms_editorial_types where id_editorial_type = ".$this->id;
			pmb_mysql_query($query);
			return true;
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_element() {
		return $this->element;
	}
	
	public function set_element($element) {
		$this->element = $element;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	public function get_fields() {
		return $this->fields;
	}
}