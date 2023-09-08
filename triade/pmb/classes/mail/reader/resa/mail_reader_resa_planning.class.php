<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_resa_planning.class.php,v 1.1 2019-04-12 13:08:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/mail/reader/resa/mail_reader_resa.class.php");

class mail_reader_resa_planning extends mail_reader_resa {
	
	protected function get_mail_object($empr) {
		global $msg;
		
		return sprintf($msg['mail_obj_resa_validee'], '');
	}
	
	protected function get_mail_content($empr) {
		global $msg, $charset;
		
		$mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
		$mail_content .= $this->get_text_madame_monsieur($empr->id_empr);
		$mail_content .= "<br />".$this->get_parameter_value('before_list');
		$mail_content .= '<hr /><strong>'.$empr->tit.'</strong>';
		$mail_content .= '<br />' ;
		$mail_content .= $msg['resa_planning_date_debut'].'  '.$empr->aff_resa_date_debut.'  '.$msg['resa_planning_date_fin'].'  '.$empr->aff_resa_date_fin ;

		$mail_content .= "<hr />".$this->get_parameter_value('after_list');
		$mail_content .= " <br />".$this->get_parameter_value('fdp');
		$mail_content .= "<br /><br />".$this->get_mail_bloc_adresse();
		$mail_content .= '</body></html>';
		return $mail_content;
	}
	
	public function send_mail($empr) {
		global $msg, $charset;
		global $biblio_name, $biblio_email, $PMBuseremailbcc;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		$mail_content = $this->get_mail_content($empr);
		$res_envoi=mailpmb($empr->empr_prenom." ".$empr->empr_nom, $empr->empr_mail, $this->get_mail_object($empr),$mail_content,$biblio_name, $biblio_email, $headers, "", $PMBuseremailbcc, 1);
		return $res_envoi;
	}
}