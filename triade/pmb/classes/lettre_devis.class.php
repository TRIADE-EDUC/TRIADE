<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_devis.class.php,v 1.10 2020/01/23 14:38:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/pdf/accounting/lettre_accounting_PDF.class.php");

class lettreDevis_PDF extends lettre_accounting_PDF {
	
	public $x_code =  '';
	public $w_code = '';
	public $x_lib = '';
	public $w_lib = '';
	public $x_qte = '';
	public $w_qte = '';
	public $filename='devis.pdf';
	
	protected static function get_parameter_prefix() {
	    return 'acquisition_pdfdev';
	}
	
	protected function _init_pos_num() {
		global $msg;
		
		parent::_init_pos_num();
		$this->text_num = $msg['acquisition_act_num_dev'];
	}
	
	protected function _init_tab() {
		global $acquisition_pdfdev_tab_dev;
		
		$pos_tab = explode(',', $acquisition_pdfdev_tab_dev);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
	}
	
	protected function display_date() {
		$ville_end=stripos($this->get_coord_fac()->ville,"cedex");	
		if($ville_end!==false) $ville=trim(substr($this->get_coord_fac()->ville,0,$ville_end));
		else $ville=$this->get_coord_fac()->ville;
		$date = $ville.$this->sep_ville_date.format_date($this->get_acte()->date_acte);
		$this->PDF->setFontSize($this->fs_date);
		$this->PDF->SetXY($this->x_date, $this->y_date);
		$this->PDF->Cell($this->l_date, $this->h_date, $date, 0, 0, 'L', 0);
	}
	
	public function doLettre($id_bibli, $id_dev) {
		global $pmb_pdf_font;
		
		//On récupère les infos du devis
		$this->id_acte = $id_dev;
		$dev = $this->get_acte();
		
		$this->PDF->AddPage();
		$this->PDF->setFont($pmb_pdf_font);
		$this->PDF->npage = 1;
		
		//Affichage logo
		if($this->get_bib()->logo != '') {
			$this->PDF->Image($this->get_bib()->logo, $this->x_logo, $this->y_logo, $this->l_logo, $this->h_logo);
		}
		
		//Affichage raison sociale
		$this->display_raison_sociale();
		
		//Affichage date $ville
		$this->display_date();
		
		//Affichage coordonnees fournisseur
		//si pas de raison sociale définie, on reprend le libellé
		//si il y a une raison sociale, pas besoin
		$this->display_supplier();
	
		//Affichage adresse facturation
		$adr_fac=$this->text_adr_fac."\n";
		$adr_fac.= $this->get_invoicing_address();
		$this->PDF->setFontSize($this->fs_adr_fac);
		$this->PDF->SetXY($this->x_adr_fac, $this->y_adr_fac);
		$this->PDF->MultiCell($this->l_adr_fac, $this->h_adr_fac, $adr_fac, 1, 'L', 0);
		
		//Affichage adresse livraison
		$adr_liv = $this->get_delivery_address();
		if($adr_liv != '') {
			$adr_liv = $this->text_adr_liv."\n".$adr_liv; 
			$this->PDF->setFontSize($this->fs_adr_liv);
			$this->PDF->SetXY($this->x_adr_liv, $this->y_adr_liv);
			$this->PDF->MultiCell($this->l_adr_liv, $this->h_adr_liv, $adr_liv, 1, 'L', 0);
		}
		
		//Affichage tiret pliage 
		$this->PDF->Line(0,105, 3, 105);
		
		//Affichage numero devis
		$numero =  $this->text_num.$dev->numero;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->Cell($this->l_num, $this->h_num, $numero, 0, 0, 'L', 0);
		$this->PDF->Ln();
				
		//Affichage texte before + commentaires
		if ($dev->commentaires_i != '') {
			if ($this->text_before != '') $this->text_before.= "\n\n";
			$this->text_before.= $dev->commentaires_i;
		}
		if ($this->text_before != '') {
			$this->PDF->SetFontSize($this->fs);
			$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_before, 0, 'J', 0);
			$this->PDF->Ln();
		}
		
		//Affichage lignes devis
		$this->PDF->SetAutoPageBreak(false);
		$this->PDF->AliasNbPages();
		
		$this->PDF->SetFontSize($this->fs_tab);
		$this->PDF->SetFillColor(230);
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_tab,$this->y);
		
		$this->x_code =  $this->x_tab;
		$this->w_code = round($this->w*20/100);
		$this->x_lib = $this->x_code + $this->w_code;
		$this->w_lib = round($this->w*60/100);
		$this->x_qte = $this->x_lib + $this->w_lib;
		$this->w_qte = round($this->w*10/100);
	
		$this->doEntete();
		
		$this->doLines();

		$this->PDF->SetAutoPageBreak(true, $this->marge_bas);
		$this->PDF->SetX($this->marge_gauche);
		$this->PDF->SetY($this->y);
		$this->PDF->SetFontSize($this->fs);
		$this->PDF->Ln();
	
		//Affichage texte after
		if ($this->text_after != '') {
			$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_after, 0, 'J', 0);
			$this->PDF->Ln();		
		}
	
		//Affichage signature
		$this->PDF->Ln();		
		$this->PDF->SetFontSize($this->fs_sign);
		$this->PDF->SetX($this->x_sign);
		$this->PDF->MultiCell($this->l_sign, $this->h_sign, $this->text_sign, 0, 'L', 0);			
	}
	
	//Entete de tableau
	public function doEntete() {
		global $msg;
		
		$this->h_header = $this->h_tab * max( 	$this->PDF->NbLines($this->w_code, $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code']),
			$this->PDF->NbLines($this->w_lib,$msg['acquisition_act_tab_lib']),
			$this->PDF->NbLines($this->w_qte, $msg['acquisition_act_tab_qte']) );
		$this->s = $this->y+$this->h_header;		
		if ($this->s > ($this->hauteur_page-$this->marge_bas)){
			$this->PDF->AddPage();
			$this->PDF->SetXY($this->x_tab, $this->y_tab);
			$this->y = $this->PDF->GetY();
		} 
		$this->PDF->SetXY($this->x_code, $this->y);
		$this->PDF->Rect($this->x_code, $this->y, $this->w_code, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_code, $this->h_tab, $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code'], 0, 'L');
		$this->PDF->SetXY($this->x_lib, $this->y);
		$this->PDF->Rect($this->x_lib, $this->y, $this->w_lib, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_lib, $this->h_tab, $msg['acquisition_act_tab_lib'], 0, 'L');
		$this->PDF->SetXY($this->x_qte, $this->y);
		$this->PDF->Rect($this->x_qte, $this->y, $this->w_qte, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_qte, $this->h_tab, $msg['acquisition_act_tab_qte'], 0, 'L');
		$this->y = $this->y+$this->h_header;
	
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

class lettreDevis_factory {
	
	public static function make() {
		
		global $acquisition_pdfdev_print, $base_path;
		$className = 'lettreDevis_PDF';
		if (file_exists("$base_path/acquisition/achats/devis/$acquisition_pdfdev_print.class.php")) {
			require_once("$base_path/acquisition/achats/devis/$acquisition_pdfdev_print.class.php");
			$className = $acquisition_pdfdev_print;	
		}
		return new $className();
	}
}