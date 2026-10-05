<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authors.class.php,v 1.2 2019/11/19 10:01:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_authors extends searcher_sphinx_authorities {
	protected $index_name = 'authors';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/authors/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'authors';
		$this->authority_type = AUT_TABLE_AUTHORS;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $type_autorite;
		// Ca ne devrait pas, mais pour le bon fonctionnement, on a besoin d'exclure cette valeur
		if($type_autorite && $type_autorite != 7){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			$filters[] = array(
					'name'=> 'author_type',
					'values' => $type_autorite
			);
		}
		return $filters;
	}
}