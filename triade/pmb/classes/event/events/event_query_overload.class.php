<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_query_overload.class.php,v 1.1 2016/09/15 15:13:08 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path.'/event/event.class.php');

class event_query_overload extends event {
	
	protected $query_overload = '';
	
	public function get_query_overload() {
		return $this->query_overload;
	}
	
	public function set_query_overload($query_overload) {
		$this->query_overload = $query_overload;
	}
}
