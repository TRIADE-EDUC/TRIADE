<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_accounting.class.php,v 1.7.4.1 2025/04/25 09:37:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once ($include_path."/mail.inc.php") ;

abstract class mail_accounting extends mail_root {

    protected $id_bibli;
    protected $id_acte;
    protected $acte;
    protected $bib;
    protected $coord_liv;
    protected $coord_fac;
    protected $coord_fou;
    protected $fou;

    protected function _init_default_settings() {
    	parent::_init_default_settings();
    	$this->_init_setting_value('sender', 'accounting_bib_coords');
    	$this->_init_setting_value('copy_bcc', '1');
    }

	protected function get_mail_to_name() {
		$this->mail_to_name = '';
		$coord_fou = $this->get_coord_fou();
		if($coord_fou->libelle) {
			$this->mail_to_name = $coord_fou->libelle;
		} else {
			$this->mail_to_name = $this->get_fou()->raison_sociale;
		}
		if($coord_fou->contact) $this->mail_to_name.=" ".$coord_fou->contact;
		return $this->mail_to_name;
	}

	protected function get_mail_to_mail() {
		$coord_fou = $this->get_coord_fou();
		$this->mail_to_mail=$coord_fou->email;
		return $this->mail_to_mail;
	}

	protected function get_mail_from_name() {
	    switch ($this->get_setting_value('sender')) {
	        case 'accounting_bib_coords':
	            $bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($this->id_bibli,1));
	            return $bib_coord->libelle;
	        default:
	            return parent::get_mail_from_name();
	    }
	}

	protected function get_mail_object() {
		$mail_object = $this->get_parameter_value('obj_mail');
		return static::render($mail_object, $this->get_formatted_data());
	}

	protected function get_mail_content() {
		$mail_content = $this->get_parameter_value('text_mail');
		return static::render($mail_content, $this->get_formatted_data());
	}

	protected function get_mail_from_mail() {
	    switch ($this->get_setting_value('sender')) {
	        case 'accounting_bib_coords':
	            $bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($this->id_bibli,1));
	            return $bib_coord->email;
	        default:
	            return parent::get_mail_from_mail();
	    }
	}

	protected function get_mail_headers() {
		global $charset;

		return "Content-Type: text/plain; charset=".$charset."\n";
	}

	protected function get_mail_do_nl2br() {
		return 1;
	}

	protected function get_mail_reply_name() {
		global $PMBuserprenom, $PMBusernom;

		return $PMBuserprenom." ".$PMBusernom;
	}

	protected function get_mail_reply_mail() {
		global $PMBuseremail;

		return $PMBuseremail;
	}

	public function get_formatted_data(){
	    if(empty($this->formatted_data)){
	        $this->formatted_data = array();
	        $this->formatted_data = array(
	            'obj_mail' => $this->get_parameter_value('obj_mail'),
	            'text_before' => $this->get_parameter_value('text_before'),
	            'text_after' => $this->get_parameter_value('text_after'),
	            'text_sign' => $this->get_parameter_value('text_sign'),
	            'acte' => $this->get_acte(),
	            'bib' => $this->get_bib(),
	            'fou' => $this->get_fou(),
	            'coord_liv' => $this->get_coord_liv(),
	            'coord_fac' => $this->get_coord_fac(),
	            'coord_fou' => $this->get_coord_fou()
	        );
	    }
	    return $this->formatted_data;
	}

	public function get_acte() {
	    if(!isset($this->acte)) {
	        $this->acte = new actes($this->id_acte);
	    }
	    return $this->acte;
	}

	public function get_bib() {
	    if(!isset($this->bib)) {
	        $this->bib = new entites($this->get_acte()->num_entite);
	    }
	    return $this->bib;
	}

	public function get_coord_liv() {
	    if(!isset($this->coord_liv)) {
	        $this->coord_liv = new coordonnees($this->get_acte()->num_contact_livr);
	    }
	    return $this->coord_liv;
	}

	public function get_coord_fac() {
	    if(!isset($this->coord_fac)) {
	        $this->coord_fac = new coordonnees($this->get_acte()->num_contact_fact);
	    }
	    return $this->coord_fac;
	}

	public function get_fou() {
	    if(!isset($this->fou)) {
	        $this->fou = new entites($this->get_acte()->num_fournisseur);
	    }
	    return $this->fou;
	}

	public function get_coord_fou() {
	    if(!isset($this->coord_fou)) {
	        $this->coord_fou = entites::get_coordonnees($this->get_acte()->num_fournisseur, '1');
	        $this->coord_fou = pmb_mysql_fetch_object($this->coord_fou);
	    }
	    return $this->coord_fou;
	}

	public function get_dest_mail() {
	    return $this->get_mail_to_mail();
	}

	public function set_id_bibli($id_bibli) {
		$this->id_bibli = intval($id_bibli);
	}

	public function set_id_acte($id_acte) {
		$this->id_acte = intval($id_acte);
	}
}