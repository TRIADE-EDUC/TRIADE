<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view.class.php,v 1.21 2022/01/03 11:17:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view extends cms_module_root{
	protected $use_jquery = false;
	protected $use_dojo = false;
	protected $cadre_parent;
	private $dojo_theme="tundra";
	
	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}
	
	protected function fetch_datas(){
		if($this->id){
		//on commence par aller chercher ses infos
			$query = " select id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_cadre_content;
				$this->hash = $row->cadre_content_hash;
				$this->cadre_parent = (int) $row->cadre_content_num_cadre;
				$this->unserialize($row->cadre_content_data);
			}	
		}
	}
	
	public function save_form(){
		$this->get_hash();
		if($this->id){
			$query = "update cms_cadre_content set";
			$clause = " where id_cadre_content=".$this->id;
		}else{
			$query = "insert into cms_cadre_content set";
			$clause = "";
		}
		$query.= " 
			cadre_content_hash = '".$this->hash."',
			cadre_content_type = 'view',
			cadre_content_object = '".$this->class_name."',".
			($this->cadre_parent ? "cadre_content_num_cadre = '".$this->cadre_parent."'," : "")."		
			cadre_content_data = '".addslashes($this->serialize())."'
			".$clause;
		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciennes vues...
			$query = "delete from cms_cadre_content where id_cadre_content != '".$this->id."' and cadre_content_type='view' and cadre_content_num_cadre = '".$this->cadre_parent."'";
			pmb_mysql_query($query);
			
			return true; 
		}
		return false;
	}
	
	public function set_cadre_parent($id){
	    $this->cadre_parent = (int) $id;
	}
	
	/*
	 * Méthode de suppression
	 */
	public function delete(){
		if($this->id){
			//on commence par éliminer les sous-éléments associé (sait-on jamais...)
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where cadre_content_num_cadre_content = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$sub_elem = new $row->cadre_content_object($row->id_cadre_content);
					$success = $sub_elem->delete();
					if(!$success){
						//TODO verbose mode
						return false;
					}
				}
			}
			//on est tout seul, éliminons-nous !
			$query = "delete from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				$this->delete_hash();
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function get_form(){
		return "";
	}
	
	public function render($datas){
		return "";		
	}
	
	public function get_headers($datas=array()){
		global $lang;
		$headers = array();
		if($this->use_jquery){
			$headers[] = "<!-- Inclusion JQuery pour le portail-->";
			$headers[] = "<script type='text/javascript' src='./cms/modules/common/includes/javascript/jquery-2.1.1.min.js"."'></script>";
		}
		if($this->use_dojo){
			$headers[] = "<link rel='stylesheet' type='text/css' href='https://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dijit/themes/".$this->dojo_theme."/".$this->dojo_theme.".css' />";
			$headers[]= "
			<script type='text/javascript'>
				dojoConfig = {
					has: {
			            'dojo-firebug': true,
			            'dojo-debug-messages': true
        			},
					parseOnLoad : true,
					locale : '".substr($lang,0,2)."',
					deps: ['dojo/parser']
				}
			</script>
			";
			$headers[]= "<script src='https://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dojo/dojo.js'/>";
			$headers[]= "
			<script type='text/javascript'>
				dojo.addOnLoad(function (){
					//on ajoute la class pour le style...
					dojo.addClass(dojo.body(),'".$this->dojo_theme."');
					//on balance un evenement pour les initialiser les modules si besoin...
					dojo.publish('init',['cms_dojo_init',{}]);
				});
			</script>";
		}
		return $headers;
	}
	
	public function set_module_class_name($module_class_name){
		$this->module_class_name = $module_class_name;
		$this->fetch_managed_datas();
	}
	
	protected function fetch_managed_datas($type="views"){
		parent::fetch_managed_datas($type);
	}
	
	protected function get_exported_datas(){
		$infos = parent::get_exported_datas();
		$infos['type'] = "view";
		return $infos;
	}
	
	
	public function get_format_data_structure(){
		return array();
	}
	
	protected function get_ace_editor_script($textarea_id = 'cms_module_common_view_django_template_content'){
		return "
		<script>
    		pmbDojo.aceManager.initEditor('".$textarea_id."');
		</script>";
	}
}