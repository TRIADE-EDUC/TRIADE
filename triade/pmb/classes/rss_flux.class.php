<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss_flux.class.php,v 1.23 2024/01/05 10:02:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/sort.class.php');
require_once($class_path.'/interface/interface_select.class.php');

// definition de la classe de gestion des 'flux RSS'
class rss_flux {

// ---------------------------------------------------------------
//		proprietes de la classe
// ---------------------------------------------------------------
	public $id_rss_flux = 0;	
	public $nom_rss_flux = ""; 
	public $link_rss_flux = "" ;
	public $descr_rss_flux = "" ;
	public $metadata_rss_flux = 1;
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
	public $tpl_title_rss_flux = "0";
	public $tpl_rss_flux = "0";
	public $tpl_link_rss_flux = "0";
	public $id_tri_rss_flux = 0;
	
	public $nb_paniers = 0;
	public $nb_bannettes = 0;
	public $num_paniers = array();
	public $num_bannettes = array();
	public $notices = "";
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id_rss_flux = intval($id);
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
			$this->metadata_rss_flux = 1 ;
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
			$this->tpl_title_rss_flux = "0";
			$this->tpl_rss_flux = "0";
			$this->tpl_link_rss_flux = "0";
			$this->id_tri_rss_flux = 0;
			$this->compte_elements();
		} else {
			$requete = "SELECT id_rss_flux, nom_rss_flux, link_rss_flux, descr_rss_flux, metadata_rss_flux, lang_rss_flux, copy_rss_flux, editor_rss_flux, webmaster_rss_flux, ttl_rss_flux, img_url_rss_flux, img_title_rss_flux, img_link_rss_flux, format_flux, export_court_flux, tpl_title_rss_flux, tpl_rss_flux, tpl_link_rss_flux, id_tri_rss_flux ";
			$requete .= "FROM rss_flux WHERE id_rss_flux='".$this->id_rss_flux."' " ;
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
			 	$this->id_rss_flux			= $temp->id_rss_flux ;
				$this->nom_rss_flux			= $temp->nom_rss_flux ;
				$this->link_rss_flux 		= $temp->link_rss_flux ;     
				$this->descr_rss_flux 		= $temp->descr_rss_flux ;
				$this->metadata_rss_flux 	= $temp->metadata_rss_flux ;
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
				$this->tpl_title_rss_flux	= $temp->tpl_title_rss_flux;
				$this->tpl_rss_flux	        = $temp->tpl_rss_flux;
				$this->tpl_link_rss_flux	= $temp->tpl_link_rss_flux;
				$this->id_tri_rss_flux      = $temp->id_tri_rss_flux;
				$this->compte_elements();
			} else {
				// pas de flux avec cette cle
			 	$this->id_rss_flux=0;
			 	$this->nom_rss_flux = "" ;
				$this->link_rss_flux = "" ;
				$this->descr_rss_flux = "" ;
				$this->metadata_rss_flux = 1 ;
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
				$this->tpl_title_rss_flux = "0";
				$this->tpl_rss_flux = "0";
				$this->tpl_link_rss_flux = "0";
				$this->id_tri_rss_flux = 0;
				$this->compte_elements();
			}
		}
	}

	public function get_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('nom_rss_flux', 'dsi_flux_form_nom')
	    ->set_class('colonne2')
	    ->add_input_node('text', $this->nom_rss_flux);
	    $interface_content_form->add_element('link_rss_flux', 'dsi_flux_form_link')
	    ->set_class('colonne_suite')
	    ->add_input_node('text', $this->link_rss_flux);
	    $interface_content_form->add_element('descr_rss_flux', 'dsi_flux_form_descr')
	    ->add_input_node('text', $this->descr_rss_flux)
	    ->set_class('saisie-80em');
	    $interface_content_form->add_element('metadata_rss_flux')
	    ->add_input_node('boolean', $this->metadata_rss_flux)
	    ->set_label_code('dsi_flux_form_metadata');
	    
	    $interface_content_form->add_element('lang_rss_flux', 'dsi_flux_form_lang')
	    ->set_class('colonne4')
	    ->add_input_node('text', $this->lang_rss_flux)
	    ->set_class('saisie-10em');
	    $interface_content_form->add_element('ttl_rss_flux', 'dsi_flux_form_ttl')
	    ->set_class('colonne4')
	    ->add_input_node('integer', $this->ttl_rss_flux);
	    $interface_content_form->add_element('copy_rss_flux', 'dsi_flux_form_copy')
	    ->set_class('colonne_suite')
	    ->add_input_node('text', $this->copy_rss_flux);
	    
	    $interface_content_form->add_element('editor_rss_flux', 'dsi_flux_form_editor')
	    ->set_class('colonne2')
	    ->add_input_node('text', $this->editor_rss_flux);
	    $interface_content_form->add_element('webmaster_rss_flux', 'dsi_flux_form_webmaster')
	    ->set_class('colonne_suite')
	    ->add_input_node('text', $this->webmaster_rss_flux);
	    
	    $interface_content_form->add_element('img_url_rss_flux', 'dsi_flux_form_img_url')
	    ->add_input_node('text', $this->img_url_rss_flux)
	    ->set_class('saisie-80em');
	    $interface_content_form->add_element('img_title_rss_flux', 'dsi_flux_form_img_title')
	    ->add_input_node('text', $this->img_title_rss_flux)
	    ->set_class('saisie-80em');
	    $interface_content_form->add_element('img_link_rss_flux', 'dsi_flux_form_img_link')
	    ->add_input_node('text', $this->img_link_rss_flux)
	    ->set_class('saisie-80em');
	    
	    $interface_content_form->add_zone('default', '', ['nom_rss_flux', 'link_rss_flux', 'descr_rss_flux', 'metadata_rss_flux']);
	    $interface_content_form->add_zone('origine', '', ['lang_rss_flux', 'ttl_rss_flux', 'copy_rss_flux']);
	    $interface_content_form->add_zone('meta', '', ['editor_rss_flux', 'webmaster_rss_flux']);
	    $interface_content_form->add_zone('properties', '', ['img_url_rss_flux', 'img_title_rss_flux', 'img_link_rss_flux']);
	    $interface_content_form->set_separator_zones("<div class='row'></div><div class='row'><hr /></div>");
	    return $interface_content_form->get_display();
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form() {
		global $msg, $charset;
		global $dsi_flux_content_form, $dsi_flux_js_script;
		global $PMBuserid;
	
		$content_form = $this->get_content_form();
		$content_form .= $dsi_flux_content_form;
		
		$interface_form = new interface_dsi_form('saisie_rss_flux');
		if(!$this->id_rss_flux){
			$interface_form->set_label($msg['dsi_flux_form_creat']);
		}else{
			$interface_form->set_label($msg['dsi_flux_form_modif']);
		}
		
		$options = notice_tpl::get_list();
		$directories = record_display::get_directories();
		foreach ($directories as $value => $label) {
		    $options[$value] = $label;
		}
		//Header de la notice
		$interface_select = new interface_select('notice_title_tpl');
		$interface_select->set_options($options)
		->set_selected($this->tpl_title_rss_flux);
		$sel_notice_title_tpl=$interface_select->get_display(0, $msg["notice_tpl_list_default"], 0, $msg["notice_tpl_list_default"]);
		
		//Description de la notice
		$interface_select = new interface_select('notice_tpl');
		$interface_select->set_options($options)
		  ->set_selected($this->tpl_rss_flux)
		  ->set_onchange('changeTemplateChoice();');
		$sel_notice_tpl=$interface_select->get_display(0, $msg["notice_tpl_list_default"], 0, $msg["notice_tpl_list_default"]);
		
		//Lien de la notice
		$interface_select = new interface_select('notice_link_tpl');
		$interface_select->set_options($options)
		->set_selected($this->tpl_link_rss_flux);
		$sel_notice_link_tpl=$interface_select->get_display(0, $msg["notice_tpl_list_default"], 0, $msg["notice_tpl_list_default"]);
		
		$sel_default_format="<select id='format_flux' name='format_flux'>";
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
		
		$content_form = str_replace('!!id_rss_flux!!', $this->id_rss_flux, $content_form);
		$content_form = str_replace('!!sel_notice_title_tpl!!', $sel_notice_title_tpl, $content_form);
		$content_form = str_replace('!!format_flux_default!!'	, $sel_default_format, $content_form);
		$content_form = str_replace('!!sel_notice_tpl!!'		, $sel_notice_tpl, $content_form);
		$content_form = str_replace('!!sel_notice_link_tpl!!'		, $sel_notice_link_tpl, $content_form);
		
		if($this->export_court_flux){
			$content_form = str_replace('!!export_court!!'			, 'checked' , $content_form);
			$content_form = str_replace('!!tpl_rss_flux!!'			, '' , $content_form);
		}else{
			$content_form = str_replace('!!tpl_rss_flux!!'			, 'checked' , $content_form);
			$content_form = str_replace('!!export_court!!'			, '' , $content_form);
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
		$content_form = str_replace('!!paniers!!', $paniers,  $content_form);
		
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
		$content_form = str_replace('!!bannettes!!', $bannettes,  $content_form);
		
		if($this->id_tri_rss_flux>0){
		    $sort = new sort("notices","base");
		    $content_form = str_replace('!!tri!!', $this->id_tri_rss_flux, $content_form);
		    $content_form = str_replace('!!tri_name!!', $sort->descriptionTriParId($this->id_tri_rss_flux), $content_form);
		}else{
		    $content_form = str_replace('!!tri!!', "", $content_form);
		    $content_form = str_replace('!!tri_name!!', $msg['dsi_flux_form_no_active_tri'], $content_form);
		}
		
		$interface_form->set_object_id($this->id_rss_flux)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($content_form)
		->set_table_name('rss_flux')
		->set_field_focus('nom_rss_flux');
		print $interface_form->get_display();
		print $dsi_flux_js_script;
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
	    global $nom_rss_flux, $link_rss_flux, $descr_rss_flux, $metadata_rss_flux;
		global $lang_rss_flux, $copy_rss_flux, $editor_rss_flux, $webmaster_rss_flux, $ttl_rss_flux;
		global $img_url_rss_flux, $img_title_rss_flux, $img_link_rss_flux;
		global $notice_title_tpl, $type_export, $notice_tpl, $notice_link_tpl, $format_flux;
		global $paniers, $bannettes;
		global $id_tri_rss_flux;
		
		$this->nom_rss_flux = stripslashes($nom_rss_flux);
		$this->link_rss_flux = stripslashes($link_rss_flux);
		$this->descr_rss_flux = stripslashes($descr_rss_flux);
		$this->metadata_rss_flux = intval($metadata_rss_flux);
		$this->lang_rss_flux = stripslashes($lang_rss_flux);
		$this->copy_rss_flux = stripslashes($copy_rss_flux);
		$this->editor_rss_flux = stripslashes($editor_rss_flux);
		$this->webmaster_rss_flux = stripslashes($webmaster_rss_flux);
		$this->ttl_rss_flux = stripslashes($ttl_rss_flux);
		$this->img_url_rss_flux = stripslashes($img_url_rss_flux);
		$this->img_title_rss_flux = stripslashes($img_title_rss_flux);
		$this->img_link_rss_flux = stripslashes($img_link_rss_flux);
		$this->tpl_title_rss_flux	= stripslashes($notice_title_tpl);
		$this->tpl_link_rss_flux	= stripslashes($notice_link_tpl);
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
		$this->id_tri_rss_flux = intval($id_tri_rss_flux);
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
		$req .= "metadata_rss_flux ='".addslashes($this->metadata_rss_flux)  ."', " ;
		$req .= "lang_rss_flux     ='".addslashes($this->lang_rss_flux)      ."', " ;
		$req .= "copy_rss_flux     ='".addslashes($this->copy_rss_flux)      ."', " ;
		$req .= "editor_rss_flux   ='".addslashes($this->editor_rss_flux)    ."', " ;
		$req .= "webmaster_rss_flux='".addslashes($this->webmaster_rss_flux) ."', " ;
		$req .= "ttl_rss_flux      ='".addslashes($this->ttl_rss_flux)       ."', " ;
		$req .= "img_url_rss_flux  ='".addslashes($this->img_url_rss_flux)   ."', " ;
		$req .= "img_title_rss_flux='".addslashes($this->img_title_rss_flux) ."', " ;
		$req .= "img_link_rss_flux ='".addslashes($this->img_link_rss_flux)  ."', " ;
		$req .= "export_court_flux ='".addslashes($this->export_court_flux)  ."', " ;
		$req .= "tpl_title_rss_flux='".addslashes($this->tpl_title_rss_flux)       ."', " ;
		$req .= "tpl_rss_flux      ='".addslashes($this->tpl_rss_flux)       ."', " ;
		$req .= "tpl_link_rss_flux='".addslashes($this->tpl_link_rss_flux)       ."', " ;
		$req .= "id_tri_rss_flux   ='".addslashes($this->id_tri_rss_flux)    ."', " ;
		$req .= "format_flux       ='".addslashes($this->format_flux)        ."' " ;
	
		$req.=$clause ;
		pmb_mysql_query($req);
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
		while ($res = pmb_mysql_fetch_object($res_nb)) {
			$this->num_bannettes[]=$res->num_contenant ;
			$this->nb_bannettes++ ;
		}
	}

} # fin de definition
