<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_categ.class.php,v 1.4.2.1 2024/10/18 12:34:00 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/translation.class.php");

class empr_categ {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';
	public $duree_adhesion=365;
	public $tarif_abt='0.00';
	public $age_min=0;
	public $age_max=0;
	public $pret_already_loaned_active=0;

	/* ---------------------------------------------------------------
			empr_categ($id) : constructeur
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

		$query = 'SELECT * FROM empr_categ WHERE id_categ_empr='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}

		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->libelle;
		$this->duree_adhesion = $data->duree_adhesion;
		$this->tarif_abt = $data->tarif_abt;
		$this->age_min = $data->age_min;
		$this->age_max = $data->age_max;
		$this->pret_already_loaned_active = $data->pret_already_loaned_active;
	}

	public function get_content_form() {
		global $pmb_gestion_financiere,$pmb_gestion_abonnement, $pmb_pret_already_loaned, $msg;

		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', '103')
		->add_input_node('text', $this->libelle);
		$interface_content_form->add_element('form_duree_adhesion', '1400')
		->add_input_node('integer', $this->duree_adhesion)
		->set_maxlength(10);
		if ($pmb_gestion_financiere && $pmb_gestion_abonnement==1) {
			$interface_content_form->add_element('form_tarif_adhesion', 'empr_categ_tarif')
			->add_input_node('float', $this->tarif_abt)
			->set_maxlength(10);
		}
		$interface_content_form->add_element('form_age_min', 'empr_categ_age_min')
		->add_input_node('integer', $this->age_min)
		->set_maxlength(3);
		$interface_content_form->add_element('form_age_max', 'empr_categ_age_max')
		->add_input_node('integer', $this->age_max)
		->set_maxlength(3);
		if($pmb_pret_already_loaned) {
			$interface_content_form->add_element('form_pret_already_loaned_active')
			->add_input_node('checkbox', $this->pret_already_loaned_active)
			->set_label($msg["empr_categ_pret_already_loaned_active"])
			->set_checked($this->pret_already_loaned_active == 1 ? true : false);
		}
		return $interface_content_form->get_display();
	}

	public function get_form() {
		global $msg;

		$interface_form = new interface_admin_form('categform');
		if(!$this->id){
			$interface_form->set_label($msg['524']);
		}else{
			$interface_form->set_label($msg['525']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('empr_categ')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $form_libelle, $form_duree_adhesion, $form_tarif_adhesion, $form_age_min, $form_age_max;
		global $pmb_pret_already_loaned, $form_pret_already_loaned_active;

		$this->libelle = stripslashes($form_libelle);
		$this->duree_adhesion = stripslashes($form_duree_adhesion);
		$this->tarif_abt = stripslashes($form_tarif_adhesion);
		$this->age_min = intval($form_age_min);
		$this->age_max = intval($form_age_max);
		//Si le paramètre PMB est désactivé, on ne touche pas à la donnée en base
		if($pmb_pret_already_loaned) {
			$this->pret_already_loaned_active = isset($form_pret_already_loaned_active) ? 1 : 0;
		}
	}

	public function get_query_if_exists() {
		return "SELECT count(1) FROM empr_categ WHERE (libelle='".addslashes($this->libelle)."' AND id_categ_empr!='".$this->id."' )";
	}

	public function save() {
		// O.K.,  now if item already exists UPDATE else INSERT
		if($this->id) {
			$requete = "UPDATE empr_categ SET libelle='".addslashes($this->libelle)."', duree_adhesion='".addslashes($this->duree_adhesion)."', tarif_abt='".addslashes($this->tarif_abt)."', age_min='".$this->age_min."', age_max='".$this->age_max."', pret_already_loaned_active='".$this->pret_already_loaned_active."' WHERE id_categ_empr=".$this->id;
			$res = pmb_mysql_query($requete);
		} else {
			$requete = "SELECT count(1) FROM empr_categ WHERE libelle='".addslashes($this->libelle)."' LIMIT 1 ";
			$res = pmb_mysql_query($requete);
			$nbr = pmb_mysql_result($res, 0, 0);
			if($nbr == 0) {
				$requete = "INSERT INTO empr_categ (id_categ_empr,libelle,duree_adhesion,tarif_abt,age_min, age_max, pret_already_loaned_active) VALUES ('', '".addslashes($this->libelle)."','".addslashes($this->duree_adhesion)."','".addslashes($this->tarif_abt)."','".$this->age_min."','".$this->age_max."','".$this->pret_already_loaned_active."') ";
				$res = pmb_mysql_query($requete);
				$this->id = pmb_mysql_insert_id();
			}
		}
		$translation = new translation($this->id, "empr_categ");
		$translation->update("libelle", "form_libelle");
	}

	public static function check_data_from_form() {
		global $form_libelle;

		if(empty($form_libelle)) {
			return false;
		}
		return true;
	}

	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_categ ='".$id."' "), 0, 0);
			if ($total==0) {
				$test = pmb_mysql_result(pmb_mysql_query("select count(1) from search_persopac_empr_categ where id_categ_empr ='".$id."' "), 0, 0);
				if($test == 0){
					translation::delete($id, "empr_categ");
					$requete = "DELETE FROM empr_categ WHERE id_categ_empr='$id' ";
					pmb_mysql_query($requete);
					$requete = "OPTIMIZE TABLE empr_categ ";
					pmb_mysql_query($requete);
					$requete = "delete from search_persopac_empr_categ where id_categ_empr = $id";
					pmb_mysql_query($requete);
					return true;
				}else{
					pmb_error::get_instance(static::class)->add_message('294', 'empr_categ_cant_delete_search_perso');
					return false;
				}
			} else {
				pmb_error::get_instance(static::class)->add_message('294', '1708');
				return false;
			}
		}
		return true;
	}

	public function get_translated_libelle() {
		return translation::get_translated_text($this->id, 'empr_categ', 'libelle', $this->libelle);
	}

} /* fin de définition de la classe */