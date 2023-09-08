<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_priorities.class.php,v 1.5 2018-12-06 12:27:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/scan_request/scan_request_priorities.tpl.php");

class scan_request_priorities {
	protected $scan_request_priorities;	//tableau des priorités 
	
	public function __construct(){
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		$this->scan_request_priorities = array();
		
		$rqt = "select * from scan_request_priorities order by scan_request_priority_weight, scan_request_priority_label asc";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->scan_request_priorities[] =array(
					'id' => $row->id_scan_request_priority,
					'label' => $row->scan_request_priority_label,
					'weight' => $row->scan_request_priority_weight
				);
			}
		}
	}

	public function get_scan_request_priorities(){
		return $this->scan_request_priorities;
	}

	public function get_selector_options($selected=0){
		global $charset;
		global $deflt_scan_request_priorities;
		
		if(!$selected){
			$selected=$deflt_scan_request_priorities;
		}		
		$options = "";
		for($i=0 ; $i<count($this->scan_request_priorities) ; $i++){
			$options.= "
			<option value='".$this->scan_request_priorities[$i]['id']."'".($this->scan_request_priorities[$i]['id']==$selected ? "selected='selected'" : "").">".htmlentities($this->scan_request_priorities[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	
	static function get_options($selected=0){
		global $charset;
		$options = '';
		$query = "select * from scan_request_priorities order by scan_request_priority_weight, scan_request_priority_label asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$options.= "
					<option value='".$row->id_scan_request_priority."'".($row->id_scan_request_priority==$selected ? "selected='selected'" : "").">".htmlentities($row->scan_request_priority_label,ENT_QUOTES,$charset)."</option>";
			}
		}
		return $options;
	}
	
	public function get_list($form_link="./admin.php?categ=scan_request&sub=priorities&action=edit"){
		global $msg,$charset;
		
		$table = "
		<table>
			<tr>
				<th>".$msg['scan_request_priorities_label']."</th>
				<th>".$msg['scan_request_priority_weight']."</th>
			</tr>";
		for($i=0 ; $i<count($this->scan_request_priorities) ; $i++){
			$class = ($i%2 ? "odd":"even");
			$table.= "
			<tr class='".($i%2 ? "odd":"even")."' onclick='document.location=\"".$form_link."&id=".$this->scan_request_priorities[$i]['id']."\"' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$class."'\">
				<td>
					".htmlentities($this->scan_request_priorities[$i]['label'],ENT_QUOTES,$charset)."
				</td>
				<td>
					".htmlentities($this->scan_request_priorities[$i]['weight'],ENT_QUOTES,$charset)."
				</td>
			</tr>";
		}
		$table.= "
		</table>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg[925]."' onclick='document.location=\"".$form_link."\"'/>
		</div>";
		return $table;
	}
	
	public function get_form($id=0,$url="./admin.php?categ=scan_request&sub=priorities"){
		global $msg,$charset;
		global $scan_request_priority_form;
		
		$form =str_replace("!!action!!",$url,$scan_request_priority_form);
		if($id){
			for($i=0 ; $i<count($this->scan_request_priorities) ; $i++){
				if($this->scan_request_priorities[$i]['id'] == $id){
					$priority = $this->scan_request_priorities[$i];
					break;
				}
			}
		}
		if($priority['id']){
			$form = str_replace("!!form_title!!",$msg['scan_request_priorities_add'],$form);
			$form = str_replace("!!label!!",htmlentities($priority['label'],ENT_QUOTES,$charset),$form);
			$form = str_replace("!!weight!!",htmlentities($priority['weight'],ENT_QUOTES,$charset),$form);
			$form = str_replace("!!id!!",$priority['id'],$form);
			$form = str_replace("!!bouton_supprimer!!","<input type='button' class='bouton' value=' ".$msg[63]." ' onclick='confirmation_delete(\"&action=delete&id=".$priority['id']."\",\"".htmlentities($priority['label'],ENT_QUOTES,$charset)."\")'/>",$form);
			$form.= confirmation_delete($url);
		}else{
			$form = str_replace("!!form_title!!",$msg['scan_request_priorities_update'],$form);	
			$form = str_replace("!!label!!","",$form);
			$form = str_replace("!!weight!!","1",$form);
			$form = str_replace("!!id!!",0,$form);
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}		
		return $form;
	}

	public function save(){
		global $dbh;
		global $scan_request_priority_label, $scan_request_priority_id, $scan_request_priority_weight;
		if($scan_request_priority_id){
			$scan_request_priority_id+=0;
			$scan_request_priority_weight+=0;
			$query = "update scan_request_priorities set ";
			$clause = "where id_scan_request_priority = ".$scan_request_priority_id;
		}else{
			$query = "insert into scan_request_priorities set ";
			$clause = "";
		}
		$query.= "
			scan_request_priority_label = '".$scan_request_priority_label."', 
			scan_request_priority_weight = '".$scan_request_priority_weight."' ";
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
		if($error){
			print "
			<script type='text/javascript'>
				alert(\"".$msg['cant_delete'].". ".$error."\");
			</script>";
		}else{
			$query = "delete from scan_request_priorities where id_scan_request_priority = ".$id;
			pmb_mysql_query($query);
		}		
		$this->fetch_data();
	}
}