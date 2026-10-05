<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_merge_data_concepts.class.php,v 1.2 2021/02/24 13:25:48 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_merge_data_concepts extends frbr_entity_common_datasource_merge_data {
	
	public function __construct($id=0){
		$this->entity_type = "concepts";
		parent::__construct($id);
	}
}