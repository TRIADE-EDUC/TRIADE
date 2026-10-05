<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authority_authperso.class.php,v 1.2 2019/09/03 15:33:13 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_authority_authperso extends cms_module_common_selector_generic_authorities_type {
	
	public function __construct($id=0){
    	$this->authorities_type = AUT_TABLE_AUTHPERSO;
		$this->vedette_class_name = 'vedette_authperso';
		parent::__construct($id);
	}
	
	protected function get_sub_selectors(){
		$sub_selectors = parent::get_sub_selectors();
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
				$query = 'select author_id from authors where author_id in ("'.implode('","', $values).'") order by index_author';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$this->authorities_raw_ids[] = $row->author_id;
					}
				}
			}
		}
		return $this->authorities_raw_ids;
	}
}