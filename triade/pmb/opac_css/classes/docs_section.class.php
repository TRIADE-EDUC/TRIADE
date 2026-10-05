<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docs_section.class.php,v 1.2 2024/01/12 16:00:45 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

// définition de la classe de gestion des 'docs_section'

if ( ! defined( 'DOCSSECTION_CLASS' ) ) {
  define( 'DOCSSECTION_CLASS', 1 );

class docs_section {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */
	public $id=0;
	public $libelle='';
	public $libelle_opac='';
	public $sdoc_codage_import="";
	public $sdoc_owner=0;
	public $pic='';
	public $visible_opac=0;
	public $num_locations=array();
	
	/* ---------------------------------------------------------------
		docs_section($id) : constructeur
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
	
		/* récupération des informations de la catégorie */
	
		$requete = "SELECT * FROM docs_section WHERE idsection='".$this->id."' ";
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->idsection;		
		$this->libelle = $data->section_libelle;
		$this->libelle_opac = $data->section_libelle_opac;
		$this->sdoc_codage_import = $data->sdoc_codage_import;
		$this->sdoc_owner = $data->sdoc_owner;
		$this->pic = $data->section_pic;
		$this->visible_opac = $data->section_visible_opac;
		
		$rqtloc = "select num_location from docsloc_section where num_section='".$this->id."' " ;
		$resloc = pmb_mysql_query($rqtloc);
		while ($loc=pmb_mysql_fetch_object($resloc)) $this->num_locations[]=$loc->num_location ;
	}

	public function get_content_form() {
		global $msg;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', '103')
		->add_input_node('text', $this->libelle)
		->set_attributes(array('data-translation-fieldname' => 'section_libelle'));
		$interface_content_form->add_element('form_libelle_opac', 'docs_section_libelle_opac')
		->add_input_node('text', $this->libelle_opac)
		->set_attributes(array('data-translation-fieldname' => 'section_libelle_opac'));
		$interface_content_form->add_element('form_section_pic', 'docs_section_pic')
		->add_input_node('text', $this->pic)
		->set_maxlength(255);
		$interface_content_form->add_element('form_section_visible_opac', 'opac_object_visible', 'flat')
		->add_input_node('boolean', $this->visible_opac);
		
		$interface_content_form->add_element('form_sdoc_codage_import', 'proprio_codage_interne')
		->add_input_node('text', $this->sdoc_codage_import)
		->set_class('saisie-20em');
		$interface_content_form->add_element('form_sdoc_owner', 'proprio_codage_proprio')
		->add_query_node('select', "select idlender, lender_libelle from lenders order by lender_libelle ", $this->sdoc_owner)
		->set_empty_option(0, $msg[556])
		->set_first_option(0, $msg["proprio_generique_biblio"]);
		
		$localisations="";
		$requete = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
		$res = pmb_mysql_query($requete) ;
		
		while ($obj=pmb_mysql_fetch_object($res)) {
			$as=array_search($obj->idlocation,$this->num_locations);
			$obj->location_libelle = translation::get_translated_text($obj->num_location, "docs_location", "location_libelle", $obj->location_libelle);
			if (($as!==null)&&($as!==false)) $localisations.="<input type='checkbox' name='num_locations[]' value='".$obj->idlocation."' checked class='checkbox' id='numloc".$obj->idlocation."' /><label for='numloc".$obj->idlocation."'>&nbsp;".$obj->location_libelle."</label><br />";
			else $localisations.="<input type='checkbox' name='num_locations[]' value='".$obj->idlocation."' class='checkbox' id='numloc".$obj->idlocation."' /><label for='numloc".$obj->idlocation."'>&nbsp;".$obj->location_libelle."</label><br />";
		}
		$interface_content_form->add_element('num_locations', 'section_visible_loc')
		->add_html_node($localisations);
		
		$interface_content_form->add_zone('default', '', ['form_libelle', 'form_libelle_opac', 'form_section_pic', 'form_section_visible_opac']);
		$interface_content_form->add_zone('codage', '', ['form_sdoc_codage_import', 'form_sdoc_owner'])
		->set_class('colonne2');
	    $interface_content_form->add_zone('locations', '', ['num_locations'])
	    ->set_class('colonne_suite');
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('typdocform');
		if(!$this->id){
			$interface_form->set_label($msg['110']);
		}else{
			$interface_form->set_label($msg['111']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('docs_section')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle, $form_libelle_opac, $form_sdoc_codage_import, $form_sdoc_owner, $form_section_pic, $form_section_visible_opac, $num_locations;
		
		$this->libelle = stripslashes($form_libelle);
		$this->libelle_opac = stripslashes($form_libelle_opac);
		$this->sdoc_codage_import = stripslashes($form_sdoc_codage_import);
		$this->sdoc_owner = intval($form_sdoc_owner);
		$this->pic = stripslashes($form_section_pic);
		$this->visible_opac = intval($form_section_visible_opac);
		$this->num_locations = $num_locations;
	}
	
	public function get_query_if_exists() {
		return " SELECT count(1) FROM docs_section WHERE (section_libelle='".addslashes($this->libelle)."' AND idsection!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.  if item already exists UPDATE else INSERT
		if ($this->id) {
			$requete = "UPDATE docs_section SET section_libelle='".addslashes($this->libelle)."', section_libelle_opac='".addslashes($this->libelle_opac)."', sdoc_codage_import='".addslashes($this->sdoc_codage_import)."', sdoc_owner='".$this->sdoc_owner."', section_pic='".addslashes($this->pic)."', section_visible_opac='".$this->visible_opac."' WHERE idsection=".$this->id;
			$res = pmb_mysql_query($requete);
		}else{
			$requete = "INSERT INTO docs_section (idsection,section_libelle,section_libelle_opac,sdoc_codage_import,sdoc_owner,section_pic, section_visible_opac) VALUES ('', '".addslashes($this->libelle)."', '".addslashes($this->libelle_opac)."','".addslashes($this->sdoc_codage_import)."','".$this->sdoc_owner."', '".addslashes($this->pic)."', '".$this->visible_opac."') ";
			$res = pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		if (!is_array($this->num_locations)) $this->num_locations=array();
		$requete="SELECT num_location FROM docsloc_section WHERE num_section='".$this->id."'";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)){
			while ($ligne=pmb_mysql_fetch_object($res)) {
				if(array_search($ligne->num_location,$this->num_locations) !== false){
					//Si l'ancienne loc est toujours dans les nouvelles je n'y touche pas
					unset($this->num_locations[array_search($ligne->num_location,$this->num_locations)]);
				}else{
					//Si l'ancienne n'est pas dans les nouvelles loc je la supprime
					$requete = "delete from docsloc_section where num_section='".$this->id."' and num_location='".$ligne->num_location."' ";
					pmb_mysql_query($requete);
				}
			}
		}
		//Si il y a des nouvelles loc pour la section je les créer
		foreach ($this->num_locations as $value ) {
			$requete = "INSERT INTO docsloc_section (num_section,num_location) VALUES ('".$this->id."', '".$value."') ";
			pmb_mysql_query($requete);
		}
		$translation = new translation($this->id, "docs_section");
		$translation->update("section_libelle", "form_libelle");
		$translation->update("section_libelle_opac", "form_libelle_opac");
	}
	
	public static function check_data_from_form() {
		global $form_libelle;
		
		if(empty($form_libelle)) {
			return false;
		}
		return true;
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'une section de document
	// ---------------------------------------------------------------
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['section_libelle'] 	
		//	$data['sdoc_codage_import']
		//	$data['sdoc_owner']
	
		// check sur le type de  la variable passée en paramètre
		if ((empty($data) && !is_array($data)) || !is_array($data)) {
		    // si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT section_libelle FROM docs_section limit 1"),0);
		$data['section_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['section_libelle']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT sdoc_codage_import FROM docs_section limit 1"),0);
		$data['sdoc_codage_import'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['sdoc_codage_import']))),0,$long_maxi));
	
		if($data['sdoc_owner']=="") $data['sdoc_owner'] = 0;
		if($data['section_libelle']=="") return 0;
		/* sdoc_codage_import est obligatoire si sdoc_owner != 0 */
		// if(($data['sdoc_owner']!=0) && ($data['sdoc_codage_import']=="")) return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['section_libelle']);
		$key1 = addslashes($data['sdoc_codage_import']);
		$key2 = $data['sdoc_owner'];
		
		/* vérification que la section existe */
		$query = "SELECT idsection FROM docs_section WHERE sdoc_codage_import='{$key1}' and sdoc_owner = '{$key2}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT docs_section ".$query);
		$docs_section  = pmb_mysql_fetch_object($result);
	
		/* le type de doc existe, on retourne l'ID */
		if($docs_section->idsection) return $docs_section->idsection;
	
		// id non-récupérée, il faut créer la forme.
		$query  = "INSERT INTO docs_section SET ";
		$query .= "section_libelle='".$key0."', ";
		$query .= "sdoc_codage_import='".$key1."', ";
		$query .= "sdoc_owner='".$key2."' ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into docs_section ".$query);
		$id_section_cree = pmb_mysql_insert_id();
		$query = "insert into docsloc_section (num_section,num_location) (SELECT $id_section_cree, idlocation FROM docs_location) ";
		$result = pmb_mysql_query($query);
	
		return $id_section_cree ;

	} /* fin méthode import */

	public static function delete($id) {
		global $msg, $admin_liste_jscript;
		
		$id = intval($id);
		if($id) {
			$total=0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_section ='".$id."' "), 0, 0);
			if ($total==0) {
				$compt=pmb_mysql_num_rows(pmb_mysql_query("select userid from users where deflt_docs_section='$id'"));
				if ($compt==0) {
					$total = pmb_mysql_result(pmb_mysql_query("select count(1) from abts_abts where section_id ='".$id."' "), 0, 0);
					if ($total==0) {
						translation::delete($id, "docs_section");
						$requete = "DELETE FROM docs_section WHERE idsection=$id ";
						pmb_mysql_query($requete);
						$requete = "delete from docsloc_section where num_section='$id' ";
						pmb_mysql_query($requete);
						return true;
					}else {
						$msg_suppr_err = $admin_liste_jscript;
						$msg_suppr_err .= $msg["section_used_abts"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='section_abts' item='".$id."' total='".$total."' alt=\"".$msg["admin_abts_list"]."\" title=\"".$msg["admin_abts_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
						pmb_error::get_instance(static::class)->add_message('294', $msg_suppr_err);
						return false;
					}
				} else {
					$msg_suppr_err = $admin_liste_jscript;
					$msg_suppr_err .= $msg["section_used_users"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='section_users' item='".$id."' total='".$compt."' alt=\"".$msg["admin_users_list"]."\" title=\"".$msg["admin_users_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
					pmb_error::get_instance(static::class)->add_message('294', $msg_suppr_err);
					return false;
				}
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg[1702]." <a href='#' onclick=\"showListItems(this);return(false);\" what='section_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				pmb_error::get_instance(static::class)->add_message('294', $msg_suppr_err);
				return false;
			}
		}
		return true;
	}
	
	/* une fonction pour générer des combo Box 
   paramêtres :
	$selected : l'élément sélectioné le cas échéant
	$num_location : doit-on filtrer la liste en fonction d'une localisation
   retourne une chaine de caractères contenant l'objet complet */
	public static function gen_combo_box ( $selected, $num_location=0) {
		global $msg;
		$requete="select idsection, section_libelle from docs_section order by section_libelle ";
		$champ_code="idsection";
		$champ_info="section_libelle";
		$nom="book_section_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_section'];
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
					$idsection = pmb_mysql_result($resultat_liste,$i,$champ_code);
					$docs_section = new docs_section($idsection);
					$gen_liste_str.="<option value=\"".$idsection."\" " ;
					if ($selected==$idsection) {
						$gen_liste_str.="selected" ;
					}
					$disabled = false;
					if($num_location && !in_array($num_location, $docs_section->num_locations)) {
						$disabled = true;
					}
					$gen_liste_str.=" data-num-locations='".implode(',', $docs_section->num_locations)."' ".($disabled ? "disabled" : "").">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
					$i++;
				}
			}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	} /* fin gen_combo_box */

	public function get_translated_libelle() {
		return translation::get_translated_text($this->id, 'docs_section', 'section_libelle', $this->libelle);
	}
	
	public function get_translated_libelle_opac() {
		return translation::get_translated_text($this->id, 'docs_section', 'section_libelle_opac', $this->libelle_opac);
	}
} /* fin de définition de la classe */

} /* fin de délaration */


