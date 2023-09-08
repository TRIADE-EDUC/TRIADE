<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_works_expressions_of.class.php,v 1.8 2017-10-16 15:31:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datasource_works_expressions_of extends frbr_entity_works_datasource_works_links {
	
	protected static $type = "expression_of";
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct oeuvre_link_from as parent, oeuvre_link_to as id FROM tu_oeuvres_links 
			WHERE oeuvre_link_expression = 1 AND oeuvre_link_other_link=0 AND oeuvre_link_from IN (".implode(',', $datas).")";
		if (!empty($this->work_link_type)) {
			if (is_array($this->work_link_type)) {
				$query .= " AND oeuvre_link_type IN ('".implode("','", $this->work_link_type)."')";
			} else {
				$query .= " AND oeuvre_link_type = '".$this->work_link_type."'";
			}
		}
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}