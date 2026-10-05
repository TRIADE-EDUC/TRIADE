<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_reader_readinglist.class.php,v 1.3 2023/09/08 06:06:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/liste_lecture.class.php");

abstract class mail_opac_reader_readinglist extends mail_opac_reader {
	
	protected $id_liste;
	
	protected $nom_liste;
	
	protected $owner;
	
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

	protected function get_owner() {
		if(empty($this->owner)) {
			$query = "select concat(empr_prenom,' ',empr_nom) as nom, empr_mail, nom_liste from empr e, opac_liste_lecture oll where oll.num_empr=e.id_empr and id_liste='".$this->id_liste."'";
			$result = pmb_mysql_query($query);
			$this->owner = pmb_mysql_fetch_object($result);
		}
		return $this->owner;
	}
	
	protected function get_mail_to_login() {
		$query ="SELECT empr_login FROM empr WHERE id_empr='".$this->mail_to_id."'";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 'empr_login');
	}
	
	protected function get_url_connexion_auto() {
		global $opac_connexion_phrase, $opac_url_base;
	
		$date = time();
		$login = $this->get_mail_to_login();
		$code=md5($opac_connexion_phrase.$login.$date);
		return $opac_url_base."empr.php?code=".$code."&emprlogin=".$login."&date_conex=".$date;
	}
	
	protected function get_intro_mail_content() {
		global $msg;
		
		return sprintf($msg['list_lecture_intro_mail'], $this->get_mail_to_name(), $this->get_nom_liste());
	}
	
	protected function get_sender_reader_name() {
		global $empr_prenom, $empr_nom;
		
		return $empr_prenom." ".$empr_nom;
	}
	
	protected function get_com_mail_content() {
		global $com, $msg;
		
		if($com) {
			return sprintf("<br />".$msg['list_lecture_corps_com_mail'], $this->get_sender_reader_name(),"<br />".stripslashes($com)."<br />");
		}
		return '';
	}
	
	public function set_id_liste($id_liste) {
		$this->id_liste = intval($id_liste);
		return $this;
	}
	
	protected function get_nom_liste() {
		if(empty($this->nom_liste)) {
			$this->nom_liste = liste_lecture::get_name_from_id($this->id_liste);
		}
		return $this->nom_liste;
	}
}