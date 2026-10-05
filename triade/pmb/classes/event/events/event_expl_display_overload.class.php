<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_expl_display_overload.class.php,v 1.2 2021/06/04 12:59:30 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/event/event.class.php');
require_once($class_path.'/event/events/event_display_overload.class.php');

class event_expl_display_overload extends event_display_overload {
	protected $params;
	protected $nb_expl;
	
	public function set_nb_expl($nb_expl) {
	    $this->nb_expl = $nb_expl;
	}
	
	public function get_nb_expl() {
	    return $this->nb_expl;
	}
	
	public function set_params($params) {
	    $this->params = $params;
	}
	
	public function get_params() {
	    return $this->params;
	}
}