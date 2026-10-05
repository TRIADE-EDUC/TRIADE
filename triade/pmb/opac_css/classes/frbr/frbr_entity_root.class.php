<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_root.class.php,v 1.10 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/encoding_normalize.class.php");

class frbr_entity_root {
	protected $parameters;
	protected $msg = array();
	protected $class_name;
	protected $id = 0;
	protected $entity_folder;
	protected $managed_datas;
	protected $entity_class_name;
	
	public function __construct(){
		//on va chercher les messages...
		$this->class_name = get_class($this);
		$this->load_msg();
		$this->fetch_data();
	}
	
	protected function load_msg(){
		if (!count($this->msg)) {
			global $lang;
			global $class_path;
	
			//on regarde si on doit intégrer des fichiers de messages de parents
			$parents = $this->get_parent_classes();
			for($i=count($parents)-1 ; $i>=0 ; $i--){
				if($parents[$i] != "frbr_entity_root"){
					$parent = str_replace("frbr_entity_","",$parents[$i]);
					if(strpos($parent,"_") !== false){	
						$module_rep = substr($parent,0,strpos($parent,"_"));
					}else{
						$module_rep = $parent;
					}
					//on regarde la langue par défaut du module
					$default_language = $this->get_default_language($module_rep);
					//si elle est différente de celle de l'interface, on l'intègre
					// la langue par défaut donne l'assurance d'avoir tous les messages...
					if($default_language != $lang){
						$file = $class_path."/frbr/entities/".$module_rep."/messages/".$default_language."/".$parents[$i].".xml";
						$this->load_msg_file($file);
					}				
					//on commence par charger les messages de la langue par défaut du module...
					$file = $class_path."/frbr/entities/".$module_rep."/messages/".$lang."/".$parents[$i].".xml";
					$this->load_msg_file($file);
				}else{
					$file = $class_path."/frbr/entities/common/messages/".$lang."/frbr_entity_root.xml";
					$this->load_msg_file($file);
				}
			}
			$var = str_replace("frbr_entity_","",$this->class_name);
			if(strpos($var,"_") !== false){	
				$module_rep = substr($var,0,strpos($var,"_"));
			}else{
				$module_rep = $var;
			}
			//on regarde la langue par défaut du module
			$default_language = $this->get_default_language($module_rep);
			//si elle est différente de celle de l'interface, on l'intègre
			// la langue par défaut donne l'assurance d'avoir tous les messages...
			if($default_language != $lang){
				$file = $class_path."/frbr/entities/".$module_rep."/messages/".$default_language."/".$this->class_name.".xml";
				$this->load_msg_file($file);
			}
			$file = $class_path."/frbr/entities/".$module_rep."/messages/".$lang."/".$this->class_name.".xml";
			$this->load_msg_file($file);
		}
	}
	
	protected function get_parent_classes(){
		$parents = array();
		$parent = get_parent_class($this->class_name);
		if($parent){
			$parents[] =$parent;
			while($parent = get_parent_class($parent)){
				$parents[] =$parent;
			}
		}
		return $parents;
	}
	
	
	protected function get_recurse_classes_parent($name,$parents=array()){		
		$parent = get_parent_class($name);
		$parents[]=$parent;
		if(get_parent_class($parent)!=""){
			$parents = $this->get_recurse_classes_parent($parent,$parents);
		}
		return $parents;
	}
	
	protected function load_msg_file($file){
		global $charset;
		global $cache_msg_file;
		if(!$cache_msg_file || !is_array($cache_msg_file)){
			$cache_msg_file=array();
		}
		if(isset($cache_msg_file[$file])){
			$this->msg=$cache_msg_file[$file];
		}elseif(file_exists($file)){
			$messages = new XMLlist($file);
			$messages->analyser();
			$this->msg = array_merge($this->msg, $messages->table);
			$cache_msg_file[$file]=$this->msg;
			return true;
		}else{
			return false;
		}	
	}
	
	protected function format_text($text){
		global $charset;
		return htmlentities($text,ENT_QUOTES,$charset);
	}
			
	public function get_default_language($module){
		global $class_path;
		//si c'est un module, on a déjà lu le manifest...
		if(isset($this->manifest)){
			$default_language = $this->informations['default_language'];
		}else{
			//sinon, le cas des common est à part, on sait que c'est en français...
			if($module == "common"){
				$default_language = "fr_FR";
			}else{
				//sinon, on va chercher l'info dans le manifest du module...
				$default_language = self::get_module_default_language($class_path."/frbr/entities/".$module."/manifest.xml");
			}
		}
		return $default_language;		
	}
	
	public static function get_module_default_language($xml){
		if(!is_object($xml)){
			$dom = new domDocument();
			$dom->load($xml);
			$xml = $dom;
			
		}	
		$default_language = $xml->getElementsByTagName("default_language")->item(0)->nodeValue;
		return $default_language;
	}
	
	public function get_entity_folder(){
		if(!$this->entity_folder){
			global $class_path;
			$var = str_replace("frbr_entity_","",$this->class_name);
			if(strpos($var,"_") !== false){	
				$entity_rep = substr($var,0,strpos($var,"_"));
			}else{
				$entity_rep = $var;
			}
			$this->entity_folder = $class_path."/frbr/entities/".$entity_rep."/";
		}
		return $this->entity_folder;
	}
	
	protected function fetch_managed_datas($type=""){
		switch($type){
			case "filters" :
			case "sorting" :
			case "backbones" :
				if($this->entity_class_name){
					$query = "select managed_entity_box from frbr_managed_entities where managed_entity_name = '".$this->entity_class_name."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$datas = encoding_normalize::charset_normalize(json_decode(pmb_mysql_result($result,0,0), true), 'utf-8');
						if(isset($datas[$type])) {
							$this->managed_datas = $datas[$type];
						} else {
							$this->managed_datas = '';
						}
					}
				}
				break;
			default :
				$query = "select managed_entity_box from frbr_managed_entities where managed_entity_name = '".$this->class_name."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->managed_datas = encoding_normalize::charset_normalize(json_decode(pmb_mysql_result($result,0,0), true), 'utf-8');
				}
				break;
		}
		$this->managed_datas = $this->stripslashes($this->managed_datas);
	}
	
	public function json_encode(){
		return encoding_normalize::json_encode($this->parameters);
	}
	
	public function json_decode($parameters){
	    global $charset;
	    $this->parameters = json_decode($parameters);
	    if (isset($this->parameters->active_template) && $charset != "utf-8") {
	        $this->parameters->active_template = encoding_normalize::charset_normalize($this->parameters->active_template, "utf-8");
	    }
	}
	
	public function get_ajax_link($args){
		global $base_path;
		$request = "";
		foreach($args as $key => $val){
			$request.="&".$key."=".$val;
		}
		return $base_path."/ajax.php?module=cms&categ=frbr_entities&elem=".$this->class_name."&id=".$this->id."&action=ajax".$request;
	}	

	protected function get_exported_datas(){
		$infos = array(
			"id" => $this->id,
			"class" => $this->class_name,
			"parameters" => $this->parameters
		);
		return $infos;
	}
	
	protected function prefix_var_tree($tree,$prefix){
		for($i=0 ; $i<count($tree) ; $i++){
			$tree[$i]['var'] = $prefix.".".$tree[$i]['var'];
			if(isset($tree[$i]['children']) && $tree[$i]['children']){
				$tree[$i]['children'] = $this->prefix_var_tree($tree[$i]['children'],$prefix);
			}
		}
		return $tree;
	}
	
	public function get_entity_dom_id(){
		if(method_exists($this,"get_dom_id")){
			return $this->get_dom_id();
		}else{
			$query = "select cadre_object from frbr_cadres where id_cadre = ".$this->num_cadre;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				return $row->cadre_object."_".$this->num_cadre;
			}
		}
	}
	
	public function object_to_array($obj) {
		if (is_object($obj)) {
			$obj = (array) $obj;
		}
		if (is_array($obj) && count($obj)) {
			foreach($obj as $k=>$v) {
				$obj[$k]=$this->object_to_array($v);
			}
		}
		return $obj;
	}
	
	public function get_managed_datas() {
		return $this->managed_datas;
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
	
	public function set_parameters($parameters) {
	    $this->parameters = $parameters;
	}
	
	public function stripslashes($data) {
		if (is_array($data)) {
			foreach ($data as $key => $adata) {
				$data[$key] = $this->stripslashes($adata);
			}
			return $data;
		}
		return isset($data)? stripslashes($data) : "";
	}
	
	public static function get_num_page_from_num_datanode($num_datanode=0) {
		$num_datanode = intval($num_datanode);
		$query = "select datanode_num_page from frbr_datanodes where id_datanode = ".$num_datanode;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result,0,'datanode_num_page');
		} else {
			return 0;
		}
	}
	
	public function add_parameter($name, $value) {
	    $this->parameters->{$name} = $value;
	}
	
	public function get_parameter($name) {
	    if (isset($this->parameters->{$name})) {
	        return $this->parameters->{$name};
	    }
	    return null;
	}
}