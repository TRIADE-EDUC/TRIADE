<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_datasource_concepts.class.php,v 1.6 2021/03/01 14:02:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_datasource_concepts extends frbr_entity_common_datasource_concept {
    
    protected $origin_type = TYPE_NOTICE;
    
	public function __construct($id=0){
		$this->entity_type = 'concepts';
		parent::__construct($id);
	}
}