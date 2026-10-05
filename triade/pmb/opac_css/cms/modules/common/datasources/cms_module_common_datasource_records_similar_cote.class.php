<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_similar_cote.class.php,v 1.7 2022/01/10 08:31:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_similar_cote extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_record_permalink",
			"cms_module_common_selector_record",
			"cms_module_common_selector_env_var"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			if($value!= 0){
				//on part du premier exemplaire...
				$query ="select expl_cote from exemplaires where expl_notice = '".($value*1)."' order by expl_cote  limit 1 ";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$row = pmb_mysql_fetch_object($result);
					$cote = $row->expl_cote;
					$query = "
					(select distinct expl_notice,expl_cote from exemplaires where expl_notice!=0 and expl_bulletin = 0 and expl_cote >= '".$cote."' and expl_notice = '".($value*1)."' order by expl_cote asc limit 5)
						union 
					(select distinct expl_notice,expl_cote from exemplaires where expl_notice!=0 and expl_bulletin = 0 and expl_cote < '".$cote."' and expl_notice = '".($value*1)."' order by expl_cote desc limit 5)" ;
					
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result) > 0){
						$return["title"] = "";
						while($row = pmb_mysql_fetch_object($result)){
							$return["records"][] = $row->expl_notice;
						}
					}
					$return['records'] = $this->filter_datas("notices",$return['records']);
					if($this->parameters['nb_max_elements'] > 0){
						$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
					}
				}
			}
			return $return;
		}
		return false;
	}
}