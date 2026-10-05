<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader.class.php,v 1.10.2.4 2024/09/24 13:17:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/group.class.php");

abstract class mail_reader extends mail_root {
	
	protected $id_group;
	
	protected $empr;
	
	protected $group;
	
	
    protected function _init_default_parameters() {
        $this->_init_parameter_value('sign_address', 1);
    }
    
    protected function _init_default_settings() {
    	parent::_init_default_settings();
    	$this->_init_setting_value('sender', 'docs_location');
    	$this->_init_setting_value('copy_bcc', '1');
    }
    
	protected function get_empr_coords() {
		global $msg;
		
		/* Récupération du nom, prénom et mail de l'utilisateur */
		$query = "select id_empr, empr_mail, empr_nom, empr_prenom, empr_lang, empr_cb, empr_login, empr_location,";
		$query .= "date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_date_expiration ";
		$query .= "from empr ";
		if (!empty($this->id_group)) {
			$query .= ", groupe where empr.id_empr=groupe.resp_groupe and id_groupe=".$this->id_group;
		} else {
			$query .= "where id_empr=".$this->mail_to_id;
		}
		$result = pmb_mysql_query($query);
		return pmb_mysql_fetch_object($result);
	}
	
	protected function get_text_madame_monsieur() {
		$query = "select empr_nom, empr_prenom from empr where id_empr='".$this->mail_to_id."'";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$text_madame_monsieur=str_replace("!!empr_name!!", $row->empr_nom,$this->get_parameter_value('madame_monsieur'));
		$text_madame_monsieur=str_replace("!!empr_first_name!!", $row->empr_prenom,$text_madame_monsieur);
		return $text_madame_monsieur;
	}
	
	protected function get_mail_bloc_adresse() {
		if($this->get_parameter_value('sign_address')) {
	        return mail_bloc_adresse();
	    }
	    return '';
	}
	
	protected function get_mail_to_name() {
		$coords = $this->get_empr_coords();
		return $coords->empr_prenom." ".$coords->empr_nom;
	}
	
	protected function get_mail_to_mail() {
		$coords = $this->get_empr_coords();
		return $coords->empr_mail;
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		return "Content-type: text/plain; charset=".$charset."\n";
	}
	
	public function send_mail() {
	    $coords = $this->get_empr_coords();
	    if($coords->empr_lang) {
	        $this->set_language($coords->empr_lang);
	    }
	    $sended = $this->mailpmb();
	    $this->restaure_language();
	    return $sended;
	}
	
	protected function get_mail_do_nl2br() {
		return 1;
	}
	
	public function get_display_sent_succeed() {
		global $msg;
		
		return "<h3>".sprintf($msg["mail_retard_succeed"], $this->get_mail_to_mail())."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a><br /><br />".nl2br($this->get_mail_content());
	}
	
	public function get_display_sent_failed() {
		global $msg;
		
		return "<h3>".sprintf($msg["mail_retard_failed"], $this->get_mail_to_mail())."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
	}
	
	public function get_display_unknown_mail() {
		global $msg;
		
		return "<h3>".sprintf($msg["mail_retard_unknown_mail"], $this->get_mail_to_name())."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
	}
	
	public function set_id_group($id_group) {
		$this->id_group = intval($id_group);
		
		//On modifie le mail_to_id
		$coords = $this->get_empr_coords();
		$this->mail_to_id = $coords->id_empr;
		return $this;
	}
	
	public function set_empr($empr) {
		$this->empr = $empr;
		return $this;
	}
	
	public function get_group() {
		if(!isset($this->group)) {
			$this->group = new group($this->id_group);
		}
		return $this->group;
	}
			
	public function set_group($group) {
		$this->group = $group;
		return $this;
	}
}