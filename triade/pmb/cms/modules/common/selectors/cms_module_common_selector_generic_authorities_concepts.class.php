<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authorities_concepts.class.php,v 1.6 2021/03/31 08:47:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_authorities_concepts extends cms_module_common_selector_generic_authorities_type{
	
	public function __construct($id=0){
		$this->authorities_type = AUT_TABLE_CONCEPT;
		$this->vedette_class_name = 'vedette_concepts';
		parent::__construct($id);
	}
	
	protected function get_sub_selectors(){
		$sub_selectors = parent::get_sub_selectors();
		$sub_selectors[] = "cms_module_common_selector_authorities_concepts_by_record";
		$sub_selectors[] = "cms_module_common_selector_authorities_concepts_by_section";
		$sub_selectors[] = "cms_module_common_selector_authorities_concepts_by_article";
		$sub_selectors[] = "cms_module_common_selector_authorities_concepts_by_animation";
		$sub_selectors[] = "cms_module_common_selector_permalink";
		return $sub_selectors;
	}
	
	/**
	 * Retourne les identifiants non uniques des autorités
	 */
	public function get_authorities_raw_ids() {
		if (!$this->authorities_raw_ids) {
			$values = parent::get_authorities_raw_ids();
			$this->authorities_raw_ids = array();
			if (is_array($values)) {
				// On gère le fait qu'on puisse avoir des id ou des uri de concepts
				foreach ($values as $value) {
					if ($value && !($value*1)) {
						// On a une uri
						$value = onto_common_uri::get_id($value);
					}
					$this->authorities_raw_ids[] = $value;
				}
			}
		}
		return $this->authorities_raw_ids;
	}
}