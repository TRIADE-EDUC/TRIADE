<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authorities_concepts_by_section.class.php,v 1.5 2022/02/24 15:17:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_authorities_concepts_by_section extends cms_module_common_selector{
	
	protected $section_id;
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	
	protected  function get_sub_selectors(){
		return array(
			"cms_module_common_selector_generic_section"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = array();
			if ($this->get_section_id()) {
				$query = 'select num_concept from index_concept where type_object = 13 and num_object = "'.$this->section_id.'"';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$this->value[] = $row->num_concept;
					}
				}
			}
		}
		return $this->value;
	}
	
	public function get_section_id() {
		if (!$this->section_id) {
			$sub = new cms_module_common_selector_generic_section($this->get_sub_selector_id("cms_module_common_selector_generic_section"));
			$this->section_id = $sub->get_value();
		}
		return $this->section_id;
	}
	
	public function get_excluded_elements() {
		return array(
				'sections_ids' => array(
						$this->get_section_id()
				)
		);
	}
}