<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_group.class.php,v 1.3.18.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class serialcirc_group {
	//public $id_serialcirc_group;	// identifiant unique
	public $num_serialcirc_diff;	// identifiant dans la liste de diffusion
	public $members;				// tableau contenant les infos d'un membre du groupe
	public $responsable;			// responsable du groupe

	public function __construct($id_serialcirc_diff){
	    $this->num_serialcirc_diff = intval($id_serialcirc_diff);
		$this->_fetch_data();
	}

	protected function _fetch_data(){
		$query = "select * from serialcirc_group where num_serialcirc_group_diff = ".$this->num_serialcirc_diff." order by serialcirc_group_order asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->members = array();
			while($row = pmb_mysql_fetch_object($result)){
				$this->members[] = $row->num_serialcirc_group_empr;
				if($row->serialcirc_group_responsable == 1){
					$this->responsable = $row->num_serialcirc_group_empr;
				}
			}
		}
	}

	public function is_inside($empr_id,$expl_id){
		if(serialcirc_empr_circ::is_subscribe($empr_id,$expl_id)){
			foreach($this->members as $member){
				if($member == $empr_id){
					return true;
				}
			}
		}
		return false;
	}

	public function get_nb($empr_id,$expl_id){
		$nb =0;
		foreach($this->members as $member){
			if(serialcirc_empr_circ::is_subscribe($member,$expl_id)){
				if($member != $empr_id){
					$nb++;
				}else{
					break;
				}
			}
		}
		return $nb;
	}

	public function get_next($current_empr,$expl_id){
		$found_current = false;
		foreach($this->members as $member){
			if(serialcirc_empr_circ::is_subscribe($member,$expl_id)){
				if($member == $current_empr){
					$found_current=true;
				}
				if($found_current){
					return $member;
				}
			}
		}
		return false;
	}


	public function get_mail_infos($empr_id){
		$mail = array();
		$found_empr = false;
		for ($i=0 ; $i<count($this->members) ; $i++){
			if($this->members[$i] == $empr_id){
				$found_empr=true;
				$query = "select empr_nom, empr_prenom, empr_mail from empr where id_empr = ".$this->members[$i];
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$mail['dest'] = array(
						'name' => $row->empr_nom.($row->empr_prenom ? " ".$row->empr_prenom : ""),
						'mail' => $row->empr_mail
					);
				}
			}
		}
		if(!$found_empr){
			$query = "select empr_nom, empr_prenom, empr_mail from empr where id_empr = ".$this->members[0];
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$mail['dest'] = array(
						'name' => $row->empr_nom.($row->empr_prenom ? " ".$row->empr_prenom : ""),
						'mail' => $row->empr_mail
					);
				}
		}
		if($this->responsable){
			$query = "select empr_mail from empr where id_empr = ".$this->responsable;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($row = pmb_mysql_fetch_object($result)){
					if($row->empr_mail != ""){
						if($mail['cc']!= "") $mail['cc'].=";";
						$mail['cc'] .= $row->empr_mail;
					}
				}
			}
		}
		return $mail;
	}
}