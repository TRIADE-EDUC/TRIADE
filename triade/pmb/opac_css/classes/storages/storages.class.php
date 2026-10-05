<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storages.class.php,v 1.6 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class storages {
	public $list = array();
	public $defined_list = array();
	
	public function __construct(){
		$this->get_storages_list();
		$this->fetch_datas();
	}
	
	protected function fetch_datas(){
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
			$storage['class'] = ($charset != "utf-8" ? encoding_normalize::utf8_decode($storages->item($i)->getAttribute('class')) : $storages->item($i)->getAttribute('class'));
			$storage['label'] = ($charset != "utf-8" ? encoding_normalize::utf8_decode($storages->item($i)->nodeValue) : $storages->item($i)->nodeValue);
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
		
		$id = intval($id);	
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
	
	public static function get_storage_class($id){
		global $class_path;
		
		$id = intval($id);
		$query = "select storage_class from storages where id_storage = ".$id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			require_once($class_path."/storages/".$row->storage_class.".class.php");
			$obj = new $row->storage_class($id);
			return $obj;
		}
		return false;
	}
	
	public function get_form($type,$id){
		global $charset,$msg;
		$form = "
		<div class='row'>
			<h4>".htmlentities($msg['storage_form_title'],ENT_QUOTES,$charset)."</h4>
		</div>
		<div class='row'>&nbsp;</div>
		";

		$id = intval($id);
		$row =array();
		if($id){
			$query ="select * from storages where storage_object_type = '".$type."' and storage_num_object = '".$id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_assoc($result);
			}
		}

		$form.="
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
					<option value='".htmlentities($storage['class'],ENT_QUOTES,$charset)."'".$selected.">".htmlentities($storage['label'],ENT_QUOTES,$charset)."</option>
			";
		}

		$form.= "
				</select>
			</div>
			<div class='row' id='storage_params_form'>";
		if($row['storage_class']){
			$form.= $this->get_params_form($row['storage_class'],$row['storage_params']);
		}	
		$form.= "
			</div>
			<script>
				function get_storage_params_form(class_name){
					if(class_name!= 0){
						var change= new http_request();
						change.request('./ajax.php?module=ajax&categ=storage&action=get_params_form&class_name='+class_name,false,'',true,gotParamsForm);
					}else {
						document.getElementById('storage_params_form').innerHTML = '';
					}
				}
				
				function gotParamsForm(data){
					document.getElementById('storage_params_form').innerHTML = data;
				}
			</script>
		</div>
		<div class='row'>&nbsp;</div>";

		return $form;
	}
	
	public function save_form($type,$id){
		global $storage_method,$storage_params;
		
		$id = intval($id);
		$row =array();
		if($id){
			$query ="select * from storages where storage_object_type = '".$type."' and storage_num_object = '".$id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_assoc($result);
			}
		}
		
		if($row['id_storage']){
			$query = "update storages set ";
			$clause =" where id_storage = ".$row['id_storage'];
		}else{
			$query = "insert into storages set ";
			$clause= "";
		}
		if($storage_method){
			$query.= "storage_object_type = '".$type."', storage_num_object = '".$id."',storage_class = '".$storage_method."', storage_params = '".addslashes(serialize($storage_params))."'";
		}else if ($row['id_storage']){
			$query ="delete from storages";
		}
		pmb_mysql_query($query.$clause);
	}
	
	public function get_params_form($class_name,$params=array()){
		global $class_path;
		
		$exists = false;
		foreach($this->list as $storage){
			if($class_name == $storage['class']){
				$exists =true;
				break;
			}
		}
		if($exists){
			require_once($class_path."/storages/".$class_name.".class.php");
			$storage = new $class_name($params);
			return $storage->get_params_form();
		}
		return "";
	}
}