<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_circ.class.php,v 1.2 2022/01/17 08:19:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class serialcirc_circ {	

	protected $id;
	
	protected $num_diff;
	
	protected $num_expl;
	
	protected $num_empr;
	
	protected $num_serialcirc;
	
	protected $order;
	
	protected $subscription;
	
	protected $hold_asked;
	
	protected $ret_asked;
	
	protected $trans_asked;
	
	protected $trans_doc_asked;
	
	protected $expected_date;
	
	protected $pointed_date;
	
	protected $group_name;
	
	protected $late_diff;
	
	protected $bulletin_id;
	
	protected $bulletin_notice;
	
	protected $bulletin_numero;
	
	protected $bulletin_mention_date;
	
	protected $current_owner;
	
	public function __construct($id) {
		$this->id=intval($id);		
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$this->num_diff = 0;
		$this->num_expl = 0;
		$this->num_empr = 0;
		$this->num_serialcirc = 0;
		$this->order = 0;
		$this->subscription = 0;
		$this->hold_asked = 0;
		$this->ret_asked = 0;
		$this->trans_asked = 0;
		$this->trans_doc_asked = 0;
		$this->expected_date = '';
		$this->pointed_date = '';
		$this->group_name = '';
		$this->late_diff = '';
		$this->bulletin_id = 0;
		$this->bulletin_notice = 0;
		$this->bulletin_numero = '';
		$this->bulletin_mention_date = '';
		$query = "SELECT *,DATEDIFF(serialcirc_circ_expected_date,CURDATE())as late_diff FROM serialcirc_circ
			JOIN exemplaires ON serialcirc_circ.num_serialcirc_circ_expl = exemplaires.expl_id 
			JOIN bulletins ON exemplaires.expl_bulletin = bulletins.bulletin_id
			JOIN notices ON bulletins.bulletin_notice = notices.notice_id
			WHERE id_serialcirc_circ=".$this->id;
		$result = pmb_mysql_query($query);	
		if (pmb_mysql_num_rows($result)) {			
			if($row=pmb_mysql_fetch_object($result)){
				$this->num_diff = $row->num_serialcirc_circ_diff;
				$this->num_expl = $row->num_serialcirc_circ_expl;
				$this->num_empr = $row->num_serialcirc_circ_empr;
				$this->num_serialcirc = $row->num_serialcirc_circ_serialcirc;
				$this->order = $row->serialcirc_circ_order;
				$this->subscription = $row->serialcirc_circ_subscription;
				$this->hold_asked = $row->serialcirc_circ_hold_asked;
				$this->ret_asked = $row->serialcirc_circ_ret_asked;
				$this->trans_asked = $row->serialcirc_circ_trans_asked;
				$this->trans_doc_asked = $row->serialcirc_circ_trans_doc_asked;
				$this->expected_date = $row->serialcirc_circ_expected_date;
				$this->pointed_date = $row->serialcirc_circ_pointed_date;
				$this->group_name = $row->serialcirc_circ_group_name;
				$this->late_diff = $row->late_diff;
				$this->bulletin_id = $row->bulletin_id;
				$this->bulletin_notice = $row->bulletin_notice;
				$this->bulletin_numero = $row->bulletin_numero;
				$this->bulletin_mention_date = $row->mention_date;
			}
		}	
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_num_diff() {
		return $this->num_diff;
	}
	
	public function get_num_expl() {
		return $this->num_expl;
	}
	
	public function get_num_empr() {
		return $this->num_empr;
	}
	
	public function get_num_serialcirc() {
		return $this->num_serialcirc;
	}
	
	public function get_order() {
		return $this->order;
	}
	
	public function get_subscription() {
		return $this->subscription;
	}
	
	public function get_hold_asked() {
		return $this->hold_asked;
	}
	
	public function get_ret_asked() {
		return $this->ret_asked;
	}
	
	public function get_trans_asked() {
		return $this->trans_asked;
	}
	
	public function get_trans_doc_asked() {
		return $this->trans_doc_asked;
	}
	
	public function get_expected_date() {
		return $this->expected_date;
	}
	
	public function get_pointed_date() {
		return $this->pointed_date;
	}
	
	public function get_group_name() {
		return $this->group_name;
	}
	
	public function get_late_diff() {
		return $this->late_diff;
	}
	
	public function get_bulletin_id() {
		return $this->bulletin_id;
	}
	
	public function get_bulletin_notice() {
		return $this->bulletin_notice;
	}
	
	public function get_bulletin_numero() {
		return $this->bulletin_numero;
	}
	
	public function get_bulletin_mention_date() {
		return $this->bulletin_mention_date;
	}
	
	public function get_current_owner() {
		return $this->current_owner;
	}
	
	public function set_current_owner($current_owner) {
		$this->current_owner = intval($current_owner);
	}
	
	public function is_late() {
		if($this->late_diff <0 && !$this->pointed_date){
			return true;
		}else{
			return false;
		}
	}
}