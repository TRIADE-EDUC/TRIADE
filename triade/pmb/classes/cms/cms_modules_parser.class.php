<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_modules_parser.class.php,v 1.18.4.3 2025/04/30 07:28:37 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_modules_parser {
	private $path;
	static private $modules_list = array();
	private $folders_list = array();
	private $cadres_list = array();
	public $cadres_classement_list = array();
	public $managed_modules = array();

	public function __construct($path=""){
		global $base_path;
		if($path == "") $path = $base_path."/cms/modules/";
		$this->path = $path;
		$this->cadres_classement_list=array();
	}

	protected function get_folders_list(){
		if(count($this->folders_list) == 0){
			if(is_dir($this->path)){
				$dh = opendir($this->path);
				//on parcours tout le répertoire
				while(($dir = readdir($dh)) !== false){
					//le répertoire parent et common ne sont pas des modules
				    if($dir != "common" && substr($dir,0,1) != "." && $dir != "CVS"){
				        $this->folders_list[] = $dir;
					}
				}
				closedir($dh);
			}
		}
		return $this->folders_list;
	}

	public function get_modules_list(){
		$tri=array();
		if(count(self::$modules_list) == 0){
			$this->get_folders_list();
			foreach ($this->folders_list as $module_name){
				$module_class_name = "cms_module_".$module_name;
				if(class_exists($module_class_name)){
					//une histoire de hash dans les formulaires...
					$hash_var = $module_class_name."_hash";
					global ${$hash_var};
					$size = (is_array(${$hash_var}) ? count(${$hash_var}) : 0);
					self::$modules_list[$module_name] = $module_class_name::get_informations();
					//c'est la même histoire...
					$other_size = (is_array(${$hash_var}) ? count(${$hash_var}) : 0);
					if ($size!= $other_size) {
						$module = new $module_class_name();
						array_unshift(${$hash_var},$module->get_hash());
					}
					$tri[$module_name]=self::$modules_list[$module_name]['name'];
				}
			}
			// tri par nom
			asort($tri);
			$memo_modules_list=self::$modules_list;
			self::$modules_list=array();
			foreach($tri as $module_name => $name){
				self::$modules_list[$module_name]=$memo_modules_list[$module_name];
			}
		}
		return self::$modules_list;
	}

	public function get_module_class($class){
		$this->get_folders_list();
		if((in_array($class,$this->folders_list))){
			$module_class_name = "cms_module_".$class;
			if(class_exists($module_class_name)){
				return new $module_class_name();
			}
		}
		return false;
	}

	public function get_cadres_list(){
		if(count($this->cadres_list) == 0){
			$this->cadres_list= array();
			$this->cadres_classement_list= array();
			$query = "select * from cms_cadres order by cadre_classement, cadre_name";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->cadres_list[] = $row;
					if($row->cadre_classement)$this->cadres_classement_list[$row->cadre_classement]=1;
				}
			}
		}
		return $this->cadres_list;
	}

	public static function get_module_class_by_id($id){
		$id = intval($id);
		$query = "select * from cms_cadres where id_cadre = ".$id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			return new $row->cadre_object($row->id_cadre);
		}
	}

	public static function get_module_class_content($class_name,$id){
	    $id = intval($id);
	    if(class_exists($class_name)){
	        if(!isset(self::$modules_classes_content[$class_name][$id])){
	           self::$modules_classes_content[$class_name][$id] = new $class_name($id);
	        }
	        return self::$modules_classes_content[$class_name][$id];
	    }
	    return false;
	}

	public function get_managed_modules(){
		global $base_path;

		$this->managed_modules = array();
		if(count($this->managed_modules) == 0){
			foreach($this->get_modules_list() as $key => $module){
				if($module['managed'] && ((SESSrights & CMS_BUILD_AUTH) || $module['managedWithoutBuild'])){
					$this->managed_modules[] = array(
						'name' => $module['name'],
						'link' => $base_path."/cms.php?categ=manage&sub=".$key."&action=get_form",
					    'url' => $base_path."/cms.php?categ=manage&sub=".$key,
						'sub' => $key
					);
				}else{
					continue;
				}
			}
		}
		return $this->managed_modules;
	}
}