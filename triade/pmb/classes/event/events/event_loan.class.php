<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_loan.class.php,v 1.2 2016/04/20 12:48:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_loan extends event {
	
	protected $id_loan;
	
	protected $id_empr;
	
	
	public function get_id_loan() {
		return $this->id_loan;
	}
	
	public function set_id_loan($id_loan) {
		$this->id_loan = $id_loan;
		return $this;
	}
	
	public function get_id_empr() {
		return $this->id_empr;
	}
	
	public function set_id_empr($id_empr) {
		$this->id_empr = $id_empr;
		return $this;
	}
}
