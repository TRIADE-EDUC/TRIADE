<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_selector_categories.class.php,v 1.5 2022/01/18 20:34:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/docwatch_category.class.php");

class cms_module_watcheslist_selector_categories extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		if (!is_array($this->parameters)) $this->parameters = array();
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_watcheslist_selector_categories'>".$this->format_text($this->msg['cms_module_watcheslist_selector_categories'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters['categories'] = $this->get_value_from_form("categories");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$query= "select id_category, category_title from docwatch_categories order by category_title";
		$result = pmb_mysql_query($query);
		$select = "
					<select name='".$this->get_form_value_name("categories")."[]' multiple='multiple'>";
		if(pmb_mysql_num_rows($result)){
			if (!is_array($this->parameters['categories'])) $this->parameters['categories'] = array();
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
						<option value='".$row->id_category."' ".(in_array($row->id_category,$this->parameters['categories']) ? "selected='selected'" : "").">".$this->format_text($row->category_title)."</option>";
			}
		}else{
			$select.= "
						<option value ='0'>".$this->format_text($this->msg['cms_module_watcheslist_selector_categories_no_category'])."</option>";
		}
		$select.= "
			</select>";
		return $select;
	}
	
// 	public function get_children_categories($id) {
// 		$docwatch_category = new docwatch_category($id);
// 		return $docwatch_category->get_children();
// 	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters['categories'];
// 			if ($this->parameters['get_children_categories']) {;
// 				$this->value = $this->parameters['categories'];
// 				foreach ($this->parameters['categories'] as $category) {
// 					$children = $this->get_children_categories($category);
// 					foreach ($children as $child) {
// 						if (!in_array($child, $this->value)) {
// 							$this->value[] = $child;
// 						}
// 					}
// 				}
// 			} else {
// 				$this->value = $this->parameters['categories'];
// 			}
		}
		return $this->value;
	}
}