<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_parent_section.class.php,v 1.3 2016-09-21 13:09:44 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_parent_section extends cms_module_common_selector_generic_section{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	protected function get_sub_selectors(){
		$sub_selectors= parent::get_sub_selectors();
		return $sub_selectors;
	}
	
	/*
	 * Retourne la valeur sélectionnée
	 */
	public function get_value(){
		global $dbh;
		if(!$this->value){
			$sub = $this->get_selected_sub_selector();
			$query = "select section_num_parent from cms_sections where id_section='".($sub->get_value()*1)."' ";
			$result = pmb_mysql_query($query,$dbh);
			if($result && pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->value = $row->section_num_parent;
				}
			}
		}
		return $this->value;
	}
}