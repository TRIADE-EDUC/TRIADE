<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_logo.class.php,v 1.3.8.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class docwatch_logo {
	public $id;		// identifiant de l'objet
	public $type;	// type d'objet
	public $data;	// donnée binaire du logo

	public function __construct($id="",$type="watch"){
		$this->id= intval($id);
		$this->type = $type;
		if($this->id){
			$this->fetch_data();
		}
	}

	protected function fetch_data(){
		$table=$this->get_sql_table();
		if(!$table) return false;
		$rqt = "select ".$this->type."_logo from ".$table." where id_".$this->type." = '".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$this->data = pmb_mysql_result($res,0,0);
		}
	}

	protected function get_sql_table(){
		switch ($this->type){
			case "watch" :
				$table = "docwatch_watches";
				break;
			default :
				$table ="";
				break;
		}
		return $table;
	}

	public function show_picture($mode=""){
   		header("Content-Type: image/png");
  		if(strpos($mode,"custom_") !== false){
	  		$elems = explode("_",$mode);
	  		$size = intval($elems[1]);
	  		if($size>0){
	  			$this->resize($size,$size);
	  		}else{
	  			$this->resize(500,500);
	  		}
	  	}else{
			switch($mode){
				case "small_vign" :
					$this->resize(16,16);
					break;
				case "vign" :
					$this->resize(100,100);
					break;
				case "small" :
					$this->resize(140,140);
					break;
				case "medium" :
					$this->resize(300,300);
					break;
				case "big" :
					$this->resize(600,600);
					break;
				case "large" :
				default :
					$this->resize(0,0);
					break;
	  		}
  		}
	}

	public function get_vign(){
	   $this->resize(100,100);
	}

	public function get_small_vign(){
	   $this->resize(16,16);
	}

	public function get_large(){
		$this->resize(0,0);
	}

	protected function resize($size_x=0,$size_y=0){
		if($this->data){
			$src_img = imagecreatefromstring($this->data);
			$maxX=$size_x;
			$maxY=$size_y;

			if(!$size_x && !$size_y){
				ImageSaveAlpha($src_img, true);
				ImageAlphaBlending($src_img, false);
				imagepng($src_img);
			}else if ($src_img) {
				$rs=$maxX/$maxY;
				$taillex=imagesx($src_img);
				$tailley=imagesy($src_img);
				if (!$taillex || !$tailley) return "" ;
				if (($taillex>$maxX)||($tailley>$maxY)) {
					$r=$taillex/$tailley;
					if (($r<1)&&($rs<1)) {
						//Si x plus petit que y et taille finale portrait
						//Si le format final est plus large en proportion
						if ($rs>$r) {
							$new_h=$maxY;
							$new_w=$new_h*$r;
						} else {
							$new_w=$maxX;
							$new_h=$new_w/$r;
						}
					} else if (($r<1)&&($rs>=1)){
						//Si x plus petit que y et taille finale paysage
						$new_h=$maxY;
						$new_w=$new_h*$r;
					} else if (($r>1)&&($rs<1)) {
						//Si x plus grand que y et taille finale portrait
						$new_w=$maxX;
						$new_h=$new_w/$r;
					} else {
						//Si x plus grand que y et taille finale paysage
						if ($rs<$r) {
							$new_w=$maxX;
							$new_h=$new_w/$r;
						} else {
							$new_h=$maxY;
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
				imagefilledrectangle($dst_img,0,0,$maxX,$maxY,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
				imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
				imagepng($dst_img);
			}
		}else{
			print file_get_contents(get_url_icon("vide.png"));
		}
	}

	public function get_vign_url($mode=""){
		global $opac_url_base;
		return $opac_url_base."docwatch_vign.php?type=".$this->type."&id=".$this->id."&mode=".$mode;
	}

	public function format_datas(){
		return array(
			'small_vign' => $this->get_vign_url("small_vign"),
			'vign' =>		$this->get_vign_url("vign"),
			'small' =>		$this->get_vign_url("small"),
			'medium' =>		$this->get_vign_url("medium"),
			'big' =>		$this->get_vign_url("big"),
			'large' =>		$this->get_vign_url("large"),
			'custom' =>		$this->get_vign_url("custom_"),
			'exists' =>		($this->data ? true : false)
		);
	}

}