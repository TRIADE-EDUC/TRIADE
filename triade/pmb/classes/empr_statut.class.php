<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_statut.class.php,v 1.4 2023/07/05 15:32:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

class empr_statut {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';
	public $allow_loan=1;
	public $allow_loan_hist=0;
	public $allow_book=1;
	public $allow_opac=1;
	public $allow_dsi=1;
	public $allow_dsi_priv=1;
	public $allow_sugg=1;
	public $allow_dema=1;
	public $allow_prol=1;
	public $allow_avis=1;
	public $allow_tag=1;
	public $allow_pwd=1;
	public $allow_liste_lecture=1;
	public $allow_self_checkout=0;
	public $allow_self_checkin=0;
	public $allow_serialcirc=0;
	public $allow_scan_request=0;
	public $allow_contribution=0;
	public $allow_pnb=0;
	
	/* ---------------------------------------------------------------
			empr_statut($id) : constructeur
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
	
		$query = 'SELECT * FROM empr_statut WHERE idstatut='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
			
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->statut_libelle;
		$this->allow_loan = $data->allow_loan;
		$this->allow_loan_hist = $data->allow_loan_hist;
		$this->allow_book = $data->allow_book;
		$this->allow_opac = $data->allow_opac;
		$this->allow_dsi = $data->allow_dsi;
		$this->allow_dsi_priv = $data->allow_dsi_priv;
		$this->allow_sugg = $data->allow_sugg;
		$this->allow_dema = $data->allow_dema;
		$this->allow_prol = $data->allow_prol;
		$this->allow_avis = $data->allow_avis;
		$this->allow_tag = $data->allow_tag;
		$this->allow_pwd = $data->allow_pwd;
		$this->allow_liste_lecture = $data->allow_liste_lecture;
		$this->allow_self_checkout = $data->allow_self_checkout;
		$this->allow_self_checkin = $data->allow_self_checkin;
		$this->allow_serialcirc = $data->allow_serialcirc;
		$this->allow_scan_request = $data->allow_scan_request;
		$this->allow_contribution = $data->allow_contribution;
		$this->allow_pnb = $data->allow_pnb;
	}

	protected function get_list_allow() {
		return array( 
				'allow_loan' => 'empr_allow_loan',
				'allow_loan_hist' => 'empr_allow_loan_hist',
				'allow_book' => 'empr_allow_book',
				'allow_opac' => 'empr_allow_opac',
				'allow_dsi' => 'empr_allow_dsi',
				'allow_dsi_priv' => 'empr_allow_dsi_priv',
				'allow_sugg' => 'empr_allow_sugg',
				'allow_dema' => 'empr_allow_dema',
				'allow_liste_lecture' => 'empr_allow_liste_lecture',
				'allow_prol' => 'empr_allow_prol',
				'allow_avis' => 'empr_allow_avis',
				'allow_tag' => 'empr_allow_tag',
				'allow_pwd' => 'empr_allow_pwd',
				'allow_self_checkout' => 'empr_allow_self_checkout',
				'allow_self_checkin' => 'empr_allow_self_checkin',
				'allow_serialcirc' => 'empr_allow_serialcirc',
				'allow_scan_request' => 'empr_allow_scan_request',
				'allow_contribution' => 'empr_allow_contribution',
				'allow_pnb' => 'empr_allow_pnb'
		);
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('statut_libelle', '103')
		->add_input_node('text', $this->libelle);
		
		$list_allow = $this->get_list_allow();
		foreach ($list_allow as $property=>$label_code) {
			$interface_content_form->add_element($property)
			->add_input_node('boolean', $this->{$property})->set_label_code($label_code);
		}
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('statutform');
		if(!$this->id){
			$interface_form->set_label($msg['empr_statut_create']);
		}else{
			$interface_form->set_label($msg['empr_statut_modif']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('empr_statut')
		->set_field_focus('statut_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $statut_libelle;
		global $allow_loan, $allow_loan_hist, $allow_book, $allow_opac, $allow_dsi, $allow_dsi_priv;
		global $allow_sugg, $allow_dema, $allow_prol, $allow_avis, $allow_tag, $allow_pwd;
		global $allow_liste_lecture, $allow_self_checkout, $allow_self_checkin, $allow_serialcirc;
		global $allow_scan_request, $allow_contribution, $allow_pnb;
		
		$this->libelle = stripslashes($statut_libelle);
		$this->allow_loan = intval($allow_loan);
		$this->allow_loan_hist = intval($allow_loan_hist);
		$this->allow_book = intval($allow_book);
		$this->allow_opac = intval($allow_opac);
		$this->allow_dsi = intval($allow_dsi);
		$this->allow_dsi_priv = intval($allow_dsi_priv);
		$this->allow_sugg = intval($allow_sugg);
		$this->allow_dema = intval($allow_dema);
		$this->allow_prol = intval($allow_prol);
		$this->allow_avis = intval($allow_avis);
		$this->allow_tag = intval($allow_tag);
		$this->allow_pwd = intval($allow_pwd);
		$this->allow_liste_lecture = intval($allow_liste_lecture);
		$this->allow_self_checkout = intval($allow_self_checkout);
		$this->allow_self_checkin = intval($allow_self_checkin);
		$this->allow_serialcirc = intval($allow_serialcirc);
		$this->allow_scan_request = intval($allow_scan_request);
		$this->allow_contribution = intval($allow_contribution);
		$this->allow_pnb = intval($allow_pnb);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM empr_statut WHERE (statut_libelle='".addslashes($this->libelle)."' AND idstatut!='".$this->id."' )";
	}
	
	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE empr_statut SET statut_libelle='".addslashes($this->libelle)."', allow_loan='".$this->allow_loan."', allow_loan_hist='".$this->allow_loan_hist."', allow_book='".$this->allow_book."', allow_opac='".$this->allow_opac."', allow_dsi='".$this->allow_dsi."', allow_dsi_priv='".$this->allow_dsi_priv."', allow_sugg='".$this->allow_sugg."', allow_dema='".$this->allow_dema."', allow_prol='".$this->allow_prol."', allow_avis='".$this->allow_avis."', allow_tag='".$this->allow_tag."', allow_pwd='".$this->allow_pwd."', allow_liste_lecture='".$this->allow_liste_lecture."', allow_self_checkout='".$this->allow_self_checkout."', allow_self_checkin='".$this->allow_self_checkin."', allow_serialcirc='".$this->allow_serialcirc."', allow_scan_request='".$this->allow_scan_request."', allow_contribution = '".$this->allow_contribution."', allow_pnb='".$this->allow_pnb."' WHERE idstatut=".$this->id;
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO empr_statut set idstatut=0, statut_libelle='".addslashes($this->libelle)."', allow_loan='".$this->allow_loan."', allow_loan_hist='".$this->allow_loan_hist."', allow_book='".$this->allow_book."', allow_opac='".$this->allow_opac."', allow_dsi='".$this->allow_dsi."', allow_dsi_priv='".$this->allow_dsi_priv."', allow_sugg='".$this->allow_sugg."', allow_dema='".$this->allow_dema."', allow_prol='".$this->allow_prol."', allow_avis='".$this->allow_avis."', allow_tag='".$this->allow_tag."', allow_pwd='".$this->allow_pwd."', allow_liste_lecture='".$this->allow_liste_lecture."', allow_self_checkout='".$this->allow_self_checkout."', allow_self_checkin='".$this->allow_self_checkin."', allow_serialcirc='".$this->allow_serialcirc."', allow_scan_request='".$this->allow_scan_request."', allow_contribution = '".$this->allow_contribution."', allow_pnb='".$this->allow_pnb."' ";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, "empr_statut");
		$translation->update("statut_libelle", "statut_libelle");
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id>2) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_statut ='".$id."' "), 0, 0);
			if ($total==0) {
				translation::delete($id, "empr_statut");
				$requete = "DELETE FROM empr_statut WHERE idstatut=$id ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message("", 'empr_statut_del_impossible');
				return false;
			}
		} else {
			pmb_error::get_instance(static::class)->add_message("", 'empr_statut_del_1_2_impossible');
			return false;
		}
		return true;
	}
	
	public function get_translated_libelle() {
		return translation::get_translated_text($this->id, 'empr_statut', 'statut_libelle', $this->libelle);
	}
	
} /* fin de définition de la classe */