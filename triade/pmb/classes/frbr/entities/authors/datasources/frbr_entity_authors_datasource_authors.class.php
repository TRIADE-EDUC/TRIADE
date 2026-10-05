<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_datasource_authors.class.php,v 1.2 2017/04/25 15:22:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authors_datasource_authors extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'authors';
		parent::__construct($id);
	}
}