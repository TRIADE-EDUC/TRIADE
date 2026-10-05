<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docs_location.class.php,v 1.31.2.3 2024/10/01 08:24:24 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'docs_location'

if ( ! defined( 'DOCSLOCATION_CLASS' ) ) {
  define( 'DOCSLOCATION_CLASS', 1 );
	
class docs_location {
	
	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */
	
	public $id=0;
	public $libelle='';
	public $pret_flag='';
	public $locdoc_codage_import='';
	public $locdoc_owner=0;
	public $pic='';
	public $visible_opac=1;
	public $name='';
	public $adr1='';
	public $adr2='';
	public $cp='';
	public $town='';
	public $state='';
	public $country='';
	public $phone='';
	public $email='';
	public $website='';
	public $logo='';
	public $commentaire='';
	public $num_infopage=0;
	public $css_style='';
	public $surloc_num=0;
	public $surloc_used=0;
	
	/* ---------------------------------------------------------------
		docs_location($id) : constructeur
   --------------------------------------------------------------- */
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		/* récupération des informations du statut */
	
		$requete = 'SELECT * FROM docs_location WHERE idlocation='.$this->id.' LIMIT 1;';
		$result = pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->location_libelle;		
		$this->locdoc_codage_import = $data->locdoc_codage_import;
		$this->locdoc_owner = $data->locdoc_owner;
		$this->pic = $data->location_pic;
		$this->visible_opac = $data->location_visible_opac;
		$this->name = $data->name;
		$this->adr1 = $data->adr1;
		$this->adr2 = $data->adr2;
		$this->cp = $data->cp;
		$this->town = $data->town;
		$this->state = $data->state;
		$this->country = $data->country;
		$this->phone = $data->phone;
		$this->email = $data->email;
		$this->website = $data->website;
		$this->logo = $data->logo;
		$this->commentaire = $data->commentaire;
		$this->num_infopage = $data->num_infopage;
		$this->css_style = $data->css_style;
		$this->surloc_num = $data->surloc_num;
		$this->surloc_used = $data->surloc_used;
	}
	
	public function get_coords_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('form_locdoc_name', 'location_details_name')
	    ->add_input_node('text', $this->name)
	    ->set_attributes(array('data-translation-fieldname' => 'name'));
	    $interface_content_form->add_element('form_locdoc_adr1', 'location_details_adr1')
	    ->add_input_node('text', $this->adr1)
	    ->set_attributes(array('data-translation-fieldname' => 'adr1'));
	    $interface_content_form->add_element('form_locdoc_adr2', 'location_details_adr2')
	    ->add_input_node('text', $this->adr2)
	    ->set_attributes(array('data-translation-fieldname' => 'adr2'));
	    
	    //Code postal / Ville
	    $element_cp = $interface_content_form->add_element('form_locdoc_cp', 'location_details_cp');
	    $element_cp->add_input_node('integer', $this->cp)
	    ->set_maxlength(15);
	    $element_cp->set_class('row colonne4');
	    $element_town = $interface_content_form->add_element('form_locdoc_town', 'location_details_town');
	    $element_town->add_input_node('text', $this->town)
	    ->set_attributes(array('data-translation-fieldname' => 'town'));
	    $element_town->set_class('colonne_suite');
	    
	    //Etat ou région / Pays
	    $element_state = $interface_content_form->add_element('form_locdoc_state', 'location_details_state');
	    $element_state->add_input_node('text', $this->state)
	    ->set_class('saisie-20em');
	    $element_state->set_class('row colonne3');
	    $element_country = $interface_content_form->add_element('form_locdoc_country', 'location_details_country');
	    $element_country->add_input_node('text', $this->country)
	    ->set_class('saisie-20em');
	    $element_country->set_class('colonne_suite');
	    
	    $interface_content_form->add_element('form_locdoc_phone', 'location_details_phone')
	    ->add_input_node('text', $this->phone)
	    ->set_class('saisie-20em')
	    ->set_maxlength(100);
	    $interface_content_form->add_element('form_locdoc_email', 'location_details_email')
	    ->add_input_node('text', $this->email)
	    ->set_maxlength(255);
	    $interface_content_form->add_element('form_locdoc_website', 'location_details_website')
	    ->add_input_node('text', $this->website)
	    ->set_maxlength(100);
	    $interface_content_form->add_element('form_locdoc_logo', 'location_details_logo')
	    ->add_input_node('text', $this->logo)
	    ->set_maxlength(255);
	    $interface_content_form->add_element('form_locdoc_commentaire', 'location_details_commentaire')
	    ->add_textarea_node($this->commentaire, 55, 5);
	    
	    return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $admin_location_content_form, $msg, $charset;
		global $pmb_sur_location_activate, $pmb_map_activate;
		
		$content_form = $admin_location_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_admin_form('locationform');
		if(!$this->id){
			$interface_form->set_label($msg['106']);
		}else{
			$interface_form->set_label($msg['107']);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->libelle,ENT_QUOTES, $charset), $content_form);
		
		$content_form = str_replace('!!location_pic!!', htmlentities($this->pic,ENT_QUOTES, $charset), $content_form);
		
		if($this->visible_opac) $checkbox="checked"; else $checkbox="";
		$content_form = str_replace('!!checkbox!!', $checkbox, $content_form);
		
		$content_form = str_replace('!!locdoc_codage_import!!', $this->locdoc_codage_import, $content_form);
		$combo_lender= gen_liste ("select idlender, lender_libelle from lenders order by lender_libelle ", "idlender", "lender_libelle", "form_locdoc_owner", "", $this->locdoc_owner, 0, $msg[556],0,$msg["proprio_generique_biblio"]) ;
		$content_form = str_replace('!!lender!!', $combo_lender, $content_form);
		
		if($pmb_sur_location_activate){
			$sur_loc= sur_location::get_info_surloc_from_location($this->id);
			$content_form = str_replace('!!sur_loc_selector!!', $sur_loc->get_list("form_sur_localisation",$sur_loc->id,1), $content_form);
		} else {
			$content_form = str_replace('!!sur_loc_selector!!', '', $content_form);
		}
		if($this->surloc_used) $checkbox="checked"; else $checkbox="";
		$content_form = str_replace('!!checkbox_use_surloc!!', $checkbox, $content_form);
		
		// map
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(TYPE_LOCATION,$this->id);
			$map_form=$map_edition->get_form();
			$content_form = str_replace('!!location_map!!', $map_form, $content_form);
			
		} else {
			$content_form = str_replace('!!location_map!!', "", $content_form);
		}
		
		$requete = "SELECT id_infopage, title_infopage FROM infopages where valid_infopage=1 ORDER BY title_infopage ";
		$infopages = gen_liste ($requete, "id_infopage", "title_infopage", "form_num_infopage", "", $this->num_infopage, 0, $msg["location_no_infopage"], 0,$msg["location_no_infopage"], 0) ;
		$content_form = str_replace('!!loc_infopage!!', $infopages, $content_form);
		
		$content_form = str_replace('!!css_style!!', $this->css_style, $content_form);
		
		$content_form = str_replace('!!location_coords!!', $this->get_coords_content_form(), $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($content_form)
		->set_table_name('docs_location')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle, $form_locdoc_codage_import, $form_locdoc_owner, $form_location_pic;
		global $form_location_visible_opac, $form_locdoc_name, $form_locdoc_adr1, $form_locdoc_adr2;
		global $form_locdoc_cp, $form_locdoc_town, $form_locdoc_state, $form_locdoc_country, $form_locdoc_phone;
		global $form_locdoc_email, $form_locdoc_website, $form_locdoc_logo, $form_locdoc_commentaire, $form_num_infopage;
		global $form_css_style, $form_sur_localisation, $form_location_use_surloc;
		
		$this->libelle = stripslashes($form_libelle);
		$this->locdoc_codage_import = stripslashes($form_locdoc_codage_import);
		$this->locdoc_owner = intval($form_locdoc_owner);
		$this->pic = stripslashes($form_location_pic);
		$this->visible_opac = intval($form_location_visible_opac);
		$this->name = stripslashes($form_locdoc_name);
		$this->adr1 = stripslashes($form_locdoc_adr1);
		$this->adr2 = stripslashes($form_locdoc_adr2);
		$this->cp = stripslashes($form_locdoc_cp);
		$this->town = stripslashes($form_locdoc_town);
		$this->state = stripslashes($form_locdoc_state);
		$this->country = stripslashes($form_locdoc_country);
		$this->phone = stripslashes($form_locdoc_phone);
		$this->email = stripslashes($form_locdoc_email);
		$this->website = stripslashes($form_locdoc_website);
		$this->logo = stripslashes($form_locdoc_logo);
		$this->commentaire = stripslashes($form_locdoc_commentaire);
		$this->num_infopage = intval($form_num_infopage);
		$this->css_style = stripslashes($form_css_style);
		$this->surloc_num = intval($form_sur_localisation);
		$this->surloc_used = intval($form_location_use_surloc);
	}
	
	public function save() {
		global $pmb_map_activate;
		
		// O.K.,  now if item already exists UPDATE else INSERT
		$set_values = "SET location_libelle='".addslashes($this->libelle)."', 
			locdoc_codage_import='".addslashes($this->locdoc_codage_import)."', 
			locdoc_owner='".$this->locdoc_owner."', 
			location_pic='".addslashes($this->pic)."', 
			location_visible_opac='".$this->visible_opac."', 
			name= '".addslashes($this->name)."', 
			adr1= '".addslashes($this->adr1)."', 
			adr2= '".addslashes($this->adr2)."', 
			cp= '".addslashes($this->cp)."', 
			town= '".addslashes($this->town)."', 
			state= '".addslashes($this->state)."', 
			country= '".addslashes($this->country)."', 
			phone= '".addslashes($this->phone)."', 
			email= '".addslashes($this->email)."', 
			website= '".addslashes($this->website)."', 
			logo= '".addslashes($this->logo)."', 
			commentaire='".addslashes($this->commentaire)."', 
			num_infopage='".$this->num_infopage."', 
			css_style='".addslashes($this->css_style)."', 
			surloc_num='".$this->surloc_num."', 
			surloc_used='".$this->surloc_used."' " ;
		if($this->id) {
			$requete = "UPDATE docs_location $set_values WHERE idlocation='".$this->id."' ";
			pmb_mysql_query($requete);
			
		} else {
			$requete = "INSERT INTO docs_location $set_values ";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		// map
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(TYPE_LOCATION,$this->id);
			$map_edition->save_form();
		}
		$translation = new translation($this->id, "docs_location");
		$translation->update("location_libelle", "form_libelle");
		$translation->update("name", "form_locdoc_name");
		$translation->update("adr1", "form_locdoc_adr1");
		$translation->update("adr2", "form_locdoc_adr2");
		$translation->update("town", "form_locdoc_town");
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'un lieu de document
	// ---------------------------------------------------------------
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['location_libelle'] 	
		//	$data['locdoc_codage_import']
		//	$data['locdoc_owner']

		// check sur le type de  la variable passée en paramètre
		if ((empty($data) && !is_array($data)) || !is_array($data)) {
		    // si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
	
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT location_libelle FROM docs_location limit 1"),0);
		$data['location_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['location_libelle']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT locdoc_codage_import FROM docs_location limit 1"),0);
		$data['locdoc_codage_import'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['locdoc_codage_import']))),0,$long_maxi));
	
		if($data['locdoc_owner']=="") $data['locdoc_owner'] = 0;
		if($data['location_libelle']=="") return 0;
		/* locdoc_codage_import est obligatoire si locdoc_owner != 0 */
		//if(($data['locdoc_owner']!=0) && ($data['locdoc_codage_import']=="")) return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['location_libelle']);
		$key1 = addslashes($data['locdoc_codage_import']);
		$key2 = $data['locdoc_owner'];
		
		/* vérification que le lieu existe */
		$query = "SELECT idlocation FROM docs_location WHERE locdoc_codage_import='{$key1}' and locdoc_owner = '{$key2}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT docs_location ".$query);
		$docs_location  = pmb_mysql_fetch_object($result);
	
		/* le lieu de doc existe, on retourne l'ID */
		if($docs_location->idlocation) return $docs_location->idlocation;
	
		// id non-récupérée, il faut créer la forme.
		
		$query  = "INSERT INTO docs_location SET ";
		$query .= "location_libelle='".$key0."', ";
		$query .= "locdoc_codage_import='".$key1."', ";
		$query .= "locdoc_owner='".$key2."' ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into docs_location ".$query);
	
		return pmb_mysql_insert_id();
	} /* fin méthode import */
	
	public static function delete($id) {
		global $msg;
		global $admin_liste_jscript;
		
		$id = intval($id);
		if($id) {
			$total1 = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_location='".$id."' "), 0, 0);
			$total2 = pmb_mysql_result(pmb_mysql_query("select count(1) from users where deflt2docs_location='".$id."' or deflt_docs_location='".$id."'"), 0, 0);
			$total3 = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_location='".$id."' "), 0, 0);
			$total4 = pmb_mysql_result(pmb_mysql_query("select count(1) from abts_abts where location_id ='".$id."' "), 0, 0);
			$total5 = pmb_mysql_result(pmb_mysql_query("select count(1) from collections_state where location_id ='".$id."' "), 0, 0);
			if (($total1+$total2+$total3+$total4+$total5)==0) {
				$requete = "DELETE FROM docs_location WHERE idlocation=$id ";
				pmb_mysql_query($requete);
				translation::delete($id, "docs_location");
				return true;
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg["location_used"] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg["location_used_docs"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_docs' item='".$id."' total='".$total1."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg["location_used_users"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_users' item='".$id."' total='".$total2."' alt=\"".$msg["admin_users_list"]."\" title=\"".$msg["admin_users_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total3) $msg_suppr_err .= "<br />- ".$msg["location_used_empr"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_empr' item='".$id."' total='".$total3."' alt=\"".$msg["admin_empr_list"]."\" title=\"".$msg["admin_empr_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total4) $msg_suppr_err .= "<br />- ".$msg["location_used_abts"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_abts' item='".$id."' total='".$total4."' alt=\"".$msg["admin_abts_list"]."\" title=\"".$msg["admin_abts_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total5) $msg_suppr_err .= "<br />- ".$msg["location_used_collections_state"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_collections_state' item='".$id."' total='".$total5."' alt=\"".$msg["admin_collections_state_list"]."\" title=\"".$msg["admin_collections_state_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				pmb_error::get_instance(static::class)->add_message('294', $msg_suppr_err);
				return false;
			}
		}
		return true;
	}
	
			
	/* une fonction pour générer des combo Box 
	   paramêtres :
		$selected : l'élément sélectioné le cas échéant
	   retourne une chaine de caractères contenant l'objet complet */
	public static function gen_combo_box ( $selected, $on_change="") {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="book_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="";
		$option_premier_info="";
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select id=\"$nom\" name=\"$nom\" onChange=\"$on_change\">\n" ;
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {	
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."\n";
			}
			$i=0;
			while ($i<$nb_liste) {
				$gen_liste_str.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" " ;
				if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) {
					$gen_liste_str.="selected" ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	} /* fin gen_combo_box */
	
	public static function gen_combo_box_empr ( $selected, $afficher_premier=1, $on_change="" ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="empr_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="0";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		else $option_premier_info='';
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {	
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."</option>\n";
			}
			$i=0;
			while ($i<$nb_liste) {
				$gen_liste_str.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" " ;
				if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) {
					$gen_liste_str.="selected" ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	} /* fin gen_combo_box_empr */
		
	public static function gen_combo_box_docs ( $selected, $afficher_premier=1, $on_change="" ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="docs_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="0";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."</option>\n";
			}
			$i=0;
			while ($i<$nb_liste) {
				$gen_liste_str.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" " ;
				if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) {
					$gen_liste_str.="selected" ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	} /* fin gen_combo_box_docs */
		
		
	public function gen_combo_box_sugg ( $selected, $afficher_premier=1, $on_change="" ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="sugg_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="0";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		else $option_premier_info='';
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {	
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."</option>\n";
			}
			$i=0;
			while ($i<$nb_liste) {
				$gen_liste_str.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" " ;
				if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) {
					$gen_liste_str.="selected" ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	} /* fin gen_combo_box_sugg */
		
		
	public function gen_multiple_combo($liste_id=array()){
		global $msg,$charset;
		
		if(!$liste_id) return;
		
		$req = "select count(1) from docs_location";
		$res = pmb_mysql_query($req);
		$nb_loc = pmb_mysql_result($res,0,0);
		$req= "select idlocation, location_libelle from docs_location";
		$res = pmb_mysql_query($req);
		$selector_location="";
		if(pmb_mysql_num_rows($res)){				
			$selector_location = "<select id='loc_selector' name='loc_selector[]' multiple>";
			$selector_location .= "<option value='-1' ".((count($liste_id) == $nb_loc) ? 'selected' : '').">".htmlentities($msg['all_location'],ENT_QUOTES,$charset)."</option>";
			while($loc = pmb_mysql_fetch_object($res)){
				if((array_search($loc->idlocation,$liste_id) !== false) && (count($liste_id) != $nb_loc))
					$selected = 'selected';
				else $selected = '';
				$selector_location .= "<option value='".$loc->idlocation."' $selected>".htmlentities($loc->location_libelle,ENT_QUOTES,$charset)."</option>";
			}
			$selector_location .= "</select>";
		}	
		return $selector_location;	
	}
		

	public static function get_html_select($selected=array(),$sel_all=array('id'=>0,'msg'=>''),$sel_attr=array()) {
		global $charset;

		$sel='';
		$q = "select idlocation, location_libelle from docs_location order by location_libelle";
		$r = pmb_mysql_query($q);
		$res = array();
		if (count($sel_all)) {
			$res[$sel_all['id']]=htmlentities($sel_all['msg'],ENT_QUOTES,$charset);
		}
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)){
				$res[$row->idlocation] = $row->location_libelle;
			}
		}
		$size=count($res);
		if (isset($sel_attr['size']) && $sel_attr['size']>$size) $sel_attr['size']=$size;
		if ($size) {
			$sel="<select ";
			if (count($sel_attr)) {
				foreach($sel_attr as $attr=>$val) {
					$sel.="$attr='".$val."' ";
				}
			}
			$sel.=">";
			foreach($res as $id=>$val){
				$sel.="<option value='".$id."'";
				if(in_array($id,$selected)) $sel.=" selected='selected'";
				$sel.=" >";
				$sel.=htmlentities($val,ENT_QUOTES,$charset);
				$sel.="</option>";
			}
			$sel.='</select>';
		}
		return $sel;
	}
	
	public function get_translated_libelle() {
	    return translation::get_translated_text($this->id, 'docs_location', 'location_libelle', $this->libelle);
	}
	
	public function get_translated_name() {
	    return translation::get_translated_text($this->id, 'docs_location', 'name', $this->name);
	}
	
	public function get_translated_adr1() {
	    return translation::get_translated_text($this->id, 'docs_location', 'adr1', $this->adr1);
	}
	
	public function get_translated_adr2() {
	    return translation::get_translated_text($this->id, 'docs_location', 'adr2', $this->adr2);
	}
	
	public function get_translated_town() {
	    return translation::get_translated_text($this->id, 'docs_location', 'town', $this->town);
	}

	public static function get_docs_location_from_mail($mail) {
		$locations = array();

		$query = "select idlocation from docs_location where email = '".addslashes($mail)."'";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$locations[] = new docs_location($row->idlocation);
		}
		return $locations;
	}
} /* fin de définition de la classe */

} /* fin de délaration */


