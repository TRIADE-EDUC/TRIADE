<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_demande_note.class.php,v 1.3 2023/09/08 06:06:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_demande_note extends mail_opac_user {
	
	protected $demande_note;
	
	protected function _init_default_settings() {
	    global $opac_biblio_email;
	    
		parent::_init_default_settings();
		if(!empty($opac_biblio_email)) {
		    $this->_init_setting_value('sender', 'parameter');
		} else {
		    $this->_init_setting_value('sender', 'docs_location');
		}
		if($this->get_mail_copy_bcc()) {
			$this->_init_setting_value('copy_bcc', '1');
		}
		$this->_init_setting_value('reply', 'reader');
	}

	protected function get_mail_object() {
		global $msg;
		
		return $msg['demandes_note_mail_new_object'];
	}
	
	protected function get_permalink() {
		global $pmb_url_base;
		
		return $pmb_url_base.'demandes.php?categ=gestion&act=see_dmde&iddemande='.$this->demande_note->num_demande.'&last_modified='.$this->demande_note->num_action.'#fin';
	}
	
	protected function get_mail_content() {
		global $msg, $empr_nom, $empr_prenom;
		
		$mail_content = sprintf($msg['demandes_note_mail_new'],$empr_prenom." ".$empr_nom." " ,$this->demande_note->libelle_action,$this->demande_note->libelle_demande).'<br />';
		$mail_content .= $this->demande_note->contenu.'<br />';
		$mail_content .= '<a href="'.$this->get_permalink().'">'.$msg['demandes_see_last_note'].'</a>';
		
		return $mail_content;
	}
	
	protected function get_mail_copy_bcc() {
		global $demandes_email_generic;
		
		$bcc="";
		if ($demandes_email_generic) {
			$deg=explode(",",$demandes_email_generic);
			if (($deg[0]==2)||($deg[0]==3)) {
				$bcc=$deg[1];
			}
		}
		return $bcc;
	}
	
	protected function get_mail_headers() {
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
		return $headers;
	}
	
	public function set_demande_note($demande_note) {
		$this->demande_note = $demande_note;
		return $this;
	}
}