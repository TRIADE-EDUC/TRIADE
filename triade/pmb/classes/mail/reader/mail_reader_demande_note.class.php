<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_demande_note.class.php,v 1.3 2022/09/08 08:04:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_demande_note extends mail_reader {
	
	protected $demande_note;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
		$this->_init_setting_value('copy_bcc', '0');
	}

	protected function get_mail_object() {
		global $msg;
		
		return $msg['demandes_note_mail_new_object'];
	}
	
	protected function get_permalink() {
		global $opac_url_base;
		
		return $opac_url_base.'empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande='.$this->demande_note->num_demande.'&last_modified='.$this->demande_note->num_action.'#fin';
	}
	
	protected function get_mail_content() {
		global $msg, $PMBuserprenom, $PMBusernom;
		
		$mail_content = sprintf($msg['demandes_note_mail_new'],$PMBuserprenom." ".$PMBusernom." ",$this->demande_note->libelle_action,$this->demande_note->libelle_demande).'<br />';
		$mail_content .= $this->demande_note->contenu.'<br />';
		$mail_content .= '<a href="'.$this->get_permalink().'">'.$msg['demandes_see_last_note'].'</a>';
		
		return $mail_content;
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