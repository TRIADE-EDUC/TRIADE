<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_vign.class.php,v 1.3 2021/03/17 13:32:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_vign extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_verifvign'];
    }
    
    protected function get_query() {
    	return implode(" union ", static::$queries['vign']);
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_verifvign'];
    }
    
    protected function get_element_label($element) {
    	return notice::get_notice_title($element->id);
    }
    
    protected function get_element_edit_link($element) {
    	return notice::get_permalink($element->id);
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