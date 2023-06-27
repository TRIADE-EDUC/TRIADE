<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss_flux.class.php,v 1.16 2019-02-12 08:28:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// definition de la classe de gestion des 'flux RSS'
class rss_flux {

// ---------------------------------------------------------------
//		proprietes de la classe
// ---------------------------------------------------------------
	public $id_rss_flux = 0;	
	public $nom_rss_flux = ""; 
	public $link_rss_flux = "" ;
	public $descr_rss_flux = "" ;
	public $lang_rss_flux = "" ;
	public $copy_rss_flux = "" ;
	public $editor_rss_flux = "" ;
	public $webmaster_rss_flux = "" ;
	public $ttl_rss_flux = 0 ;
	public $img_url_rss_flux = "" ;
	public $img_title_rss_flux = "" ;
	public $img_link_rss_flux = "" ;

	public $format_flux = "";
	public $export_court_flux = 0;
	public $tpl_rss_flux = 0;
	
	public $nb_paniers = 0;
	public $nb_bannettes = 0;
	public $num_paniers = array();
	public $num_bannettes = array();
	public $notices = "";
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id_rss_flux = $id+0;
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : recuperation infos
	// ---------------------------------------------------------------
	public function getData() {
		if (!$this->id_rss_flux) {
			// pas d'identifiant. on retourne un tableau vide
		 	$this->id_rss_flux=0;
		 	$this->nom_rss_flux = "" ;
			$this->link_rss_flux = "" ;
			$this->descr_rss_flux = "" ;
			$this->lang_rss_flux = "" ;
			$this->copy_rss_flux = "" ;
			$this->editor_rss_flux = "" ;
			$this->webmaster_rss_flux = "" ;
			$this->ttl_rss_flux = 0 ;
			$this->img_url_rss_flux = "" ;
			$this->img_title_rss_flux = "" ;
			$this->img_link_rss_flux = "" ;
			$this->format_flux = "";
			$this->export_court_flux = 0;
			$this->tpl_rss_flux = 0;
			$this->compte_elements();
		} else {
			$requete = "SELECT id_rss_flux, nom_rss_flux, link_rss_flux, descr_rss_flux, lang_rss_flux, copy_rss_flux, editor_rss_flux, webmaster_rss_flux, ttl_rss_flux, img_url_rss_flux, img_title_rss_flux, img_link_rss_flux, format_flux, export_court_flux,tpl_rss_flux ";
			$requete .= "FROM rss_flux WHERE id_rss_flux='".$this->id_rss_flux."' " ;
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
			 	$this->id_rss_flux			= $temp->id_rss_flux ;
				$this->nom_rss_flux			= $temp->nom_rss_flux ;
				$this->link_rss_flux 		= $temp->link_rss_flux ;     
				$this->descr_rss_flux 		= $temp->descr_rss_flux ;    
				$this->lang_rss_flux 		= $temp->lang_rss_flux ;     
				$this->copy_rss_flux 		= $temp->copy_rss_flux ;     
				$this->editor_rss_flux 		= $temp->editor_rss_flux ;   
				$this->webmaster_rss_flux 	= $temp->webmaster_rss_flux ;
				$this->ttl_rss_flux 		= $temp->ttl_rss_flux ;      
				$this->img_url_rss_flux 	= $temp->img_url_rss_flux ;  
				$this->img_title_rss_flux 	= $temp->img_title_rss_flux ;
				$this->img_link_rss_flux 	= $temp->img_link_rss_flux ; 
				$this->format_flux			= $temp->format_flux ;
				$this->export_court_flux	= $temp->export_court_flux;
				$this->tpl_rss_flux	        = $temp->tpl_rss_flux;
				$this->compte_elements();
			} else {
				// pas de flux avec cette cle
			 	$this->id_rss_flux=0;
			 	$this->nom_rss_flux = "" ;
				$this->link_rss_flux = "" ;
				$this->descr_rss_flux = "" ;
				$this->lang_rss_flux = "" ;
				$this->copy_rss_flux = "" ;
				$this->editor_rss_flux = "" ;
				$this->webmaster_rss_flux = "" ;
				$this->ttl_rss_flux = 0 ;
				$this->img_url_rss_flux = "" ;
				$this->img_title_rss_flux = "" ;
				$this->img_link_rss_flux = "" ;
				$this->format_flux="";
				$this->export_court_flux = 0;
				$this->tpl_rss_flux = 0;
				$this->compte_elements();
			}
		}
	}

	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form() {
	
		global $msg, $charset;
		global $dsi_flux_form;
		global $PMBuserid;
	
		if($this->id_rss_flux) {
			$action = "./dsi.php?categ=fluxrss&sub=&id_rss_flux=$this->id_rss_flux&suite=update";
			$button_delete = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">";
			$libelle = $msg['dsi_flux_form_modif'];
		} else {
			$action = "./dsi.php?categ=fluxrss&sub=&id_rss_flux=0&suite=update";
			$libelle = $msg['dsi_flux_form_creat'];
			$button_delete ='';
		}
		
		$sel_notice_tpl=notice_tpl_gen::gen_tpl_select("notice_tpl",$this->tpl_rss_flux);
	
		$sel_default_format="<select name='format_flux'>";
		if(!$this->format_flux){
			$sel_default_format.="<option selected value='0'>$msg[dsi_flux_form_format_flux_default_empty]</option>";
		}else{
			$sel_default_format.="<option value='0'>$msg[dsi_flux_form_format_flux_default_empty]</option>";
		}
		if($this->format_flux=='TITLE'){
			$sel_default_format.="<option selected value='TITLE'>$msg[dsi_flux_form_format_flux_default_title]</option>";
		}else{
			$sel_default_format.="<option value='TITLE'>$msg[dsi_flux_form_format_flux_default_title]</option>";
		}
		if($this->format_flux=='ISBD'){
			$sel_default_format.="<option selected value='ISBD'>$msg[dsi_flux_form_format_flux_default_isbd]</option>";
		}else{
			$sel_default_format.="<option value='ISBD'>$msg[dsi_flux_form_format_flux_default_isbd]</option>";
		}
		if($this->format_flux=='ABSTRACT'){
			$sel_default_format.="<option selected value='ABSTRACT'>$msg[dsi_flux_form_format_flux_default_abstract]</option>";
		}else{
			$sel_default_format.="<option value='ABSTRACT'>$msg[dsi_flux_form_format_flux_default_abstract]</option>";
		}
		$sel_default_format.="</select>";
		
		$dsi_flux_form = str_replace('!!libelle!!', $libelle, $dsi_flux_form);
		$dsi_flux_form = str_replace('!!id_rss_flux!!', $this->id_rss_flux, $dsi_flux_form);
		$dsi_flux_form = str_replace('!!action!!', $action, $dsi_flux_form);
		$dsi_flux_form = str_replace('!!nom_rss_flux!!'			, htmlentities($this->nom_rss_flux			,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!link_rss_flux!!'		, htmlentities($this->link_rss_flux     	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!descr_rss_flux!!'		, htmlentities($this->descr_rss_flux    	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!lang_rss_flux!!'		, htmlentities($this->lang_rss_flux     	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!copy_rss_flux!!'		, htmlentities($this->copy_rss_flux     	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!editor_rss_flux!!'		, htmlentities($this->editor_rss_flux   	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!webmaster_rss_flux!!'	, htmlentities($this->webmaster_rss_flux	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!ttl_rss_flux!!'			, htmlentities($this->ttl_rss_flux      	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!img_url_rss_flux!!'		, htmlentities($this->img_url_rss_flux  	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!img_title_rss_flux!!'	, htmlentities($this->img_title_rss_flux	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!img_link_rss_flux!!'	, htmlentities($this->img_link_rss_flux 	,ENT_QUOTES, $charset), $dsi_flux_form);
		$dsi_flux_form = str_replace('!!format_flux_default!!'	, $sel_default_format, $dsi_flux_form);
		$dsi_flux_form = str_replace('!!sel_notice_tpl!!'		, $sel_notice_tpl, $dsi_flux_form);
		

		
		if($this->export_court_flux){
			$dsi_flux_form = str_replace('!!export_court!!'			, 'checked' , $dsi_flux_form);
			$dsi_flux_form = str_replace('!!tpl_rss_flux!!'			, '' , $dsi_flux_form);
		}else{
			$dsi_flux_form = str_replace('!!tpl_rss_flux!!'			, 'checked' , $dsi_flux_form);
			$dsi_flux_form = str_replace('!!export_court!!'			, '' , $dsi_flux_form);
		}
		
		$rqt="select idcaddie as id_obj, name as name_obj from caddie where type='NOTI' ";
		if ($PMBuserid!=1) $rqt.=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		$rqt.=" order by name ";
		
		$result = pmb_mysql_query($rqt);
		$paniers = "";
		while (($contenant = pmb_mysql_fetch_object($result))) {
			if (array_search($contenant->id_obj,$this->num_paniers)!==false) $checked="checked" ; 
				else $checked="" ;
			$paniers .= "<div class='usercheckbox'>
							<input  type='checkbox' id='paniers[".$contenant->id_obj."]' name='paniers[]' ".$checked." value='".$contenant->id_obj."' />
							<label for='paniers[".$contenant->id_obj."]' >".htmlentities($contenant->name_obj,ENT_QUOTES, $charset)."</label>
						</div>";	
		}
		$dsi_flux_form = str_replace('!!paniers!!', $paniers,  $dsi_flux_form);
		
		$rqt="select id_bannette as id_obj, nom_bannette as name_obj from bannettes where proprio_bannette=0 order by nom_bannette ";
		$result = pmb_mysql_query($rqt);
		$bannettes = "";
		while (($contenant = pmb_mysql_fetch_object($result))) {
			if (array_search($contenant->id_obj,$this->num_bannettes)!==false) $checked="checked" ; 
				else $checked="" ;
			$bannettes .= "<div class='usercheckbox'>
							<input  type='checkbox' id='bannettes[".$contenant->id_obj."]' name='bannettes[]' ".$checked." value='".$contenant->id_obj."' />
							<label for='bannettes[".$contenant->id_obj."]' >".htmlentities($contenant->name_obj,ENT_QUOTES, $charset)."</label>
							</div>";	
		}
		$dsi_flux_form = str_replace('!!bannettes!!', $bannettes,  $dsi_flux_form);
		
		$dsi_flux_form = str_replace('!!delete!!', $button_delete,  $dsi_flux_form);
	
		// afin de revenir ou on etait : $form_cb, le critere de recherche
		global $form_cb ;
		$dsi_flux_form = str_replace('!!form_cb!!', $form_cb,  $dsi_flux_form);
		print $dsi_flux_form;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression 
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;
		
		if (!$this->id_rss_flux) return $msg['dsi_flux_no_access']; // impossible d'acceder 
	
		$requete = "delete from rss_flux_content WHERE num_rss_flux='$this->id_rss_flux'";
		pmb_mysql_query($requete);
	
		$requete = "delete from rss_flux WHERE id_rss_flux='$this->id_rss_flux'";
		pmb_mysql_query($requete);
	}
	
	
	public function set_properties_from_form() {
		global $nom_rss_flux, $link_rss_flux, $descr_rss_flux;
		global $lang_rss_flux, $copy_rss_flux, $editor_rss_flux, $webmaster_rss_flux, $ttl_rss_flux;
		global $img_url_rss_flux, $img_title_rss_flux, $img_link_rss_flux;
		global $type_export, $notice_tpl, $format_flux;
		global $paniers, $bannettes;
		
		$this->nom_rss_flux = stripslashes($nom_rss_flux);
		$this->link_rss_flux = stripslashes($link_rss_flux);
		$this->descr_rss_flux = stripslashes($descr_rss_flux);
		$this->lang_rss_flux = stripslashes($lang_rss_flux);
		$this->copy_rss_flux = stripslashes($copy_rss_flux);
		$this->editor_rss_flux = stripslashes($editor_rss_flux);
		$this->webmaster_rss_flux = stripslashes($webmaster_rss_flux);
		$this->ttl_rss_flux = stripslashes($ttl_rss_flux);
		$this->img_url_rss_flux = stripslashes($img_url_rss_flux);
		$this->img_title_rss_flux = stripslashes($img_title_rss_flux);
		$this->img_link_rss_flux = stripslashes($img_link_rss_flux);
		switch ($type_export){
			case 'tpl':
				$this->export_court_flux="0";
				$this->tpl_rss_flux	= $notice_tpl;
				if($notice_tpl==0){
					$this->format_flux=$format_flux;
				}else{
					$this->format_flux="";
				}
				break;
			case 'export_court':
				$this->export_court_flux="1";
				$this->tpl_rss_flux	="0";
				$this->format_flux="";
				break;
			default:
				$this->format_flux=$format_flux ;
				break;
		}
		if (empty($paniers)) $paniers = array();
		if (empty($bannettes)) $bannettes = array();
		$this->num_paniers = $paniers;
		$this->num_bannettes = $bannettes;
	}
	
	// ---------------------------------------------------------------
	//		update 
	// ---------------------------------------------------------------
	public function update() {
		if ($this->id_rss_flux) {
			// update
			$req = "UPDATE rss_flux set ";
			$clause = " WHERE id_rss_flux='".$this->id_rss_flux."' ";
		} else {
			$req = "insert into rss_flux set ";
			$clause = "";
		}
		$req .= "id_rss_flux       ='".$this->id_rss_flux        ."', " ;
		$req .= "nom_rss_flux      ='".addslashes($this->nom_rss_flux)       ."', " ;
		$req .= "link_rss_flux     ='".addslashes($this->link_rss_flux)      ."', " ;
		$req .= "descr_rss_flux    ='".addslashes($this->descr_rss_flux)     ."', " ;
		$req .= "lang_rss_flux     ='".addslashes($this->lang_rss_flux)      ."', " ;
		$req .= "copy_rss_flux     ='".addslashes($this->copy_rss_flux)      ."', " ;
		$req .= "editor_rss_flux   ='".addslashes($this->editor_rss_flux)    ."', " ;
		$req .= "webmaster_rss_flux='".addslashes($this->webmaster_rss_flux) ."', " ;
		$req .= "ttl_rss_flux      ='".addslashes($this->ttl_rss_flux)       ."', " ;
		$req .= "img_url_rss_flux  ='".addslashes($this->img_url_rss_flux)   ."', " ;
		$req .= "img_title_rss_flux='".addslashes($this->img_title_rss_flux) ."', " ;
		$req .= "img_link_rss_flux ='".addslashes($this->img_link_rss_flux)  ."', " ;
		$req .= "export_court_flux ='".addslashes($this->export_court_flux)  ."', " ;
		$req .= "tpl_rss_flux      ='".addslashes($this->tpl_rss_flux)       ."', " ;
		$req .= "format_flux       ='".addslashes($this->format_flux)        ."' " ;
	
		$req.=$clause ;
		$res = pmb_mysql_query($req);
		if (!$this->id_rss_flux) $this->id_rss_flux = pmb_mysql_insert_id() ;
		if (!$this->id_rss_flux);
		
		pmb_mysql_query("delete from rss_flux_content where num_rss_flux='$this->id_rss_flux' " ) ;
		for ($i=0;$i<count($this->num_paniers);$i++) {
			pmb_mysql_query("insert into rss_flux_content set num_rss_flux='$this->id_rss_flux', type_contenant='CAD', num_contenant='".$this->num_paniers[$i]."' " ) ;
		}
	
		for ($i=0;$i<count($this->num_bannettes);$i++) {
			pmb_mysql_query("insert into rss_flux_content set num_rss_flux='$this->id_rss_flux', type_contenant='BAN', num_contenant='".$this->num_bannettes[$i]."' " ) ;
		}
	}
	
	// ---------------------------------------------------------------
	//		compte_elements() : methode pour pouvoir recompter en dehors !
	// ---------------------------------------------------------------
	public function compte_elements() {
		$this->nb_paniers=0;
		$this->nb_bannettes=0;
		$this->num_paniers=array();
		$this->num_bannettes=array();
	
		$req_nb = "SELECT num_contenant from rss_flux_content WHERE num_rss_flux='".$this->id_rss_flux."' and type_contenant='CAD' " ;
		$res_nb = pmb_mysql_query($req_nb);
		while (($res = pmb_mysql_fetch_object($res_nb))) {
			$this->num_paniers[]=$res->num_contenant ;
			$this->nb_paniers++ ;
		}
		
		$req_nb = "SELECT num_contenant from rss_flux_content WHERE num_rss_flux='".$this->id_rss_flux."' and type_contenant='BAN' " ;
		$res_nb = pmb_mysql_query($req_nb);
		while (($res = pmb_mysql_fetch_object($res_nb))) {
			$this->num_bannettes[]=$res->num_contenant ;
			$this->nb_bannettes++ ;
		}
	}

} # fin de definition
