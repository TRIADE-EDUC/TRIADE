<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_subcollections.class.php,v 1.1 2017/10/09 11:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_subcollections extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_verifautsco'];
    }
    
    protected function get_query() {
    	return "select sub_coll_id as id, subcollection_web as link from sub_collections where subcollection_web!='' and subcollection_web is not null order by index_sub_coll ";
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_verifurl_ss_coll'];
    }
    
	protected function get_element_label($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $element->id);
    	return $object_instance->get_isbd();
    }
    
    protected function get_element_edit_link($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $element->id);
    	return $object_instance->get_gestion_link();
    }
    
}
?>