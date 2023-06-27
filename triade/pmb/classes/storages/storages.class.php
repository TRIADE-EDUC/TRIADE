<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storages.class.php,v 1.4 2019-06-07 13:30:42 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/storages/storage.class.php");

class storages {
	public $list = array();
	public $defined_list = array();
	
	public function __construct(){
		$this->get_storages_list();
		$this->fetch_datas();
	}
	
	protected function fetch_datas(){
		$this->defined_list[] = array();
		$query = "select * from storages order by storage_name";
		$result = pmb_mysql_query($query);
		$this->defined_list = array();
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->defined_list[] = array(
					'id' => $row->id_storage,
	 				'name' => $row->storage_name,
					'class' => $row->storage_class,
					'params' => unserialize($row->storage_params)
				);
			}
		}
	}
	
	public function process($action="",$id){
		switch($action){
			case "edit":
				print $this->get_form($id);
				break;
			case "delete" :
				$this->delete($id);
				$this->fetch_datas();
				print $this->get_table();
				break;
			case "save" :
				$this->save_form();
				$this->fetch_datas();
				print $this->get_table();
				break;
			default : 
				print $this->get_table();
				break;
		}
	}
	
	protected function get_storages_list(){
		global $class_path;
		global $charset,$msg;
		$xml = new DOMDocument();
		if(file_exists($class_path."/storages/storages_subst.xml")){
			$file = $class_path."/storages/storages_subst.xml";
		}else{
			$file = $class_path."/storages/storages.xml";
		}
		$xml->load($file);
		$storages = $xml->getElementsByTagName("storage");
		for($i=0 ; $i<$storages->length ; $i++){
			$storage = array();
			$storage['class'] = ($charset != "utf-8" ? utf8_decode($storages->item($i)->getAttribute('class')) : $storages->item($i)->getAttribute('class'));
			$storage['label'] = ($charset != "utf-8" ? utf8_decode($storages->item($i)->nodeValue) : $storages->item($i)->nodeValue);
			if(substr($storage['label'],0,4) == "msg:"){
				$storage['label'] = $msg[substr($storage['label'],4)];
			}
			$this->list[] = $storage;
		}
	}
	
	public function get_item_form($id=0){
		global $charset,$msg;
		$form = "
		<div class='row'>
			<h4>".htmlentities($msg['storage_form_title'],ENT_QUOTES,$charset)."</h4>
		</div>
		<div class='row'>&nbsp;</div>
		";
		
		$id+=0;	
		$form.="
		<div class='row'>
			<div class='colonne3'>
				<label for='storage_method'>".htmlentities($msg['storage_method_label'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='colonne_suite'>
				<select name='storage_method'>
					<option value='0'>".htmlentities($msg['storage_method_choice'],ENT_QUOTES,$charset)."</option>";
		foreach($this->defined_list as $storage){
			if($storage['id'] == $id){
				$selected = " selected='selected'";
			}else $selected = "";
			$form.="
					<option value='".htmlentities($storage['id'],ENT_QUOTES,$charset)."'".$selected.">".htmlentities($storage['name'],ENT_QUOTES,$charset)."</option>";
		}
		$form.= "
				</select>
			</div>
		</div>";
		return $form;
	}
	
	public function get_table($form_link=""){
		global $msg,$charset;
		
		if(!$form_link){
			$form_link="./admin.php?categ=docnum&sub=storages&action=edit";
		}
		$table = "
		<table>
		<tr>
		<th>".$msg['storage_name']."</th>
		<th>".$msg['storage_type']."</th>
		<th>".$msg['storage_resume']."</th>
		</tr>";
		for($i=0 ; $i<count($this->defined_list) ; $i++){
			$table.="
			<tr class='".($i%2 ? "odd" : "even")."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($i%2 ? "odd" : "even")."'\"  >
				<td onclick='document.location=\"".$form_link."&id=".$this->defined_list[$i]['id']."\"' style='cursor: pointer'>".htmlentities($this->defined_list[$i]['name'],ENT_QUOTES,$charset)."</td>
				<td>".htmlentities($this->get_type($this->defined_list[$i]['class']),ENT_QUOTES,$charset)."</td>
				<td>".$this->get_stockage_infos($this->defined_list[$i]['id'])."</td>
			</tr>";
		}
		$table.="
		</table>
		<div class='row'>&nbsp;</div>
		<div class='row'>
		<input type='button' class='bouton' value='".$msg['storage_add']."' onclick='document.location=\"".$form_link."&id=0\"'/>
		</div>";
		return $table;		
	}
	
	public function get_type($class){
		foreach($this->list as $method){
			if($method['class'] == $class){
				return $method['label'];
			}
		}
		return "";
	}
	
	public static function get_storage_class($id){
		global $base_path,$include_path,$class_path;
		$query = "select storage_class from storages where id_storage = ".($id*1);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			require_once($class_path."/storages/".$row->storage_class.".class.php");
			$obj = new $row->storage_class($id);
			return $obj;
		}
		return false;
	}
	
	public function get_stockage_infos($id){
		$obj = self::get_storage_class($id);
		if($obj){
			return $obj->get_infos();
		}
		return "";
	}
	
	public function get_form($id,$action="./admin.php?categ=docnum&sub=storages&action=save&id="){
		global $charset,$msg;
		$form = "
		<form method='post' action='".$action.$id."'>
			<div class='row'>
				<h3>".htmlentities($msg['storage_form_title'],ENT_QUOTES,$charset)."</h3>
			</div>
			<div class='row'>&nbsp;</div>";
		$id+=0;
		$row =array();
		if($id){
			$query ="select * from storages where id_storage = '".$id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_assoc($result);
			}
		}

		$form.="
			<div class='form-contenu'>
				<div class='row'>
					<div class='colonne3'>
						<label for='storage_name'>".$msg['storage_name']."</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='storage_name' value='".(!empty($row['storage_name']) ? htmlentities($row['storage_name'],ENT_QUOTES,$charset) : "")."' />
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='storage_method'>".htmlentities($msg['storage_method_label'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<select name='storage_method' onchange='get_storage_params_form(this.value);'>
							<option value='0'>".htmlentities($msg['storage_method_choice'],ENT_QUOTES,$charset)."</option>";
		foreach($this->list as $storage){
			if(count($row) && $storage['class'] == $row['storage_class']){
				$selected = " selected='selected'";
			}else $selected = "";
			$form.="
							<option value='".htmlentities($storage['class'],ENT_QUOTES,$charset)."'".$selected.">".htmlentities($storage['label'],ENT_QUOTES,$charset)."</option>";
		}

		$form.= "
						</select>
					</div>
					<div class='row'>&nbsp;</div>
					<div class='row' id='storage_params_form'>";
		if(!empty($row['storage_class'])){
			$form.= $this->get_params_form($row['storage_class'],$row['id_storage']);
		}	
		$form.= "
					</div>
					<script type='text/javascript'>
						function get_storage_params_form(class_name){
							if(class_name!= 0){
								var change= new http_request();
								change.request('./ajax.php?module=ajax&categ=storage&action=get_params_form&class_name='+class_name+'&id=".$id."',false,'',true,gotParamsForm);
							}else {
								document.getElementById('storage_params_form').innerHTML = '';
							}
						}
						
						function gotParamsForm(data){
							document.getElementById('storage_params_form').innerHTML = data;
						}
					</script>
				</div>
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div class='left'>
						<input class='bouton' type='submit' value='".htmlentities($msg['storage_save'],ENT_QUOTES,$charset)."' />&nbsp;
						<input class='bouton' type='button' value='".htmlentities($msg['storage_cancel'],ENT_QUOTES,$charset)."' onclick='history.go(-1);'/>
					</div>";
		if(!empty($row['id_storage'])) {
			$form.= "
					<div class='right'>
						<input class='bouton' type='button' value='".htmlentities($msg['storage_delete'],ENT_QUOTES,$charset)."' onclick='confirm_storage_delete(".$id.");'/>
					</div>";
		}
			$form.= "
					<div class='row'>&nbsp;</div>
				</div>
			</div>
			<script type='text/javascript'>
				function confirm_storage_delete(id){
					if(confirm('".$msg['storage_confirm_delete']."')){
						document.location='".str_replace("action=save","action=delete",$action.$id)."';
					}
				}
			</script>
		</form>";

		return $form;
	}
	
	public function save_form(){
		global $id,$storage_name,$storage_method,$storage_params;
		
		$id = intval($id);
		$row = array();
		if($id){
			$query ="select * from storages where id_storage = '".$id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_assoc($result);
			}
		}
		
		if(!empty($row['id_storage'])) {
			$query = "update storages set ";
			$clause =" where id_storage = ".$row['id_storage'];
		}else{
			$query = "insert into storages set ";
			$clause= "";
		}
		if($storage_method){
			$query.= "storage_name='".$storage_name."', storage_class = '".$storage_method."', storage_params = '".addslashes(serialize($storage_params))."'";
		}else if (!empty($row['id_storage'])){
			$query = "delete from storages";
		}
		pmb_mysql_query($query.$clause);
	}
	
	public function delete($id){
		$id+=0;
		pmb_mysql_query("delete from storages where id_storage='".$id."'");
	}
	
	public function get_params_form($class_name,$id){
		global $base_path,$include_path,$class_path;
		$storage = new storage($id);
		return $storage->get_form($class_name);
	}
}
