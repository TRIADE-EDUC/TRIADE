<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_admin_status.class.php,v 1.11 2018-12-06 12:27:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/scan_request/scan_request_admin_status.tpl.php");

class scan_request_admin_status {
	protected $scan_request_status;	//tableau des status 
	protected $scan_request_status_workflow; //workflow : tableau des status from to
	
	public function __construct(){
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		$this->scan_request_status = array();
		$this->scan_request_status_workflow = array();
		
		$rqt = "select * from scan_request_status order by scan_request_status_label asc";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->scan_request_status[] =array(
					'id' => $row->id_scan_request_status,
					'label' => $row->scan_request_status_label,
					'opac_show' => $row->scan_request_status_opac_show,
					'class_html' => $row->scan_request_status_class_html,
					'infos_editable' => $row->scan_request_status_infos_editable,
					'cancelable' => $row->scan_request_status_cancelable,
					'is_closed' => $row->scan_request_status_is_closed
				);
				$this->scan_request_status_workflow[$row->id_scan_request_status]= array();
			}

			$rqt = "select * from scan_request_status_workflow ";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){					
					$this->scan_request_status_workflow[$row->scan_request_status_workflow_from_num][] =$row->scan_request_status_workflow_to_num;
				}
			}
		}
	}

	public function get_scan_request_status(){
		return $this->scan_request_status;
	}
	
	public function get_scan_request_status_workflow(){
		return $this->scan_request_status_workflow;
	}

	public function get_selector_options($selected=0){
		global $charset;
		global $deflt_scan_request_status;
		
		if(!$selected){
			$selected=$deflt_scan_request_status;
		}		
		$options = "";
		for($i=0 ; $i<count($this->scan_request_status) ; $i++){
			$options.= "
			<option value='".$this->scan_request_status[$i]['id']."' ".($this->scan_request_status[$i]['id']==$selected ? "selected='selected'" : "").">".htmlentities($this->scan_request_status[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	
	public function get_selector_options_multiple($list_status = array()){
		global $charset;
	
		$options = "";
		for($i=0 ; $i<count($this->scan_request_status) ; $i++){
			$options.= "
			<option value='".$this->scan_request_status[$i]['id']."' ".(count($list_status) && in_array($this->scan_request_status[$i]['id'],$list_status)? "selected='selected'" : "").">".htmlentities($this->scan_request_status[$i]['label'],ENT_QUOTES,$charset)."</option>";
		}
		return $options;
	}
	
	public function get_list($form_link="./admin.php?categ=scan_request&sub=status&action=edit"){
		global $msg,$charset;
		
		$table = "
		<table>
			<tr>
				<th>".$msg['scan_request_status_label']."</th>
				<th>".$msg['scan_request_status_visible']."</th>
				<th>".$msg['scan_request_cancelable']."</th>
				<th>".$msg['scan_request_infos_editable']."</th>
				<th>".$msg['scan_request_is_closed']."</th>
						
			</tr>";
		for($i=0 ; $i<count($this->scan_request_status) ; $i++){
			$class = ($i%2 ? "odd":"even");
			$table.= "
			<tr class='".($i%2 ? "odd":"even")."' style='cursor: pointer' onclick='document.location=\"".$form_link."&id=".$this->scan_request_status[$i]['id']."\"' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$class."'\">
				<td><span class='".$this->scan_request_status[$i]['class_html']."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>
					".htmlentities($this->scan_request_status[$i]['label'],ENT_QUOTES,$charset)."
				</td>
				<td>".($this->scan_request_status[$i]['opac_show'] ? "X" : "")."</td>
					<td>".($this->scan_request_status[$i]['cancelable'] ? "X" : "")."</td>
				<td>".($this->scan_request_status[$i]['infos_editable'] ? "X" : "")."</td>
				<td>".($this->scan_request_status[$i]['is_closed'] ? "X" : "")."</td>
			</tr>";
		}
		$table.= "
		</table>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['editorial_content_publication_state_add']."' onclick='document.location=\"".$form_link."\"'/>
		</div>";
		return $table;
	}
	
	public function get_form($id=0,$url="./admin.php?categ=scan_request&sub=status"){
		global $msg,$charset;
		global $scan_request_status_form;
		
		
		$form =str_replace("!!action!!",$url,$scan_request_status_form);
		if($id){
			for($i=0 ; $i<count($this->scan_request_status) ; $i++){
				if($this->scan_request_status[$i]['id'] == $id){
					$publication_state = $this->scan_request_status[$i];
					break;
				}
			}
		}
		if($publication_state['id']){
			$form = str_replace("!!form_title!!",$msg['editorial_content_publication_state_edit'],$form);
			$form = str_replace("!!label!!",htmlentities($publication_state['label'],ENT_QUOTES,$charset),$form);
			$form = str_replace("!!visible!!",($publication_state['opac_show'] ? "checked='checked'": ""),$form);
			$form = str_replace("!!cancelable!!",($publication_state['cancelable'] ? "checked='checked'": ""),$form);
			$form = str_replace("!!infos_editable!!",($publication_state['infos_editable'] ? "checked='checked'": ""),$form);
			$form = str_replace("!!is_closed!!",($publication_state['is_closed'] ? "checked='checked'": ""),$form);
			$form = str_replace("!!id!!",$publication_state['id'],$form);
			$form = str_replace("!!bouton_supprimer!!","<input type='button' class='bouton' value=' ".$msg[63]." ' onclick='confirmation_delete(\"&action=delete&id=".$publication_state['id']."\",\"".htmlentities($publication_state['label'],ENT_QUOTES,$charset)."\")'/>",$form);
			$form.= confirmation_delete($url);
		}else{
			$form = str_replace("!!form_title!!",$msg['editorial_content_publication_state_add'],$form);	
			$form = str_replace("!!label!!","",$form);
			$form = str_replace("!!visible!!","",$form);
			$form = str_replace("!!cancelable!!","",$form);
			$form = str_replace("!!infos_editable!!","",$form);
			$form = str_replace("!!is_closed!!","",$form);
			$form = str_replace("!!id!!",0,$form);
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}
		for ($i=1;$i<=20; $i++) {
			if ($publication_state['class_html']=="statutnot".$i) $checked = "checked";
			else $checked = "";
			$couleur[$i]="<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
					<input id='statutnot".$i."' type=radio name='scan_request_status_class_html' value='statutnot".$i."' $checked class='checkbox' /></span>";
			if ($i==10) $couleur[10].="<br />";
			elseif ($i!=20) $couleur[$i].="<b>|</b>";
		}
		$couleurs=implode("",$couleur);
		$form = str_replace('!!class_html!!', $couleurs, $form);
		
		return $form;
	}

	public function get_form_workflow($url="./admin.php?categ=scan_request&sub=workflow"){
		global $msg,$charset, $current_module;
		global $opac_scan_request_create_status;
		global $opac_scan_request_cancel_status;
		global $opac_scan_request_send_mail_status;
		
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
					<th colspan=".count($this->scan_request_status).">".$msg["admin_scan_request_workflow_after_title"]."</th>
				</tr>
				<tr>
				";
					$ligne="";
					$parity=0;
					foreach($this->scan_request_status as $statusfrom) {
						$form.="<th>".$statusfrom['label']."</th>";
						if ($parity++ % 2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$ligne.="</tr><tr class='$pair_impair'><td>".$statusfrom['label']."</td>";
						foreach($this->scan_request_status as $statusto) {
							if(in_array($statusto['id'],$this->scan_request_status_workflow[$statusfrom['id']])) $check=" checked='checked' ";
							else $check="";
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
						".$this->get_selector_options($opac_scan_request_create_status)."
					</select>
				</div>
			</div>
			<div class='row'>
				<div class='colonne25'>
					<label for='scan_request_cancel_opac_status'>".$msg['scan_request_workflow_opac_status_to_cancel']."</label>
				</div>
				<div class='colonne_suite'>
					<select name='scan_request_cancel_opac_status'>
						".$this->get_selector_options($opac_scan_request_cancel_status)."
					</select>
				</div>
			</div>
			<div class='row'>
				<div class='colonne25'>
					<label for='scan_request_send_mail_status'>".$msg['scan_request_workflow_send_mail_status']."</label>
				</div>
				<div class='colonne_suite'>
					<select name='scan_request_send_mail_status[]' multiple>
						".$this->get_selector_options_multiple($send_mail_status)."
					</select>
				</div>
			</div>
			<div class='row'>&nbsp;</div>
			<input type='hidden' name='from_form' value='1' >				
			<input class='bouton' type='submit' value=' ".$msg[77] ." ' />
		</form>";
		return $form;
	}

	public function save_workflow(){
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
		
		$this->fetch_data();		
	}
	public function save(){
		global $scan_request_status_label;
		global $scan_request_status_visible;
		global $scan_request_status_visible_abo; 
		global $scan_request_status_id;
		global $scan_request_status_class_html;
		global $scan_request_cancelable;
		global $scan_request_infos_editable;
		global $scan_request_is_closed;
		
		if($scan_request_status_id){
			$scan_request_status_id+=0;
			$query = "update scan_request_status set ";
			$clause = "where id_scan_request_status = ".$scan_request_status_id;
		}else{
			$query = "insert into scan_request_status set ";
			$clause = "";
		}
		$query.= "
			scan_request_status_label = '".$scan_request_status_label."',
			scan_request_status_opac_show = ".($scan_request_status_visible ? 1 : 0).",
			scan_request_status_cancelable = ".($scan_request_cancelable ? 1 : 0).",
			scan_request_status_infos_editable = ".($scan_request_infos_editable ? 1 : 0).",
			scan_request_status_class_html = '".$scan_request_status_class_html."',
			scan_request_status_is_closed = ".($scan_request_is_closed ? 1 : 0);
		$query.= " ".$clause;
		pmb_mysql_query($query);		
		
		$this->fetch_data();
	}
	
	public function delete($id){
		global $msg,$charset;
		$id+=0;
		if(!$id){
			return;
		}
		$error = array();
		if($id == 1){
			$error[] = $msg['scan_request_status_forbidden'];
		} else {
			$result = pmb_mysql_query("select count(1) from scan_requests where scan_request_num_status ='".$id."'");
			$total = pmb_mysql_result($result, 0, 0);
			if($total){
				$error[] = $msg['scan_request_status_used'];
			}
			$result = pmb_mysql_query("select count(1) from scan_request_status_workflow where scan_request_status_workflow_from_num != scan_request_status_workflow_to_num and (scan_request_status_workflow_from_num ='".$id."' or scan_request_status_workflow_to_num ='".$id."')");
			$total = pmb_mysql_result($result, 0, 0);
			if($total){
				$error[] = $msg['scan_request_status_workflow_used'];
			}
		}
		if($error){
			print "
			<script type='text/javascript'>
				alert(\"".implode('.', $error)."\");
			</script>";
		}else{
			$query = "delete from scan_request_status where id_scan_request_status = ".$id;
			pmb_mysql_query($query);
			$query = "delete from scan_request_status_workflow where scan_request_status_workflow_from_num = scan_request_status_workflow_to_num and scan_request_status_workflow_from_num = ".$id;
			pmb_mysql_query($query);
		}		
		$this->fetch_data();
	}
}