<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authorities_uniform_titles_by_record.class.php,v 1.2 2016-09-20 14:33:53 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_authorities_uniform_titles_by_record extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_sub_selectors(){
		return array(
			"cms_module_common_selector_generic_record"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		global $dbh;
		if(!$this->value){
			$this->value = array();
			$sub = new cms_module_common_selector_generic_record($this->get_sub_selector_id("cms_module_common_selector_generic_record"));
			$query = 'select ntu_num_tu from notices_titres_uniformes where ntu_num_notice = "'.($sub->get_value()*1).'" order by ntu_ordre';
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->value[] = $row->ntu_num_tu;
				}
			}
		}
		return $this->value;
	}
}