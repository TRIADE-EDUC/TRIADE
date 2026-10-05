<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authors.class.php,v 1.3.8.1 2024/10/17 08:22:50 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');

class searcher_authorities_authors extends searcher_autorities {

	public function __construct($user_query){
		$this->authority_type = AUT_TABLE_AUTHORS;
		parent::__construct($user_query);
		$this->object_table = "authors";
		$this->object_table_key = "author_id";
	}
	
	public function _get_search_type(){
		return parent::_get_search_type()."_authors";
	}
	
	protected function _get_authorities_filters(){
		global $type_autorite;
		
		$filters = parent::_get_authorities_filters();
		if ($type_autorite && ($type_autorite != '7')) {
			$filters[] = 'author_type = "'.$type_autorite.'"';
		}
		return $filters;
	}
	
	protected function _get_sign_elements($sorted=false) {
		global $type_autorite;
		$str_to_hash = parent::_get_sign_elements($sorted);
		$str_to_hash .= "&type_autorite=".$type_autorite;
		return $str_to_hash;
	}
	
	public function get_authority_tri() {
		return ' index_author';
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
		 	$elements_authorities_list_ui = new elements_authorities_list_ui($result, 20, 1);
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
		global $type_autorite;
		
		$human_queries = parent::_get_human_queries();
		if ($type_autorite && ($type_autorite != '7')) {
			switch ($type_autorite) {
				case '70' :
					$type_autorite_label = $msg['203'];
					break;
				case '71' :
					$type_autorite_label = $msg['204'];
					break;
				case '72' :
					$type_autorite_label = $msg['congres_libelle'];
					break;
			}
			$human_queries[] = array(
					'name' => $msg['search_extended_author_type'],
					'value' => $type_autorite_label
			);
		}
		
		return $human_queries;
	}
}