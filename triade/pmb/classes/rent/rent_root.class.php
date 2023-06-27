<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_root.class.php,v 1.11 2017-11-21 12:01:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");

class rent_root {
	
	/**
	 * Type d'objet
	 * @var string
	 */
	protected $objects_type;
	
	/**
	 * Liste des objets
	 * @var rent_request
	 */
	protected $objects;
	
	/**
	 * Tri appliqué
	 */
	protected $applied_sort;
	
	/**
	 * Filtres
	 * @var array
	 */
	protected $filters;
	
	/**
	 * Pagination
	 * @var array
	 */
	protected $pager;
	
	/**
	 * Message d'information pour l'utilisateur
	 * @var string
	 */
	protected $messages;

	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		$this->objects_type = str_replace('rent_', '', get_class($this));
		$this->init_filters($filters);
		$this->init_pager($pager);
		$this->init_applied_sort($applied_sort);
		$this->fetch_data();
		$this->_sort();
		$this->_limit();
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->messages = "";
	}
	
	/**
	 * Sélecteur des exercices comptables en cours
	 */
	static public function gen_selector_exercices($id_entity, $filter_type = '', $selected = 0) {
		global $msg;
	
		$display = '';
		entites::setSessionBibliId($id_entity);
		$query = exercices::listByEntite($id_entity,1);
		$display=gen_liste($query,'id_exercice','libelle', $filter_type.'_search_form_exercices', '', $selected, 0,$msg['acquisition_account_exercices_empty'],0,'');
			
		return $display;
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
	}
	
	/**
	 * Initialisation de la pagination
	 */
	public function init_pager($pager=array()) {
		
		$this->pager = array(
			'page' => 1,
			'nb_per_page' => 15,
			'nb_results' => 0,
			'nb_page' => 1
		);
		if(isset($_SESSION['rent_'.$this->objects_type.'_pager']['nb_per_page'])) {
			$this->pager['nb_per_page'] = $_SESSION['rent_'.$this->objects_type.'_pager']['nb_per_page'];
		}
		if(count($pager)){
			foreach ($pager as $key => $val){
				$this->pager[$key]=$val;
			}
		}
	}
	
	/**
	 * Initialisation du tri appliqué
	 */
	public function init_applied_sort($applied_sort=array()) {
		$this->applied_sort = array(
				'by' => 'id',
				'asc_desc' => 'desc'
		);
		if(isset($_SESSION['rent_'.$this->objects_type.'_applied_sort']['by'])) {
			$this->applied_sort['by'] = $_SESSION['rent_'.$this->objects_type.'_applied_sort']['by'];
			if(isset($_SESSION['rent_'.$this->objects_type.'_applied_sort']['asc_desc'])) {
				$this->applied_sort['asc_desc'] = $_SESSION['rent_'.$this->objects_type.'_applied_sort']['asc_desc'];
			} else {
				$this->applied_sort['asc_desc'] = 'asc';
			}
		}
		if(count($applied_sort)){
			foreach ($applied_sort as $key => $val){
				$this->applied_sort[$key]=$val;
			}
		}
		//Sauvegarde du tri appliqué en session
		$this->set_applied_sort_in_session();
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		
	}
	
	/**
	 * Pagination provenant du formulaire
	 */
	public function set_pager_from_form() {
		$page = $this->objects_type.'_page';
		global ${$page};
		$nb_per_page = $this->objects_type.'_nb_per_page';
		global ${$nb_per_page};
		
		if(${$page}*1) {
			$this->pager['page'] = ${$page}*1;
		}
		if(${$nb_per_page}*1) {
			$this->pager['nb_per_page'] = ${$nb_per_page}*1;
		}
		//Sauvegarde de la pagination en session
		$this->set_pager_in_session();
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		return "";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		return '';
	}
	
	/**
	 * Limit SQL
	 */
	protected function _get_query_pager() {
		
		$limit_query = '';
		
		$this->set_pager_from_form();
		
		$limit_query .= ' limit '.(($this->pager['page']-1)*$this->pager['nb_per_page']).', '.$this->pager['nb_per_page'];
		
		return $limit_query;
	}
	
	protected function intcmp($a,$b) {
	    if((int)$a == (int)$b)return 0;
	    else if((int)$a  > (int)$b)return 1;
	    else if((int)$a  < (int)$b)return -1;
	}
	
	/**
	 * Fonction de callback
	 * @param $a
	 * @param $b
	 */
	protected function _compare_objects($a, $b) {

	}
	
	/**
	 * Tri des objets
	 */
	protected function _sort() {
		if($this->applied_sort['asc_desc'] == 'desc') {
			usort($this->objects, array($this, "_compare_objects"));
			$this->objects= array_reverse($this->objects);
		} else {
			usort($this->objects, array($this, "_compare_objects"));
		}
	}
	
	/**
	 * Limite des demandes
	 */
	protected function _limit() {
	
		$this->set_pager_from_form();
		
		$this->objects = array_slice(
				$this->objects, 
				($this->pager['page']-1)*$this->pager['nb_per_page'], 
				$this->pager['nb_per_page']);
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		return '';
	}
	
	/**
	 * Construction dynamique des cellules du header 
	 * @param unknown $name
	 */
	protected function _get_cell_header($name) {
		return '';
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		return '';
	}
	
	/**
	 * Affiche la recherche + la liste des demandes
	 */
	public function get_display_list() {
		return '';
	}
	
	protected function pager() {
		global $msg;
		
		if (!$this->pager['nb_results']) return;
		
		$this->pager['nb_page']=ceil($this->pager['nb_results']/$this->pager['nb_per_page']);
		$suivante = $this->pager['page']+1;
		$precedente = $this->pager['page']-1;
		$nav_bar = '';
		// affichage du lien précédent si nécéssaire
		if($precedente > 0) {
			$nav_bar .= "<a href='#' onClick=\"document.".$this->objects_type."_search_form.".$this->objects_type."_page.value=".$precedente."; document.".$this->objects_type."_search_form.submit(); return false;\"><img src='".get_url_icon('left.gif')."' style='border:0px; margin:3px 3px'  title='$msg[48]' alt='[$msg[48]]' class='align_middle'></a>";
		}
		$deb = $this->pager['page'] - 10 ;
		if ($deb<1) $deb=1;
		for($i = $deb; ($i <= $this->pager['nb_page']) && ($i <= $this->pager['page']+10); $i++) {
			if($i==$this->pager['page']) $nav_bar .= "<strong>".$i."</strong>";
			else $nav_bar .= "<a href='#' onClick=\"document.".$this->objects_type."_search_form.".$this->objects_type."_page.value=".$i."; document.".$this->objects_type."_search_form.submit(); return false;\">".$i."</a>";
			if($i<$this->pager['nb_page']) $nav_bar .= " ";
		}
		if($suivante <= $this->pager['nb_page']) {
			$nav_bar .= "<a href='#' onClick=\"document.".$this->objects_type."_search_form.".$this->objects_type."_page.value=".$suivante."; document.".$this->objects_type."_search_form.submit(); return false;\"><img src='".get_url_icon('right.gif')."' style='border:0px; margin:3px 3px' title='$msg[49]' alt='[$msg[49]]' class='align_middle'></a>";
		}
		if($this->pager['nb_page'] && ($this->pager['nb_results'] > $this->pager['nb_per_page'])) {
			$nav_bar .= " | ".$msg['per_page']." ";
			$nav_bar .= "<a href='#' onClick=\"document.".$this->objects_type."_search_form.".$this->objects_type."_page.value=1;document.".$this->objects_type."_search_form.".$this->objects_type."_nb_per_page.value=25; document.".$this->objects_type."_search_form.submit(); return false;\"> 25 </a>";
			$nav_bar .= "<a href='#' onClick=\"document.".$this->objects_type."_search_form.".$this->objects_type."_page.value=1;document.".$this->objects_type."_search_form.".$this->objects_type."_nb_per_page.value=50; document.".$this->objects_type."_search_form.submit(); return false;\"> 50 </a>";
			$nav_bar .= "<a href='#' onClick=\"document.".$this->objects_type."_search_form.".$this->objects_type."_page.value=1;document.".$this->objects_type."_search_form.".$this->objects_type."_nb_per_page.value=100; document.".$this->objects_type."_search_form.submit(); return false;\"> 100 </a>";
		}
		// affichage de la barre de navigation
		return "<div class='center'><br />".$nav_bar."<br /></div>";
	}
	
	protected function _get_query_human() {
		return '';
	}
	
	/**
	 * Sauvegarde des filtres en session
	 */
	public function set_filter_in_session() {
		foreach ($this->filters as $name=>$filter) {
			$_SESSION['rent_'.$this->objects_type.'_filter'][$name] = $filter;
		}
	}
	
	/**
	 * Sauvegarde de la pagination en session
	 */
	public function set_pager_in_session() {
		$_SESSION['rent_'.$this->objects_type.'_pager']['nb_per_page'] = $this->pager['nb_per_page'];
	}
	
	/**
	 * Sauvegarde du tri appliqué en session
	 */
	public function set_applied_sort_in_session() {
		foreach ($this->applied_sort as $name=>$applied_sort) {
			$_SESSION['rent_'.$this->objects_type.'_applied_sort'][$name] = $applied_sort;
		}
	}
	
	public function get_objects_type() {
		return $this->objects_type;
	}
	
	public function get_objects() {
		return $this->objects;
	}
	
	public function get_applied_sort() {
		return $this->applied_sort;
	}
	
	public function get_filters() {
		return $this->filters;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function set_objects_type($objects_type) {
		$this->objects_type = $objects_type;
	}
	
	public function set_objects($objects) {
		$this->objects = $objects;
	}
	
	public function set_applied_sort($applied_sort) {
		$this->applied_sort = $applied_sort;
	}

	public function set_filters($filters) {
		$this->filters = $filters;
	}
	
	public function set_messages($messages) {
		$this->messages = $messages;
	}
}