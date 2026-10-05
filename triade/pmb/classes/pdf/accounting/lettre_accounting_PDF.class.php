<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_accounting_PDF.class.php,v 1.9 2022/09/20 08:13:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/lettre_PDF.class.php");
require_once("$class_path/entites.class.php");
require_once("$class_path/coordonnees.class.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/types_produits.class.php");

class lettre_accounting_PDF extends lettre_PDF {
	
	public $x_logo = 10;				//Distance du logo / bord gauche de page
	public $y_logo = 10;				//Distance du logo / bord haut de page
	public $l_logo = 20;				//Largeur logo
	public $h_logo = 20;				//Hauteur logo
	public $x_raison = 35;				//Distance raison sociale / bord gauche de page
	public $y_raison = 10;				//Distance raison sociale / bord haut de page
	public $l_raison = 100;			//Largeur raison sociale
	public $h_raison = 10;				//Hauteur raison sociale
	public $fs_raison = 16;			//Taille police raison sociale
	public $x_date = 150;				//Distance date / bord gauche de page
	public $y_date = 10;				//Distance date / bord haut de page
	public $l_date = 0;				//Largeur date
	public $h_date = 6;				//Hauteur date
	public $fs_date = 8;				//Taille police date
	public $sep_ville_date = '';		//Séparateur entre ville et date
	public $x_adr_fac = 10;			//Distance adr facture / bord gauche de page
	public $y_adr_fac = 35;			//Distance adr facture / bord haut de page
	public $l_adr_fac = 60;			//Largeur adr facture
	public $h_adr_fac = 5;				//Hauteur adr facture
	public $fs_adr_fac = 10;			//Taille police adr facture
	public $text_adr_fac = '';
	public $text_adr_fac_tel = '';
	public $text_adr_fac_fax = '';
	public $text_adr_fac_email = '';
	public $x_adr_liv = 10;			//Distance adr livraison / bord gauche de page
	public $y_adr_liv = 75;			//Distance adr livraison / bord haut de page
	public $l_adr_liv = 60;			//Largeur adr livraison
	public $h_adr_liv = 5;				//Hauteur adr livraison
	public $fs_adr_liv = 10;			//Taille police adr livraison
	public $text_adr_liv = '';
	public $text_adr_liv_tel = '';
	public $text_adr_liv_tel2 = '';
	public $text_adr_liv_email = '';
	public $x_adr_fou = 100;			//Distance adr fournisseur / bord gauche de page
	public $y_adr_fou = 55;			//Distance adr fournisseur / bord haut de page
	public $l_adr_fou = 100;			//Largeur adr fournisseur
	public $h_adr_fou = 6;				//Hauteur adr fournisseur
	public $fs_adr_fou = 14;			//Police adr fournisseur
	public $text_adr_fou = '';		
	public $x_num = 10;				//Distance num acte / bord gauche de page
	public $w_num = 0;
	public $y_num = 110;				//Distance num acte / bord haut de page
	public $l_num = 0;					//Largeur num acte
	public $h_num = 10;				//Hauteur num acte
	public $fs_num = 16;				//Taille police num acte
	public $text_num = '';				//Texte commande
	public $text_before = '';			//texte avant table acte
	public $text_after = '';			//texte après table acte
	public $h_tab = 5;					//Hauteur de ligne table acte
	public $fs_tab = 10;				//Taille police table acte
	public $x_tab = 10;				//position table acte / bord gauche page 
	public $y_tab = 10;				//position table acte / haut page sur pages 2 et + 
	public $x_sign = 10;				//Distance signature / bord gauche de page
	public $l_sign = 60;				//Largeur cellule signature
	public $h_sign = 5;				//Hauteur signature
	public $fs_sign = 10;				//Taille police signature
	public $text_sign = '';			//Texte signature
	public $y_footer = 15;				//Distance footer / bas de page
	public $fs_footer = 8;				//Taille police footer
	public $y = 0;
	public $h = 0;
	public $s = 0;
	public $filename='acte.pdf';
	public $h_header = 0;
	
	protected $id_acte;
	protected $acte;
	protected $bib;
	protected $coord_liv;
	protected $coord_fac;
	protected $coord_fou;
	
	protected function _init() {
		global $msg, $charset, $pmb_pdf_font;
		global $acquisition_pdfdev_tab_dev;
			
		parent::_init();
		
		$this->_init_pos_logo();
		
		$this->_init_pos_raison();
		
		$this->_init_pos_date();
		
		$this->_init_pos_adr_fac();
		$this->text_adr_fac = $msg['acquisition_adr_fac']." :";
		$this->text_adr_fac_tel = $msg['acquisition_tel'].".";
		$this->text_adr_fac_tel2 = $msg['acquisition_tel2'].".";
		$this->text_adr_fac_fax = $msg['acquisition_fax'].".";
		$this->text_adr_fac_email = $msg['acquisition_mail']." :";
		
		$this->_init_pos_adr_liv();
		$this->text_adr_liv = $msg['acquisition_adr_liv']." :";
		$this->text_adr_liv_tel = $msg['acquisition_tel'].".";
		$this->text_adr_liv_tel2 = $msg['acquisition_tel2'].".";
		$this->text_adr_liv_email = $msg['acquisition_mail']." :";
		
		$this->_init_pos_adr_fou();
		$this->text_adr_fou = $msg['acquisition_act_formule'];
		
		$this->_init_pos_num();
		
		$this->text_before = $this->get_parameter_value('text_before');
		$this->text_after = $this->get_parameter_value('text_after');
		
		$this->_init_tab();
		$this->x_tab = $this->marge_gauche;
		$this->y_tab = $this->marge_haut;
		
		$this->_init_pos_sign();
			
		if ($this->get_parameter_value('text_sign')) {
			$this->text_sign = $this->get_parameter_value('text_sign');
		} else {
			$this->text_sign = $msg['acquisition_act_sign'];
		}
	}
	
	protected function _init_PDF() {
		if($this->get_parameter_value('orient_page')) {
			$this->orient_page = $this->get_parameter_value('orient_page');
		} else {
			$this->orient_page = 'P';
		}
	
		$format_page = explode('x',$this->get_parameter_value('format_page'));
		if(!empty($format_page[0])) $this->largeur_page = $format_page[0];
		if(!empty($format_page[1])) $this->hauteur_page = $format_page[1];
	
		$this->PDF = pdf_factory::make($this->orient_page, $this->unit, array($this->largeur_page, $this->hauteur_page));
	}
	
	protected function _init_pos_logo() {
		$pos_logo = explode(',', $this->get_parameter_value('pos_logo'));
		$this->_init_position('logo', $pos_logo);
	}
	
	protected function _init_pos_raison() {
		$pos_raison = explode(',', $this->get_parameter_value('pos_raison'));
		$this->_init_position('raison', $pos_raison);
	}
	
	protected function _init_pos_date() {
		global $msg;
		
		$pos_date = explode(',', $this->get_parameter_value('pos_date'));
		$this->_init_position('date', $pos_date);
		$this->sep_ville_date = $msg['acquisition_act_sep_ville_date'];
	}
	
	protected function _init_pos_adr_fac() {
		$pos_adr_fac = explode(',', $this->get_parameter_value('pos_adr_fac'));
		$this->_init_position('adr_fac', $pos_adr_fac);
	}
	
	protected function _init_pos_adr_liv() {
		$pos_adr_liv = explode(',', $this->get_parameter_value('pos_adr_liv'));
		$this->_init_position('adr_liv', $pos_adr_liv);
	}
	
	protected function _init_pos_adr_fou() {
		$pos_adr_fou = explode(',', $this->get_parameter_value('pos_adr_fou'));
		$this->_init_position('adr_fou', $pos_adr_fou);
	}
	
	protected function _init_pos_num() {
		$pos_num = explode(',', $this->get_parameter_value('pos_num'));
		$this->_init_position('num', $pos_num);
	}
	
	protected function _init_pos_sign() {
		$pos_sign = explode(',', $this->get_parameter_value('pos_sign'));
		//Insertion de la valeur 0 pour la position Y inexistante dans le paramétrage
		array_splice($pos_sign, 1, 0, array('0'));
		$this->_init_position('sign', $pos_sign);
	}
	
	protected function _open() {
		global $msg;
		
		parent::_open();
		$this->PDF->msg_footer = $msg['acquisition_act_page'];
	}
	
	protected function display_raison_sociale() {
		$this->PDF->setFontSize($this->fs_raison);
		$this->PDF->SetXY($this->x_raison, $this->y_raison);
		$this->PDF->MultiCell($this->l_raison, $this->h_raison, $this->get_bib()->raison_sociale, 0, 'L', 0);
	}
	
	protected function display_date() {
		$this->PDF->setFontSize($this->fs_date);
		$this->PDF->SetXY($this->x_date, $this->y_date);
		$this->PDF->Cell($this->l_date, $this->h_date, formatdate(today()), 0, 0, 'L', 0);
	}
	
	protected function get_supplier_address() {
		$coord = $this->get_coord_fou();
		$address = '';
		if($this->get_fou()->raison_sociale != '') {
		    $address.= $this->get_fou()->raison_sociale.PHP_EOL;
		} elseif (!empty($address)) {
		    $address.= $coord->libelle.PHP_EOL;
		}
		if(is_object($coord)) {
			if($coord->contact != '') $address.= $coord->contact.PHP_EOL;
			if($coord->adr1 != '') $address.= $coord->adr1.PHP_EOL;
			if($coord->adr2 != '') $address.= $coord->adr2.PHP_EOL;
			if($coord->cp != '') $address.= $coord->cp." ";
			if($coord->ville != '') $address.= $coord->ville.PHP_EOL.PHP_EOL;
		}
		return $address;
	}
	
	protected function display_supplier() {
		$adr_fou = '';
		if($this->text_adr_fou) {
		  $adr_fou .= $this->text_adr_fou."\n";
		}
		$adr_fou .= $this->get_supplier_address();
		$this->PDF->setFontSize($this->fs_adr_fou);
		$this->PDF->SetXY($this->x_adr_fou, $this->y_adr_fou);
		$this->PDF->MultiCell($this->l_adr_fou, $this->h_adr_fou, $adr_fou, 0, 'L', 0);
	}
	
	protected function get_invoicing_address() {
		$coord = new coordonnees($this->get_coord_fac()->id_contact);
		$address = '';
		if($coord->libelle != '') $address.= $coord->libelle."\n";
		if($coord->adr1 != '') $address.= $coord->adr1."\n";
		if($coord->adr2 != '') $address.= $coord->adr2."\n";
		if($coord->cp != '') $address.= $coord->cp." ";
		if($coord->ville != '') $address.= $coord->ville."\n";
		if($coord->tel1 != '') $address.= $this->text_adr_fac_tel." ".$coord->tel1."\n";
		if($coord->tel2 != '') $address.= $this->text_adr_fac_tel2." ".$coord->tel2."\n";
		if($coord->fax != '') $address.= $this->text_adr_fac_fax." ".$coord->fax."\n";
		if($coord->email != '') $address.= $this->text_adr_fac_email." ".$coord->email."\n";
		return $address;
	}
	
	protected function display_invoicing() {
		$adr_fac = '';
		if($this->text_adr_fac) {
			$adr_fac .= $this->text_adr_fac."\n";
		}
		$adr_fac .= $this->get_invoicing_address();
		$this->PDF->setFontSize($this->fs_adr_fac);
		$this->PDF->SetXY($this->x_adr_fac, $this->y_adr_fac);
		$this->PDF->MultiCell($this->l_adr_fac, $this->h_adr_fac, $adr_fac, 0, 'L', 0);
	}
	
	protected function get_delivery_address() {
		$coord = new coordonnees($this->get_coord_liv()->id_contact);
		$address = '';
		if($coord->libelle != '') $address.= $coord->libelle."\n"; 
		if($coord->adr1 != '') $address.= $coord->adr1."\n";
		if($coord->adr2 != '') $address.= $coord->adr2."\n";
		if($coord->cp != '') $address.= $coord->cp." ";
		if($coord->ville != '') $address.= $coord->ville."\n";
		if($coord->tel1 != '') $address.= $this->text_adr_liv_tel." ".$coord->tel1."\n";
		if($coord->tel2 != '') $address.= $this->text_adr_liv_tel2." ".$coord->tel2."\n";
		if($coord->email != '') $address.= $this->text_adr_liv_email." ".$coord->email."\n";
		return $address;
	}
	
	protected function display_delivery() {
		global $msg;
		
		$adr_liv = $msg['acquisition_adr_liv']."\n";
		$adr_liv .= $this->get_delivery_address();
		$this->PDF->setFontSize($this->fs_adr_liv);
		$this->PDF->SetXY($this->x_adr_liv, $this->y_adr_liv);
		$this->PDF->MultiCell($this->l_adr_liv, $this->h_adr_liv, $adr_liv, 1, 'L', 0);
	}
	
	public function get_acte() {
		if(isset($this->acte) && $this->acte->id_acte != $this->id_acte) {
			$this->unset_coords();
		}
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
	
	public function unset_coords() {
		unset($this->acte);
		unset($this->bib);
		unset($this->coord_liv);
		unset($this->coord_fac);
		unset($this->fou);
		unset($this->coord_fou);
	}
}