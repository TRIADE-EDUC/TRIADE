<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authority_type.class.php,v 1.4 2016/09/21 13:09:44 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authority_type extends cms_module_common_selector{
	
	/**
	 * Type de l'autorité
	 * @var int
	 */
	protected $authority_type;
	
	/**
	 * Identifiant non unique de l'autorité
	 * @var int
	 */
	protected $authority_raw_id;
	
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
			"cms_module_common_selector_env_var",
			"cms_module_common_selector_global_var"
		);
	}

	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			if (!$this->get_authority_raw_id() || !$this->authority_type) {
				return 0;
			}
			$query = 'select id_authority from authorities where type_object = "'.($this->authority_type*1).'" and num_object = "'.($this->authority_raw_id*1).'"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->value = pmb_mysql_result($result, 0, 0);
			}
		}
		return $this->value;
	}
	
	/**
	 * Retourne l'identifiant non unique de l'autorité
	 */
	public function get_authority_raw_id() {
		if (!$this->authority_raw_id) {
			$sub = $this->get_selected_sub_selector();
			$value = $sub->get_value();
			if (is_array($value)) {
				$value = $value[0];
			}
			$this->authority_raw_id = $value;
		}
		return $this->authority_raw_id;
	}
}