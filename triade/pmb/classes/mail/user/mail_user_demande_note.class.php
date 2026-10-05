<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_user_demande_note.class.php,v 1.2 2022/08/01 06:44:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_user_demande_note extends mail_user {
	
	protected $demande_note;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
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
		global $msg, $PMBuserprenom, $PMBusernom;
		
		$mail_content = sprintf($msg['demandes_note_mail_new'],$PMBuserprenom." ".$PMBusernom." ",$this->demande_note->libelle_action,$this->demande_note->libelle_demande).'<br />';
		$mail_content .= $this->demande_note->contenu.'<br />';
		$mail_content .= '<a href="'.$this->get_permalink().'">'.$msg['demandes_see_last_note'].'</a>';
		
		return $mail_content;
	}
	
	public function set_demande_note($demande_note) {
		$this->demande_note = $demande_note;
		return $this;
	}
}