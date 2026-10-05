<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_publishers.class.php,v 1.1 2017/07/06 09:11:41 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_publishers extends searcher_sphinx_authorities {
	protected $index_name = 'publishers';
	
	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/publishers/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'publishers';
		$this->authority_type = AUT_TABLE_PUBLISHERS;
 	}
 	
//  	protected function get_filters(){
// 		$filters = parent::get_filters();
// 		global $oeuvre_nature_selector,$oeuvre_type_selector;
// 		if($oeuvre_nature_selector){
// 			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
// 			$filters[] = array(
// 				'name'=> 'oeuvre_nature',
// 				'values' => $oeuvre_nature_selector
// 			);
// 		}
// 		if($oeuvre_type_selector){
// 			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
// 			$filters[] = array(
// 				'name'=> 'oeuvre_type',
// 				'values' => $oeuvre_type_selector
// 			);
// 		}
// 		return $filters;
// 	}
}