<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_resa.class.php,v 1.2 2018/03/27 09:49:07 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path.'/event/event.class.php');

class event_resa extends event {
	
	/**
	 * resa
	 * @var resa
	 */
	protected $resa_id;
	protected $id_empr;
	protected $loc_form;
	
	/**
	 * 
	 * @return resa
	 */
	public function get_resa() {
		return $this->resa;
	}
	
	/**
	 * 
	 * @param int $resa
	 */
	
	public function set_resa_id($resa_id){
		$this->resa_id = $resa_id;
	}
	
	public function set_empr_id($id_empr){
		$this->id_empr = $id_empr;
	}
	
	public function get_resa_id(){
		return $this->resa_id;
	}
	
	public function get_empr_id(){
		return $this->id_empr;
	}
	
	public function set_location_form($form){
	    $this->loc_form = $form;
	}
	
	public function get_location_form(){
	    return $this->loc_form;
	}
}