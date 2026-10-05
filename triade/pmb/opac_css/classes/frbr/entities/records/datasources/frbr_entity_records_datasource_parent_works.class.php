<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_datasource_parent_works.class.php,v 1.1 2018/05/09 14:10:45 pmbs Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_datasource_parent_works extends frbr_entity_works_datasource_works_links {
        protected static $type='all';
    
	public function __construct($id=0){
		$this->entity_type = 'works';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct oeuvre_link_to as id, ntu_num_notice as parent FROM notices_titres_uniformes join tu_oeuvres_links
                        on (ntu_num_tu=oeuvre_link_from".(count($this->parameters->work_link_type)?" and oeuvre_link_type in ('".implode("','",$this->parameters->work_link_type)."')":"").")
			WHERE ntu_num_notice IN (".implode(',', $datas).") union all select distinct oeuvre_link_from as id, ntu_num_notice as parent FROM notices_titres_uniformes join tu_oeuvres_links
                        on (ntu_num_tu=oeuvre_link_to".(count($this->parameters->work_link_type)?" and oeuvre_link_type in ('".implode("','",$this->parameters->work_link_type)."')":"").")
			WHERE ntu_num_notice IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}