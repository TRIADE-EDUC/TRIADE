<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_info.class.php,v 1.8 2023/05/04 12:18:50 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/map/map_objects_controler.class.php");

class map_info {

	public $id;

	public $map;

	public $isbd;

	public $public;

	public function __construct($id) {
		$this->id=intval($id);
	   	$this->fetch_datas();
  	} // end of member function __construct

  	
  	public function get_isbd() {
  		return $this->isbd;
  	}
  	
  	public function get_public() {
  		return $this->public;
  	}
  	
  	public function get_data() {
  		return $this->map;
  	}
  	public function fetch_datas() {
  		global $msg;
  	
  		$this->map=array();
  		$this->isbd="";
  		$this->public="";
  		if(!$this->id) return;  		
  	
  		$req="select map_echelle_num, map_projection_num, map_ref_num, map_equinoxe
  				from notices where notice_id=".$this->id;  	
  		$res=pmb_mysql_query($req);
  		if (pmb_mysql_num_rows($res)) {
  			if($r=pmb_mysql_fetch_object($res)){
  				$this->map['echelle_num']=$r->map_echelle_num;
  				$this->map['projection_num']=$r->map_projection_num;
  				$this->map['ref_num']=$r->map_ref_num;
  				$this->map['equinoxe']=$r->map_equinoxe;
  					 
  				if($this->map['echelle_num']){
  					$req_echelle = "SELECT map_echelle_name FROM map_echelles where map_echelle_id =".$this->map['echelle_num'];
  					$res_echelle=pmb_mysql_query($req_echelle);
  					if (pmb_mysql_num_rows($res_echelle)) {
  						$r_echelle=pmb_mysql_fetch_object($res_echelle);
  						$this->map['echelle']=$r_echelle->map_echelle_name;
  						$this->isbd.=$this->map['echelle'];
  						$this->public.="<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg["map_notice_echelle"]."</span></td><td>".$this->map['echelle']."</td></tr>";
  					}
  				}
  				if($this->map['projection_num']){
  					$req_projection = "SELECT map_projection_name FROM map_projections where map_projection_id =".$this->map['projection_num'];
  					$res_projection=pmb_mysql_query($req_projection);
  					if (pmb_mysql_num_rows($res_projection)) {
  						$r_projection=pmb_mysql_fetch_object($res_projection);
  						$this->map['projection']=$r_projection->map_projection_name;
  						if($this->isbd) $this->isbd.=" ; ";
  						$this->isbd.=$this->map['projection'];  							
  						$this->public.="<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg["map_notice_projection"]."</span></td><td>".$this->map['projection']."</td></tr>";
  					}
  				}
  				if($this->map['ref_num']){
  					$req_ref = "SELECT map_ref_name FROM map_refs where map_ref_id =".$this->map['ref_num'];
  					$res_ref=pmb_mysql_query($req_ref);
  					if (pmb_mysql_num_rows($res_ref)) {
  						$r_ref=pmb_mysql_fetch_object($res_ref);
  						$this->map['ref']=$r_ref->map_ref_name;
  						if($this->isbd) $this->isbd.=". ";
  						$this->isbd.=$this->map['ref'];
  						$this->public.="<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg["map_notice_ref"]."</span></td><td>".$this->map['ref']."</td></tr>";
  					}
  				} 
  				$ids=array();
  				$ids[]=$this->id;
  				$map=new map_objects_controler(TYPE_RECORD,$ids);
				$bounding_box=$map->get_bounding_box();
				if($bounding_box){
  					if($this->isbd)$this->isbd.=" ";
  					$this->map['transcription']=$bounding_box->get_transcription();
  					$this->isbd.=$this->map['transcription'];
  					$this->public.="<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg["map_notice_transcription"]."</span></td><td>".$this->map['transcription']."</td></tr>";
				}
  				if($this->map['equinoxe']) $this->isbd.=" (".$this->map['equinoxe'].") ";
  				if($this->map['equinoxe'])$this->public.="<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg["map_notice_equinoxe"]."</span></td><td>".$this->map['equinoxe']."</td></tr>";
   				
  				if($this->isbd)$this->isbd=". - ".$this->isbd;
  			}
  		}
  	}
	
	public function get_form() {
		global $msg;
				
		$form_map = "
		        <div id='map_extended_fields'>
					<div class='row'>
						<label class='etiquette' for='f_map_echelle'>".$msg["map_echelle"]."</label>
					</div>
					<div class='row'>
						!!map_echelle_list!!
					</div>
					<div class='row'>
						<label class='etiquette' for='f_map_projection'>".$msg["map_projection"]."</label>
					</div>
					<div class='row'>
						!!map_projection_list!!
					</div>		
					<div class='row'>
						<label class='etiquette' for='f_map_ref'>".$msg["map_ref"]."</label>
					</div>
					<div class='row'>
						!!map_ref_list!!
					</div>				
					<div class='row'>
						<label class='etiquette' for='f_map_equinoxe'>".$msg["map_equinoxe"]."</label>
					</div>
					<div class='row'>
						<input id='f_map_equinoxe' class='saisie-80em' type='text' value='!!map_equinoxe_value!!' name='f_map_equinoxe'>
					</div>
				</div>
				<div class='row'>
					<input id='f_polygon_address_0' name='f_polygon_address_0' type='hidden'>
				</div>
				";
				
		$requete = "SELECT map_echelle_id, map_echelle_name FROM map_echelles ORDER BY map_echelle_name ";
	    $echelles = gen_liste($requete, "map_echelle_id", "map_echelle_name", "f_map_echelle", "", $this->map['echelle_num'], 0, $msg['map_echelle_vide'], 0, $msg['map_echelle_vide']);
	    $form_map = str_replace("!!map_echelle_list!!", $echelles, $form_map);
		
		$requete = "SELECT map_projection_id, map_projection_name FROM map_projections ORDER BY map_projection_name ";
	    $projections = gen_liste($requete, "map_projection_id", "map_projection_name", "f_map_projection", "", $this->map['projection_num'], 0, $msg['map_projection_vide'], 0, $msg['map_projection_vide']);
	    $form_map = str_replace("!!map_projection_list!!", $projections, $form_map);
		
		$requete = "SELECT map_ref_id, map_ref_name FROM map_refs ORDER BY map_ref_name ";
		$refs = gen_liste($requete, "map_ref_id", "map_ref_name", "f_map_ref", "", $this->map['ref_num'], 0, $msg['map_ref_vide'], 0, $msg['map_ref_vide']);
		$form_map = str_replace("!!map_ref_list!!", $refs, $form_map);
		
		$form_map = str_replace("!!map_equinoxe_value!!", $this->map['equinoxe'], $form_map);
		$form_map = str_replace("!!id!!", $this->id, $form_map);
		return $form_map;
	}
	
	public function save_form() {
		global $f_map_echelle;
		global $f_map_projection;
		global $f_map_ref;
		global $f_map_equinoxe;			
		
		
		$req = "update notices SET 
			map_echelle_num=$f_map_echelle, 
			map_projection_num=$f_map_projection,
			map_ref_num=$f_map_ref,
			map_equinoxe='$f_map_equinoxe' 
			where notice_id=".$this->id;	
			
		pmb_mysql_query($req);		
	   	$this->fetch_datas();
  		
	}
	 		

} // end of class