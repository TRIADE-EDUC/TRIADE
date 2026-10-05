<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_selector_watches_by_categories.class.php,v 1.6.6.2 2025/01/21 15:29:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watcheslist_selector_watches_by_categories extends cms_module_watcheslist_selector_categories_generic{

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
		$form=parent::get_form();
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_watcheslist_selector_get_children_categories'>".$this->format_text($this->msg['cms_module_watcheslist_selector_get_children_categories'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='".$this->get_form_value_name("get_children_categories")."' value='1' ".($this->parameters['get_children_categories'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['yes'])."
					&nbsp;<input type='radio' name='".$this->get_form_value_name("get_children_categories")."' value='0' ".(!$this->parameters['get_children_categories'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['no'])."
				</div>
			</div>";
		return $form;
	}

	public function save_form(){
		$this->parameters['get_children_categories'] = $this->get_value_from_form("get_children_categories");
		return parent ::save_form();
	}
	/*
	 * Retourne la valeur sélectionné
	*/
	public function get_value(){
		if(!$this->value){
			$this->value = array();
			if($this->parameters['sub_selector']){
				$sub_selector = $this->get_selected_sub_selector();
				$sub_value = $sub_selector->get_value();
				if(!is_array($sub_value) && $sub_value){
					$sub_value = intval($sub_value);
					$sub_value = array($sub_value);
				}
				if($this->parameters['get_children_categories']){
					$sub_value =$this->get_children_categories($sub_value);
				}
				if(is_countable($sub_value) && count($sub_value)){
					$temp = array();
					foreach ($sub_value as $value) {
						$temp[] = intval($value);
					}
					$sub_value = $temp;
					$query = "select id_watch from docwatch_watches where watch_num_category in ('".implode("','",$sub_value)."')";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$this->value[] = $row->id_watch;
						}
					}
				}
			}
		}
		return $this->value;
	}

	public function get_children_categories($ids) {
		$categories = array();
		if(is_array($ids) && count($ids)){
			foreach($ids as $id){
				$categories[] = $id;
				$categories = array_merge($categories,$this->get_children($id));
				$categories = array_unique($categories);
			}
		}
		return $categories;
	}

	public function get_children($id){
		$categories = array($id);
		$docwatch_category = new docwatch_category($id);
		$children = $docwatch_category->get_children();
		if(is_array($children) && count($children)){
			foreach($children as $child){
				$categories[] = $child;
				$categories = array_merge($categories,$this->get_children($child));
			}
		}
		return $categories;
	}
}