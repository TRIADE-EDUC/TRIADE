<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: type_abt.class.php,v 1.3.10.2 2025/01/30 11:20:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class type_abt {
	
	/* ---------------------------------------------------------------
	 propriétés de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $libelle='';
	public $prepay=0;
	public $prepay_deflt_mnt=0;
	public $tarif=0;
	public $commentaire='';
	public $caution=0;
	public $localisations='';
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
	 getData() : récupération des propriétés
	 --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		$requete = 'SELECT * FROM type_abts WHERE id_type_abt='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->type_abt_libelle;
		$this->prepay = $data->prepay;
		$this->prepay_deflt_mnt = $data->prepay_deflt_mnt;
		$this->tarif = $data->tarif;
		$this->commentaire = $data->commentaire;
		$this->caution = $data->caution;
		$this->localisations = $data->localisations;
	}
	
	public function get_form() {
		global $msg;
		global $finance_abts_content_form ;
		global $charset;
		
		$content_form = $finance_abts_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_admin_form('type_abts_form');
		if(!$this->id){
			$interface_form->set_label($msg['type_abts_add']);
		}else{
			$interface_form->set_label($msg['type_abts_update']);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->libelle, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!commentaire!!', htmlentities($this->commentaire,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!prepay_checked!!', ($this->prepay ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!prepay_deflt_mnt!!', htmlentities($this->prepay_mnt_deflt,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!tarif!!', htmlentities($this->tarif,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!caution!!', htmlentities($this->caution,ENT_QUOTES, $charset), $content_form);
		
		//Localisations
		$loc_checkbox="";
		$loc=explode(",",$this->localisations);
		$requete="select idlocation, location_libelle from docs_location";
		$resultat=pmb_mysql_query($requete);
		$n=0;
		$c=0;
		if ($resultat) {
			while ($l=pmb_mysql_fetch_object($resultat)) {
				if ($c==0) $loc_checkbox.="<div class='row'>";
				$loc_checkbox.="<div class='colonne3'>";
				$loc_checkbox.="<input type='checkbox' name='localisation[]' id='l_$n' value='".$l->idlocation."' ";
				$as=array_search($l->idlocation,$loc);
				if (($as!==false)&&($as!==null)) $loc_checkbox.="checked";
				$loc_checkbox.=">";
				$loc_checkbox.="<label class='class='etiquette' for='l_$n'>".htmlentities($l->location_libelle,ENT_QUOTES,$charset)."</label>&nbsp;";
				$loc_checkbox.="</div>";
				$n++;
				$c++;
				if ($c==3) {
					$c=0;
					$loc_checkbox.="</div>";
				}
			}
			if ($c!=0) $loc_checkbox.="<div class='colonne_suite'>&nbsp;</div></div>";
			$loc_checkbox.="<div class='row'></div>";
		}
		$content_form = str_replace('!!localisations!!', $loc_checkbox, $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($content_form)
		->set_table_name('type_abts')
		->set_field_focus('typ_abt_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $typ_abt_libelle, $prepay, $prepay_deflt_mnt, $tarif, $commentaire;
		global $caution, $localisation;
		
		$this->libelle = stripslashes($typ_abt_libelle);
		$this->prepay = stripslashes($prepay);
		$this->prepay_deflt_mnt = stripslashes($prepay_deflt_mnt);
		$this->tarif = stripslashes($tarif);
		$this->commentaire = stripslashes($commentaire);
		$this->caution = stripslashes($caution);
		$this->localisations = (is_countable($localisation) && is_array($localisation) ? implode(",",$localisation) : '');
	}
	
	public function save() {
		if($this->id) {
			$requete = "UPDATE type_abts SET type_abt_libelle='".addslashes($this->libelle)."', prepay='".addslashes($this->prepay)."', prepay_deflt_mnt='".addslashes($this->prepay_deflt_mnt)."', tarif='".addslashes($this->tarif)."', commentaire='".addslashes($this->commentaire)."', caution='".addslashes($this->caution)."', localisations='".addslashes($this->localisations)."' WHERE id_type_abt=".$this->id;
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO type_abts (id_type_abt,type_abt_libelle, prepay, prepay_deflt_mnt, tarif, commentaire, caution, localisations) VALUES ('', '".addslashes($this->libelle)."','".addslashes($this->prepay)."', '".addslashes($this->prepay_deflt_mnt)."', '".addslashes($this->tarif)."', '".addslashes($this->commentaire)."', '".addslashes($this->caution)."','".addslashes($this->localisations)."') ";
			pmb_mysql_query($requete);
		}
	}
	
	public static function check_data_from_form() {
		global $typ_abt_libelle;
		
		if(empty($typ_abt_libelle)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where type_abt ='".$id."' "), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM type_abts WHERE id_type_abt=$id ;";
				pmb_mysql_query($requete);
				$requete = "OPTIMIZE TABLE type_abts ";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('type_abts_type', 'type_abts_del_error');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */