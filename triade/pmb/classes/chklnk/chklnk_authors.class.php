<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_authors.class.php,v 1.1 2017/10/09 11:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_authors extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_verifautaut'];
    }
    
    protected function get_query() {
    	return "select author_id as id, author_web as link from authors where author_web!='' and author_web is not null order by index_author ";
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_verifurl_auteur'];
    }
    
    protected function get_element_label($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $element->id);
    	return $object_instance->get_isbd();
    }
    
    protected function get_element_edit_link($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $element->id);
    	return $object_instance->get_gestion_link();
    }
    
}
?>