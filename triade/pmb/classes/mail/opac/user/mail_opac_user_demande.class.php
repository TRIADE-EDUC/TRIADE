<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_demande.class.php,v 1.3 2023/09/08 06:06:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_demande extends mail_opac_user {
	
	protected $demande;
	
	protected function _init_default_settings() {
	    global $opac_biblio_email;
	    
		parent::_init_default_settings();
		if(!empty($opac_biblio_email)) {
		    $this->_init_setting_value('sender', 'parameter');
		} else {
		    $this->_init_setting_value('sender', 'docs_location');
		}
		$this->_init_setting_value('reply', 'reader');
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg['demandes_mail_new_demande_object'];
	}
	
	protected function get_permalink() {
		global $pmb_url_base;
		
		return $pmb_url_base.'demandes.php?categ=gestion&act=see_dmde&iddemande='.$this->demande->get_id();
	}
	
	protected function get_mail_content() {
		global $msg, $empr_nom, $empr_prenom;
		
		$mail_content =  $msg['demandes_mail_new_demande'];
		$mail_content = str_replace("!!nom!!", $empr_prenom." ".$empr_nom." ", $mail_content);
		$mail_content = str_replace("!!titre_demande!!", $this->demande->get_titre_demande(), $mail_content);
		$mail_content .= '<br />'.$this->demande->get_sujet_demande().'<br />';
		$mail_content .= '<a href="'.$this->get_permalink().'">'.$msg['demandes_see_last_note'].'</a>';
		return $mail_content;
	}
	
	protected function get_mail_headers() {
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
		return $headers;
	}
	
	public function set_demande($demande) {
		$this->demande = $demande;
		return $this;
	}
}