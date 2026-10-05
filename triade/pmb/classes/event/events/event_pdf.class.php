<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_pdf.class.php,v 1.2 2021/03/05 14:16:41 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_pdf extends event {
	protected $params = [];

	public function get_params() {
		return $this->params;
	}
	
	public function set_params($params) {    
	    if (!is_array($params)) {
	        $params = encoding_normalize::json_decode(stripslashes($params), true);
	    }
	    $this->params = $params;
	}
}