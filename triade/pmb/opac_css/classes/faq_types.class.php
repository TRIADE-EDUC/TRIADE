<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_types.class.php,v 1.2 2015-04-03 11:16:17 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/liste_simple.class.php");

class faq_types extends liste_simple {
	
	function setParametres(){
		$this->setMessages('faq_ajout_type','faq_modif_type','faq_del_type','faq_add_type','faq_no_type_available','faq_used_type');
		$this->setActions('admin.php?categ=faq&sub=type','admin.php?categ=faq&sub=type');
	}
	function hasElements(){
		
		global $dbh;
		
		$q = "select count(1) from faq_questions where faq_question_num_type = '".$this->id_liste."' ";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	static function get_qty() {
		
		global $dbh;
		$q = "select count(1) from faq_types";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	/*
	 * Création/Modification
	*/
	function save(){
		parent::save();
		$this->update_index();
	}
	
	function update_index(){
		global $dbh,$include_path;
		$query = "select id_faq_question from faq_questions where faq_question_num_type = ".$this->id_liste;
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$index = new indexation($include_path."/indexation/faq/question.xml", "faq_questions");
			while($row = pmb_mysql_fetch_object($result)){
				$index->maj($row->id_faq_question,"type");
			}
		}
	}
}?>