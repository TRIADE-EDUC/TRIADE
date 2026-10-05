<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas_datasource_metadatas_article.class.php,v 1.6 2023/05/05 08:40:14 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas_datasource_metadatas_article extends cms_module_metadatas_datasource_metadatas_generic{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_common_selector_article",
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
			$article_id = $selector->get_value();
			$article_ids = $this->filter_datas("articles",array($selector->get_value()));
			if($article_ids[0]){
				$group_metadatas = parent::get_group_metadatas();
				
				$article = new cms_article($article_ids[0]);
				$links = ["article" => $this->get_constructed_link("article", "!!id!!")];
				$datas = $article->format_datas($links);
				$datas->details = $datas;
				$datas = array_merge($datas,parent::get_datas());
				$datas->logo_url = $datas->logo["big"];
				//Passage en tableau pour le render
				$datas = [$datas];
				foreach ($group_metadatas as $i=>$metadatas) {
				    if (isset($metadatas["metadatas"]) && is_array($metadatas["metadatas"])) {
						foreach ($metadatas["metadatas"] as $key=>$value) {
							try {
								$template_path = $base_path.'/temp/'.LOCATION.'_datasource_metadatas_article_'.$article_ids[0].'_'.md5($value);
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
		$datas = cms_article::get_format_data_structure();
		$datas[] = array(
				'var' => "link",
				'desc'=> $this->msg['cms_module_metadatas_datasource_metadatas_article_link_desc']
		);
		
		$format_datas = array(
				array(
						'var' => "details",
						'desc' => $this->msg['cms_module_metadatas_datasource_metadatas_article_article_desc'],
						'children' => $this->prefix_var_tree($datas,"details")
				)
		);
		$format_datas = array_merge(parent::get_format_data_structure(),$format_datas);
		return $format_datas;
	}
}