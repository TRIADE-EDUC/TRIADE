<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_diff.class.php,v 1.4.18.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/serialcirc.class.php");
require_once($class_path."/serialcirc_group.class.php");
require_once($class_path."/serialcirc_empr.class.php");
require_once($include_path."/serialcirc.inc.php");

class serialcirc_diff {
	public $num_serialcirc;		// identifiant de la circulation
	public $serialcirc;			// instance de serialcirc
	public $list;					// tableau d'instance de serialcirc_diff_dest

	public function __construct($id_serialcirc){
	    $this->num_serialcirc = intval($id_serialcirc);
		$this->_fetch_data();
	}

	private function _fetch_data(){
		$this->serialcirc = new serialcirc($this->num_serialcirc);
		$query = "select id_serialcirc_diff from serialcirc_diff where num_serialcirc_diff_serialcirc = ".$this->num_serialcirc." order by num_serialcirc_diff_serialcirc,serialcirc_diff_order asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->list[] = new serialcirc_diff_dest($row->id_serialcirc_diff);
			}
		}
	}

	public function get_id_diff($empr_id){
		$id = 0;
		for($i=0 ; $i<count($this->list) ; $i++){
			if($this->list[$i]->type == 0){
				if($this->list[$i]->num_empr == $empr_id){
					$id = $this->list[$i]->id_serialcirc_diff;
				}
			}else{
				for($j=0 ; $j<count($this->list[$i]->group->members) ; $j++){
					if($this->list[$i]->group->members[$j] == $empr_id){
						$id = $this->list[$i]->id_serialcirc_diff;
					}
				}
			}
		}
		return $id;
	}

	public function get_start_rank($empr_id,$expl_id){
		$rank = 0;
		$empr_found = false;
		for($i=0 ; $i<count($this->list) ; $i++){
			$rank+= $this->list[$i]->get_nb($empr_id,$expl_id);
			if($this->list[$i]->is_inside($empr_id,$expl_id)){
				$empr_found =true;
				break;
			}
		}
		if(!$empr_found){
			$rank = "";
		}
		return $rank;
	}

	public function get_next($current_empr,$expl_id){
		$found_current = false;
		for($i=0 ; $i<count($this->list) ; $i++){
			if($this->list[$i]->type == 0){
				if($this->list[$i]->num_empr == $current_empr){
					$found_current =true;
					continue;
				}
			}else{
				if($this->list[$i]->group->is_inside($current_empr,$expl_id)){
					if($this->list[$i]->group->get_next($current_empr,$expl_id)){
						return $this->list[$i];
					}else{
						$found_current =true;
						continue;
					}
				}
			}
			if($found_current){
				return $this->list[$i];
			}
		}
		return false;
	}
}

class serialcirc_diff_dest {
	public $id_serialcirc_diff;	// identifiant unique
	public $type;					// booléen définissant si le dest est un groupe ou non...
	public $type_diff;				// booléen définissant si la circulation dans le cas d'un groupe est en marguerite ou non
	public $num_empr;				// identifiant de l'emprunteur
	public $group_name;			// nom du groupe
	public $duration;				// durée en nombre de jours de disponibilité pour le destinataire
	public $order;					// ordre dans la liste de diffusion
	public $group;					// instance de serialcirc_group
	public $num_serialcirc;		// identifiant de serialcirc

	public function __construct($id_serialcirc_diff){
	    $this->id_serialcirc_diff = intval($id_serialcirc_diff);
		$this->_fetch_data();
	}

	protected function _fetch_data(){
		$query = "select * from serialcirc_diff where id_serialcirc_diff = ".$this->id_serialcirc_diff;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$this->type = $row->serialcirc_diff_empr_type;
			$this->type_diff = $row->serialcirc_diff_type_diff;
			$this->num_empr = $row->num_serialcirc_diff_empr;
			$this->group_name = $row->serialcirc_diff_group_name;
			$this->duration = $row->serialcirc_diff_duration;
			$this->order = $row->serialcirc_diff_order;
			if($this->type == 1){
				$this->group = new serialcirc_group($this->id_serialcirc_diff);
			}
			$this->num_serialcirc = $row->num_serialcirc_diff_serialcirc;
		}
	}

	public function is_inside($empr_id,$expl_id){
		if($this->type == 0){
			if(serialcirc_empr_circ::is_subscribe($this->num_empr,$expl_id)){
				if($this->num_empr == $empr_id){
					return true;
				}
			}else{
				return false;
			}
		}else{
			return $this->group->is_inside($empr_id,$expl_id);
		}
	}

	public function get_nb($empr_id,$expl_id){
		if($this->type == 0){
			if(serialcirc_empr_circ::is_subscribe($this->num_empr,$expl_id) && $empr_id!= $this->num_empr){
				return 1;
			}else{
				return 0;
			}
		}else{
			if($this->type_diff == 0 && !$this->group->is_inside($empr_id,$expl_id)){
				return 1;
			}
			return $this->group->get_nb($empr_id,$expl_id);
		}
	}

	public function get_mail_infos($empr_id){
		$mail = array();
		if($this->type == 0){
			$query = "select empr_nom, empr_prenom, empr_mail from empr where id_empr = ".$this->num_empr;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$mail['dest'] = array(
					'name' => $row->empr_nom.($row->empr_prenom ? " ".$row->empr_prenom : ""),
					'mail' => $row->empr_mail
				);
				$mail['cc'] = "";
			}
		}else{
			$mail = $this->group->get_mail_infos($empr_id);
		}
		return $mail;
	}
}