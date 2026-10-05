<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_accounting_devis.class.php,v 1.4 2022/08/01 06:44:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_accounting_devis extends mail_accounting {
	
    protected static function get_parameter_prefix() {
		return "acquisition_pdfdev";
	}
	
	protected function get_mail_attachments() {
	    $lettre = lettreDevis_factory::make();
	    $lettre->doLettre($this->id_bibli, $this->id_acte);
	    $piece_jointe=array();
	    $piece_jointe[0]['contenu']=$lettre->getLettre('S');
	    $piece_jointe[0]['nomfichier']=$lettre->getFileName();
	    return $piece_jointe;
	}
}