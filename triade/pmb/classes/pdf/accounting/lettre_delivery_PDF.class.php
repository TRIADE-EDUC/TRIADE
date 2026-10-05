<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_delivery_PDF.class.php,v 1.5 2020/01/23 14:38:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/accounting/lettre_accounting_PDF.class.php");

class lettre_delivery_PDF extends lettre_accounting_PDF {
	
	public $x_raison = 10;				//Distance raison sociale / bord gauche de page
	
	public $x_date = 170;				//Distance date / bord gauche de page
	
	public $y_adr_liv = 20;			//Distance adr livraison / bord haut de page
	
	public $x_adr_fou = 110;			//Distance adr fournisseur / bord gauche de page
	public $y_adr_fou = 20;			//Distance adr fournisseur / bord haut de page
	public $h_adr_fou = 5;				//Hauteur adr fournisseur
	public $fs_adr_fou = 10;			//Police adr fournisseur
	
	public $y_num = 60;				//Distance num acte / bord haut de page
	public $h_num = 6;				//Hauteur num acte
	public $fs_num = 14;				//Taille police num acte
	
	
	public $text_liv_ref_fou = '';
	public $text_num_cde = '';
	
	public $x_code =  '';
	public $w_code = '';
	public $x_lib = '';
	public $w_lib = '';
	public $x_qte = '';
	public $w_qte = '';
	
	protected static function get_parameter_prefix() {
	    return 'acquisition_pdfliv';
	}
	
	protected function _init() {
		global $msg;
		
		parent::_init();
		$this->text_adr_fou = $msg['acquisition_ach_fou2'];
	}
	
	protected function _init_pos_num() {
		global $msg;
		
		parent::_init_pos_num();
		$this->text_num = $msg['acquisition_act_num_liv'];
		$this->text_liv_ref_fou = $msg['acquisition_liv_ref_fou'];
		$this->text_num_cde = $msg['acquisition_act_num_cde'];
	}
	
	protected function _init_tab() {
		global $acquisition_pdfliv_tab_liv;
		
		$pos_tab = explode(',', $acquisition_pdfliv_tab_liv);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
	}
	
	public function doLettre($id_bibli, $id_liv) {
		global $pmb_pdf_font;
		
		//On récupère les infos de la livraison
		$this->id_acte = $id_liv;
		$liv = $this->get_acte();
		
		$id_cde = liens_actes::getParent($this->id_acte);
		$cde = new actes($id_cde);
		
		$this->PDF->addPage();
		
		$this->PDF->setFont($pmb_pdf_font);
		
		//Affichage date
		$this->display_date();
		
		//Affichage raison sociale
		$this->PDF->setFontSize($this->fs_raison);
		$this->PDF->SetXY($this->x_raison, $this->y_raison);
		$this->PDF->Cell($this->l_raison, $this->h_raison, $this->get_bib()->raison_sociale, 0, 0, 'L', 0);
		
		//Affichage coordonnees fournisseur
		$this->display_supplier();
		
		//Affichage adresse livraison
		$this->display_delivery();
		
		//Affichage numero bon de livraison et numero commande
		$numero = str_replace('!!numero!!', $liv->numero, $this->text_num);
		$numero = str_replace('!!date!!', formatdate($liv->date_acte), $numero);
		$numero.= "\n".$this->text_num_cde." ".$cde->numero."\n";
		$numero.= $this->text_liv_ref_fou." ".$liv->reference;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->SetXY($this->x_num, $this->y_num);
		$this->PDF->MultiCell($this->l_num, $this->h_num, $numero, 0, 'L', 0);
		$this->PDF->Ln();
		
		//Affichage lignes livraison
		$this->PDF->SetAutoPageBreak(false);
		$this->PDF->AliasNbPages();
		
		$this->PDF->SetFontSize($this->fs_tab);
		$this->PDF->SetFillColor(230);
		$this->PDF->Ln();
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_tab,$this->y);
		
		$w = $this->largeur_page-$this->marge_gauche-$this->marge_droite;
		$this->x_code =  $this->x_tab;
		$this->w_code = round($w*20/100);
		$this->x_lib = $this->x_code + $this->w_code;
		$this->w_lib = round($w*60/100);
		$this->x_qte = $this->x_lib + $this->w_lib;
		$this->w_qte = round($w*10/100);
		
		$this->doEntete();
		
		$this->doLines();
		
		$this->y = $this->PDF->SetY($this->y);
		
		$this->PDF->SetAutoPageBreak(true, $this->marge_bas);
		$this->PDF->SetX($this->marge_gauche);
		$this->PDF->Ln();
		
		$this->PDF->OutPut();
	}
	
	//Entete de tableau
	public function doEntete() {
		global $msg;

		$this->h = $this->h_tab * max( 	$this->PDF->NbLines($this->w_code, $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code']),
				$this->PDF->NbLines($this->w_lib,$msg['acquisition_act_tab_lib']),
				$this->PDF->NbLines($this->w_qte, $msg['acquisition_act_tab_qte']) );
		$this->s = $this->y+$this->h;
		if ($this->s > ($this->hauteur_page-$this->marge_bas)){
		
			$this->PDF->AddPage();
			$this->PDF->SetXY($this->x_tab, $this->y_tab);
			$this->y = $this->PDF->GetY();
		
		}
		$this->PDF->SetXY($this->x_code, $this->y);
		$this->PDF->Rect($this->x_code, $this->y, $this->w_code, $this->h, 'FD');
		$this->PDF->MultiCell($this->w_code, $this->h_tab, $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code'], 0, 'L');
		$this->PDF->SetXY($this->x_lib, $this->y);
		$this->PDF->Rect($this->x_lib, $this->y, $this->w_lib, $this->h, 'FD');
		$this->PDF->MultiCell($this->w_lib, $this->h_tab, $msg['acquisition_act_tab_lib'], 0, 'L');
		$this->PDF->SetXY($this->x_qte, $this->y);
		$this->PDF->Rect($this->x_qte, $this->y, $this->w_qte, $this->h, 'FD');
		$this->PDF->MultiCell($this->w_qte, $this->h_tab, $msg['acquisition_act_tab_qte'], 0, 'L');
		$this->y = $this->y+$this->h;
	}
	
	public function doLines() {
		$lignes = actes::getLignes($this->id_acte);
		while (($row = pmb_mysql_fetch_object($lignes))) {
			$typ = new types_produits($row->num_type);
			$col1 = $typ->libelle."\n".$row->code;
			
			$this->h = $this->h_tab * max( 	$this->PDF->NbLines($this->w_code, $col1),
					$this->PDF->NbLines($this->w_lib, $row->libelle),
					$this->PDF->NbLines($this->w_qte, $row->nb) );
			
			$this->s = $this->y+$this->h;
			if ($this->s > ($this->hauteur_page-$this->marge_bas)){
				
				$this->PDF->AddPage();
				$this->PDF->SetXY($this->x_tab, $this->y_tab);
				$this->y = $this->PDF->GetY();
				$this->doEntete();
				
			}
			$this->PDF->SetXY($this->x_code, $this->y);
			$this->PDF->Rect($this->x_code, $this->y, $this->w_code, $this->h);
			$this->PDF->MultiCell($this->w_code, $this->h_tab, $col1, 0, 'L');
			$this->PDF->SetXY($this->x_lib, $this->y);
			$this->PDF->Rect($this->x_lib, $this->y, $this->w_lib, $this->h);
			$this->PDF->MultiCell($this->w_lib, $this->h_tab, $row->libelle, 0, 'L');
			$this->PDF->SetXY($this->x_qte, $this->y);
			$this->PDF->Rect($this->x_qte, $this->y, $this->w_qte, $this->h);
			$this->PDF->MultiCell($this->w_qte, $this->h_tab, $row->nb, 0, 'L');
			$this->y = $this->y+$this->h;
		}
	}
}

class lettre_delivery_factory {

	public static function make() {
		global $acquisition_pdfliv_print, $base_path;

		$className = 'lettre_delivery_PDF';
		if (file_exists("$base_path/classes/pdf/accounting/".$acquisition_pdfliv_print.".class.php")) {
			require_once("$base_path/classes/pdf/accounting/".$acquisition_pdfliv_print.".class.php");
			$className = $acquisition_pdfliv_print;
		}
		return new $className();
	}
}