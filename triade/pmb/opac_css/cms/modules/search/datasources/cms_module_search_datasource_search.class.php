<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_search_datasource_search.class.php,v 1.6.8.1 2024/07/09 10:22:19 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_search_datasource_search extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_search_selector_dest"
		);
	}
	
	public function get_datas(){
		$selector = $this->get_selected_selector();
		$datas = array();
		if($selector){
			$dests =  $selector->get_value();
			$query = "select managed_module_box from cms_managed_modules join cms_cadres on id_cadre = '".($this->cadre_parent*1)."' and cadre_object = managed_module_name";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$box = pmb_mysql_result($result,0,0);
				$infos =unserialize($box);
				foreach($dests as $dest){
					if(!empty($infos['module']['search_dests'][$dest])){
						$destination = $infos['module']['search_dests'][$dest];
						$destination['default_segment'] = 0;
						if(!empty($destination['universe']) && $destination['universe'] != 0){
							$query = "select search_universe_default_segment from search_universes where id_search_universe = ".$destination['universe'];
							$result = pmb_mysql_query($query);
							$destination['default_segment'] = pmb_mysql_result($result,0,0);
						}
						$datas[]=$destination;
					}
				}
			}
		}
		return $datas;
	}
}