<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_datasource_categories_link.class.php,v 1.3 2024/03/13 10:43:13 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_datasource_categories_link extends frbr_entity_common_datasource {

    public $origin_entity;

    public $prefix;

    public function __construct($id=0) {
        $this->entity_type = "categories";
        $this->origin_entity = "records";
        parent::__construct($id);
        $this->prefix = 'categ';
    }

    /*
     * Récupération des données de la source...
     */
    public function get_datas($datas = array()) {
        $query = "SELECT distinct num_noeud as id, notcateg_notice as parent
	              FROM notices_categories
	              WHERE notcateg_notice IN (".implode(',', $datas).")";
        $datas = $this->get_datas_from_query($query);
        $datas = parent::get_datas($datas);
        return $datas;
    }
}