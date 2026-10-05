<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storages.class.php,v 1.11 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/storages/storage.class.php");

class storages {
	public $list = array();
	public $defined_list = array();
	
	public static $storages_list;
	
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
	
	public function process($action="",$id=0){
		$id = intval($id);
		switch($action){
			case "add":
			case "edit":
				print $this->get_form($id);
				break;
			case "delete" :
			    $deleted = static::delete($id);
				if($deleted) {
    				$this->fetch_datas();
    				print list_configuration_explnum_storages_ui::get_instance()->get_display_list();
				} else {
    			    pmb_error::get_instance(get_class($this))->display(1, $this->get_url_base());
				}
				break;
			case "save" :
				$this->save_form();
				$this->fetch_datas();
				print list_configuration_explnum_storages_ui::get_instance()->get_display_list();
				break;
			default : 
				print list_configuration_explnum_storages_ui::get_instance()->get_display_list();
				break;
		}
	}
	
	protected function get_storages_list(){
		global $class_path;
		global $charset,$msg;
		if(!empty(static::$storages_list)) {
			$this->list = static::$storages_list;
			return static::$storages_list;
		}
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
		static::$storages_list = $this->list;
		return static::$storages_list;
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
	
	public function get_type($class){
		foreach($this->list as $method){
			if($method['class'] == $class){
				return $method['label'];
			}
		}
		return "";
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
		$id = intval($id);
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
						<input type='text' id='storage_name' name='storage_name' value='".(!empty($row['storage_name']) ? htmlentities($row['storage_name'],ENT_QUOTES,$charset) : "")."' />
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='storage_method'>".htmlentities($msg['storage_method_label'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<select id='storage_method' name='storage_method' onchange='get_storage_params_form(this.value);'>
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
	
	public static function delete($id){
		$id = intval($id);
		if ($id) {
		    $total = pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM cms_collections WHERE collection_num_storage =".$id));
		    if ($total) {
		        pmb_error::get_instance(static::class)->add_message('storage_method_label', 'storage_used_in_cms_collections');
		        return false;
		    }
		    $total = pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM cms_documents WHERE document_num_storage =".$id));
		    if ($total) {
		        pmb_error::get_instance(static::class)->add_message('storage_method_label', 'storage_used_in_cms_documents');
		        return false;
		    }
		    pmb_mysql_query("DELETE FROM storages WHERE id_storage='".$id."'");
	        return true;
		}
		return true;
	}
	
	public function get_params_form($class_name,$id){
		$storage = new storage($id);
		return $storage->get_form($class_name);
	}
	
	public function get_url_base() {
	    global $base_path, $current_module, $categ, $sub;
	    return  $base_path.'/'.$current_module.'.php?categ='.$categ.(!empty($sub) ? '&sub='.$sub : '');
	}
}
