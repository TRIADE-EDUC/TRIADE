<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_resa.class.php,v 1.8 2022/12/13 08:47:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once ($include_path."/mail.inc.php") ;

class mail_reader_resa extends mail_reader {
	
    protected static function get_parameter_prefix() {
		return "pdflettreresa";
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		return $headers;
	}
	
	protected function get_mail_object() {
		global $msg, $charset;
		
		return sprintf($msg["mail_obj_resa_validee"], " : ".$this->empr->empr_prenom." ".mb_strtoupper($this->empr->empr_nom,$charset)." (".$this->empr->empr_cb.")");
	}
	
	protected function get_mail_content() {
		global $msg, $charset;
		global $pmb_transferts_actif, $transferts_choix_lieu_opac;
		global $opac_url_base;
		
		$empr = $this->empr;
		
		$mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
		$mail_content .= $this->get_text_madame_monsieur();
		$mail_content .= "<br />".$this->get_parameter_value('before_list');
		if($empr->niveau_biblio == 'm' || $empr->niveau_biblio == 'b'){
			$affichage=new mono_display($empr->id_notice,0,'','','','','','','','','','','',true,'','');
			$mail_content .= "<hr /><strong><a href='" . $opac_url_base . "index.php?lvl=notice_display&id=" . $empr->id_notice . "'>" . $affichage->header . "</a></strong>";
		} elseif($empr->niveau_biblio == 's' || $empr->niveau_biblio == 'a'){
			$affichage_perio=new serial_display($empr->id_notice,0);
			$mail_content .= "<hr /><strong><a href='" . $opac_url_base . "index.php?lvl=notice_display&id=" . $empr->id_notice . "'>" . $affichage_perio->header . "</a></strong>";
		}
		
		$rqt_detail = "select resa_confirmee, resa_cb,location_libelle, expl_cote from resa
		left join exemplaires on expl_cb=resa_cb
		left join docs_location on idlocation=expl_location
		where id_resa =$empr->id_resa  and resa_cb is not null and resa_cb!='' ";
		$res_detail = pmb_mysql_query($rqt_detail) ;
		$expl_detail = pmb_mysql_fetch_object($res_detail);
		
		$mail_content .= "<br />";
		$mail_content .= strip_tags($msg['291']." : ".$expl_detail->resa_cb." ".$msg['296']." : ".$expl_detail->expl_cote);
		$mail_content .= "<br />";
		$mail_content .= $msg['fpdf_reserv_enreg']." ".$empr->aff_resa_date_resa." - ".$msg['fpdf_valable_debut']." ".$empr->aff_resa_date_debut." - ".$msg['fpdf_valable']." - ".$empr->aff_resa_date_fin ;
		$lieu_retrait="";
		if($pmb_transferts_actif && $transferts_choix_lieu_opac==3) {
			$rqt = "select resa_confirmee, resa_cb,resa_loc_retrait from resa where id_resa in (".$empr->id_resa.")  and resa_cb is not null and resa_cb!='' ";
			$res = pmb_mysql_query($rqt) ;
			if(($resa_lue = pmb_mysql_fetch_object($res))) {
				if ($resa_lue->resa_confirmee) {
					if ($resa_lue->resa_loc_retrait) {
						$loc_retait=$resa_lue->resa_loc_retrait;
					} else {
						$rqt = "select expl_location from exemplaires where expl_cb='".$resa_lue->resa_cb."' ";
						$res = pmb_mysql_query($rqt) ;
						if(($res_expl = pmb_mysql_fetch_object($res))) {
							$loc_retait=$res_expl->expl_location;
						}
					}
					$rqt = "select location_libelle from docs_location where idlocation=".$loc_retait;
					$res = pmb_mysql_query($rqt) ;
					if(($res_expl = pmb_mysql_fetch_object($res))) {
						$lieu_retrait=str_replace("!!location!!",$res_expl->location_libelle,$msg["resa_lettre_lieu_retrait"]);
					}
				}
			}
		} else {
			$lieu_retrait=str_replace("!!location!!",$expl_detail->location_libelle,$msg["resa_lettre_lieu_retrait"]);
		}
		$mail_content .= "<br />$lieu_retrait<br /><hr />";
		$mail_content .= $this->get_parameter_value('after_list')." <br />";
		$mail_content .= $this->get_parameter_value('fdp');
		$mail_content .= "<br /><br />".$this->get_mail_bloc_adresse();
		$mail_content .= "</body></html> ";
		return $mail_content;
	}
	
	public function send_mail() {
		if(static::class == 'mail_reader_resa' && !is_resa_confirme($this->empr->id_resa)) {
			return false;
		}
		return parent::send_mail();
	}
}