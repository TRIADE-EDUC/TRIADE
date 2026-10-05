<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_list_tab.class.php,v 1.8 2022/02/21 08:12:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class elements_list_tab {
	
	/**
	 * Nom de l'onglet
	 * @var string
	 */
	private $name;
	
	/**
	 * Libellé de l'onglet
	 * @var string
	 */
	private $label; 
	
	/**
	 * Type de contenu de l'onglet ('authorities' ou 'records')
	 * @var string
	 */
	private $content_type;
	
	/**
	 * Indique si les éléments de l'onglet doivent être regroupés
	 * @var array
	 */
	private $groups;
	
	/**
	 * Liste des ids des éléments de l'onglet
	 * @var array
	 */
	private $contents;
	
	/**
	 * Nombre de résultats total
	 * @var int
	 */
	private $nb_results;
	
	/**
	 * Indique si il s'agit d'un onglet mixte
	 * @var bool
	 */
	private $mixed;
	
	/**
	 * Filtres appliqués sur le contenu de l'onglet
	 * @var array
	 */
	private $filters = array();
	
	/**
	 * Tableau des éléments pour construire les requêtes
	 * @var array
	 */
	private $query_elements = array();
	
	/**
	 * Callable à appeler pour générer l'onglet
	 * @var callable
	 */
	private $callable = array();
	
	/**
	 * Dans le cas d'un onglet d'un seul type d'autorité, on spécifie ici le type de l'autorité
	 * @var int
	 */
	private $content_authority_type;
	
	/**
	 * Nombre de résultats après passage des filtres
	 * @var int
	 */
	private $nb_filtered_results = 0;
	
	/**
	 * Nombre de résultats potentiels avec les filtres
	 * @var int
	 */
	private $nb_prefiltered_results = 0;
	
	/**
	 * Constructeur d'un onglet de page autorité
	 * @param string $name Nom de l'onglet
	 * @param string $label Label de l'onglet
	 * @param string $content_type Type du contenu d'un onglet
	 * @param int $nb_results Nombre de résultats contenu dans l'onglet
	 * @param array $contents Contenu de l'onglet
	 */
	public function __construct($name, $label, $content_type, $mixed = false) {
		$this->name = $name;
		$this->label = $label;
		$this->content_type = $content_type;
		$this->mixed = $mixed;
	}
	
	/**
	 * Retourne le nom de l'onglet
	 * @return string Nom de l'onglet
	 */
	public function get_name(){
		return $this->name;
	}
	
	/**
	 * Retourne le label de l'onglet
	 * @return string Label de l'onglet
	 */
	public function get_label(){
		return $this->label;
	}
	
	/**
	 * Retourne le type du contenu d'un onglet
	 * @return string Type du contenu d'un onglet
	 */
	public function get_content_type(){
		return $this->content_type;
	}

	/**
	 * Retourne le nombre de résultats contenu dans l'onglet
	 * @return int Nombre de résultats contenu dans l'onglet
	 */
	public function get_nb_results(){
		return $this->nb_results;
	}
	
	/**
	 * Défini le nombre de résultats contenu dans l'onglet
	 * @param int $nb_results Nombre de résultats à affecter
	 */
	public function set_nb_results($nb_results){
		$this->nb_results = intval($nb_results);
	}
	
	/**
	 * Retourne le contenu de l'onglet
	 * @return array Tableau des résultats 
	 */
	public function get_contents(){
		return $this->contents;
	}
	
	/**
	 * Permet de définir le contenu de l'onglet
	 * @param array $contents Contenu à affecter
	 */
	public function set_contents($contents){
		$this->contents = $contents;
	}
	
	/**
	 * Permet de définir le groupement appliqué au contenu de l'onglet
	 * @param array $groups Contenu à affecter
	 */
	public function set_groups($groups){
		$this->groups = $groups;
	}
	
	/**
	 * Retourne le tableau des groupes
	 * @return array Tableau des groupes
	 */
	public function get_groups(){
		return $this->groups;
	}	
	
	/**
	 * Indique si l'onglet est mixte ou non
	 * @return bool 
	 */
	public function is_mixed(){
		return $this->mixed;
	}
	
	/**
	 * Ajoute un groupement à appliquer au contenu de l'onglet
	 * @param string $groups_parent Type des groupes à affecter
	 * @param array $groups Contenu à affecter
	 */
	public function add_groups($groups_parent, $groups) {
		$this->groups[$groups_parent] = $groups;
	}
	
	public function set_filters($filters) {
		$this->filters = $filters;
	}
	
	public function get_filters() {
		return $this->filters;
	}
	
	public function get_filter_values($filter_name) {
		global $elements_list_filters, ${'elements_list_filters_'.$filter_name.'_post'};
		if (isset(${'elements_list_filters_'.$filter_name.'_post'})) {
			$_SESSION['elements_list_filters'][$filter_name] = $elements_list_filters[$filter_name];
		}
		if (isset($_SESSION['elements_list_filters'][$filter_name])) {
			return $_SESSION['elements_list_filters'][$filter_name];
		}
		return array();
	}
	
	/**
	 * Indique si des filtres sont cochés
	 * @return boolean
	 */
	public function has_filters_values() {
		foreach ($this->filters as $filter) {
			// On vérifie que les filtres présents en session sont présents dans les filtres de l'élément sur lequel on est
			if (isset($this->groups[$filter['name']]['elements']) && is_array($this->groups[$filter['name']]['elements']) && count(array_intersect($this->get_filter_values($filter['name']), array_keys($this->groups[$filter['name']]['elements'])))) {
				return true;
			}
		}
		return false;
	}
	
	public function set_query_elements($query_elements = array()) {
		$this->query_elements = $query_elements;
	}
	
	public function get_query_elements() {
		return $this->query_elements;
	}
	
	public function set_callable($callable) {
		$this->callable = $callable;
	}
	
	public function get_callable() {
		return $this->callable;
	}
	
	public function set_content_authority_type($content_authority_type) {
		$this->content_authority_type = $content_authority_type;
	}
	
	public function get_content_authority_type() {
		return $this->content_authority_type;
	}
	
	/**
	 * Définit le nombre de résultats après passage des filtres
	 * @param int $nb_filtered_results Nombre de résultats après passage des filtres
	 */
	public function set_nb_filtered_results($nb_filtered_results) {
		$this->nb_filtered_results = intval($nb_filtered_results);
	}
	
	/**
	 * Retourne le nombre de résultats après passage des filtres
	 * @return int Nombre de résultats après passage des filtres
	 */
	public function get_nb_filtered_results() {
		return $this->nb_filtered_results;
	}
	
	/**
	 * Définit le nombre de résultats après passage des filtres
	 * @param int $nb_filtered_results Nombre de résultats après passage des filtres
	 */
	public function set_nb_prefiltered_results($nb_prefiltered_results) {
		$this->nb_prefiltered_results = intval($nb_prefiltered_results);
	}
	
	/**
	 * Retourne le nombre de résultats après passage des filtres
	 * @return int Nombre de résultats après passage des filtres
	 */
	public function get_nb_prefiltered_results() {
		return $this->nb_prefiltered_results;
	}
	
	public function can_display_tab() {
	    global $pmb_entity_graph_activate;
	    if ($this->content_type == "graph") {
	        return $pmb_entity_graph_activate;
	    }
	    return ($this->nb_results != 0);
	}
}