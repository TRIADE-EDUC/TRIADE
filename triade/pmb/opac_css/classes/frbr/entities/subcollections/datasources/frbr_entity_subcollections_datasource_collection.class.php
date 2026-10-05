<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_subcollections_datasource_collection.class.php,v 1.1.2.1 2025/04/29 12:27:40 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_subcollections_datasource_collection extends frbr_entity_common_datasource
{

    public function __construct($id = 0)
    {
        $this->entity_type = 'collections';
        parent::__construct($id);
    }

    /*
	 * Récupération des données de la source...
	 */
    public function get_datas($datas = array())
    {
        $query = "select distinct sub_coll_parent as id, sub_coll_id as parent FROM sub_collections
			WHERE sub_coll_id IN (" . implode(',', $datas) . ")";
        $datas = $this->get_datas_from_query($query);
        $datas = parent::get_datas($datas);
        return $datas;
    }
}
