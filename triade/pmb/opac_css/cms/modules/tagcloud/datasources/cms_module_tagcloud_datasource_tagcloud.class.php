<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_tagcloud_datasource_tagcloud.class.php,v 1.2 2014/06/27 15:13:49 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_tagcloud_datasource_tagcloud extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
		);
	}
	
	public function get_form(){
		$form = parent::get_form();
		$form.= $this->format_text($this->msg['cms_module_tagcloud_datasource_tagcloud_no_parameters']);

		return $form;
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;
		
		$this->parameters= array();
		return parent::save_form();
	}


	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
// 	$selector = $this->get_selected_selector();
// 		if($selector){
// 			$article_id = $selector->get_value();
// 			$article_ids = $this->filter_datas("articles",array($selector->get_value()));
// 			if($article_ids[0]){
// 				$article = new cms_article($article_ids[0]);
// 				return $article->format_datas();
// 			}
// 		}
// 		return false;
		return array(
			array( 
				'label' => "un label 1",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array( 
				'label' => "un label ",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array( 
				'label' => "un label 4",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array( 
				'label' => "un label 5",
				'link' => "ici un lien a mettre",
				'weight' => 2,
				'js' => ""
			),
			array( 
				'label' => "un label 6",
				'link' => "ici un lien a mettre",
				'weight' => 4,
				'js' => ""
			),
			array( 
				'label' => "un label 7",
				'link' => "ici un lien a mettre",
				'weight' => 10,
				'js' => ""
			),
			array( 
				'label' => "un label 8",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array( 
				'label' => "un label 9",
				'link' => "ici un lien a mettre",
				'weight' => 1,
				'js' => ""
			),
			array( 
				'label' => "un label 10",
				'link' => "ici un lien a mettre",
				'weight' => 2,
				'js' => ""
			)	
		);
	}
}