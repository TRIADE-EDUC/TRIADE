<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_series.class.php,v 1.1 2017/07/06 09:11:41 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_series extends searcher_sphinx_authorities {
	protected $index_name = 'series';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/series/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'series';
		$this->authority_type = AUT_TABLE_SERIES;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		return $filters;
	}
}