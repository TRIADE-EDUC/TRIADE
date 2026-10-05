<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authorities_used_by_composed_concepts.class.php,v 1.1.18.1 2025/01/17 10:40:43 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_authorities_used_by_composed_concepts extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_sub_selectors(){
		return array(
			"cms_module_common_selector_generic_authorities_concepts"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		global $dbh;
		if(!$this->value){
			$this->value = array();
			$sub = new cms_module_common_selector_generic_authorities_concepts($this->get_sub_selector_id("cms_module_common_selector_generic_authorities_concepts"));
			$concepts_ids = $sub->get_authorities_raw_ids();
			if (is_countable($concepts_ids) && count($concepts_ids)) {
				foreach ($concepts_ids as $concept_id) {
					$vedette_id = vedette_composee::get_vedette_id_from_object($concept_id, TYPE_CONCEPT_PREFLABEL);
					if ($vedette_id) {
						$vedette = new vedette_composee($vedette_id);
						foreach ($vedette->get_elements() as $subdivision) {
						    if (!is_countable($subdivision)) {
						        continue;
						    }
							for ($i = 0; $i < count($subdivision); $i++) {
								/* @var $authority vedette_element */
								$authority = $subdivision[$i];
								if (!isset($this->value[get_class($authority)])) {
									$this->value[get_class($authority)] = array();
								}
								if (!in_array($authority, $this->value[get_class($authority)])) {
									$this->value[get_class($authority)][] = $authority->get_id();
								}
							}
						}
					}
				}
			}
		}
		return $this->value;
	}
}