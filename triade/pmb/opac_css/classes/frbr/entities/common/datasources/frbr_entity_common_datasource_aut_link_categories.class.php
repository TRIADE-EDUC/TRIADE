<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_categories.class.php,v 1.1 2017/05/05 17:07:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link_categories extends frbr_entity_common_datasource_aut_link_authorities {
	
	protected $key_name = 'id_noeud';
	protected $table_name = 'noeuds';
	protected $authority_type = AUT_TABLE_CATEG;
	
	public function __construct($id=0){
		$this->entity_type = "categories";
		parent::__construct($id);
	}
}