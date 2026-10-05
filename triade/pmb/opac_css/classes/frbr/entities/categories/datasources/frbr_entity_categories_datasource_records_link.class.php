<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_categories_datasource_records_link.class.php,v 1.2 2019/10/21 13:47:50 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_categories_datasource_records_link extends frbr_entity_common_datasource {
    
    public function __construct($id=0) {
        $this->entity_type = "records";
        $this->origin_entity = "categories";
        parent::__construct($id);
        $this->prefix = 'notices';
    }
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas = array()) {
	    $query = "SELECT distinct notcateg_notice as id, num_noeud as parent
	              FROM notices_categories
	              WHERE num_noeud IN (".implode(',', $datas).")";
	    $datas = $this->get_datas_from_query($query);
	    $datas = parent::get_datas($datas);
	    return $datas;
	}
}