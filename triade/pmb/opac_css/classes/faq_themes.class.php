<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_themes.class.php,v 1.4 2021/01/14 09:17:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/liste_simple.class.php");

class faq_themes extends liste_simple {
	
	public function setParametres(){
		$this->setMessages('faq_ajout_theme','faq_modif_theme','faq_del_theme','faq_add_theme','faq_no_theme_available','faq_used_theme');
		$this->setActions('admin.php?categ=faq&sub=theme','admin.php?categ=faq&sub=theme');
	}
	public function hasElements(){
		$q = "select count(1) from faq_questions where faq_question_num_theme = '".$this->id_liste."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	public static function get_qty() {
		$q = "select count(1) from faq_themes";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
}?>