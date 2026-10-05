<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_expl.class.php,v 1.8.6.1 2025/05/06 09:11:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/expl.class.php");

class serialcirc_expl {	

	protected $id;
	
	protected $expl_id;
	
	protected $num_serialcirc;
	
	protected $bulletine_date;
	
	protected $state_circ;
	
	protected $num_serialcirc_diff;
	
	protected $ret_asked;
	
	protected $trans_asked;
	
	protected $trans_doc_asked;
	
	protected $num_current_empr;
	
	protected $start_date;

	protected $exemplaire;
	
	protected $serialcirc_diff;
	
	protected $abt_num;
	
	protected $abt_name;
	
	protected $bulletin_numero;
	
	protected $mention_date;
	
	protected $bulletin_notice;
	
	protected $bulletin_id;
	
	protected $num_notice;
	
	protected $serialcirc_type;
	
	protected $serialcirc_checked;
	
	protected $classement;
	
	protected $classements;
	
	protected $info_circ;
	
	public function __construct($id) {
		$this->id=intval($id);		
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$this->expl_id = 0;
		$this->num_serialcirc = 0;
		$this->bulletine_date = '';
		$this->state_circ = 0;
		$this->num_serialcirc_diff = 0;
		$this->ret_asked = 0;
		$this->trans_asked = 0;
		$this->trans_doc_asked = 0;
		$this->num_current_empr = 0;
		$this->start_date = '';
		$this->abt_num = 0;
		$this->abt_name = '';
		$this->bulletin_numero = '';
		$this->mention_date = '';
		$this->bulletin_notice = 0;
		$this->bulletin_id = 0;
		$this->num_notice = 0;
		$this->serialcirc_type = '';
		$this->serialcirc_checked = '';
		$this->classement = '';
		
		$query = "SELECT * FROM serialcirc_expl WHERE id_serialcirc_expl=".$this->id;
		$result = pmb_mysql_query($query);	
		if (pmb_mysql_num_rows($result)) {			
			if($row=pmb_mysql_fetch_object($result)){					
				$this->expl_id = $row->num_serialcirc_expl_id;
				$this->num_serialcirc = $row->num_serialcirc_expl_serialcirc;
				$this->bulletine_date = $row->serialcirc_expl_bulletine_date;
				$this->state_circ = $row->serialcirc_expl_state_circ;
				$this->num_serialcirc_diff = $row->num_serialcirc_expl_serialcirc_diff;
				$this->ret_asked = $row->serialcirc_expl_ret_asked;
				$this->trans_asked = $row->serialcirc_expl_trans_asked;
				$this->trans_doc_asked = $row->serialcirc_expl_trans_doc_asked;
				$this->num_current_empr = $row->num_serialcirc_expl_current_empr;
				$this->start_date = $row->serialcirc_expl_start_date;
			}
		}	
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_expl_id() {
		return $this->expl_id;
	}
	
	public function get_num_serialcirc() {
		return $this->num_serialcirc;
	}
	
	public function get_bulletine_date() {
		return $this->bulletine_date;
	}
	
	public function get_state_circ() {
		return $this->state_circ;
	}
	
	public function get_num_serialcirc_diff() {
		return $this->num_serialcirc_diff;
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
	
	public function get_num_current_empr() {
		return $this->num_current_empr;
	}
	
	public function get_start_date() {
		return $this->start_date;
	}
	
	public function get_exemplaire() {
		if(!isset($this->exemplaire)) {
			$this->exemplaire = new exemplaire('', $this->expl_id);
		}
		return $this->exemplaire;
	}
	
	public function get_serialcirc_diff() {
		if(!isset($this->serialcirc_diff)) {
			$this->serialcirc_diff = new serialcirc_diff($this->num_serialcirc);
		}
		return $this->serialcirc_diff;
	}
	
	public function get_abt_num() {
		return $this->abt_num;
	}
	
	public function set_abt_num($abt_num) {
		$this->abt_num = intval($abt_num);
	}
	
	public function get_abt_name() {
		return $this->abt_name;
	}
	
	public function set_abt_name($abt_name) {
		$this->abt_name = $abt_name;
	}
	
	public function get_bulletin_numero() {
		return $this->bulletin_numero;
	}
	
	public function set_bulletin_numero($bulletin_numero) {
		$this->bulletin_numero = $bulletin_numero;
	}
	
	public function get_mention_date() {
		return $this->mention_date;
	}
	
	public function set_mention_date($mention_date) {
		$this->mention_date = $mention_date;
	}
	
	public function get_bulletin_notice() {
		return $this->bulletin_notice;
	}
	
	public function set_bulletin_notice($bulletin_notice) {
		$this->bulletin_notice = intval($bulletin_notice);
	}
	
	public function get_bulletin_id() {
		return $this->bulletin_id;
	}
	
	public function set_bulletin_id($bulletin_id) {
		$this->bulletin_id = intval($bulletin_id);
	}
	
	public function get_num_notice() {
		return $this->num_notice;
	}
	
	public function set_num_notice($num_notice) {
		$this->num_notice = intval($num_notice);
	}
	
	public function get_serialcirc_type() {
		return $this->serialcirc_type;
	}
	
	public function set_serialcirc_type($serialcirc_type) {
		$this->serialcirc_type = $serialcirc_type;
	}
	
	public function get_serialcirc_checked() {
		return $this->serialcirc_checked;
	}
	
	public function set_serialcirc_checked($serialcirc_checked) {
		$this->serialcirc_checked = $serialcirc_checked;
	}
	
	public function get_classement() {
		return $this->classement;
	}
	
	public function set_classement($classement) {
		$this->classement = $classement;
	}
	
	public function get_classements() {
		if(empty($this->classements)) {
			$this->classements = array();
			if($this->is_serialcirc_diff_no_ret_circ()) {
				$this->classements[] = 'no_ret_circ';
				return $this->classements;
			}
			if($this->is_in_alert()) {
				$this->classements[] = 'alert';
			} else if($this->is_in_to_be_circ()) {
				$this->classements[] = 'to_be_circ';
			}
			if($this->is_in_circ()) {
				$this->classements[] = 'in_circ';
			}
			if($this->is_in_late()) {
				$this->classements[] = 'in_late';
			}
// 			if($this->is_in_reproduction_ask()) {
// 				$this->classements[] = 'reproduction_ask';
// 			}
// 			if($this->is_in_resa_ask()) {
// 				$this->classements[] = 'is_in_resa_ask';
// 			}
		}
		return $this->classements;
	}
	
	public function is_in_alert(){
		if($this->get_serialcirc_diff()->virtual_circ){
			if( $this->start_date=="0000-00-00")$start_date=$this->bulletine_date;
			else $start_date=$this->start_date;
			$req="select DATEDIFF(DATE_ADD('".$start_date."',	INTERVAL ".$this->get_serialcirc_diff()->duration_before_send." DAY),CURDATE())";
			
			$result=pmb_mysql_query($req);
			if($row = pmb_mysql_fetch_row($result)) {
				if($row[0]>0){
					return true;
				}
			}
		}
		return false;
	}
	
	public function is_alerted(){
		//if($this->is_in_alert()) return false;
		if($this->start_date!="0000-00-00")	return true;
		return false;
	}
	
	public function is_in_to_be_circ(){
		if($this->is_in_alert() && $this->is_alerted())	 return false;
		if(!$this->state_circ && !$this->num_serialcirc_diff){
			return true;
		}
		return false;
	}
	
	public function is_in_circ(){
		if($this->state_circ){
			return true;
		}
		return false;
	}
	
	public function is_in_late(){
		if(!$this->serialcirc_checked) return false;
		if( $this->start_date=="0000-00-00") return false;
		
		$query = "select *,DATEDIFF(serialcirc_circ_expected_date,CURDATE())as late_diff from serialcirc_circ where num_serialcirc_circ_expl=".$this->expl_id." order by serialcirc_circ_order ";
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->late_diff <0 && !$row->serialcirc_circ_pointed_date){
				return true;
			}else{
				return true;
			}
		}
		return false;
	}
	
	public function is_in_reproduction_ask(){
		if(!$this->state_circ && !$this->num_serialcirc_diff){
			return true;
		}
	}
	
	public function is_in_resa_ask(){
	}
	
	public function is_serialcirc_diff_no_ret_circ(){
		if($this->get_serialcirc_diff()->no_ret_circ) {
			return true;
		}
		return false;
	}
	
	public function is_lost_num_serialcirc_abt(){
		if($this->is_in_circ() || !empty($this->info_circ)) {
			return false;
		}
		$query = "SELECT num_serialcirc_abt FROM serialcirc WHERE id_serialcirc = ".$this->num_serialcirc." AND num_serialcirc_abt = 0";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
	
	public function fetch_info_circ(){
		if(!isset($this->info_circ)) {
			$this->info_circ=array();
			$req="select *,DATEDIFF(serialcirc_circ_expected_date,CURDATE())as late_diff from serialcirc_circ where num_serialcirc_circ_expl=".$this->expl_id." order by serialcirc_circ_order ";
			$resultat=pmb_mysql_query($req);
			$last_owner = 0;
			if ($resultat && pmb_mysql_num_rows($resultat)) {
				while($r=pmb_mysql_fetch_object($resultat)){
					$this->info_circ[$r->num_serialcirc_circ_empr] = new serialcirc_circ($r->id_serialcirc_circ);
					if ($r->serialcirc_circ_pointed_date) {
						$last_owner = $r->num_serialcirc_circ_empr;
					}
					$this->info_circ[$r->num_serialcirc_circ_empr]->set_current_owner(0);
				}
				if ($last_owner) {
					$this->info_circ[$last_owner]->set_current_owner(1);
				}
			}
		}
		return $this->info_circ;
	}
	
	public function build_diff_sel(){
		global $charset;
		$tpl="
			<select name='".$this->classement."_group_circ_select_".$this->expl_id."' id='".$this->classement."_group_circ_select_".$this->expl_id."' style='width: 15em;'>
				!!diff_select!!
			</select>";
		$list="";
		$this->fetch_info_circ();
		if (count($this->info_circ)) {
			$current_group = '';
			$current_group_first_empr = 0;
			$checked = '';
			foreach ($this->info_circ as $empr_id => $info_circ) {
				if ($current_group && ($info_circ->get_group_name() != $current_group)) {
					// On a fini le parcourt d'un groupe, on l'affiche, on réinitialise
					$list.="<option value='".$current_group_first_empr."' $checked >".htmlentities($current_group, ENT_QUOTES, $charset)."</option>";
					$current_group = '';
					$current_group_first_empr = 0;
					$checked = '';
				}
				if ($info_circ->get_current_owner() || ($this->num_current_empr == $empr_id)) {
					$checked = " selected='selected' ";
				}
				if ($info_circ->get_group_name() && ($info_circ->get_group_name() == $current_group)) {
					// On est toujours dans le groupe, on ne fait rien
					continue;
				}
				if ($info_circ->get_group_name()) {
					// On rentre dans un groupe, on le stocke, on n'affiche rien pour l'instant
					$current_group = $info_circ->get_group_name();
					$current_group_first_empr = $empr_id;
					continue;
				}
				// Si on arrive ici, on est dans le cas d'un emprunteur sans groupe
				$empr_infos = serialcirc::empr_info($empr_id);
				$list.="<option value='".$empr_id."' $checked >".htmlentities($empr_infos['empr_libelle'], ENT_QUOTES, $charset)."</option>";
				$checked = '';
			}
			if($list) {
				$tpl=str_replace("!!diff_select!!", $list, $tpl);
				return $tpl;
			} else {
				return "";
			}
			
		}
		foreach($this->get_serialcirc_diff()->diffusion as $diffusion){
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_empr && $this->get_serialcirc_diff()->virtual_circ ){
				if(empty($this->info_circ[$diffusion["num_empr"]]) || empty($this->info_circ[$diffusion["num_empr"]]->get_subscription()))	{
					continue;
				}
			}
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ) {
				$name=$diffusion["empr_name"];
				// On récupère le premier emprunteur du groupe
				$num_empr = $diffusion['group'][0]['num_empr'];
			} else {
				$name=$this->get_serialcirc_diff()->empr_info[$diffusion["num_empr"]]["empr_libelle"];
				$num_empr = $diffusion['num_empr'];
			}
			if($this->num_serialcirc_diff == $diffusion['id']) {
				$checked=" selected='selected' ";
			} else {
				$checked="";
			}
			$list.="<option value='".$num_empr."' $checked >".htmlentities($name, ENT_QUOTES, $charset)."</option>";
		}
		if($list) {
			$tpl=str_replace("!!diff_select!!", $list, $tpl);
			return $tpl;
		} else {
			return "";
		}
	}
	
	public function build_empr_list(){
		global $charset;
		// on liste les empr réel et ceux du group
		$name_list="";
		$this->fetch_info_circ();
		foreach ($this->info_circ as $empr_id => $info_circ) {
			$empr_infos = serialcirc::empr_info($empr_id);
			$name = "<a href='".$empr_infos['view_link']."'>".htmlentities(($info_circ->get_group_name() ? '('.$info_circ->get_group_name().') ' : '').$empr_infos["empr_libelle"],ENT_QUOTES,$charset)."</a><br />";
			if ($info_circ->get_current_owner() || ($this->num_current_empr == $empr_id))	 {
				$name = "<span class='erreur'>".$name."</span><br />";
			}
			$name_list.= $name;
		}
		return $name_list;
	}
}