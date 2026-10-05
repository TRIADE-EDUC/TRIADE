<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sur_location.class.php,v 1.13.6.2.2.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des vues Opac

// inclusions principales
global $class_path, $include_path;
require_once("$include_path/templates/sur_location.tpl.php");
require_once($class_path."/map/map_edition_controler.class.php");


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
    public $sur_location_list;
    public $selector;

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

		$this->get_list();
	}

	public static function get_info_surloc_from_location($id_docs_location=0){
		$id_docs_location = intval($id_docs_location);
		if($id_docs_location){
			$requete = "SELECT * FROM docs_location where idlocation='$id_docs_location'";
			$res = pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete");
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

	// fonction récupérant les infos pour la liste de sur-loc
	public function get_list($name='form_sur_localisation', $value_selected=0,$no_sel=0) {
		global $msg, $charset;

		$this->sur_location_list=array();
		$selector = "<select name='$name' id='$name'>";
		if($no_sel) {
			$selector .= "<option value='0'";
			!$value_selected ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
	 		$selector .= htmlentities($msg["sur_location_aucune"],ENT_QUOTES, $charset).'</option>';
		}
		$myQuery = pmb_mysql_query("SELECT * FROM sur_location order by surloc_libelle ");
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

	public function set_properties_from_form() {
		global $form_libelle,$form_location_pic,$form_location_visible_opac;
		global $form_locdoc_name,$form_locdoc_adr1,$form_locdoc_adr2, $form_locdoc_cp,$form_locdoc_town;
		global $form_locdoc_state,$form_locdoc_country,$form_locdoc_phone,$form_locdoc_email;
		global $form_locdoc_website,$form_locdoc_logo,$form_locdoc_commentaire;
		global $form_num_infopage,$form_css_style;

		$this->libelle = stripslashes($form_libelle);
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
		$this->comment = stripslashes($form_locdoc_commentaire);
		$this->num_infopage = intval($form_num_infopage);
		$this->css_style = stripslashes($form_css_style);

	}

	// fonction de mise à jour ou de création
	public function save() {
	    global $pmb_map_activate;

		$set_values = "SET
			surloc_libelle='".addslashes($this->libelle)."',
			surloc_pic='".addslashes($this->pic)."',
			surloc_visible_opac='".$this->visible_opac."',
			surloc_name= '".addslashes($this->name)."',
			surloc_adr1= '".addslashes($this->adr1)."',
			surloc_adr2= '".addslashes($this->adr2)."',
			surloc_cp= '".addslashes($this->cp)."',
			surloc_town= '".addslashes($this->town)."',
			surloc_state= '".addslashes($this->state)."',
			surloc_country= '".addslashes($this->country)."',
			surloc_phone= '".addslashes($this->phone)."',
			surloc_email= '".addslashes($this->email)."',
			surloc_website= '".addslashes($this->website)."',
			surloc_logo= '".addslashes($this->logo)."',
			surloc_comment='".addslashes($this->comment)."',
			surloc_num_infopage='".$this->num_infopage."',
			surloc_css_style='".addslashes($this->css_style)."' " ;
		if($this->id) {
			$requete = "UPDATE sur_location $set_values WHERE surloc_id='$this->id' ";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO sur_location $set_values ";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}

		// map
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(TYPE_SUR_LOCATION,$this->id);
			$map_edition->save_form();
		}

		$requete = "UPDATE docs_location SET surloc_num='0' WHERE surloc_num='$this->id' ";
		pmb_mysql_query($requete);

		// mémo des localisations associées
		foreach($this->docs_location_data as $docs_loc){
			$selected=0;
			eval("
			global \$form_location_selected_".$docs_loc["idlocation"].";
			\$selected =\$form_location_selected_".$docs_loc["idlocation"].";
			");
			if($selected){
				$requete = "UPDATE docs_location SET surloc_num='$this->id' WHERE idlocation=".$docs_loc["idlocation"];
				pmb_mysql_query($requete);
			}
		}

		$translation = new translation($this->id, "sur_location");
		$translation->update("surloc_name", "form_locdoc_name");
		$translation->update("surloc_adr1", "form_locdoc_adr1");
		$translation->update("surloc_adr2", "form_locdoc_adr2");
		$translation->update("surloc_town", "form_locdoc_town");

		// rafraischissement des données
		$this->fetch_data();
	}

	public function get_coords_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('form_locdoc_name', 'sur_location_details_name')
	    ->add_input_node('text', $this->name)
	    ->set_attributes(array('data-translation-fieldname' => 'surloc_name'));
	    $interface_content_form->add_element('form_locdoc_adr1', 'sur_location_details_adr1')
	    ->add_input_node('text', $this->adr1)
	    ->set_attributes(array('data-translation-fieldname' => 'surloc_adr1'));
	    $interface_content_form->add_element('form_locdoc_adr2', 'sur_location_details_adr2')
	    ->add_input_node('text', $this->adr2)
	    ->set_attributes(array('data-translation-fieldname' => 'surloc_adr2'));

	    //Code postal / Ville
	    $element_cp = $interface_content_form->add_element('form_locdoc_cp', 'sur_location_details_cp');
	    $element_cp->add_input_node('integer', $this->cp)
	    ->set_maxlength(15);
	    $element_cp->set_class('row colonne4');
	    $element_town = $interface_content_form->add_element('form_locdoc_town', 'sur_location_details_town');
	    $element_town->add_input_node('text', $this->town)
	    ->set_attributes(array('data-translation-fieldname' => 'surloc_town'));
	    $element_town->set_class('colonne_suite');

	    //Etat ou région / Pays
	    $element_state = $interface_content_form->add_element('form_locdoc_state', 'sur_location_details_state');
	    $element_state->add_input_node('text', $this->state)
	    ->set_class('saisie-20em');
	    $element_state->set_class('row colonne3');
	    $element_country = $interface_content_form->add_element('form_locdoc_country', 'sur_location_details_country');
	    $element_country->add_input_node('text', $this->country)
	    ->set_class('saisie-20em');
	    $element_country->set_class('colonne_suite');

	    $interface_content_form->add_element('form_locdoc_phone', 'sur_location_details_phone')
	    ->add_input_node('text', $this->phone)
	    ->set_class('saisie-20em')
	    ->set_maxlength(100);
	    $interface_content_form->add_element('form_locdoc_email', 'sur_location_details_email')
	    ->add_input_node('text', $this->email)
	    ->set_maxlength(255);
	    $interface_content_form->add_element('form_locdoc_website', 'sur_location_details_website')
	    ->add_input_node('text', $this->website)
	    ->set_maxlength(100);
	    $interface_content_form->add_element('form_locdoc_logo', 'sur_location_details_logo')
	    ->add_input_node('text', $this->logo)
	    ->set_maxlength(255);
	    $interface_content_form->add_element('form_locdoc_commentaire', 'sur_location_comment')
	    ->add_textarea_node($this->comment, 55, 5);

	    return $interface_content_form->get_display();
	}

	// fonction générant le form de saisie
	public function get_form() {
		global $msg;
		global $tpl_sur_location_content_form,$tpl_docs_loc_table_line;
		global $charset;
		global $pmb_map_activate;

		$content_form = $tpl_sur_location_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);

		$interface_form = new interface_admin_form('surlocform');
		if(!$this->id){
			$interface_form->set_label($msg['sur_location_ajouter_title']);
		}else{
			$interface_form->set_label($msg['sur_location_modifier_title']);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->libelle,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!location_pic!!', htmlentities($this->pic,ENT_QUOTES, $charset), $content_form);

		if($this->visible_opac) $checkbox="checked"; else $checkbox="";
		$content_form = str_replace('!!checkbox!!', $checkbox, $content_form);
		$lines="";
		$pair="odd";
		foreach($this->docs_location_data as $docs_loc){
			$line=$tpl_docs_loc_table_line;
			if($pair!="odd")$pair="odd"; else $pair="even";
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
		$content_form = str_replace('!!docs_loc_lines!!', 	$lines  , $content_form);

		// map
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(TYPE_SUR_LOCATION,$this->id);
			$map_form=$map_edition->get_form();
			$content_form = str_replace('!!sur_location_map!!', $map_form, $content_form);

		} else {
			$content_form = str_replace('!!sur_location_map!!', "", $content_form);
		}

		$content_form = str_replace('!!sur_location_coords!!', $this->get_coords_content_form(), $content_form);

		$requete = "SELECT id_infopage, title_infopage FROM infopages where valid_infopage=1 ORDER BY title_infopage ";
		$infopages = gen_liste ($requete, "id_infopage", "title_infopage", "form_num_infopage", "", $this->num_infopage, 0, $msg['location_no_infopage'], 0,$msg['location_no_infopage'], 0) ;
		$content_form = str_replace('!!loc_infopage!!', $infopages, $content_form);
		$content_form = str_replace('!!css_style!!', $this->css_style, $content_form);

		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($content_form)
		->set_table_name('sur_location')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}

	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$requete = "UPDATE docs_location SET surloc_num='0' WHERE surloc_num='$id' ";
			pmb_mysql_query($requete);
			translation::delete($id, "sur_location");
			pmb_mysql_query("DELETE from sur_location WHERE surloc_id='".$id."' ");
		}
	}

	public function get_translated_name() {
	    return translation::get_translated_text($this->id, 'sur_location', 'surloc_name', $this->name);
	}

	public function get_translated_adr1() {
	    return translation::get_translated_text($this->id, 'sur_location', 'surloc_adr1', $this->adr1);
	}

	public function get_translated_adr2() {
	    return translation::get_translated_text($this->id, 'sur_location', 'surloc_adr2', $this->adr2);
	}

	public function get_translated_town() {
	    return translation::get_translated_text($this->id, 'sur_location', 'surloc_town', $this->town);
	}
} // fin définition classe
