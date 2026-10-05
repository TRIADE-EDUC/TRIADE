<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lender.class.php,v 1.22.4.1 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

// définition de la classe de gestion des 'prêteurs d'ouvrage'

if ( ! defined( 'LENDER_CLASS' ) ) {
  define( 'LENDER_CLASS', 1 );

class lender {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $idlender=0;			// MySQL idlender in table 'lenders'
	public $lender_libelle='';		// nom du prêteur

	// ---------------------------------------------------------------
	//		lender($id) : constructeur
	// ---------------------------------------------------------------

	public function __construct($id=0) {
		$this->idlender = intval($id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos du lender
	// ---------------------------------------------------------------

	public function getData() {
		if($this->idlender) {
			$requete = "SELECT idlender, lender_libelle FROM lenders WHERE idlender='".$this->idlender."'";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$this->idlender		= $temp->idlender;
				$this->lender_libelle		= $temp->lender_libelle;
			} else {
				pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			}
		}
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', '558')
		->add_input_node('text', $this->lender_libelle);
		return $interface_content_form->get_display();
	}

	public function get_form() {
		global $msg;

		$interface_form = new interface_admin_form('lenderform');
		if(!$this->idlender){
			$interface_form->set_label($msg['555']);
		}else{
			$interface_form->set_label($msg['557']);
		}
		$interface_form->set_object_id($this->idlender)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->lender_libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('lenders')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $form_libelle;

		$this->lender_libelle = stripslashes($form_libelle);
	}

	public function get_query_if_exists() {
		return " SELECT count(1) FROM lenders WHERE (lender_libelle='".addslashes($this->lender_libelle)."' AND idlender!='".$this->idlender."' )";
	}

	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->idlender != 0) {
			$query = "UPDATE lenders SET lender_libelle='".addslashes($this->lender_libelle)."' WHERE idlender=".$this->idlender;
			pmb_mysql_query($query);
		} else {
			$query = "INSERT INTO lenders (idlender,lender_libelle) VALUES (0, '".addslashes($this->lender_libelle)."') ";
			pmb_mysql_query($query);
			$this->idlender = pmb_mysql_insert_id();
		}
		$translation = new translation($this->idlender, "lenders");
		$translation->update("lender_libelle", "form_libelle");
		return true;
	}

	public static function check_data_from_form() {
		global $form_libelle;

		if(empty($form_libelle)) {
			return false;
		}
		return true;
	}

	/* une fonction pour générer des combo Box
   paramêtres :
	$selected : l'élément sélectioné le cas échéant
   retourne une chaine de caractères contenant l'objet complet */

	public static function gen_combo_box ( $selected ) {
		global $msg;
		$requete="select idlender, lender_libelle from lenders order by lender_libelle ";
		$champ_code="idlender";
		$champ_info="lender_libelle";
		$nom="book_lender_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info= $msg['class_lender'];
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


	public static function gen_multiple_combo_box($liste_id=array()){
		global $msg,$charset;

		$requete="select idlender, lender_libelle from lenders order by lender_libelle ";
		$champ_code="idlender";
		$champ_info="lender_libelle";
		$nom="book_lender_id[]";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info= $msg['class_lender'];
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" multiple >\n" ;
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			$i=0;
			$gen_liste_str.="<option value='-1' ".(!count($liste_id) ? "selected='selected'" : "").">".htmlentities($msg['lenders_all'], ENT_QUOTES, $charset)."</option>";
			while ($i<$nb_liste) {
				$id=pmb_mysql_result($resultat_liste,$i,$champ_code);
				$gen_liste_str.="<option value=\"".$id."\" " ;
				if(in_array($id, $liste_id)) {
					$gen_liste_str.=" selected " ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;

	}

	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['lender_libelle']

		// check sur le type de la variable passée en paramètre
		if (!is_array($data) || empty($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau

		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT lender_libelle FROM lenders limit 1"),0);
		$data['lender_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['lender_libelle']))),0,$long_maxi));

		if($data['lender_libelle']=="") return 0;

		// préparation de la requête
		$key0 = addslashes($data['lender_libelle']);

		/* vérification que le lender existe */
		$query = "SELECT idlender FROM lenders WHERE lender_libelle='{$key0}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT lenders ".$query);
		$lenders  = pmb_mysql_fetch_object($result);

		/* le lender existe, on retourne l'ID */
		if($lenders->idlender) return $lenders->idlender;

		// id non-récupérée, il faut créer la forme.

		$query  = "INSERT INTO lenders SET lender_libelle='".$key0."' ";
		$result = @pmb_mysql_query($query);
		if(!$result) die("can't INSERT into lenders ".$query);

		return pmb_mysql_insert_id();

	} /* fin méthode import */

	public static function delete($id) {
		$id = intval($id);
		if($id!="") {
			$total = 0;
			$total = pmb_mysql_num_rows(pmb_mysql_query("select 1 from exemplaires where expl_owner='".$id."' limit 0,1"));
			$total = $total + pmb_mysql_num_rows(pmb_mysql_query("select 1 from docs_codestat where statisdoc_owner='".$id."' limit 0,1"));
			$total = $total + pmb_mysql_num_rows(pmb_mysql_query("select 1 from docs_location where locdoc_owner='".$id."' limit 0,1"));
			$total = $total + pmb_mysql_num_rows(pmb_mysql_query("select 1 from docs_section where sdoc_owner ='".$id."' limit 0,1"));
			$total = $total + pmb_mysql_num_rows(pmb_mysql_query("select 1 from docs_statut where statusdoc_owner ='".$id."' limit 0,1"));
			$total = $total + pmb_mysql_num_rows(pmb_mysql_query("select 1 from docs_type where tdoc_owner ='".$id."' limit 0,1"));
			if ($total==0) {
				translation::delete($id, "lenders");
				$requete = "DELETE FROM lenders WHERE idlender=$id ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('294', '1705');
				return false;
			}
		} else {
			pmb_error::get_instance(static::class)->add_message('294', '1706');
			return false;
		}
		return true;
	}

	public function get_translated_lender_libelle() {
		return translation::get_translated_text($this->idlender, 'lenders', 'lender_libelle', $this->lender_libelle);
	}
} # fin de définition de la classe serie

} # fin de délaration

