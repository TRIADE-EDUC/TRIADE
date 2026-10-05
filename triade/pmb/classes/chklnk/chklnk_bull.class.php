<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk_bull.class.php,v 1.1 2017/10/09 11:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/chklnk/chklnk.class.php");

class chklnk_bull extends chklnk {
	    
    protected function get_title() {
    	global $msg;
    	
    	return $msg['chklnk_verifbull'];
    }
    
    protected function get_query() {
    	return implode(" union ", static::$queries['explnum_bull']);
    }
    
    protected function get_label_progress_bar() {
    	global $msg;
    	
    	return $msg['chklnk_verifurl_bull'];
    }
    
    protected function get_element_label($element) {
    	return $element->tit;
    }
    
    protected function get_element_edit_link($element) {
    	return "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=".$element->bulletin_id."&explnum_id=".$element->explnum_id;
    }
    
}
?>