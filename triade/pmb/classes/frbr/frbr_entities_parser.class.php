<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entities_parser.class.php,v 1.4 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entities_parser {
	private $path;
	static private $entities_list = array();
	private $folders_list = array();
// 	private $cadres_list = array();
// 	public $cadres_classement_list = array();
	public $managed_entities = array();

	public function __construct($path=""){
		global $class_path;
		if($path == "") $path = $class_path."/frbr/entities/";			
		$this->path = $path;	
// 		$this->cadres_classement_list=array();
	}

	protected function get_folders_list(){
		if(count($this->folders_list) == 0){
			if(is_dir($this->path)){
				$dh = opendir($this->path);
				//on parcours tout le répertoire
				while(($dir = readdir($dh)) !== false){
					//le répertoire parent et common ne sont pas des modules
					if($dir != "common"  & substr($dir,0,1) != "."){
						$this->folders_list[] = $dir;
					}
				}
				closedir($dh);
			}
		}
		return $this->folders_list;
	}

	public function get_entities_list(){
		$tri=array();
		if(count(self::$entities_list) == 0){
			$this->get_folders_list();
			foreach ($this->folders_list as $entity_name){
				$entity_class_name = "frbr_entity_".$entity_name;
				if(class_exists($entity_class_name)){
					self::$entities_list[$entity_name] = $entity_class_name::get_informations();
					$tri[$entity_name]=self::$entities_list[$entity_name]['name'];
				}
			}
			// tri par nom
			asort($tri);
			$memo_entities_list=self::$entities_list;
			self::$entities_list=array();
			foreach($tri as $entity_name => $name){
				self::$entities_list[$entity_name]=$memo_entities_list[$entity_name];
			}
		}
		return self::$entities_list;
	}

// 	public function get_module_class($class){
// 		$this->get_folders_list();
// 		if((in_array($class,$this->folders_list))){
// 			$module_class_name = "cms_module_".$class;
// 			if(class_exists($module_class_name)){
// 				return new $module_class_name();
// 			}
// 		}
// 		return false;
// 	}

// 	public function get_cadres_list(){
// 		global $dbh;
// 		if(count($this->cadres_list) == 0){
// 			$this->cadres_list= array();
// 			$this->cadres_classement_list= array();
// 			$query = "select * from cms_cadres order by cadre_classement, cadre_name";
// 			$result = pmb_mysql_query($query,$dbh);
// 			if(pmb_mysql_num_rows($result)){
// 				while($row = pmb_mysql_fetch_object($result)){
// 					$this->cadres_list[] = $row;
// 					if($row->cadre_classement)$this->cadres_classement_list[$row->cadre_classement]=1;
// 				}
// 			}
// 		}
// 		return $this->cadres_list;
// 	}

// 	public static function get_module_class_by_id($id){
// 		global $dbh;
// 		$id = intval($id);
// 		$query = "select * from cms_cadres where id_cadre = ".$id;
// 		$result = pmb_mysql_query($query,$dbh);
// 		if(pmb_mysql_num_rows($result)){
// 			$row = pmb_mysql_fetch_object($result);
// 			return new $row->cadre_object($row->id_cadre);
// 		}
// 	}
	
	public function get_managed_entities(){
// 		global $base_path;
		
		$this->managed_entities = array();
		if(count($this->managed_entities) == 0){
			foreach($this->get_entities_list() as $key => $entity){
// 				if($module['managed']){
					$this->managed_entities[$key] = array(
						'name' => $entity['name'],
// 						'link' => $base_path."/cms.php?categ=manage&sub=".$key."&action=get_form"
					);
// 				}else{
// 					continue;
// 				}
			}
		}
		return $this->managed_entities;
	}

}