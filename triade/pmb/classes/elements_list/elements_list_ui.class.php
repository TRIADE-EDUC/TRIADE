<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_list_ui.class.php,v 1.22.8.1 2025/01/29 13:16:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/h2o/pmb_h2o.inc.php');

class elements_list_ui {
	
	protected $contents;
	protected $groups;
	protected $nb_results;
	protected $elements_list;
	protected $mixed;
	protected $current_url;
	/**
	 * Nombre d'éléments total de la liste après passage des filtres
	 * @var int
	 */
	protected $nb_filtered_results;
	
	protected $context_parameters;
	
	protected $instances_class_name;
	
	public function __construct($contents, $nb_results, $mixed, $groups=array(), $nb_filtered_results = 0) {
		$this->contents = $contents;
		$this->nb_results = $nb_results;
		$this->mixed = $mixed;
		$this->groups = $groups;
		$this->nb_filtered_results = $nb_filtered_results;
		$this->context_parameters = array();
	}
	
	protected function generate_elements_list(){
		global $class_path;
		
		$elements_list = '';
		$recherche_ajax_mode = 0;
		$nb = 0;
		//On est sur une liste mixte
		if(static::class == 'elements_list_ui') {
			foreach($this->contents as $element){
				if(!$recherche_ajax_mode && ($nb++>5)) $recherche_ajax_mode=1;
				$class_name = 'elements_'.$element['type'].'_list_ui';
				if(!isset($this->instances_class_name[$class_name])) {
					require_once($class_path.'/elements_list/'.$class_name.'.class.php');
					$this->instances_class_name[$class_name] = new $class_name($this->contents, $this->nb_results, $this->mixed, $this->groups, $this->nb_filtered_results);
				}
				$elements_list.= $this->instances_class_name[$class_name]->generate_element($element['id'], $recherche_ajax_mode);
			}
		} else {
			foreach($this->contents as $element_id){
				if(!$recherche_ajax_mode && ($nb++>5)) $recherche_ajax_mode=1;
				$elements_list.= $this->generate_element($element_id, $recherche_ajax_mode);
			}
		}
		return $elements_list;
	}
	
	protected function generate_element($element_id, $recherche_ajax_mode=0){
		//Surcharge dans les classes dérivées
	}
	
	protected static function render($template_path, $context, $context_parameters=array()) {
		if(file_exists($template_path)){
			$h2o = H2o_collection::get_instance($template_path, array('context_parameters', $context_parameters));
			return $h2o->render($context);
		}
		return '';
	}
	
	public function get_elements_list(){
		if (!$this->elements_list) {
			$this->elements_list = array();
			$recherche_ajax_mode=0;
			$this->elements_list = $this->generate_elements_list();
		}
		return $this->elements_list;
	}
		
	public function get_elements_list_nav(){
		global $tab_page;
		global $pmb_nb_elems_per_tab;
		global $tab_nb_per_page;
		
		if(!$tab_page){
			$tab_page = 1;
		}
		if(!$tab_nb_per_page){
			$tab_nb_per_page = $pmb_nb_elems_per_tab;
		}
		$nav_bar = $this->get_tabs_pagination($tab_nb_per_page, $tab_page);
		
		return $nav_bar;
	}
	
	public function is_mixed(){
		return $this->mixed;
	}
	
	public function get_groups() {
		return $this->groups;
	}
	
	public function set_current_url($current_url){
		$this->current_url = $current_url;
	}
	
	public function get_current_url(){
		return $this->current_url;
	}
	
	private function get_tabs_pagination($tab_nb_per_page=0, $tab_page=0, $etendue=10, $aff_extr=false ){
		global $msg, $charset;
		
		$is_filtered = false;
		if ($this->groups && is_array($this->groups)) {
			foreach (array_keys($this->groups) as $group_name) {
				if (isset($_SESSION['elements_list_filters'][$group_name]) && $_SESSION['elements_list_filters'][$group_name] && count($_SESSION['elements_list_filters'][$group_name])) {
					$is_filtered = true;
					break;
				}
			}
		}

		if ($is_filtered) {
			$nb_results = $this->nb_filtered_results;
		} else {
			$nb_results = $this->nb_results;
		}
		// Si on n'a pas de résultats, pas la peine d'aller plus loin
		if (!$nb_results) return '';
		$nbepages = ceil($nb_results/$tab_nb_per_page);
		$suivante = $tab_page+1;
		$precedente = $tab_page-1;
		$deb = $tab_page - $etendue;
		if ($deb<1) $deb=1;
		$fin = $tab_page + $etendue;
		if($fin>$nbepages)$fin=$nbepages;
	
		$nav_bar = "";
		
		$nav_bar = "<div id='result_per_page' style='position: absolute;'><input type='text' name='tab_nb_per_page' id='tab_nb_per_page' class='saisie-2em' value='".$tab_nb_per_page."' />&nbsp;".htmlentities($msg['1905'], ENT_QUOTES, $charset)."&nbsp;";
		$nav_bar.= "<input type='button' class='bouton' value='".$msg['actualiser']."' ";
		$nav_bar.="onclick=\"try{
		var page=".$tab_page.";
		var old_tab_nb_per_page=".$tab_nb_per_page.";
		var tab_nbr_lignes=".$nb_results.";
		var new_tab_nb_per_page=document.getElementById('tab_nb_per_page').value;
		var new_nbepages=Math.ceil(tab_nbr_lignes/new_tab_nb_per_page);
		if(page>new_nbepages) page=new_nbepages;
		document.location='".$this->current_url."&tab_page='+page+'&tab_nb_per_page='+new_tab_nb_per_page;
		}catch(e){}; \" /></div>";
	
	
		$nav_bar .= '<div class="center">';
		if($aff_extr && (($tab_page-$etendue)>1) ) {
			$nav_bar .= "<a id='premiere' href='".$this->current_url."&tab_page=1&tab_nb_per_page=".$tab_nb_per_page."' ><img src='".get_url_icon('first.gif')."' style='border:0px; margin:6px 6px' alt='".$msg['first_page']."' class='align_middle' title='".$msg['first_page']."' /></a>";
		}
	
		// affichage du lien precedent si necessaire
		if($precedente > 0) {
			$nav_bar .= "<a id='precedente' href='".$this->current_url."&tab_page=".$precedente."&tab_nb_per_page=".$tab_nb_per_page."' ><img src='".get_url_icon('left.gif')."' style='border:0px; margin:6px 6px' alt='".$msg[48]."' class='align_middle' title='".$msg[48]."' /></a>";
		}
	
		for ($i = $deb; ($i <= $nbepages) && ($i<=$tab_page+$etendue) ; $i++) {
			if($i==$tab_page) {
				$nav_bar .= "<strong>".$i."</strong>";
			} else {
				$nav_bar .= "<a href='".$this->current_url."&tab_page=".$i."&tab_nb_per_page=".$tab_nb_per_page."' >".$i."</a>";
			}
			if($i<$nbepages) $nav_bar .= " ";
		}
	
	
		if ($suivante<=$nbepages) {
			$nav_bar .= "<a href='".$this->current_url."&tab_page=".$suivante."&tab_nb_per_page=".$tab_nb_per_page."' ><img src='".get_url_icon('right.gif')."' style='border:0px; margin:6px 6px' alt='".$msg[49]."' class='align_middle' title='".$msg[49]."' /></a>";
		}
	
		if($aff_extr && (($tab_page+$etendue)<$nbepages) ) {
			$nav_bar .= "<a id='derniere' href='".$this->current_url."&tab_page=".$nbepages."&tab_nb_per_page=".$tab_nb_per_page."' ><img src='".get_url_icon('last.gif')."' style='border:0px; margin:6px 6px' alt='".$msg['last_page']."' class='align_middle' title='".$msg['last_page']."' /></a>";
		}
		$nav_bar .= '</div>';
		
		$nav_bar = "<div id='navbar_container' style='position: relative;'>".$nav_bar."</div>";
		return $nav_bar ;
	}
	
	public function get_nb_results() {
		return $this->nb_results;
	}
	
	public function is_expandable() {
		return true;
	}
	
	public function get_context_parameters() {
		return $this->context_parameters;
	}
	
	public function set_context_parameters($context_parameters=array()) {
		$this->context_parameters = $context_parameters;
	}
	
	public function add_context_parameter($key, $value) {
		$this->context_parameters[$key] = $value;
	}
	
	public function delete_context_parameter($key) {
		unset($this->context_parameters[$key]);
	}
	
	public function can_display_content() {
	    return ($this->nb_results != 0);
	}
}