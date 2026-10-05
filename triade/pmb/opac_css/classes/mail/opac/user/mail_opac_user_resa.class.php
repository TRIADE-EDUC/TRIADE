<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_resa.class.php,v 1.3 2023/10/24 09:57:08 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_resa extends mail_opac_user {
	
	protected $id_notice;
	
	protected $id_bulletin;
	
	protected $empr;
	
	protected $annul;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'docs_location');
	}
	
	protected function get_mail_object() {
		global $msg;
		
		if ($this->annul == 1) {
			$mail_object = $msg["mail_obj_resa_canceled"];
		} elseif ($this->annul == 2) {
			$mail_object = $msg["mail_obj_resa_reaffected"];
		} else {
			$mail_object = $msg["mail_obj_resa_added"];
		}
		return $mail_object." ".$this->empr->aff_quand;
	}
	
	protected function get_gestion_permalink() {
		global $pmb_url_base;
		
		$permalink = '';
		if ($this->id_notice) {
			$niveau_biblio = notice::get_niveau_biblio(intval($this->id_notice));
			switch ($niveau_biblio) {
				case 'a':
					$query = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice = ".intval($this->id_notice);
					$bul_id = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
					$permalink = $pmb_url_base."catalog.php?categ=serials&sub=analysis&action=analysis_form&bul_id=".intval($bul_id)."&analysis_id=".intval($this->id_notice);
					break;
				case 's':
					$permalink = $pmb_url_base."catalog.php?categ=serials&sub=view&serial_id=".intval($this->id_notice);
					break;
				case 'm':
				default:
					$permalink = $pmb_url_base."catalog.php?categ=isbd&id=".intval($this->id_notice);
					break;
			}
		} else {
			$permalink = $pmb_url_base."catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".intval($this->id_bulletin);
		}
		return $permalink;
	}
	
	protected function get_gestion_empr_permalink() {
		global $pmb_url_base;
		
		return $pmb_url_base."circ.php?categ=pret&form_cb=".$this->empr->empr_cb;
	}
	
	protected function get_mail_content() {
		global $msg, $charset, $opac_url_base;
		global $pmb_transferts_actif, $transferts_choix_lieu_opac;
		global $idloc;
		
		$mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
		if ($this->annul == 1) {
			$mail_content .= "<a href='".$opac_url_base."'><span style='color:red'><strong>".$this->get_mail_object();
		} elseif ($this->annul == 2) {
			$mail_content .= "<a href='".$opac_url_base."'><span style='color:blue'><strong>".$this->get_mail_object();
		} else {
			$mail_content .= "<a href='".$this->get_gestion_permalink()."'><span style='color:green'><strong>".$this->get_mail_object();
		}
		$mail_content .= "</strong></span></a> ".$this->empr->aff_quand."
									<br /><a href='".$this->get_gestion_empr_permalink()."'><strong>".$this->empr->empr_prenom." ".$this->empr->empr_nom."</strong></a>
									<br /><i>".$this->empr->empr_mail." / ".$this->empr->empr_tel1." / ".$this->empr->empr_tel2."</i>";
		if ($this->empr->empr_cp || $this->empr->empr_ville) {
			$mail_content .= "<br /><u>".$this->empr->empr_cp." ".$this->empr->empr_ville."</u>";
		}
		$mail_content .= "<hr />".$msg['resa_empr_location'].": ".$this->empr->location_libelle;
		if (($pmb_transferts_actif=="1")&&($transferts_choix_lieu_opac=="1")) {
			$docs_location = new docs_location($idloc);
			$mail_content .= "<br />".$msg['resa_loc_retrait'].": ".$docs_location->libelle;
		}
		$mail_content .= "<hr />";
		if (!empty($this->id_notice)) {
			record_display::init_record_datas($this->id_notice);
			$current = new notice_affichage($this->id_notice,array(),0,1);
			$current->do_header();
			$current->do_isbd(1,1);
			$mail_content .= "<h3>".$current->notice_header."</h3>";
			$mail_content .= $current->notice_isbd;
			$mail_content .= $current->affichage_expl ;
		} else {
			$mail_content .= bulletin_affichage_reduit($this->id_bulletin) ;
		}
		$mail_content .= "<hr /></body></html> ";
		return $mail_content;
	}
	
	protected function get_mail_do_nl2br() {
		return 1;
	}
	
	public function set_id_notice($id_notice) {
		$this->id_notice = intval($id_notice);
		return $this;
	}
	
	public function set_id_bulletin($id_bulletin) {
		$this->id_bulletin = intval($id_bulletin);
		return $this;
	}
	
	public function set_empr($empr) {
		$this->empr = $empr;
		return $this;
	}
	
	public function set_annul($annul) {
		$this->annul = $annul;
		return $this;
	}
}