<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docs_codestat.class.php,v 1.22 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

// définition de la classe de gestion des 'docs_codestat'

if ( ! defined( 'DOCSCODESTAT_CLASS' ) ) {
  define( 'DOCSCODESTAT_CLASS', 1 );

class docs_codestat {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';
	public $statisdoc_codage_import="";
	public $statisdoc_owner=0;

	/* ---------------------------------------------------------------
		docs_codestat($id) : constructeur
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
	
		/* récupération des informations du code statistique */
	
		$requete = 'SELECT * FROM docs_codestat WHERE idcode='.$this->id.' LIMIT 1;';
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->idcode;		
		$this->libelle = $data->codestat_libelle;
		$this->statisdoc_codage_import = $data->statisdoc_codage_import;
		$this->statisdoc_owner = $data->statisdoc_owner;

	}

	public function get_content_form() {
		global $msg;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', '103')
		->add_input_node('text', $this->libelle)
		->set_attributes(array('data-translation-fieldname' => 'codestat_libelle'));
		$interface_content_form->add_element('form_statisdoc_codage_import', 'proprio_codage_interne')
		->add_input_node('text', $this->statisdoc_codage_import)
		->set_class('saisie-20em');
		$interface_content_form->add_element('form_statisdoc_owner', 'proprio_codage_proprio')
		->add_query_node('select', "select idlender, lender_libelle from lenders order by lender_libelle ", $this->statisdoc_owner)
		->set_empty_option(0, $msg[556])
		->set_first_option(0, $msg["proprio_generique_biblio"])
		->set_class('saisie-20em');
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('typdocform');
		if(!$this->id){
			$interface_form->set_label($msg['101']);
		}else{
			$interface_form->set_label($msg['102']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('docs_codestat')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle, $form_statisdoc_codage_import, $form_statisdoc_owner;
		
		$this->libelle = stripslashes($form_libelle);
		$this->statisdoc_codage_import = stripslashes($form_statisdoc_codage_import);
		$this->statisdoc_owner = intval($form_statisdoc_owner);
	}
	
	public function get_query_if_exists() {
		return " SELECT count(1) FROM docs_codestat WHERE (codestat_libelle='".addslashes($this->libelle)."' AND idcode!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.  if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE docs_codestat SET codestat_libelle='".addslashes($this->libelle)."', statisdoc_codage_import='".addslashes($this->statisdoc_codage_import)."', statisdoc_owner='".$this->statisdoc_owner."' WHERE idcode=".$this->id;
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO docs_codestat (idcode,codestat_libelle,statisdoc_codage_import,statisdoc_owner) VALUES ('', '".addslashes($this->libelle)."','".addslashes($this->statisdoc_codage_import)."','".$this->statisdoc_owner."') ";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, "docs_codestat");
		$translation->update("codestat_libelle", "form_libelle");
		return true;
	}
	
	public static function check_data_from_form() {
		global $form_libelle;
		
		if(empty($form_libelle)) {
			return false;
		}
		return true;
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'un code statistique de document
	// ---------------------------------------------------------------
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['codestat_libelle'] 	
		//	$data['statisdoc_codage_import']
		//	$data['statisdoc_owner']

		// check sur le type de la variable passée en paramètre
		if ((empty($data) && !is_array($data)) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
		
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT codestat_libelle FROM docs_codestat limit 1"),0);
		$data['codestat_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['codestat_libelle']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT statisdoc_codage_import FROM docs_codestat limit 1"),0);
		$data['statisdoc_codage_import'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['statisdoc_codage_import']))),0,$long_maxi));
	
		if($data['statisdoc_owner']=="") $data['statisdoc_owner'] = 0;
		if($data['codestat_libelle']=="") return 0;
		/* statisdoc_codage_import est obligatoire si statisdoc_owner != 0 */
		// commenté depuis le choix de quel codage rec995 on récupère if(($data['statisdoc_owner']!=0) && ($data['statisdoc_codage_import']=="")) return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['codestat_libelle']);
		$key1 = addslashes($data['statisdoc_codage_import']);
		$key2 = $data['statisdoc_owner'];
		
		/* vérification que le code statistique existe */
		$query = "SELECT idcode FROM docs_codestat WHERE statisdoc_codage_import='{$key1}' and statisdoc_owner = '{$key2}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT docs_codestat ".$query);
		if(pmb_mysql_num_rows($result)) {
			$docs_codestat  = pmb_mysql_fetch_object($result);
			/* le code statistique de doc existe, on retourne l'ID */
			if($docs_codestat->idcode) {
				return $docs_codestat->idcode;
			}
		}
	
		// id non-récupérée, il faut créer la forme.
		
		$query  = "INSERT INTO docs_codestat SET ";
		$query .= "codestat_libelle='".$key0."', ";
		$query .= "statisdoc_codage_import='".$key1."', ";
		$query .= "statisdoc_owner='".$key2."' ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into docs_codestat ".$query);
	
		return pmb_mysql_insert_id();

	} /* fin méthode import */

	public static function delete($id) {
		global $msg, $admin_liste_jscript;
		
		$id = intval($id);
		if($id) {
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_codestat ='".$id."' "), 0, 0);
			if ($total==0) {
				translation::delete($id, "docs_codestat");
				$requete = "DELETE FROM docs_codestat WHERE idcode=$id ";
				pmb_mysql_query($requete);
				return true;
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg[1701]." <a href='#' onclick=\"showListItems(this);return(false);\" what='codestat_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
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
	public static function gen_combo_box ( $selected ) {
		global $msg;
		$requete="select idcode, codestat_libelle from docs_codestat order by codestat_libelle ";
		$champ_code="idcode";
		$champ_info="codestat_libelle";
		$nom="book_codestat_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_codestat'];
		$option_premier_code="";
		$option_premier_info="";
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete) or die (pmb_mysql_error()." ".$requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\">\n" ;
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

	public function get_translated_libelle() {
		return translation::get_translated_text($this->id, 'docs_codestat', 'codestat_libelle', $this->libelle);
	}
} /* fin de définition de la classe */

} /* fin de délaration */


