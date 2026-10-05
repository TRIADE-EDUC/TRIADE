<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_expl_datasource_custom_fields.class.php,v 1.1 2019/08/19 09:27:30 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_expl_datasource_custom_fields extends frbr_entity_common_datasource_custom_fields {
	
	public function __construct($id=0){
		$this->entity_type = "expl";
		parent::__construct($id);
	}
}