<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_workflow.class.php,v 1.1.12.1 2025/03/06 08:11:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/scan_request/scan_request_admin_status.class.php");

class scan_request_workflow {
	
	protected $scan_request_status_workflow; //workflow : tableau des status from to
	
	public function __construct(){
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		$this->scan_request_status_workflow = array();
		
		$rqt = "select * from scan_request_status_workflow ";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				if(empty($this->scan_request_status_workflow[$row->scan_request_status_workflow_from_num])) {
					$this->scan_request_status_workflow[$row->scan_request_status_workflow_from_num] = array();
				}
				$this->scan_request_status_workflow[$row->scan_request_status_workflow_from_num][] =$row->scan_request_status_workflow_to_num;
			}
		}
	}

	public function get_form(){
		global $msg, $current_module;
		global $opac_scan_request_create_status;
		global $opac_scan_request_cancel_status;
		global $opac_scan_request_send_mail_status;
		
		$url="./admin.php?categ=scan_request&sub=workflow";
				
		$scan_request_admin_status = new scan_request_admin_status();
		$scan_request_status = $scan_request_admin_status->get_scan_request_status();
		
		if (trim($opac_scan_request_send_mail_status)) {
			$send_mail_status = json_decode($opac_scan_request_send_mail_status);
		} else {
			$send_mail_status = array();
		}

		$form=
		"<h1>".$msg["admin_scan_request_workflow_form_title"]."</h1>
		<form class='form-$current_module' id='userform' name='scan_request_status_workflow_form' method='post' action='".$url."&action=save'>
			<table>
				<tr>
					<th rowspan='2'>".$msg["admin_scan_request_workflow_title"]."</th>
					<th colspan=".count($scan_request_status).">".$msg["admin_scan_request_workflow_after_title"]."</th>
				</tr>
				<tr>
				";
					$ligne="";
					$parity=0;
					foreach($scan_request_status as $statusfrom) {
						$form.="<th>".$statusfrom['label']."</th>";
						if ($parity++ % 2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$ligne.="</tr><tr class='$pair_impair'><td>".$statusfrom['label']."</td>";
						foreach($scan_request_status as $statusto) {
						    if(!empty($this->scan_request_status_workflow[$statusfrom['id']]) && in_array($statusto['id'],$this->scan_request_status_workflow[$statusfrom['id']])) {
						        $check=" checked='checked' ";
						    } else {
						        $check="";
						    }
							if($statusfrom['id']==$statusto['id']){
								$ligne.="<td class='center'><input type='checkbox' value='' disabled='disabled' checked='checked'><input value='1' name='scan_request_status_tab[".$statusfrom['id']."][".$statusto['id']."]' type='hidden'  ></td>";
							}else{
								$ligne.="<td class='center'><input value='1' name='scan_request_status_tab[".$statusfrom['id']."][".$statusto['id']."]' type='checkbox' $check ></td>";
							}	
						}
					}
					$form.=$ligne."
				</tr>
			</table>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne25'>
					<label for='scan_request_create_opac_status'>".$msg['scan_request_workflow_opac_status_to_create']."</label>
				</div>
				<div class='colonne_suite'>
					<select name='scan_request_create_opac_status'>
						".$scan_request_admin_status->get_selector_options($opac_scan_request_create_status)."
					</select>
				</div>
			</div>
			<div class='row'>
				<div class='colonne25'>
					<label for='scan_request_cancel_opac_status'>".$msg['scan_request_workflow_opac_status_to_cancel']."</label>
				</div>
				<div class='colonne_suite'>
					<select name='scan_request_cancel_opac_status'>
						".$scan_request_admin_status->get_selector_options($opac_scan_request_cancel_status)."
					</select>
				</div>
			</div>
			<div class='row'>
				<div class='colonne25'>
					<label for='scan_request_send_mail_status'>".$msg['scan_request_workflow_send_mail_status']."</label>
				</div>
				<div class='colonne_suite'>
					<select name='scan_request_send_mail_status[]' multiple>
						".$scan_request_admin_status->get_selector_options_multiple($send_mail_status)."
					</select>
				</div>
			</div>
			<div class='row'>&nbsp;</div>
			<input type='hidden' name='from_form' value='1' >				
			<input class='bouton' type='submit' value=' ".$msg[77] ." ' />
		</form>";
					
		$this->fetch_data();
		return $form;
	}

	public function save(){
		global $scan_request_status_tab;
		global $from_form;
		global $scan_request_create_opac_status;
		global $scan_request_cancel_opac_status;
		global $scan_request_send_mail_status;
		global $opac_scan_request_create_status;
		global $opac_scan_request_cancel_status;
		global $opac_scan_request_send_mail_status;
		
		
		if(!($from_form*1)) return;
		
		$query="TRUNCATE TABLE scan_request_status_workflow";
		pmb_mysql_query($query);	
		foreach ($scan_request_status_tab as $from => $tolist){
			foreach ($tolist as $to => $val){
				$query = "insert into scan_request_status_workflow set scan_request_status_workflow_from_num='".$from."', scan_request_status_workflow_to_num='".$to."'";
				pmb_mysql_query($query);
			}
		}
		$query = "UPDATE parametres SET valeur_param='".$scan_request_create_opac_status."' WHERE type_param='opac' and sstype_param='scan_request_create_status'";
		pmb_mysql_query($query);
		
		$query = "UPDATE parametres SET valeur_param='".$scan_request_cancel_opac_status."' WHERE type_param='opac' and sstype_param='scan_request_cancel_status'";
		pmb_mysql_query($query);
		
		if (!is_array($scan_request_send_mail_status)) {
			$scan_request_send_mail_status = array();
		}
		$scan_request_send_mail_status = json_encode($scan_request_send_mail_status);
		$query = "UPDATE parametres SET valeur_param='".$scan_request_send_mail_status."' WHERE type_param='opac' and sstype_param='scan_request_send_mail_status'";
		pmb_mysql_query($query);
		
		$opac_scan_request_create_status=$scan_request_create_opac_status;
		$opac_scan_request_cancel_status=$scan_request_cancel_opac_status;
		$opac_scan_request_send_mail_status=$scan_request_send_mail_status;
	}
	
	public function get_scan_request_status_workflow(){
		return $this->scan_request_status_workflow;
	}
}