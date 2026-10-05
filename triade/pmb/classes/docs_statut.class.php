<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docs_statut.class.php,v 1.20 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

// définition de la classe de gestion des 'docs_statut'

if ( ! defined( 'DOCSSTATUT_CLASS' ) ) {
  define( 'DOCSSTATUT_CLASS', 1 );

class docs_statut {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';
	public $libelle_opac='';
	public $pret_flag='';
	public $statusdoc_codage_import="";
	public $statusdoc_owner=0;
	public $transfert_flag=0;
	public $visible_opac=0;
	public $allow_resa=0;

	/* ---------------------------------------------------------------
			docs_statut($id) : constructeur
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
	
		$requete = 'SELECT * FROM docs_statut WHERE idstatut='.$this->id.' LIMIT 1;';
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->idstatut;		
		$this->libelle = $data->statut_libelle;
		$this->libelle_opac = $data->statut_libelle_opac;
		$this->pret_flag = $data->pret_flag;
		$this->statusdoc_codage_import = $data->statusdoc_codage_import;
		$this->statusdoc_owner = $data->statusdoc_owner;
		$this->transfert_flag = $data->transfert_flag;
		$this->visible_opac = $data->statut_visible_opac;
		$this->allow_resa = $data->statut_allow_resa;
	}

	public function get_content_form() {
		global $msg;
		global $pmb_transferts_actif;
		
		$interface_content_form = new interface_content_form(static::class);
		
		$interface_content_form->add_element('form_libelle', '103')
		->add_input_node('text', $this->libelle)
		->set_attributes(array('data-translation-fieldname' => 'statut_libelle'));
		$interface_content_form->add_element('form_libelle_opac', 'docs_statut_form_libelle_opac')
		->add_input_node('text', $this->libelle_opac)
		->set_attributes(array('data-translation-fieldname' => 'statut_libelle_opac'));
		$interface_content_form->add_element('form_pret', '117', 'flat')
		->add_input_node('boolean', $this->pret_flag)
		->set_attributes(array('onClick', 'test_check(this.form)'));
		$interface_content_form->add_element('form_allow_resa', 'statut_allow_resa_title', 'flat')
		->add_input_node('boolean', $this->allow_resa);
		if ($pmb_transferts_actif) {
			$interface_content_form->add_element('form_trans', 'transferts_statut_lib_transferable', 'flat')
			->add_input_node('boolean', $this->transfert_flag)
			->set_attributes(array('onClick', 'test_check_trans(this.form)'));
		}
		$interface_content_form->add_element('form_visible_opac', 'opac_object_visible', 'flat')
		->add_input_node('boolean', $this->visible_opac)
		->set_attributes(array('onClick', 'test_check_visible_opac(this.form)'));
		$interface_content_form->add_element('form_statusdoc_codage_import', 'proprio_codage_interne')
		->add_input_node('text', $this->statusdoc_codage_import)
		->set_class('saisie-20em');
		$interface_content_form->add_element('form_statusdoc_owner', 'proprio_codage_proprio')
		->add_query_node('select', "select idlender as id, lender_libelle as label from lenders order by label", $this->statusdoc_owner)
		->set_empty_option(0, $msg[556])
		->set_first_option(0, $msg["proprio_generique_biblio"])
		->set_class('saisie-20em');
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('typdocform');
		if(!$this->id){
			$interface_form->set_label($msg['115']);
		}else{
			$interface_form->set_label($msg['118']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('docs_statut')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle, $form_pret, $form_allow_resa, $form_trans, $form_statusdoc_codage_import, $form_statusdoc_owner;
		global $form_libelle_opac, $form_visible_opac;
		
		$this->libelle = stripslashes($form_libelle);
		$this->libelle_opac = stripslashes($form_libelle_opac);
		$this->pret_flag = intval($form_pret);
		$this->statusdoc_codage_import = stripslashes($form_statusdoc_codage_import);
		$this->statusdoc_owner = intval($form_statusdoc_owner);
		$this->transfert_flag = intval($form_trans);
		$this->visible_opac = intval($form_visible_opac);
		$this->allow_resa = intval($form_allow_resa);
	}
	
	public function get_query_if_exists() {
		return " SELECT count(1) FROM docs_statut WHERE (statut_libelle='".addslashes($this->libelle)."' AND idstatut!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE docs_statut SET statut_libelle='".addslashes($this->libelle)."',pret_flag='".$this->pret_flag."',statut_allow_resa='".$this->allow_resa."', transfert_flag='".$this->transfert_flag."',statusdoc_codage_import='".addslashes($this->statusdoc_codage_import)."', statusdoc_owner='".$this->statusdoc_owner."', statut_libelle_opac='".addslashes($this->libelle_opac)."', statut_visible_opac='".$this->visible_opac."' WHERE idstatut=".$this->id;
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO docs_statut SET statut_libelle='".addslashes($this->libelle)."',pret_flag='".$this->pret_flag."',statut_allow_resa='".$this->allow_resa."', transfert_flag='".$this->transfert_flag."',statusdoc_codage_import='".addslashes($this->statusdoc_codage_import)."', statusdoc_owner='".$this->statusdoc_owner."', statut_libelle_opac='".addslashes($this->libelle_opac)."', statut_visible_opac='".$this->visible_opac."' ";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, "docs_statut");
		$translation->update("statut_libelle", "form_libelle");
		$translation->update("statut_libelle_opac", "form_libelle_opac");
	}
	
	public static function check_data_from_form() {
		global $form_libelle;
		
		if(empty($form_libelle)) {
			return false;
		}
		return true;
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'un statut de document
	// ---------------------------------------------------------------
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['statut_libelle'] 	
		//	$data['pret_flag']
		//	$data['statusdoc_codage_import']
		//	$data['statusdoc_owner']
	
		// check sur le type de  la variable passée en paramètre
		if ((empty($data) && !is_array($data)) || !is_array($data)) {
		    // si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT statut_libelle FROM docs_statut limit 1"),0);
		$data['statut_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['statut_libelle']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT statusdoc_codage_import FROM docs_statut limit 1"),0);
		$data['statusdoc_codage_import'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['statusdoc_codage_import']))),0,$long_maxi));
	
		if($data['statusdoc_owner']=="") $data['statusdoc_owner'] = 0;
		if($data['statut_libelle']=="") return 0;
		/* statusdoc_codage_import est obligatoire si statusdoc_owner != 0 */
		if(($data['statusdoc_owner']!=0) && ($data['statusdoc_codage_import']=="")) return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['statut_libelle']);
		$key1 = addslashes($data['statusdoc_codage_import']);
		$key2 = $data['statusdoc_owner'];
		
		/* vérification que le statut existe */
		$query = "SELECT idstatut FROM docs_statut WHERE statusdoc_codage_import='{$key1}' and statusdoc_owner = '{$key2}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT docs_statut ".$query);
		$docs_statut  = pmb_mysql_fetch_object($result);
	
		/* le statut de doc existe, on retourne l'ID */
		if($docs_statut->idstatut) return $docs_statut->idstatut;
	
		// id non-récupérée, il faut créer la forme.
		/* une petite valeur par défaut */
		if ($data['pret_flag']=="") $data['pret_flag']=1;
		
		$query  = "INSERT INTO docs_statut SET ";
		$query .= "statut_libelle='".$key0."', ";
		$query .= "pret_flag='".$data['pret_flag']."', ";
		$query .= "statusdoc_codage_import='".$key1."', ";
		$query .= "statusdoc_owner='".$key2."' ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into docs_statut ".$query);
	
		return pmb_mysql_insert_id();

	} /* fin méthode import */

	public static function delete($id) {
		global $msg;
		global $admin_liste_jscript, $finance_statut_perdu;
		
		$id = intval($id);
		if($id) {
			$total_serialcirc = 0;
			$total_serialcirc = pmb_mysql_result(pmb_mysql_query("select count(1) from serialcirc where serialcirc_expl_statut_circ='".$id."' or serialcirc_expl_statut_circ_after='".$id."'"), 0, 0);
			if ($total_serialcirc > 0) {
				pmb_error::get_instance(static::class)->add_message('294', $msg["admin_docs_statut_serialcirc_delete_forbidden"]);
				return false;
			} else {
				$total = 0;
				$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_statut ='".$id."' "), 0, 0);
				if ($total > 0) {
					$msg_suppr_err = $admin_liste_jscript;
					$msg_suppr_err .= $msg[1703]." <a href='#' onclick=\"showListItems(this);return(false);\" what='statut_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
					pmb_error::get_instance(static::class)->add_message('294', $msg_suppr_err);
					return false;
				} else {
					if ($finance_statut_perdu == '') $statut_perdu = 0;
					else $statut_perdu = $finance_statut_perdu;
					if ($statut_perdu == $id) {
						pmb_error::get_instance(static::class)->add_message('294', $msg["admin_docs_statut_gestion_financiere_delete_forbidden"]);
						return false;
					} else {
						translation::delete($id, "docs_statut");
						$requete = "DELETE FROM docs_statut WHERE idstatut=$id ";
						pmb_mysql_query($requete);
						return true;
					}
				}
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
	
		$requete="select idstatut, statut_libelle from docs_statut order by statut_libelle ";
		$champ_code="idstatut";
		$champ_info="statut_libelle";
		$nom="book_statut_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_statut'];
		$option_premier_code="";
		$option_premier_info="";
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
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
		return translation::get_translated_text($this->id, 'docs_statut', 'statut_libelle', $this->libelle);
	}
	
	public function get_translated_libelle_opac() {
		return translation::get_translated_text($this->id, 'docs_statut', 'statut_libelle_opac', $this->libelle_opac);
	}
} /* fin de définition de la classe */

} /* fin de délaration */


