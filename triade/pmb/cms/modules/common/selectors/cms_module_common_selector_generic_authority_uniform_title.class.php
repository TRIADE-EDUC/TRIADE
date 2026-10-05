<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authority_uniform_title.class.php,v 1.1 2016/04/15 10:29:21 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authority_uniform_title extends cms_module_common_selector_generic_authority_type{

	public function __construct($id=0){
		$this->authority_type = AUT_TABLE_TITRES_UNIFORMES;
		parent::__construct($id);
	}
}