<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_works_links.class.php,v 1.8 2021/02/25 16:27:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datasource_works_links extends frbr_entity_common_datasource {
	
	protected $work_link_type;
	protected static $type = "all";
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		parent::__construct($id);
	}
	
	public function set_work_link_type($link_type){
		$this->work_link_type = $link_type;		
	}
	

	public function get_expression_type_selector($selected = array()) {
		global $charset, $msg;
		
		$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		$selector = "<select name='datanode_work_link_type[]' id='datanode_work_link_type' multiple='yes'>";
		
		foreach($oeuvre_link->table as $group => $types) {
			$options = '';
			foreach($types as $code => $libelle){
				if (($oeuvre_link->attributes[$code]['GROUP'] == static::$type)||(static::$type == 'all')) {
					if ((is_array($selected) && in_array($code, $selected)) || ($code == $selected)) {						
						$options .= "<option value='".$code."' selected='selected'>".$libelle."</option>";
					} else {
						$options .= "<option value='".$code."'>".$libelle."</option>";
					}
				}
			}
			if($options) {
				if (!isset($optgroup_list)) {
					$optgroup_list = array();
				}
				$optgroup_list[$group]=$options;
			}
		}
		if (!empty($optgroup_list)) {
			if(count($optgroup_list)>1){
				foreach ($optgroup_list as $group=>$options) {
					$selector .= '<optgroup label="'.htmlentities($group,ENT_QUOTES,$charset).'">'.$options.'</optgroup>';
				}
			}elseif(count($optgroup_list)){
				foreach ($optgroup_list as $group=>$options) {
					$selector.= $optgroup_list[$group];
				}
			}
		}else{
			$selector.= "<option value=''>".$msg['authority_marc_list_empty_filter']."</option>";
		}
		$selector.= '</select>';
		return $selector;
	}
        
	public function get_form() {
		if (!isset($this->parameters->work_link_type)) {
			$this->parameters->work_link_type = array();
		}
		$form = parent::get_form();
		if(static::$type){
			$form.= "<div class='row'>
					<div class='colonne3'>
						<label for='datanode_work_link_type'>".$this->format_text($this->msg['frbr_entity_common_datasource_link_type'])."</label>
					</div>
					<div class='colonne-suite'>
						".$this->get_expression_type_selector($this->parameters->work_link_type)."
					</div>
				</div>";
		}
		return $form;
	}
	
	protected function get_label_from_group($group) {
	    $oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
	    foreach($oeuvre_link->table as $key => $types) {
	        if (isset($types[$group])) {
	            return $types[$group];
	        }
        }
	    return "";
	}
}