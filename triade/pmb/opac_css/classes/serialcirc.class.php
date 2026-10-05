<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.class.php,v 1.6 2022/02/01 07:57:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/serialcirc_diff.class.php") ;
require_once($include_path."/serialcirc.inc.php");

class serialcirc {
	public $id_serialcirc;			// identifiant unique
	public $num_abt;				// identifiant de l'abonnement associé
	public $type;					// type de circulation
	public $virtual;				// booléen définissant si la circulation est virtuelle ou non
	public $check;					// booléen définissant si le pointage est demandée!
	public $allow_resa;			// booléen définissant si la demande de résa est permise
	public $allow_copy;			// booléen définissant si la demande de copie est permise
	public $duration_before_send;	// nombre de jours avant le démarrage de la circulation depuis la date de bulletinnage
	public $allow_subscription;	// booléen définissant si l'inscription est permise
	public $serial_title;			// titre du périodique
	public $state;					// état de la circulation
	public $late_mode;				// mode de retard
	
	public function __construct($id){
		$this->id_serialcirc = intval($id);
		$this->_fetch_data();
	}
	
	protected function _fetch_data(){
		$query = "select * from serialcirc where id_serialcirc = ".$this->id_serialcirc;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$this->num_abt = $row->num_serialcirc_abt;
			$this->type = $row->serialcirc_type;
			$this->virtual = $row->serialcirc_virtual;
			//$row->serialcirc_duration;
			$this->check = $row->serialcirc_checked;
			$this->late_mode = $row->serialcirc_retard_mode;
			$this->allow_resa = $row->serialcirc_allow_resa;
			$this->allow_copy = $row->serialcirc_allow_copy;
			//$row->serialcirc_allow_send_ask;
			$this->allow_subscription = $row->serialcirc_allow_subscription;
			$this->duration_before_send = $row->serialcirc_duration_before_send;
			//$row->serialcirc_expl_statut_circ;
			//$row->serialcirc_expl_statut_circ_after;
			$this->state = $row->serialcirc_state;
		}
	}
	
	public function is_virtual(){
		if($this->virtual) return true;
		else return false;
	}
	
	public function resa_is_allowed(){
		if($this->allow_resa) return true;
		else return false;
	}

	public function copy_is_allowed(){
		if($this->allow_copy) return true;
		else return false;
	}
	public function subscription_is_allowed(){
		if($this->allow_subscription) return true;
		else return false;
	}
	
	public function get_serial_title(){
	    if(!$this->serial_title && $this->num_abt){
			$query="select tit1 from notices join abts_abts on num_notice = notice_id where abt_id = ".$this->num_abt;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->serial_title = pmb_mysql_result($result,0,0);
			}
		}
		return $this->serial_title;
	}
	
	public function empr_is_subscribe($empr_id){
		$serialcirc_diff = new serialcirc_diff($this->id_serialcirc);
		for($i=0 ; $i<count($serialcirc_diff->list) ; $i++){
			if($serialcirc_diff->list[$i]->type == 0){
				if($serialcirc_diff->list[$i]->num_empr == $empr_id){
					return true;
				}
			}else{
				for($j=0 ; $j<count($serialcirc_diff->list[$i]->group->members) ; $i++){
					if($serialcirc_diff->list[$i]->group->members[$j] == $empr_id){
						return true;
					}	
				}
			}
		}
		return false;
	}
}