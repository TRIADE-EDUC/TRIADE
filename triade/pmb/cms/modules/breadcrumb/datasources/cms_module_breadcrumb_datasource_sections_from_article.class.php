<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_breadcrumb_datasource_sections_from_article.class.php,v 1.8.10.1 2025/02/25 13:40:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_breadcrumb_datasource_sections_from_article extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			'cms_module_common_selector_article',
			'cms_module_common_selector_env_var'
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$article_id = intval($selector->get_value() ?? 0);
			if($article_id){
			    $article = new cms_article($article_id);
			    $links = ["article" => $this->get_constructed_link("article", "!!id!!")];
			    $datas = [
			        'article' => $article->format_datas($links),
			        'sections' => array()
			    ];
				$query = "select num_section from cms_articles where id_article = '". $article_id ."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$section_id = pmb_mysql_result($result,0,0);
					$section_id = intval($section_id);
					if($section_id){
						$i=0;
						do {
							$i++;
							$query = "select id_section,section_num_parent from cms_sections where id_section = '". $section_id ."'";
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$row = pmb_mysql_fetch_object($result);
								$section_id = $row->section_num_parent;
								$datas['sections'][] = $row->id_section;
								
							}else{
								break;
							}
						//en théorie on sort toujours, mais comme c'est un pays formidable, on lock à 100 itérations...
						}while ($row->section_num_parent != 0 || $i>100);
						$datas['sections'] = array_reverse($datas['sections']);
					}
				}
				return $datas;
			}
		}
		return false;
	}
}