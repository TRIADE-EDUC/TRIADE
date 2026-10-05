<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sur_location.class.php,v 1.1 2023/12/13 09:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class sur_location {
	public $id;
    public $libelle;
    public $pic;
    public $visible_opac;
    public $name;
    public $adr1;
    public $adr2;
    public $cp;
    public $town;
    public $state;
    public $country;
    public $phone;
    public $email;
    public $website;
    public $logo;
    public $comment;
    public $num_infopage;
    public $css_style;
    public $docs_location_data;
    
	// constructeur
	public function __construct($id=0) {	
		// si id, allez chercher les infos dans la base
	    $this->id = intval($id);
		$this->fetch_data();
	}
	    
	// récupération des infos en base
	public function fetch_data() {
		$this->docs_location_data=array();
		if($this->id){
			$requete="SELECT * FROM sur_location WHERE surloc_id='".$this->id."' LIMIT 1";
			$res = pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete");
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
			}	
			$this->libelle=$row->surloc_libelle;
			$this->pic=$row->surloc_pic; 
			$this->visible_opac=$row->surloc_visible_opac; 
			$this->name=$row->surloc_name; 
			$this->adr1=$row->surloc_adr1; 
			$this->adr2=$row->surloc_adr2; 
			$this->cp=$row->surloc_cp; 
			$this->town=$row->surloc_town; 
			$this->state=$row->surloc_state; 
			$this->country=$row->surloc_country; 
			$this->phone=$row->surloc_phone; 
			$this->email=$row->surloc_email; 
			$this->website=$row->surloc_website; 
			$this->logo=$row->surloc_logo; 
			$this->comment=$row->surloc_comment; 
			$this->num_infopage=$row->surloc_num_infopage; 
			$this->css_style=$row->surloc_css_style;	
		
			$requete = "SELECT * FROM docs_location where surloc_num='".$this->id."' or surloc_num=0 ORDER BY location_libelle";		
		}else{ 
			$requete = "SELECT * FROM docs_location where surloc_num=0 ORDER BY location_libelle";		
		}		
		$myQuery = pmb_mysql_query($requete);					
		while(($r=pmb_mysql_fetch_assoc($myQuery))) {	
			$this->docs_location_data[]=$r;
		}
	}
}
