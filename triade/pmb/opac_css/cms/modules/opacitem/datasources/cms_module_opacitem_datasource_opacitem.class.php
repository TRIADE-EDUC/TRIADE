<?php

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_opacitem_datasource_opacitem extends cms_module_common_datasource{

	public function __construct($id=0){
		parent::__construct($id);
	}

	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_opacitem_selector_opacitem"
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
		if ($this->parameters['selector'] != "") {
			if (is_countable($this->selectors)) {
				for($i=0 ; $i<count($this->selectors) ; $i++){
					if($this->selectors[$i]['name'] == $this->parameters['selector']){
						$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
						break;
					}
				}
			}

			if (isset($selector)) {
				return $selector->get_value();
			}
		}
		return false;
	}

}