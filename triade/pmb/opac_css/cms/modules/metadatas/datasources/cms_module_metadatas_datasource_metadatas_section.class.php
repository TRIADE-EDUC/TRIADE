<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas_datasource_metadatas_section.class.php,v 1.3 2018-08-23 15:09:39 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas_datasource_metadatas_section extends cms_module_metadatas_datasource_metadatas_generic{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_common_selector_section",
				"cms_module_common_selector_env_var",
				"cms_module_common_selector_global_var"
		);
	}
			
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
	    global $base_path;
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$section_id = $selector->get_value();
			$section_ids = $this->filter_datas("sections",array($section_id));
			if($section_ids[0]){
				$group_metadatas = parent::get_group_metadatas();
				
				$datas = array();
				$section = cms_provider::get_instance("section",$section_ids[0]);
				$datas = $section->format_datas();
				$datas["details"] = $datas;
				$datas = array_merge($datas,parent::get_datas());
				$datas['link'] = $this->get_constructed_link("section",$section_ids[0]);
				$datas['logo_url'] = $datas["logo"]["big"];
				foreach ($group_metadatas as $i=>$metadatas) {
					if (is_array($metadatas["metadatas"])) {
						foreach ($metadatas["metadatas"] as $key=>$value) {
							try {
							    $template_path = $base_path.'/temp/'.LOCATION.'_datasource_metadatas_article_'.$this->id;
							    if(!file_exists($template_path) || (md5($value) != md5_file($template_path))){
							        file_put_contents($template_path, $value);
							    }
							    $H2o = H2o_collection::get_instance($template_path);
							    $group_metadatas[$i]["metadatas"][$key] = $H2o->render($datas);
							}catch(Exception $e){
							    
							}
						}
					}
				}
				return $group_metadatas;
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){
		$datas = cms_section::get_format_data_structure();
		$datas[] = array(
				'var' => "link",
				'desc'=> $this->msg['cms_module_metadatas_datasource_metadatas_section_link_desc']
		);
		
		$format_datas = array(
				array(
						'var' => "details",
						'desc' => $this->msg['cms_module_metadatas_datasource_metadatas_section_section_desc'],
						'children' => $this->prefix_var_tree($datas,"details")
				)
		);
		$format_datas = array_merge(parent::get_format_data_structure(),$format_datas);
		return $format_datas;
	}
}