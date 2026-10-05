<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_article.class.php,v 1.13.8.1 2024/12/18 16:01:30 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_article extends cms_module_common_datasource{

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
			"cms_module_common_selector_global_var",
		    "cms_module_common_selector_article_by_section_and_cp_and_reader_status",
		    "cms_module_common_selector_article_by_section_and_cp_and_reader_category",
		    "cms_module_common_selector_article_by_section_and_cp_and_search_segment",
		    "cms_module_common_selector_article_by_section_and_cp_and_search_universe"
		);
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;

		$this->parameters= array();
		$this->parameters['selector'] = $selector_choice;
		return parent::save_form();
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if ($selector){
			$article_ids = $this->filter_datas("articles",array(intval($selector->get_value())));
			$article_ids[0] = intval($article_ids[0] ?? 0);
			if (!empty($article_ids[0])){
			    $article = cms_provider::get_instance("article",$article_ids[0]);
			    $links = ["article" => $this->get_constructed_link("article", "!!id!!")];
				return $article->format_datas($links);
			}
		}
		return false;
	}

	public function get_format_data_structure(){
		return cms_article::get_format_data_structure();
	}
}