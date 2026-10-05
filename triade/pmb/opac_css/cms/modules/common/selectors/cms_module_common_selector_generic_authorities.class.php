<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authorities.class.php,v 1.1 2016/04/20 13:54:54 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authorities extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}
	
	protected function get_sub_selectors(){
		return array(
// 			"cms_module_common_selector_authorities_from_record", // Toutes autorités d'une notice
// 			"cms_module_common_selector_authorities_from_authority", // Autorités liées à une autorité
			"cms_module_common_selector_generic_authorities_by_type"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$sub = $this->get_selected_sub_selector();
			$this->value = $sub->get_value();
		}
		return $this->value;
	}
}