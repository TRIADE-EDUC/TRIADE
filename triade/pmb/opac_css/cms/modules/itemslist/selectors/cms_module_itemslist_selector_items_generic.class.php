<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_selector_items_generic.class.php,v 1.4.6.1 2025/01/17 10:40:45 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_selector_items_generic extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}

	public function get_sub_selectors(){
		return array(
				"cms_module_watcheslist_selector_watches_generic",
				"cms_module_watch_selector_watch_generic",
				"cms_module_watcheslist_selector_categories_generic",
				"cms_module_itemslist_selector_items_by_tags"
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
				if ($values != "") {
					$values = [intval($values)];
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
				$itemslist = array();
				switch($this->parameters['sub_selector']) {
					case "cms_module_watcheslist_selector_watches_generic":
					case "cms_module_watch_selector_watch_generic":
						$query = "select id_item from docwatch_items where item_num_watch in ('".implode("','", $values)."')";
						$result = pmb_mysql_query($query);
						if ($result) {
							while ($row = pmb_mysql_fetch_object($result)) {
								$itemslist[] = $row->id_item;
							}
						}
						break;
					case "cms_module_watcheslist_selector_categories_generic":
						$query = "select id_watch from docwatch_watches where watch_num_category in ('".implode("','", $values)."')";
						$result = pmb_mysql_query($query);
						$watcheslist = array();
						if ($result) {
							while ($row = pmb_mysql_fetch_object($result)) {
								$watcheslist[] = $row->id_watch;
							}
						}
						if (count($watcheslist)) {
							$query = "select id_item from docwatch_items where item_num_watch in ('".implode("','", $watcheslist)."')";
							$result = pmb_mysql_query($query);
							if ($result) {
								while ($row = pmb_mysql_fetch_object($result)) {
									$itemslist[] = $row->id_item;
								}
							}
						}
						break;
					case "cms_module_itemslist_selector_items_by_tags":
						$query = "select id_item from docwatch_items left join docwatch_items_tags on num_item=id_item where num_tag in ('".implode("','", $values)."')";
						$result = pmb_mysql_query($query);
						if ($result) {
							while ($row = pmb_mysql_fetch_object($result)) {
								$itemslist[] = $row->id_item;
							}
						}
						break;
				}
				return $itemslist;
			}
		}
		return array();
	}
}