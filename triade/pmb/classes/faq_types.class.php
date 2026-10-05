<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_types.class.php,v 1.4 2021/01/14 09:17:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/liste_simple.class.php");
require_once($class_path."/indexation.class.php");

class faq_types extends liste_simple {
	
	public function setParametres(){
		$this->setMessages('faq_ajout_type','faq_modif_type','faq_del_type','faq_add_type','faq_no_type_available','faq_used_type');
		$this->setActions('admin.php?categ=faq&sub=type','admin.php?categ=faq&sub=type');
	}
	public function hasElements(){
		$q = "select count(1) from faq_questions where faq_question_num_type = '".$this->id_liste."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	public static function get_qty() {
		$q = "select count(1) from faq_types";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
}?>