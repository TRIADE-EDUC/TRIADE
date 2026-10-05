<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_collections.class.php,v 1.2 2023/07/05 08:57:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_collections extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_verifautcol'];
    }
    
    protected function get_query() {
    	return "select collection_id as id, collection_web as link from collections where collection_web!='' and collection_web is not null order by index_coll ";
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_verifurl_coll'];
    }
    
	protected function get_element_label($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $element->id);
    	return $object_instance->get_isbd();
    }
    
    protected function get_element_edit_link($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $element->id);
    	return $object_instance->get_gestion_link();
    }
    
}
?>