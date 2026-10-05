<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: affichage.class.php,v 1.17.2.1 2024/12/18 08:44:58 qvarin Exp $

class affichage {
	public $doc;			//le document numérique à afficher
	public $params;			//paramètres éventuels
	public $driver;			//driver de la visionneuse
	public $allowedFunction = array();
	public $message;
	public $toDisplay = array();

	public function __construct($doc) {
    	$this->doc = $doc;
    	$this->driver = $doc->driver;
    	$this->params = $doc->params;
    }

    public function fetchDisplay(){
        global $charset;

     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	//le pdf
    	//$this->toDisplay["doc"] = "<iframe src='".$visionneuse_path."/pdf.php?id=".$this->doc->id."' width='".$this->params["maxX"]."' height='".$this->params["maxY"]."'></iframe>";
    	$url_doc = $this->driver->getDocumentUrl($this->doc->id);
    	if (strpos($this->doc->path, "http") === 0) {
    	    $url_doc = $this->doc->path;
    	}
    	$this->toDisplay["doc"] = "<iframe title='".htmlentities($this->doc->titre, ENT_QUOTES, $charset)."' name='docnum' id='docnum' src='".$url_doc."' width='".$this->driver->getParam("maxX")."' height='".$this->driver->getParam("maxY")."'></iframe>";
		$this->toDisplay["doc"] .= 	"
		<script>
			window.onload = checkSize;
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
		//toPost
		return $this->toDisplay;
    }


    //exécution de l'appel AJAX
    public function exec($method){
    	if ($this->checkAllowedFunction($method)){
    		$this->{$method}();
    	} else {
    		print "forbidden";
    	}
    	return false;
    }

	public function checkAllowedFunction($method){
    	return in_array($method,$this->allowedFunction);
	}

	public function setMessage($message){
		$this->message = $message;
	}
}
?>