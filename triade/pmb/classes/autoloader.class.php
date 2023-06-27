<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autoloader.class.php,v 1.16 2019-06-06 09:56:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/*
 * Classe d'inclusion automatique
 */
class autoloader{
	protected $debug = false;

	public function __construct($set_classic=true,$debug = false){
		$this->debug = $debug;
		if($set_classic){
			//on ajoute dans le registe d'appel automatique, la méthode de chargement classique...
			$this->add_register();
		}
	}
	
	/*
	 * On empile dans le registe automatique un nouvelle méthode d'appel (qui peut être mis en tête de la pile)
	 * à partir de php 5.3
	 */
	public function add_register($type="classic",$first = false){
	    if(PHP_MINOR_VERSION >= 3){
			spl_autoload_register(array($this, $type),true,$first);
		}else{
			spl_autoload_register(array($this, $type),true);
		}
	}
	
	/*
	 * On empile dans le registe automatique un nouvelle méthode d'appel (qui peut être mis en tête de la pile)
	 */
	public function del_register($type="classic"){
		spl_autoload_unregister(array($this, $type));
	}
	
	/*
	 * 
	 */
	public function set_clean_pile(){
		$functions = spl_autoload_functions();
		foreach($functions as $func){
			spl_autoload_unregister(array($this,$func[1]));
		}
	}
	
	public function list_pile(){
		return spl_autoload_functions();	
	}
	
	private function load($file){
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		global $msg,$charset;
		global $current_module;
		if(file_exists($file)){
			require_once($file);
			if($this->debug){
				print "Load success<br>";
			}
		}else{
			if($this->debug){
				print "File ".$file." can't find<br>";
			}
			return false;
		}
		return true;
	}

	/*
	 * Inclusion classique...
	 * Les classes sont dans le répertoire classes/ ou un sous-répertoire...,
	 * Eventuellement un template dans includes/templates/ ou un sous-répertoire...
	 */
	private function classic($class_name) {
		global $class_path;
		global $include_path;
		if($this->debug){
			print '<br>Trying to load '. $class_name. ' via '. __METHOD__. "()<br>";
		}
		//fichier de la classe
		$class_file = $class_path."/".$class_name.".class.php";
		$success = $this->load($class_file);
		if($success){
			//fichier de template associé à la classe
			$tpl_file = $include_path."/templates/".$class_name.".tpl.php";
			$this->load($tpl_file);	
		}else{
			$class_file = "";
			$subfolder = substr($class_name,0,strpos($class_name,"_"));
			if($subfolder != ""){
				//fichier de la classe
				$class_file = $class_path."/".$subfolder."/".$class_name.".class.php";
				$success = $this->load($class_file);
				if($success){
					//fichier de template associé à la classe
					$tpl_file = $include_path."/templates/".$subfolder."/".$class_name.".tpl.php";
					$this->load($tpl_file);
				}
			}
		}
	}	

	/*
	 * Inclusion pour les classes modules du portail
	 */
	private function cms_modules($class_name) {
		global $base_path;
		if($this->debug){
			echo '<br>Trying to load ', $class_name, ' via ', __METHOD__, "()<br>";
		}
		//inclusion de la classe d'un module...
		if(!class_exists($class_name)){
			$module = str_replace("cms_module_","",$class_name);
			$class_file = $base_path."/cms/modules/".$module."/".$class_name.".class.php";
			$success = $this->load($class_file);
			if(!$success){
				$module = $class_file = "";
				$var = str_replace("cms_module_","",$class_name);
				$module = substr($var,0,strpos($var,"_"));
				$element = substr($var,strpos($var,"_")+1);
				if(strpos($element,"_") !== false){
					$element = substr($element,0,strpos($element,"_"));
					$class_file = $base_path."/cms/modules/".$module."/".$element."s/".$class_name.".class.php";
				}else{
					$class_file = $base_path."/cms/modules/".$module."/".$element."s/".$class_name.".class.php";
				}
				$this->load($class_file);	
			}
		}else if($this->debug){
			echo "Already loaded<br>";
		}
	}

	private function onto_class($class_name){
		global $class_path;
		
		if($this->debug){
			echo '<br>Trying to load ', $class_name, ' via ', __METHOD__, "()<br>";
		}
		//inclusion de la classe d'un module...
		if(!class_exists($class_name)){
			$class_file = $class_path."/onto/".$class_name.".class.php";
			$success = $this->load($class_file);
			if(!$success){
				$module = $class_file = "";
				$var = str_replace("onto_","",$class_name);
				$module = substr($var,0,strpos($var,"_"));
				$class_file = $class_path."/onto/".$module."/".$class_name.".class.php";
				$this->load($class_file);
			}
		}else if($this->debug){
			echo "Already loaded<br>";
		}
	}
	
	/*
	 * Inclusion pour les classes la veille documentaire
	 */
	private function docwatch($class_name) {
		global $class_path;
		if($this->debug){
			echo '<br>Trying to load ', $class_name, ' via ', __METHOD__, "()<br>";
		}
		//inclusion de la classe d'un module...
		if(!class_exists($class_name)){
			$elements = explode("_",$class_name);
			if(count($elements) < 2) return;
			$class_file = $class_path."/".$elements[0]."/".$elements[1]."s/".$class_name.".class.php";
			$this->load($class_file);
		}else if($this->debug){
			echo "Already loaded<br>";
		}
	}
	
	/*
	 * Inclusion pour les classes de sélecteurs
	 */
	private function selectors_class($class_name) {
		global $base_path;
		if($this->debug){
			echo '<br>Trying to load ', $class_name, ' via ', __METHOD__, "()<br>";
		}
		//inclusion de la classe d'un module...
		if(!class_exists($class_name)){
			$class_file = $base_path."/selectors/classes/".$class_name.".class.php";
			$this->load($class_file);
		}else if($this->debug){
			echo "Already loaded<br>";
		}
	}
	
	/*
	 * Inclusion pour les classes de sélecteurs
	 */
	private function rdf_entities_integration($class_name) {
		global $class_path;
		if($this->debug){
			echo '<br>Trying to load ', $class_name, ' via ', __METHOD__, "()<br>";
		}
		//inclusion de la classe d'un module...
		if(!class_exists($class_name)){
			$class_file = $class_path."/rdf_entities_integration/".$class_name.".class.php";
			$this->load($class_file);
		}else if($this->debug){
			echo "Already loaded<br>";
		}
	}
	
	/*
	 * Inclusion pour les classes d'entités frbr
	 */
	private function frbr_entities($class_name) {
		global $class_path;
		if($this->debug){
			echo '<br>Trying to load ', $class_name, ' via ', __METHOD__, "()<br>";
		}
		//inclusion de la classe d'un module...
		if(!class_exists($class_name)){
			$entity = str_replace(array("frbr_entity_", "_datanode", "_cadre", "_page"),"",$class_name);
			$class_file = $class_path."/frbr/entities/".$entity."/".$class_name.".class.php";
			$success = $this->load($class_file);
			if(!$success){
				$entity = $class_file = "";
				$var = str_replace("frbr_entity_","",$class_name);
				$entity = substr($var,0,strpos($var,"_"));
				$element = substr($var,strpos($var,"_")+1);
				if(strpos($element,"_") !== false){
					$element = substr($element,0,strpos($element,"_"));
				}
				$class_file = $class_path."/frbr/entities/".$entity."/".($element == 'entity' ? 'entities' : $element."s")."/".$class_name.".class.php";
				$this->load($class_file);	
			}
		}else if($this->debug){
			echo "Already loaded<br>";
		}
	}
}
