<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authorities_concepts_by_animation.class.php,v 1.2 2022/02/24 14:55:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_authorities_concepts_by_animation extends cms_module_common_selector {
	
	protected $animation_id;
	
	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_generic_animation"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = array();
			if ($this->get_animation_id()) {
			    $query = 'select num_concept from index_concept where type_object = "'.TYPE_ANIMATION.'" and num_object = "'.$this->animation_id.'"';
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
	
	public function get_animation_id() {
	    if (!$this->animation_id) {
	        $sub = new cms_module_common_selector_generic_animation($this->get_sub_selector_id("cms_module_common_selector_generic_animation"));
			$this->animation_id = $sub->get_value();
		}
		return $this->animation_id;
	}
	
	public function get_excluded_elements() {
		return array(
			'animation_ids' => array(
                $this->get_animation_id()
			)
		);
	}
}