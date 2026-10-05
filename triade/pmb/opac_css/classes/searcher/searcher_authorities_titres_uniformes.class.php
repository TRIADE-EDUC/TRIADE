<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_titres_uniformes.class.php,v 1.3.8.1 2024/10/17 08:22:50 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_titres_uniformes extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_TITRES_UNIFORMES;
		parent::__construct($user_query);
		$this->object_table = "titres_uniformes";
		$this->object_table_key = "tu_id";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_titres_uniformes";
	}

	protected function _get_authorities_filters(){
		global $oeuvre_nature_selector, $oeuvre_type_selector;
		
		$filters = parent::_get_authorities_filters();
		if ($oeuvre_nature_selector) {
			$filters[] = 'tu_oeuvre_nature = "'.$oeuvre_nature_selector.'"';
		}
		if ($oeuvre_type_selector) {
			$filters[] = 'tu_oeuvre_type = "'.$oeuvre_type_selector.'"';
		}
		return $filters;
	}
	
	protected function _get_sign_elements($sorted=false) {
		global $oeuvre_nature_selector, $oeuvre_type_selector;
		$str_to_hash = parent::_get_sign_elements($sorted);
		$str_to_hash .= "&oeuvre_nature_selector=".$oeuvre_nature_selector."&oeuvre_type_selector=".$oeuvre_type_selector;
		return $str_to_hash;
	}
	
	public function get_authority_tri() {
		return ' index_tu ';
	}
	
	public function explain($display, $mode = 'records',$mini=false){
		global $begin_result_liste, $end_result_liste;
		
		error_reporting(E_ALL & ~E_NOTICE);
		print '<div style="margin-left:10px;width:49%;overflow:hidden;float:left">';
		print '<h1>Recherche Native</h1>';
		print '<p>QUERY : '.$this->user_query.'</p>';
		$start = microtime(true);
 		print '<p>Nombre de resultats trouves: '.$this->get_nb_results().'</p>';
 		$result = $this->get_sorted_result();
		if($this->get_nb_results()>0 && $result){
	 		$inter = microtime(true);
		 	print '<p>Temps de calcul (en seconde) : '.($inter - $start).'</p>';
		 	$elements_authorities_list_ui = new elements_authorities_list_ui($result, 20, 7);
		 	$elements = $elements_authorities_list_ui->get_elements_list();
		 	print $begin_result_liste;
		 	print $elements;
		 	print $end_result_liste;
	 		print '<p>Temps de gen page (en seconde) : '.(microtime(true) - $inter).'</p>';
 		}	
 		print '<p>Temps Total (en seconde) : '.(microtime(true) - $start).'</p></div>';
	}
	
	protected function _get_human_queries() {
		global $msg;
		global $oeuvre_nature_selector, $oeuvre_type_selector;
		
		$human_queries = parent::_get_human_queries();
		if ($oeuvre_nature_selector) {
			$marc = marc_list_collection::get_instance('oeuvre_nature');
			$human_queries[] = array(
					'name' => $msg['search_extended_titre_uniforme_oeuvre_nature'],
					'value' => $marc->table[$oeuvre_nature_selector]
			);
		}
		if ($oeuvre_type_selector) {
			$marc = marc_list_collection::get_instance('oeuvre_type');
			$human_queries[] = array(
					'name' => $msg['search_extended_titre_uniforme_oeuvre_type'],
					'value' => $marc->table[$oeuvre_type_selector]
			);
		}
		
		return $human_queries;
	}
}