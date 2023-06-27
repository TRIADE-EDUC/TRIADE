<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sur_location.class.php,v 1.10 2019-06-07 12:35:52 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des vues Opac

// inclusions principales
require_once("$include_path/templates/sur_location.tpl.php");
require_once($class_path."/map/map_edition_controler.class.php");


class sur_location {
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
		global $dbh;
		$this->docs_location_data=array();
		if($this->id){
			$requete="SELECT * FROM sur_location WHERE surloc_id='".$this->id."' LIMIT 1";
			$res = pmb_mysql_query($requete, $dbh) or die(pmb_mysql_error()."<br />$requete");
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
		$myQuery = pmb_mysql_query($requete, $dbh);					
		while(($r=pmb_mysql_fetch_assoc($myQuery))) {	
			$this->docs_location_data[]=$r;
		}
				
		$this->get_list();
	}
		
	public static function get_info_surloc_from_location($id_docs_location=0){	
		global $dbh;
		$id_docs_location = intval($id_docs_location);
		if($id_docs_location){
			$requete = "SELECT * FROM docs_location where idlocation='$id_docs_location'";
			$res = pmb_mysql_query($requete, $dbh) or die(pmb_mysql_error()."<br />$requete");
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				if($row->surloc_num){
					$sur_loc= new sur_location($row->surloc_num);
					return $sur_loc;
				}		
			}
		}
		return $sur_loc= new sur_location();	
	}
	
	// fonction générant le tableau de la liste de sur-loc 
	public function do_list() {
		global $tpl_sur_location_tableau,$tpl_sur_location_tableau_ligne;	
		
		$liste="";
		for($i=0;$i<count($this->sur_location_list);$i++) {
			if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";
	        $td_javascript="  onmousedown=\"document.location='./admin.php?categ=docs&sub=sur_location&action=add&id=!!surloc_id!!'\" ";
	        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
			if($this->sur_location_list[$i]->visible_opac) $visible="X" ; else $visible="&nbsp;" ;
	
	        $line = str_replace('!!td_javascript!!',$td_javascript , $tpl_sur_location_tableau_ligne);
	        $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	        $line = str_replace('!!pair_impair!!',$pair_impair , $line);
	        
			$line =str_replace('!!visible_opac!!', $visible, $line);
			$line =str_replace('!!surloc_id!!', $this->sur_location_list[$i]->id, $line);
			$line = str_replace('!!name!!', $this->sur_location_list[$i]->libelle, $line);
			$line = str_replace('!!comment!!', $this->sur_location_list[$i]->comment, $line);	
						
			$liste.=$line;
		}
		return $tpl = str_replace('!!lignes_tableau!!',$liste , $tpl_sur_location_tableau);
	}
	
	// fonction récupérant les infos pour la liste de sur-loc 
	public function get_list($name='form_sur_localisation', $value_selected=0,$no_sel=0) {
		global $dbh, $msg, $charset;	
		
		$this->sur_location_list=array();
		$selector = "<select name='$name' id='$name'>";
		if($no_sel) {		
			$selector .= "<option value='0'";
			!$value_selected ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
	 		$selector .= htmlentities($msg["sur_location_aucune"],ENT_QUOTES, $charset).'</option>';
		}
		$myQuery = pmb_mysql_query("SELECT * FROM sur_location order by surloc_libelle ", $dbh);
		if(pmb_mysql_num_rows($myQuery)){
			$i=0;
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->sur_location_list[$i]=new stdClass();
				$this->sur_location_list[$i]->id=$r->surloc_id;
				$this->sur_location_list[$i]->libelle=$r->surloc_libelle;
				$this->sur_location_list[$i]->comment=$r->surloc_comment;
				$this->sur_location_list[$i]->visible_opac=$r->surloc_visible_opac;
				
				$selector .= "<option value='".$r->surloc_id."'";
				$r->surloc_id == $value_selected ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
				$selector .= htmlentities($r->surloc_libelle,ENT_QUOTES, $charset).'</option>';
				
				$i++;			
			}	
		}
		$selector .= '</select>';   
		$this->selector=$selector;	
		return $selector;	
	}
	
	// fonction de mise à jour ou de création 
	public function update() {	
		global $dbh,$msg;
	    global $form_libelle,$form_location_pic,$form_location_visible_opac,$form_locdoc_name,$form_locdoc_adr1,$form_locdoc_adr2,
		$form_locdoc_cp,$form_locdoc_town,$form_locdoc_state,$form_locdoc_country,$form_locdoc_phone,$form_locdoc_email,
		$form_locdoc_website,$form_locdoc_logo,$form_locdoc_commentaire,$form_num_infopage,$form_css_style,$pmb_map_activate;
		
		$set_values = "SET 
			surloc_libelle='$form_libelle', 
			surloc_pic='$form_location_pic', 
			surloc_visible_opac='$form_location_visible_opac', 
			surloc_name= '$form_locdoc_name', 
			surloc_adr1= '$form_locdoc_adr1', 
			surloc_adr2= '$form_locdoc_adr2', 
			surloc_cp= '$form_locdoc_cp', 
			surloc_town= '$form_locdoc_town', 
			surloc_state= '$form_locdoc_state', 
			surloc_country= '$form_locdoc_country',
			surloc_phone= '$form_locdoc_phone', 
			surloc_email= '$form_locdoc_email', 
			surloc_website= '$form_locdoc_website', 
			surloc_logo= '$form_locdoc_logo', 
			surloc_comment='$form_locdoc_commentaire', 
			surloc_num_infopage='$form_num_infopage', 
			surloc_css_style='$form_css_style' " ;
		if($this->id) {
			$requete = "UPDATE sur_location $set_values WHERE surloc_id='$this->id' ";
			$res = pmb_mysql_query($requete, $dbh);
		} else {
			$requete = "INSERT INTO sur_location $set_values ";
			$res = pmb_mysql_query($requete, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		}
	
		// map
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(TYPE_SUR_LOCATION,$this->id);
			$map_form=$map_edition->save_form();
		}
		
		$requete = "UPDATE docs_location SET surloc_num='0' WHERE surloc_num='$this->id' ";
		$res = pmb_mysql_query($requete, $dbh);
		
		// mémo des localisations associées
		foreach($this->docs_location_data as $docs_loc){
			$selected=0;
			eval("
			global \$form_location_selected_".$docs_loc["idlocation"].";
			\$selected =\$form_location_selected_".$docs_loc["idlocation"].";
			");
			if($selected){
				$requete = "UPDATE docs_location SET surloc_num='$this->id' WHERE idlocation=".$docs_loc["idlocation"];
				$res = pmb_mysql_query($requete, $dbh);
			}	
		}	
		// rafraischissement des données
		$this->fetch_data();
	}
	
	
		
	// fonction générant le form de saisie 
	public function do_form() {
		global $msg;	
		global $tpl_sur_location_form,$tpl_docs_loc_table_line;
		global $charset;
		global $pmb_map_activate;
		
		$tpl=$tpl_sur_location_form;
		$tpl = str_replace('!!id!!', $this->id, $tpl);
	
		if($this->id) $tpl = str_replace('!!form_title!!', $msg["sur_location_modifier_title"], $tpl);
		else $tpl = str_replace('!!form_title!!', $msg["sur_location_ajouter_title"], $tpl);
	
		$tpl = str_replace('!!libelle!!', htmlentities($this->libelle,ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!libelle_suppr!!', htmlentities(addslashes($this->libelle),ENT_QUOTES, $charset), $tpl);
	
		$tpl = str_replace('!!location_pic!!', htmlentities($this->pic,ENT_QUOTES, $charset), $tpl);
	
		if($this->visible_opac) $checkbox="checked"; else $checkbox="";
		$tpl = str_replace('!!checkbox!!', $checkbox, $tpl);
		$lines="";
		$pair="odd";
		foreach($this->docs_location_data as $docs_loc){
			$line=$tpl_docs_loc_table_line;
			if($pair!="odd")$pair="odd"; else $pair="even";		
			$style = "cursor: pointer;";	
			if($docs_loc["surloc_num"]==$this->id) $checked = " checked='checked' ";else $checked="";
			if($docs_loc["location_visible_opac"]) $visible="X" ; else $visible="&nbsp;" ;
			
			$line=str_replace('!!docs_loc_visible_opac!!', $visible, $line);	
			$line=str_replace('!!odd_even!!', $pair, $line);	
			$line = str_replace('!!docs_loc_id!!', 	$docs_loc["idlocation"]  , $line);
			$line = str_replace('!!checkbox!!', 	$checked  , $line);
			$line = str_replace('!!docs_loc_libelle!!', 	htmlentities($docs_loc["location_libelle"],ENT_QUOTES, $charset)     , $line);
			$line = str_replace('!!docs_loc_comment!!', 	htmlentities($docs_loc["commentaire"],ENT_QUOTES, $charset)     , $line);
			
			$lines.=$line;
		}
		$tpl = str_replace('!!docs_loc_lines!!', 	$lines  , $tpl);
		
		// map
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(TYPE_SUR_LOCATION,$this->id);
			$map_form=$map_edition->get_form();
			$tpl = str_replace('!!sur_location_map!!', $map_form, $tpl);
		
		} else {
			$tpl = str_replace('!!sur_location_map!!', "", $tpl);
		}
		
		$tpl = str_replace('!!loc_name!!', 	htmlentities($this->name,ENT_QUOTES, $charset)     , $tpl);
		$tpl = str_replace('!!loc_adr1!!', 	htmlentities($this->adr1,ENT_QUOTES, $charset)     , $tpl);
		$tpl = str_replace('!!loc_adr2!!', 	htmlentities($this->adr2,ENT_QUOTES, $charset)     , $tpl);
		$tpl = str_replace('!!loc_cp!!', 	$this->cp       , $tpl);
		$tpl = str_replace('!!loc_town!!', 	htmlentities($this->town,ENT_QUOTES, $charset)     , $tpl);
		$tpl = str_replace('!!loc_state!!', htmlentities($this->state,ENT_QUOTES, $charset)    , $tpl);
		$tpl = str_replace('!!loc_country!!',htmlentities($this->country,ENT_QUOTES, $charset)  , $tpl);
		$tpl = str_replace('!!loc_phone!!', $this->phone    , $tpl);
		$tpl = str_replace('!!loc_email!!', $this->email    , $tpl);
		$tpl = str_replace('!!loc_website!!',$this->website  , $tpl);
		$tpl = str_replace('!!loc_logo!!', 	$this->logo     , $tpl);
		$tpl = str_replace('!!loc_commentaire!!', htmlentities($this->comment,ENT_QUOTES, $charset), $tpl);
	
		$requete = "SELECT id_infopage, title_infopage FROM infopages where valid_infopage=1 ORDER BY title_infopage ";
		$infopages = gen_liste ($requete, "id_infopage", "title_infopage", "form_num_infopage", "", $this->num_infopage, 0, $msg['location_no_infopage'], 0,$msg['location_no_infopage'], 0) ;
		$tpl = str_replace('!!loc_infopage!!', $infopages, $tpl);	
		$tpl = str_replace('!!css_style!!', $this->css_style, $tpl);
			
		return confirmation_delete("./admin.php?categ=docs&sub=sur_location&action=del&id=").$tpl;	
	}
	
	
	public function delete() {
		global $dbh;
		
		if($this->id) {
			$requete = "UPDATE docs_location SET surloc_num='0' WHERE surloc_num='$this->id' ";
			$res = pmb_mysql_query($requete, $dbh);
			pmb_mysql_query("DELETE from sur_location WHERE surloc_id='".$this->id."' ", $dbh);
		}
		$this->id=0;
		$this->get_list();
	}    
} // fin définition classe
