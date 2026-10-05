<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_resa_planning.class.php,v 1.4 2022/08/01 06:44:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_resa_planning extends mail_reader_resa {
	
	protected function get_mail_object() {
		global $msg;
		
		return sprintf($msg['mail_obj_resa_validee'], '');
	}
	
	protected function get_mail_content() {
		global $msg, $charset;
		
		$mail_content = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
		$mail_content .= $this->get_text_madame_monsieur();
		$mail_content .= "<br />".$this->get_parameter_value('before_list');
		$mail_content .= '<hr /><strong>'.$this->empr->tit.'</strong>';
		$mail_content .= '<br />' ;
		$mail_content .= $msg['resa_planning_date_debut'].'  '.$this->empr->aff_resa_date_debut.'  '.$msg['resa_planning_date_fin'].'  '.$this->empr->aff_resa_date_fin ;

		$mail_content .= "<hr />".$this->get_parameter_value('after_list');
		$mail_content .= " <br />".$this->get_parameter_value('fdp');
		$mail_content .= "<br /><br />".$this->get_mail_bloc_adresse();
		$mail_content .= '</body></html>';
		return $mail_content;
	}
}