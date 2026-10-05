<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: images.class.php,v 1.14.6.1.2.1 2025/04/03 14:19:40 dgoron Exp $

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");

class images extends affichage{
	public $doc;				//le document numérique à afficher
	public $driver;			//class driver de la visionneuse
	public $params;			//paramètres éventuels
	public $toDisplay= array();//tableau des infos à afficher
	public $tabParam = array();	//tableau décrivant les paramètres de la classe
	public $parameters = array();	//tableau des paramètres de la classe
	
    public function __construct($doc="") {
      	if($doc){
    		$this->doc = $doc; 
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    	}
    }
    
    public function fetchDisplay(){
    	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	//l'image
    	$this->toDisplay["doc"] = "<img src='".$this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id)."' />";
   		//la description
		$this->toDisplay["desc"] = $this->doc->desc;   
		
		return $this->toDisplay; 	
    }   
    
    public function render(){
    	header("Content-Type: image/png");
    	$this->resizeToDisplay();
    }
    
    public function resizeToDisplay(){

    	$src_img = imagecreatefromstring($this->driver->openCurrentDoc());
    	
    	if($src_img === false && extension_loaded('imagick')){
    	    $im = new Imagick();
    	    $document = $this->driver->openCurrentDoc();
    	    if (!empty($document)) {
    	        $im->readimageblob($document);
        	    $file = tempnam(sys_get_temp_dir(), 'imagick');
        	    $im->writeImage($file.".png");
        	    $src_img = imagecreatefrompng($file.".png");
        	    unlink($file.".png");
    	    }
    	}
    	
		if ($src_img) {
			$photo_mean_size_x=imagesx($src_img);
			$photo_mean_size_y=imagesy($src_img);
		}else {
			$photo_mean_size_x=200 ;
			$photo_mean_size_y=200 ;
		}
		if (!empty($this->parameters["size_x"])) {
		    $photo_mean_size_x = intval($this->parameters["size_x"]);
		}
		if (!empty($this->parameters["size_y"])) {
		    $photo_mean_size_y = intval($this->parameters["size_y"]);
		}
	
		if ($src_img) {
			$rs=$photo_mean_size_x/$photo_mean_size_y;
			$taillex=imagesx($src_img);
			$tailley=imagesy($src_img);
			if (!$taillex || !$tailley) return "" ;
			if (($taillex>$photo_mean_size_x)||($tailley>$photo_mean_size_y)) {
				$r=$taillex/$tailley;
				if (($r<1)&&($rs<1)) {
					//Si x plus petit que y et taille finale portrait 
					//Si le format final est plus large en proportion
					if ($rs>$r) {
						$new_h=$photo_mean_size_y; 
						$new_w=$new_h*$r; 
					} else {
						$new_w=$photo_mean_size_x;
						$new_h=$new_w/$r;
					}
				} else if (($r<1)&&($rs>=1)){ 
					//Si x plus petit que y et taille finale paysage
					$new_h=$photo_mean_size_y;
					$new_w=$new_h*$r;  
				} else if (($r>1)&&($rs<1)) {
					//Si x plus grand que y et taille finale portrait
					$new_w=$photo_mean_size_x;
					$new_h=$new_w/$r;
				} else {
					//Si x plus grand que y et taille finale paysage
					if ($rs<$r) {
						$new_w=$photo_mean_size_x;
						$new_h=$new_w/$r;
					} else {
						$new_h=$photo_mean_size_y;
						$new_w=$new_h*$r;
					}
				}
			} else {
				$new_h = $tailley ;
				$new_w = $taillex ;
			}
			$dst_img=imagecreatetruecolor($new_w,$new_h);
			ImageSaveAlpha($dst_img, true);
			ImageAlphaBlending($dst_img, false);
			imagefilledrectangle($dst_img,0,0,$photo_mean_size_x,$photo_mean_size_y,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
			imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
			$watermark = $this->driver->getUrlImage($this->parameters['watermark']);
			if($watermark!= ""){
			
				$size = @getimagesize($watermark);
				switch ($size[2]) {
					case 1:
						$wat_img = imagecreatefromgif($watermark);
					 	break;
					case 2:
						$wat_img = imagecreatefromjpeg($watermark);
						break;
					case 3:
						$wat_img = imagecreatefrompng($watermark);
						break;
					case 6:
						$wat_img = imagecreatefromwbmp($watermark);
						break;
					default:
						$wat_img="";
						break;
				}
				
				if ($wat_img) {
					$wr_img=imagecreatetruecolor($new_w,$new_h);
					imagecolortransparent($wr_img,imagecolorallocatealpha($wr_img,0, 0, 0, 127));
					ImageSaveAlpha($wr_img, true);
					ImageAlphaBlending($wr_img, false);
					imagefilledrectangle($wr_img,0,0,$new_w,$new_h,imagecolorallocatealpha($wr_img,0, 0, 0, 127));
					imagecopyresized($wr_img,$wat_img,0,0,0,0,$new_w,$new_h,ImageSX($wat_img),ImageSY($wat_img));
					imagecopymerge($dst_img,$wr_img,0,0,0,0,$new_w,$new_h,$this->parameters['transparence']);

				}
			}
			imagepng($dst_img);

		}
    }
     
    public function getTabParam(){
    	if(!isset($this->parameters['size_x'])) $this->parameters['size_x'] = '';
    	if(!isset($this->parameters['size_y'])) $this->parameters['size_y'] = '';
    	if(!isset($this->parameters['watermark'])) $this->parameters['watermark'] = '';
    	if(!isset($this->parameters['transparence'])) $this->parameters['transparence'] = '';
		$this->tabParam = array(
			"size_x"=>array("type"=>"text","name"=>"size_x","value"=>$this->parameters['size_x'],"desc"=>"Largeur maximale de l'image"),
			"size_y"=>array("type"=>"text","name"=>"size_y","value"=>$this->parameters['size_y'],"desc"=>"Hauteur maximale de l'image"),
			"watermark"=>array("type"=>"text","name"=>"watermark","value"=>$this->parameters['watermark'],"desc"=>"Watermark &agrave; ajouter sur les photos, si vide, pas de watermark"),
			"transparence"=>array("type"=>"text","name"=>"transparence","value"=>$this->parameters['transparence'],"desc"=>"Transparence du watermark de 0 &agrave; 100 en %"),
		);
       	return $this->tabParam;
    }
    
	public function getParamsPerso(){
		$params = $this->driver->getClassParam('images');
		$this->unserializeParams($params);
		if($this->parameters['size_x'] == 0) $this->parameters['size_x'] = $this->driver->getParam("maxX");
		if($this->parameters['size_y'] == 0) $this->parameters['size_y'] = $this->driver->getParam("maxY");
		if($this->parameters['transparence'] == 0) $this->parameters['transparence'] = 10;
	}
	
	public function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		return $this->parameters;
	}
	
	public function serializeParams($paramsToSerialized){
		$this->parameters =$paramsToSerialized;
		return addslashes(serialize($paramsToSerialized));
	}
}
?>
