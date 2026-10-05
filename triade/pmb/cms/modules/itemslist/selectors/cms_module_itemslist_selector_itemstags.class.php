<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_selector_itemstags.class.php,v 1.4.6.1 2025/01/17 10:40:43 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_selector_itemstags extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}

	public function get_sub_selectors(){
		return array(
				"cms_module_item_selector_item_generic",
				"cms_module_common_selector_env_var"
		);
	}

	/*
	 * Retourne la valeur sélectionné
	*/
	public function get_value(){
		if($this->parameters['sub_selector']){
			$sub_selector = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
			$values = $sub_selector->get_value();
			if (!is_array($values)) {
				$values = intval($values);
				if ($values != 0) {
					$values = [$values];
				} else {
					$values = array();
				}
			}

			if (count($values)) {
				$temp = array();
				foreach ($values as $value) {
					$temp[] = intval($value);
				}
				$values = $temp;
				$tagslist = array();
				switch($this->parameters['sub_selector']) {
					case "cms_module_item_selector_item_generic":
						$query = "select id_tag from docwatch_tags left join docwatch_items_tags on num_tag=id_tag where num_item in ('".implode("','", $values)."')";
						$result = pmb_mysql_query($query);
						if ($result) {
							while ($row = pmb_mysql_fetch_object($result)) {
								$tagslist[] = $row->id_tag;
							}
						}
						return $tagslist;
						break;
					case "cms_module_common_selector_env_var":
						return $values;
						break;
				}
			}
		}
		return array();
	}
}