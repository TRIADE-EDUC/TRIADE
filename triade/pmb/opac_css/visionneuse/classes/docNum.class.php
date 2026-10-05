<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docNum.class.php,v 1.14.2.1.2.1 2025/01/30 09:08:07 tsamson Exp $

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");
require_once($visionneuse_path."/classes/defaultConf.class.php");
require_once($visionneuse_path."/classes/mimetypeClass.class.php");

class docNum {
	public $titre;
	public $path;
	public $desc;
	public $mimetype;
	public $extension;
	public $id;
	public $search;
	public $mimetypeClass;
	public $infos;
	public $toDisplay;
	public $driver;				//classe driver dela visionneuse
	public $displayClass = false;
	public $defaultClass;
	public $params=array();
	public $message = array();


	public function __construct($infos,$driver,$params=array()) {
		$this->titre = $infos["titre"];
		$this->path = $infos["path"];
		$this->desc = $infos["desc"];
		$this->mimetype = $infos["mimetype"];
		$this->extension = $infos["extension"];
		$this->id = $infos["id"];
		$this->driver = $driver;
		$this->params = $params;
		$this->mimetypeClass = $this->driver->getMimetypeConf();
		if($infos["searchterms"]) $this->search = "#search=\"".trim(stripslashes($infos["searchterms"]))."\"";
		else $this->search = "";
    }

    public function fetchDisplay(){
    	if($this->driver->is_allowed($this->id)){
			$this->selectDisplayClass();

			return $this->displayClass->fetchDisplay();
    	}else{
    		//le titre
    		$this->toDisplay["titre"] = $this->titre;
	    	$this->toDisplay["doc"] = false;
			//la description
			$this->toDisplay["desc"] = $this->desc;
			return $this->toDisplay;
    	}
    }
    public function setMessage($message){
    	$this->message = $message;
    }

    public function render(){
    	$this->selectDisplayClass();
    	if(method_exists($this->displayClass, "render")){
    	    $this->displayClass->render();
    	}else{
    	    $url_doc = $this->driver->getDocumentUrl($this->id);
    	    print '<script> document.location = "'.$url_doc.'"</script>';
    	}
    }

    public function exec($method){
    	$this->selectDisplayClass();
    	if(method_exists($this->displayClass, "exec") && $method){
    		$this->displayClass->exec($method);
    	}
    	return false;
    }

    public function selectDisplayClass(){
    	global $visionneuse_path;

    	if (is_countable($this->mimetypeClass) && sizeof($this->mimetypeClass)>0){
    	//si une configuration existe
    		if(!empty($this->mimetypeClass[$this->mimetype])){
    		//et le mimetype courant est défini
    			//on récupère la bonne classe
	 			require_once($visionneuse_path."/classes/mimetypes/".$this->mimetypeClass[$this->mimetype]."/".$this->mimetypeClass[$this->mimetype].".class.php");
				$this->displayClass = new $this->mimetypeClass[$this->mimetype]($this);
    		}else $this->displayClass = false;
    	}

    	//sinon celle attribué par défaut...
    	if ($this->displayClass === false){
    		//on instancie les choix par défaut
	    	$this->defaultClass= new defaultConf();
	    	//si le mimetype est défini
			if(!empty($this->defaultClass->defaultMimetype[$this->mimetype])){
				//on récupère la bonne classe
				require_once($visionneuse_path."/classes/mimetypes/".$this->defaultClass->defaultMimetype[$this->mimetype]."/".$this->defaultClass->defaultMimetype[$this->mimetype].".class.php");
				$this->displayClass = new $this->defaultClass->defaultMimetype[$this->mimetype]($this);
			//sinon
			}else{
				//on prend la classe principale...
				$this->displayClass = new affichage($this);
			}
    	}
    	$this->displayClass->setMessage($this->message);
    }
}
?>