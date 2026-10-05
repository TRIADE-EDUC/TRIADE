<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_reader_readinglist_request_access.class.php,v 1.2 2022/08/01 06:44:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_reader_readinglist_request_access extends mail_opac_reader_readinglist {
	
	protected function get_mail_object() {
		global $msg;
		
		return sprintf($msg['list_lecture_objet_mail'], $this->get_nom_liste());
	}
	
	protected function get_mail_content() {
		global $msg;
		
		$mail_content = $this->get_intro_mail_content();
		$mail_content .= ", <br />".sprintf($msg['list_lecture_corps_mail'], $this->get_sender_reader_name(), $this->get_nom_liste());
		$mail_content .= $this->get_com_mail_content();
		$mail_content .= "<br /><br /><a href='".$this->get_url_connexion_auto()."&tab=lecture&lvl=demande_list' >".$msg['list_lecture_activation_mail']."</a>";
		
		return $mail_content;
	}	
}