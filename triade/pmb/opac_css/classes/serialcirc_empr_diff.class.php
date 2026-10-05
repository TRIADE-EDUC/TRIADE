<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_empr_diff.class.php,v 1.4.18.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/mail.inc.php");

class serialcirc_empr_diff {
	public $empr_id;				//id empr
	public $id_serialcirc;			//id serialcirc
	public $num_abt;				//id de l'abonnement
	public $virtual;				//circulation virtuelle
	public $rank = false;			//rang
	public $expl_id;				//id exemplaire
	public $check_activate;		//booléen définissant si pointage
	public $expl_infos = array();	//infos exemplaires
	public $serial_infos=array();	//infos périodiques
	public $current_empr;			//id de lecteur en possession du pério
	public $state;					//flag : circulation débutée au non
	public $bulletine_date;		//date de bulletinnage...
	public $duration_before_start;	// durée avt le démarrage de la circulation
	public $start_date;			// date de démarrage de la circulation


	public function __construct($empr_id,$id_serialcirc,$expl_id=0){
		$this->empr_id = $empr_id;
		$this->id_serialcirc = $id_serialcirc;
		$this->expl_id = $expl_id;
		$this->fetch_data();
	}

	protected function fetch_data(){
		$query_alone = "select serialcirc_expl_state_circ, serialcirc_expl_bulletine_date, serialcirc_duration_before_send, num_serialcirc_abt, num_serialcirc_expl_id, serialcirc_virtual, serialcirc_expl_start_date, serialcirc_checked, num_serialcirc_expl_current_empr from serialcirc_diff join serialcirc on num_serialcirc_diff_serialcirc = id_serialcirc left join serialcirc_expl on num_serialcirc_expl_serialcirc = id_serialcirc where num_serialcirc_diff_serialcirc=".$this->id_serialcirc." and num_serialcirc_diff_empr =".$this->empr_id.($this->expl_id !=0 ? " and num_serialcirc_expl_id=".$this->expl_id : "");
		$query_grp =   "select serialcirc_expl_state_circ, serialcirc_expl_bulletine_date, serialcirc_duration_before_send,num_serialcirc_abt, num_serialcirc_expl_id, serialcirc_virtual, serialcirc_expl_start_date, serialcirc_checked, num_serialcirc_expl_current_empr from serialcirc_diff join serialcirc_group on id_serialcirc_diff = num_serialcirc_group_diff join serialcirc on num_serialcirc_diff_serialcirc = id_serialcirc left join serialcirc_expl on num_serialcirc_expl_serialcirc = id_serialcirc where num_serialcirc_diff_serialcirc=".$this->id_serialcirc." and num_serialcirc_group_empr = ".$this->empr_id.($this->expl_id !=0 ? " and num_serialcirc_expl_id=".$this->expl_id : "");
		$query = $query_alone." union ".$query_grp;
		$res=pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->state = $row->serialcirc_expl_state_circ;
				$this->virtual = $row->serialcirc_virtual;
				$this->check_activate = $row->serialcirc_checked;
				$this->num_abt = intval($row->num_serialcirc_abt);
				$this->expl_id = intval($row->num_serialcirc_expl_id);
				$this->duration_before_start = $row->serialcirc_duration_before_send;
				$this->bulletine_date = $row->serialcirc_expl_bulletine_date;
				$this->get_expl_infos();
				$this->get_serial_infos();
				$this->get_rank();
				$start_date = $row->serialcirc_expl_start_date;
				if ($start_date!=0) $this->start_date = format_date($start_date);
				else $this->start_date = "";
			}
		}
	}

	public function get_rank(){
		if($this->state == SERIALCIRC_EXPL_STATE_CIRC_inprogress){
			$start_rank = "select 1 from serialcirc_expl where num_serialcirc_expl_id =".$this->expl_id." and serialcirc_expl_start_date!=0 ";
			$res = pmb_mysql_query($start_rank);
			if(pmb_mysql_num_rows($res)){
				$rank=0;
			}else $rank =1;
			//on va chercher toutes les lignes...
			$diff = "select * from serialcirc_diff left join serialcirc_expl on num_serialcirc_expl_serialcirc = num_serialcirc_diff_serialcirc where num_serialcirc_diff_serialcirc = ".$this->id_serialcirc." and num_serialcirc_expl_id = ".$this->expl_id." order by serialcirc_diff_order asc";
			$res_diff = pmb_mysql_query($diff);
			$empr_found = false;
			if(pmb_mysql_num_rows($res_diff)){
				while($row_diff = pmb_mysql_fetch_object($res_diff)){
					//highlight_string(print_r($row_diff,true));
					if($row_diff->serialcirc_diff_empr_type == 0){
						if($this->empr_is_subscribe($row_diff->num_serialcirc_diff_empr)){
							$pointed = "select 1 from serialcirc_circ where num_serialcirc_circ_expl = ".$this->expl_id." and num_serialcirc_circ_empr = ".$row_diff->num_serialcirc_diff_empr." and num_serialcirc_circ_serialcirc = ".$row_diff->num_serialcirc_diff_serialcirc." and serialcirc_circ_pointed_date != 0";
							$res_pointed = pmb_mysql_query($pointed);
							if(pmb_mysql_num_rows($res_pointed)){
								$rank = 0;
								$empr_found = false;
							}
							if($row_diff->num_serialcirc_diff_empr == $this->empr_id){
								$empr_found=true;
								$this->rank = $rank;
							}
							$rank++;
						}
					}else{
						if($row_diff->serialcirc_diff_type_diff == 1){
							$gp = "select * from serialcirc_group where num_serialcirc_group_diff = ".$row_diff->id_serialcirc_diff." order by serialcirc_group_order asc" ;
							$res_gp = pmb_mysql_query($gp);
							if(pmb_mysql_num_rows($res_gp)){
								while($row_gp = pmb_mysql_fetch_object($res_gp)){
									if($this->empr_is_subscribe($row_gp->num_serialcirc_group_empr)){
										$pointed = "select 1 from serialcirc_circ where num_serialcirc_circ_expl = ".$this->expl_id." and num_serialcirc_circ_empr = ".$row_gp->num_serialcirc_group_empr." and num_serialcirc_circ_serialcirc = ".$row_diff->num_serialcirc_diff_serialcirc." and serialcirc_circ_pointed_date != 0";
										$res_pointed = pmb_mysql_query($pointed);
										if(pmb_mysql_num_rows($res_pointed)){
											$rank = 0;
											$empr_found = false;
										}
										if($row_gp->num_serialcirc_group_empr == $this->empr_id){
											$empr_found=true;
											$this->rank = $rank;
										}
										$rank++;
									}
								}
							}
						}else{
							$rank++;
						}
					}
				}
			}
			if($empr_found == false){
				$this->rank = false;
			}
		}else{
			$this->rank = "";
		}
	}

	protected function empr_is_subscribe($empr_id){
		$is_subscribe = false;
		if($this->virtual){
			$query = "select serialcirc_circ_subscription from serialcirc_circ where num_serialcirc_circ_expl = ".$this->expl_id." and num_serialcirc_circ_empr = ".$empr_id." and num_serialcirc_circ_serialcirc = ".$this->id_serialcirc;
			$res = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($res)){
				$subscription = pmb_mysql_result($res,0,0);
				if($subscription) $is_subscribe =true;
			}
		}else{
			$is_subscribe =true;
		}
		return $is_subscribe;
	}

	public function get_expl_infos(){

		$this->expl_infos = array();
		if($this->expl_id != 0){
			$query = "select expl_id,expl_cb,bulletin_titre,mention_date,bulletin_numero from exemplaires join bulletins on bulletin_id = expl_bulletin where expl_id =".$this->expl_id;
			$res = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($res)){
				$infos = pmb_mysql_fetch_object($res);
			}
			$this->expl_infos['issue'] = $infos->bulletin_numero;
			if($infos->mention_date) $this->expl_infos['issue'].=" ".$infos->mention_date;
			if($expl_infos->bulletin_titre) $this->expl_infos['issue'].=" ".$infos->bulletin_titre;
			$this->expl_infos['cb'] = $infos->expl_cb;
		}
	}

	public function get_serial_infos(){
		$this->serial_infos = array();
		$query = "select tit1 from notices join abts_abts on num_notice = notice_id where abt_id = ".$this->num_abt;
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$infos = pmb_mysql_fetch_object($res);
		}
		$this->serial_infos['title'] = $infos->tit1;
	}

	public function get_actions_form(){
		global $charset,$msg;
		//pas d'actions si pas de pointage...
		$form ="";
		if($this->check_activate){
			if($this->expl_id!=0){
				$query ="select serialcirc_circ_trans_asked, serialcirc_circ_trans_doc_asked, serialcirc_circ_ret_asked, serialcirc_circ_subscription from serialcirc_circ join serialcirc_expl on num_serialcirc_circ_empr = num_serialcirc_expl_current_empr where num_serialcirc_circ_expl = ".$this->expl_id;
				$res = pmb_mysql_query($query);
				$circ_infos = pmb_mysql_fetch_object($res);
//				print $query."<br>";
//				highlight_string(print_r($circ_infos,true));
				$form="
					<form method='post' action='' name='actions_form_".$this->id_serialcirc.$this->expl_id."'>
						<input type='hidden' name='expl_id' value='".htmlentities($this->expl_id,ENT_QUOTES,$charset)."'/>
						<input type='hidden' name='actions_form_submit' value ='1' />";
				$query = "select * from serialcirc_expl where num_serialcirc_expl_id = ".$this->expl_id;
				$res= pmb_mysql_query($query);
				if(pmb_mysql_num_rows($res)){
					$infos = pmb_mysql_fetch_object($res);
					//si on a l'utilisateur courant
					if($this->rank == 0){
						//retour demandé
						if($infos->serialcirc_expl_ret_asked == SERIALCIRC_EXPL_RET_asked){
							$form.="
						<input type='hidden' name='ret_accepted' value='1' />
						<input type='button' class='imp_bouton' value='".htmlentities($msg['serialcirc_ret_asked'],ENT_QUOTES,$charset)."' onclick='document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].submit();'/>";
						//transmission demandé par le centre de doc
						}else if($infos->serialcirc_expl_trans_doc_asked == SERIALCIRC_EXPL_TRANS_DOC_asked) {
							$form.="
						<input type='hidden' name='trans_doc_accepted' value='1' />
						<input type='button' class='imp_bouton' value='".htmlentities($msg['serialcirc_trans_doc_asked'],ENT_QUOTES,$charset)."' onclick='document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].submit();'/>";
						//transmission demandée
						}else if($infos->serialcirc_expl_trans_asked == SERIALCIRC_EXPL_TRANS_asked){
							$form.="
						<input type='hidden' name='trans_accepted' value='1' />
						<input type='button' class='imp_bouton' value='".htmlentities($msg['serialcirc_trans_asked'],ENT_QUOTES,$charset)."' onclick='document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].submit();'/>";
						}
					//le suivant
					}else if($this->rank == 1 && $this->start_date){
						$form.="
						<input type='hidden' name='report_late' value='1' />
						<input type='hidden' name='ask_transmission' value='1' />";

						if($infos->serialcirc_expl_trans_doc_asked != SERIALCIRC_EXPL_TRANS_DOC_ask ){
							$form.="
						<input type='button' class='bouton' onclick='document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].report_late.value=1;document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].ask_transmission.value=0;document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].submit();' value='".htmlentities($msg['serialcirc_report_late'].($circ_infos->serialcirc_circ_trans_doc_asked*1 >0 ? " (".$circ_infos->serialcirc_circ_trans_doc_asked.")":""),ENT_QUOTES,$charset)."'/>";
						}else{
							$form.="
						<input type='button' class='bouton' disabled='disabled' value='".htmlentities($msg['serialcirc_late_reported'],ENT_QUOTES,$charset)."'/>";

						}
						$form.="
						<input type='button' class='bouton' onclick='document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].ask_transmission.value=1;document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].report_late.value=0;document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].submit();' value='".htmlentities($msg['serialcirc_ask_transmission'].($circ_infos->serialcirc_circ_trans_asked*1 >0 ? " (".$circ_infos->serialcirc_circ_trans_asked.")":""),ENT_QUOTES,$charset)."'/>";

					}
				}
				if($this->virtual && $this->state == SERIALCIRC_EXPL_STATE_CIRC_pending){
				$query = "select date_add('".$this->bulletine_date."', interval ".$this->duration_before_start." day)";
				$res = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($res)){
					$end_subscription = pmb_mysql_result($res,0,0);
					$query = "select datediff('".$end_subscription."',now())";
					$res = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($res)){
						$test = pmb_mysql_result($res,0,0);
					}else $test = -1;
					if($test >=0 && !$circ_infos->serialcirc_circ_subscription){
						$form.="
					<input type='hidden' name='subscription' value='1' />
					<input type='button' class='bouton' onclick='document.forms[\"actions_form_".$this->id_serialcirc.$this->expl_id."\"].submit();' value='".htmlentities(sprintf($msg['serialcirc_subscribe_list'],formatdate($end_subscription)),ENT_QUOTES,$charset)."' />";
					}else{
						$form.="
					<input type='button' class='bouton' disabled='disabled' value='".htmlentities(sprintf($msg['serialcirc_subscribe_list'],formatdate($end_subscription)),ENT_QUOTES,$charset)."' />";

					}

				}
			}
			}
			$form.="
				</form>";
		}
		return $form;
	}
}