<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_authorities_thumbnail.class.php,v 1.2 2020/03/04 08:09:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_authorities_thumbnail extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_authorities_thumbnail'];
    }
    
    protected function get_query() {
    	return "select id_authority, num_object as id, type_object, thumbnail_url as link from authorities where thumbnail_url !='' and thumbnail_url is not null ";
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_authorities_thumbnail'];
    }
    
    protected function get_element_label($element) {
    	$object_instance = authorities_collection::get_authority($element->type_object, $element->id);
    	return $object_instance->get_isbd();
    }
    
    protected function get_element_edit_link($element) {
    	$object_instance = authorities_collection::get_authority($element->type_object, $element->id);
    	return $object_instance->get_gestion_link();;
    }
    
    protected function process_element($element) {
    	global $pmb_url_base;
    	
    	$url=$element->link;
		if(preg_match('`^[a-zA-Z0-9_]+\.php`',$url)){
    		$url=$pmb_url_base."/".$url;
		}
		$element->link = $url;
    	return $this->check_link($element);
    }
}
?>