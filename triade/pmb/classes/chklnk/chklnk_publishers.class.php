<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_publishers.class.php,v 1.1 2017/10/09 11:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_publishers extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_verifautpub'];
    }
    
    protected function get_query() {
    	return "select ed_id as id, ed_web as link from publishers where ed_web!='' and ed_web is not null order by index_publisher ";
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_verifurl_editeur'];
    }
    
	protected function get_element_label($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $element->id);
    	return $object_instance->get_isbd();
    }
    
    protected function get_element_edit_link($element) {
    	$object_instance = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $element->id);
    	return $object_instance->get_gestion_link();
    }
    
}
?>