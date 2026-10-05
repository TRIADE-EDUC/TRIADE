<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authorities_uniform_titles.class.php,v 1.5 2016/09/21 13:09:44 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authorities_uniform_titles extends cms_module_common_selector_generic_authorities_type{
	
	public function __construct($id=0){
		$this->authorities_type = AUT_TABLE_TITRES_UNIFORMES;
		$this->vedette_class_name = 'vedette_titres_uniformes';
		parent::__construct($id);
	}
	
	protected function get_sub_selectors(){
		$sub_selectors = parent::get_sub_selectors();
		$sub_selectors[] = "cms_module_common_selector_authorities_uniform_titles_by_record";
		$sub_selectors[] = "cms_module_common_selector_authorities_uniform_titles_by_expressions_of";
		$sub_selectors[] = "cms_module_common_selector_authorities_uniform_titles_by_other_links";
		return $sub_selectors;
	}
	
	/**
	 * Retourne les identifiants non uniques des autorités
	 */
	public function get_authorities_raw_ids() {
		if (!$this->authorities_raw_ids) {
			$values = parent::get_authorities_raw_ids();
			if (is_array($values)) {
				$this->authorities_raw_ids = array();
				// On trie par titre par défaut
				$query = 'select tu_id from titres_uniformes where tu_id in ("'.implode('","', $values).'") order by index_tu';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$this->authorities_raw_ids[] = $row->tu_id;
					}
				}
			}
		}
		return $this->authorities_raw_ids;
	}
}