<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_resa_multiple.class.php,v 1.1 2018/03/27 09:49:07 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path.'/event/event.class.php');

class event_resa_mutiple extends event {
	
	/**
	 * resa
	 * @var resa
	 */
	protected $id_empr;
	protected $form;
	protected $notices = array();
	protected $notice = 0;
	protected $bulletin = 0;
	protected $id_loc = 0;
	protected $resa_id = 0;
	
	
	
	/**
	 * 
	 * @return resa
	 */
	
	public function set_empr_id($id_empr){
		$this->id_empr = $id_empr;
	}

	
	public function get_empr_id(){
		return $this->id_empr;
	}
	
	public function set_form($form){
	    $this->form = $form;
	}
	
	public function get_form(){
	    return $this->form;
	}
	
	public function set_notices($notices){
	    $this->notices = $notices;
	}
	
	public function get_notices(){
	    return $this->notices;
	}
	
	public function set_notice($notice){
	    $this->notice = $notice;
	}
	
	public function get_notice(){
	    return $this->notice;
	}
	
	public function set_bulletin($bulletin){
	    $this->bulletin = $bulletin;
	}
	
	public function get_bulletin(){
	    return $this->bulletin;
	}	
	
	public function set_id_loc($id_loc){
	    $this->id_loc = $id_loc;
	}
	
	public function get_id_loc(){
	    return $this->id_loc;
	}
	
	public function set_resa_id($resa_id){
	    $this->resa_id = $resa_id;
	}
	public function get_resa_id(){
	    return $this->resa_id;
	}
	
	
}