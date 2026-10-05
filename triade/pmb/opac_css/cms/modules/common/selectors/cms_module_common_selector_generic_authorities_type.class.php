<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authorities_type.class.php,v 1.5.18.2 2025/01/21 15:29:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authorities_type extends cms_module_common_selector{

	/**
	 * Type des autorités
	 * @var int
	 */
	protected $authorities_type;

	/**
	 * Identifiants non uniques des autorités
	 * @var int
	 */
	protected $authorities_raw_ids;

	/**
	 * Nom de la classe vedette associé à l'autorité, pour l'extraction des autorités utilisées par des concepts composés
	 * @var string
	 */
	protected $vedette_class_name;

	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}

	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_type_section",
			"cms_module_common_selector_type_article",
			"cms_module_common_selector_type_article_generic",
			"cms_module_common_selector_type_section_generic",
			"cms_module_common_selector_record_cp_val",
			"cms_module_common_selector_authorities_used_by_composed_concepts"
		);
	}

	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if (!$this->value) {
			$this->value = array();
			if ((!is_countable($this->get_authorities_raw_ids()) || !count($this->get_authorities_raw_ids())) || !$this->authorities_type) {
				return $this->value;
			}
			$query = 'select id_authority from authorities where type_object = "'.($this->authorities_type*1).'" and num_object in ("'.implode('","', $this->authorities_raw_ids).'")';
			// On garde l'ordre défini dans la classe spécifique à l'autorité
			$query.= ' order by field(num_object, "'.implode('","', $this->authorities_raw_ids).'")';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->value[] = $row->id_authority;
				}
			}
		}
		return $this->value;
	}

	/**
	 * Retourne les identifiants non uniques des autorités
	 */
	public function get_authorities_raw_ids() {
		if (!$this->authorities_raw_ids) {
			$this->authorities_raw_ids = array();
			$sub = $this->get_selected_sub_selector();
			$sub_value = $sub->get_value();
			if (get_class($sub) == 'cms_module_common_selector_authorities_used_by_composed_concepts') {
				// On récupère les bons éléments
				if (!isset($sub_value[$this->vedette_class_name])) {
					return $this->authorities_raw_ids;
				}
				$sub_value = $sub_value[$this->vedette_class_name];
			}
			if (is_array($sub_value)) {
				$this->authorities_raw_ids = $sub_value;
			}
		}
		return $this->authorities_raw_ids;
	}
}