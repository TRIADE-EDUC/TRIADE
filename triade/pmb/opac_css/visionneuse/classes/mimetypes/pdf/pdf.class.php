<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pdf.class.php,v 1.17 2023/12/07 15:18:26 dgoron Exp $

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");

class pdf extends affichage{
	public $doc;					//le document numérique à afficher
	public $driver;				//class driver de la visionneuse
	public $params;				//paramètres éventuels
	public $toDisplay= array();	//tableau des infos à afficher	
	public $tabParam = array();	//tableau décrivant les paramètres de la classe
	public $parameters = array();	//tableau des paramètres de la classe
 
    public function __construct($doc=0) {
    	if($doc){
    		$this->doc = $doc; 
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    	}
    }
    
    public function fetchDisplay(){
        global $visionneuse_path, $charset;
        
     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	//le pdf
    	$this->toDisplay["doc"] = "
		<iframe 
            title='". htmlentities($this->doc->titre, ENT_QUOTES, $charset) ."'
            name='docnum' 
            id='docnum' 
            src='".$this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id.$this->doc->search)."' 
            width='".$this->parameters["size_x"]."' 
            height='".$this->parameters["size_y"]."'>
        </iframe>
		<div id='wait'>
			<img src='$visionneuse_path/images/ajax-loader.gif' />
		</div>
		<script>
			window.onload = function(){
				var wait = document.getElementById('wait');
				if(wait) wait.style.display = 'none';
				if (typeof(checkSize) != 'undefined') checkSize();
			}
		</script>";
		if ($this->parameters['autoresize'] == 1)
		$this->toDisplay["doc"].= "
		<script>
			function checkSize(){
				var iframe= document.getElementById('docnum');
				if (isNaN(iframe.width) || iframe.width/getFrameWidth() <= 0.9 || iframe.width/getFrameWidth() >= 1){
					iframe.width = '90%';
					iframe.height = ((getFrameHeight()-40-80)*0.9)+'px';
				}				
			}
		</script>";
		//la description
		$this->toDisplay["desc"] = $this->doc->desc;
		return $this->toDisplay;  	
    }
    
    public function render(){
    	header("Content-Type: application/pdf");
    	if(!empty($this->doc->path)) {
    		header('Content-disposition: inline; filename="'.basename($this->doc->path).'"');
    	}
    	print $this->driver->openCurrentDoc();
    }
    
    public function getTabParam(){
    	if (!isset($this->parameters['size_x'])) {
    		$this->parameters['size_x'] = 0;
    	}
    	if (!isset($this->parameters['size_y'])) {
    		$this->parameters['size_y'] = 0;
    	}
		$this->tabParam = array(
			"size_x"=>array("type"=>"text","name"=>"size_x","value"=>$this->parameters['size_x'],"desc"=>"Largeur du document"),
			"size_y"=>array("type"=>"text","name"=>"size_y","value"=>$this->parameters['size_y'],"desc"=>"Hauteur du document"),
			"autoresize"=>array("type"=>"checkbox","name"=>"autoresize","value"=>1,"desc"=>"Autoriser le redimensionnement automatique"),
		);
       	return $this->tabParam;
    }
    
	public function getParamsPerso(){
		$params = $this->driver->getClassParam('pdf');
		$this->unserializeParams($params);
		if(empty($this->parameters['size_x'])) $this->parameters['size_x'] = $this->driver->getParam("maxX");
		if(empty($this->parameters['size_y'])) $this->parameters['size_y'] = $this->driver->getParam("maxY");
	}
	
	public function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		if(!isset($this->parameters['autoresize']) || !$this->parameters['autoresize']) {
			$this->parameters['autoresize'] = 0;
		} else {
			$this->parameters['autoresize'] = 1;
		}
		return $this->parameters;
	}
	
	public function serializeParams($paramsToSerialized){
		$this->parameters =$paramsToSerialized;
		if (!isset($this->parameters['autoresize']) || !$this->parameters['autoresize']) {
			$this->parameters['autoresize'] = 0;
		}
		return serialize($paramsToSerialized);
	}
}
?>
