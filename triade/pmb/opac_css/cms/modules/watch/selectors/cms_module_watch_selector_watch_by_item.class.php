<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watch_selector_watch_by_item.class.php,v 1.3 2016-09-20 10:25:42 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watch_selector_watch_by_item extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}
	
	public function get_sub_selectors(){
		return array(
				"cms_module_item_selector_item_generic"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	*/
	public function get_value(){
		global $dbh;
		if($this->parameters['sub_selector']){
			$sub_selector = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
			$value = $sub_selector->get_value()*1;
			if ($value) {
				$query="select item_num_watch from docwatch_items where id_item='".$value."'";
				$result = pmb_mysql_query($query,$dbh);
				if ($result) {
					if (pmb_mysql_num_rows($result)) {
						$row = pmb_mysql_fetch_object($result);
						return $row->item_num_watch;
					}
				}
			}
		}
		return "";
	}
}