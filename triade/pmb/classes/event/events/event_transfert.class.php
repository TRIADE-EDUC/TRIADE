<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_transfert.class.php,v 1.1 2016/04/14 14:50:32 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_transfert extends event {
	protected $id_transfert;
	
	
	public function get_id_transfert() {
	  return $this->id_transfert;
	}
	
	public function set_id_transfert($id_transfert) {
	  $this->id_transfert = $id_transfert;
	}
	
}
