<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_author_deduplication.class.php,v 1.1 2018/07/30 13:17:21 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';
require_once($class_path.'/authority.class.php');

class event_author_deduplication extends event_author {
	protected $author_data = array();
	protected $query;
	protected $checked_elts = array();

	public function set_author_query($query){
	    $this->query = $query;
	}
	
	public function get_author_query(){
	    return $this->query;
	}
}