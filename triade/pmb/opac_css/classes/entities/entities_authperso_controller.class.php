<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_authperso_controller.class.php,v 1.4 2022/02/16 12:38:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/entities/entities_authorities_controller.class.php");
require_once($class_path.'/authperso.class.php');

class entities_authperso_controller extends entities_authorities_controller {
	
	protected $model_class_name = 'authperso';
	
	protected $id_authperso;
	
	protected $object_instance;
	
	public function get_object_instance() {
		if (isset($this->object_instance)) {
			return $this->object_instance;
		}
		$model_class_name = $this->get_model_class_name();
		$this->object_instance = new $model_class_name($this->id_authperso, $this->id);
		if(method_exists($model_class_name, 'set_controller')) {
			$model_class_name::set_controller($this);
		}
		return $this->object_instance;
	}
	
	public function set_id_authperso($id_authperso=0) {
	    $this->id_authperso = (int) $id_authperso;
	}
	
	public function get_display_list() {
		//Il faut la globaliser pour que les autorités perso fonctionnent...
		global $url_base;
		global $categ;
		
		$url_base = $this->get_pagination_link();
		$object_instance = $this->get_object_instance();
		$this->set_session_history($this->object_instance->get_searcher_instance()->get_human_query(), $categ, 'QUERY');
		$this->set_session_history($this->object_instance->get_searcher_instance()->get_human_query(), $categ, 'AUT');
		
		return $object_instance->get_list();
	}
	
	public function proceed_delete() {
	    global $msg;
		$object_instance = $this->get_object_instance();
		$sup_result = $object_instance->delete($this->id);
		if(!$sup_result) {
			print $this->get_display_list();
		}else {
			error_message($msg[132], $sup_result, 1, $this->get_edit_link());
		}
	}
	
	public function proceed_replace() {
		global $msg;
		global $by, $aut_link_save;
	
		$object_instance = $this->get_object_instance();
		if(!$by) {
			print $object_instance->replace_form($this->id);
		}else {
// 		    routine de remplacement
			$rep_result = $object_instance->replace($this->id, $by,$aut_link_save);
			if(!$rep_result) {
				print $this->get_display_list();
			}else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}
	
	public function proceed_duplicate() {
		$object_instance = $this->get_object_instance();
		print $object_instance->get_form($this->id, true);
	}
	
	public function proceed_update() {
		global $msg;
		
		$object_instance = $this->get_object_instance();
		$this->id = $object_instance->update_from_form($this->id);
		if($object_instance->get_cp_error_message()){//Traitement des messages d'erreurs champs persos
			error_message($msg['search_by_authperso_title'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
			return 0;
		}else{
			return $this->id;
		}
	}
	
	public function proceed_form() {
	    $unlock_unload_script = "";
	    if($this->id){
	        $entity_locking = new entity_locking($this->id, $this->get_type_const());
	        $entity_locking->lock_entity();
	        $unlock_unload_script = $entity_locking->get_polling_script();
	    }
		$object_instance = $this->get_object_instance();
		print $object_instance->get_form($this->id);
		print $unlock_unload_script;
	}
	
	public function proceed_last() {
		print $this->get_display_list();
	}
	
	public function proceed_default() {
		global $pmb_allow_authorities_first_page;
	
		if(!$pmb_allow_authorities_first_page && (!isset($this->user_input) || $this->user_input == '')){
			$object_instance = $this->get_object_instance();
			print $object_instance->get_list(true);
		}else {
			// affichage du début de la liste
			print $this->get_display_list();
		}
	}
	
	protected function get_pagination_link() {
		global $id_authperso;
		return $this->url_base."&sub=reach&user_input=".rawurlencode($this->user_input)."&id_authperso=".$id_authperso;
	}
	
	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=authperso&id_authperso=".$this->id_authperso."&id=".$id;
	}
	
	protected function get_edit_link($id=0) {
		global $id_authperso;
		
		if(!$id) $id = $this->id;

		return $this->url_base."&sub=authperso_form&id=".$id.'&id_authperso='.$id_authperso;
	}
	
	public function get_searcher_instance()	{
		return $this->get_object_instance()->get_searcher_instance();
	}
	
	protected function get_aut_const(){
	    return TYPE_AUTHPERSO;
	}
}
