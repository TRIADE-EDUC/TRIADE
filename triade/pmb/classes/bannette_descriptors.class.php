<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_descriptors.class.php,v 1.2 2022/02/10 10:46:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/categories.class.php");

class bannette_descriptors{
	public $num_bannette;
	public $descriptors;
	
	public function __construct($num_bannette) {
		$this->num_bannette = intval($num_bannette);		
		$this->fetch_data();
	}
	
	public function fetch_data() {		
		$this->descriptors=array();
		// les descripteurs...
		$rqt = "select num_noeud from bannettes_descriptors where num_bannette = '".$this->num_bannette."' order by bannette_descriptor_order";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->descriptors[] = $row->num_noeud;
			}
		}
	}
	
	public function get_form(){
		global $charset,$lang;
		global $dsi_desc_field;
		global $dsi_desc_first_desc,$dsi_desc_other_desc;
	
		$categs = "";
		if(count($this->descriptors)){
			for ($i=0 ; $i<count($this->descriptors) ; $i++){
				if($i==0) $categ=$dsi_desc_first_desc;
				else $categ = $dsi_desc_other_desc;
				//on y va
				$categ = str_replace('!!icateg!!', $i, $categ);
				$categ = str_replace('!!categ_id!!', $this->descriptors[$i], $categ);
				$categorie = new categories($this->descriptors[$i],$lang);
				$categ = str_replace('!!categ_libelle!!', htmlentities($categorie->libelle_categorie,ENT_QUOTES, $charset), $categ);
				$categs.=$categ;
			}
			$categs = str_replace("!!max_categ!!",count($this->descriptors),$categs);
		}else{
			$categs=$dsi_desc_first_desc;
			$categs = str_replace('!!icateg!!', 0, $categs) ;
			$categs = str_replace('!!categ_id!!', "", $categs);
			$categs = str_replace('!!categ_libelle!!', "", $categs);
			$categs = str_replace('!!max_categ!!', 1, $categs);
		}
		return str_replace("!!cms_categs!!",$categs,$dsi_desc_field);
	}
	
	public function set_properties_from_form() {
		global $max_categ;
		
		$this->descriptors=array();
		for ($i=0 ; $i<$max_categ ; $i++){
			$categ_id = 'f_categ_id'.$i;
			global ${$categ_id};
			if(${$categ_id} > 0){
				$this->descriptors[] = ${$categ_id};
			}
		}
	}
	
	public function save() {
		static::delete($this->num_bannette);
		for($i=0 ; $i<count($this->descriptors) ; $i++){
			$rqt = "insert into bannettes_descriptors set num_bannette = '".$this->num_bannette."', num_noeud = '".$this->descriptors[$i]."', bannette_descriptor_order='".$i."'";
			pmb_mysql_query($rqt);
		}
	}
	
	public static function delete($num_bannette=0) {
		$num_bannette = intval($num_bannette);
		$del = "delete from bannettes_descriptors where num_bannette = '".$num_bannette."'";
		pmb_mysql_query($del);
	}
		
}// end class
