<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_shelveslist_datasource_shelveslist.class.php,v 1.8 2018-05-25 12:05:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_shelveslist_datasource_shelveslist extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = false;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_shelves_generic"
		);
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"default",
			"idetagere",
			"name"	
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $opac_url_base;
		global $opac_etagere_order;
		
		$selector = $this->get_selected_selector();
		if ($selector) {
			$return = array();
			if (count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$return[] = $value*1;
				}
			}
			
			if(count($return)){
				$query = "select idetagere, name, comment from etagere where idetagere in ('".implode("','",$return)."')";
				if(empty($this->parameters["sort_by"]) || $this->parameters["sort_by"] == 'default') {
					if (!$opac_etagere_order) $opac_etagere_order =" name ";
					$query .= " order by ".$opac_etagere_order;
				} else {
					$query .= " order by ".$this->parameters["sort_by"];
					if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
				}
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$return = array();
					while($row=pmb_mysql_fetch_object($result)){
						$link_rss = "";
						$query2 = "select num_rss_flux from ((select etagere_id, group_concat(distinct caddie_id order by caddie_id asc separator ',') as gc0 from etagere_caddie group by etagere_id) a0 join (select num_rss_flux, group_concat(distinct num_contenant order by num_contenant asc separator ',') as gc1 from rss_flux_content where type_contenant='CAD' group by num_rss_flux) a1 on (a0.gc0 like a1.gc1)) where etagere_id = '".($row->idetagere*1)."'";
						$result2 = pmb_mysql_query($query2);
						if (pmb_mysql_num_rows($result2)) {
							while ($row2 = pmb_mysql_fetch_object($result2)) {
								$link_rss = "./rss.php?id=".$row2->num_rss_flux;
							}
						}
						$return[] = array("id" => $row->idetagere, "name" => $row->name, "comment" => $row->comment, "link_rss" => $link_rss, "link" => $this->get_constructed_link("shelve",$row->idetagere));
					}
				}
			}
			return array('shelves' => $return);
		}
		return false;
	}
}