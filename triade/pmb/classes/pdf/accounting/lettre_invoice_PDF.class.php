<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_invoice_PDF.class.php,v 1.6 2020/01/23 14:38:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/accounting/lettre_accounting_PDF.class.php");

class lettre_invoice_PDF extends lettre_accounting_PDF {
	
	public $x_raison = 10;				//Distance raison sociale / bord gauche de page
	
	public $x_date = 170;				//Distance date / bord gauche de page
	
	public $y_adr_fac = 20;			//Distance adr facture / bord haut de page

	public $x_adr_fou = 110;			//Distance adr fournisseur / bord gauche de page
	public $y_adr_fou = 20;			//Distance adr fournisseur / bord haut de page
	public $h_adr_fou = 5;				//Hauteur adr fournisseur
	public $fs_adr_fou = 10;			//Police adr fournisseur
	
	public $y_num = 60;				//Distance num devis / bord haut de page
	public $h_num = 6;				//Hauteur num devis
	public $fs_num = 14;				//Taille police num acte
	
	public $x_tot = 10;				//position total / bord gauche de page
	public $l_tot = 40;				//largeur total
	public $h_tot = 5;					//hauteur total
	public $fs_tot = 10;				//Taille police total
	
	public $filename='devis.pdf';
		
	public $text_fac_ref_fou = '';
	public $text_num_cde = '';
	
	public $x_code =  '';
	public $w_code = '';
	public $x_lib = '';
	public $w_lib = '';
	public $x_qte = '';
	public $w_qte = '';
	public $x_pri = '';
	public $w_pri = '';
	public $x_dat = '';
	public $w_dat = '';
	
	public $prix = '';
	public $tot_ht = 0;
	public $tot_tva = 0;
	public $tot_ttc = 0;
	
	protected static function get_parameter_prefix() {
	    return 'acquisition_pdffac';
	}
	
	protected function _init_pos_num() {
		global $msg;
		
		parent::_init_pos_num();
		$this->text_num = $msg['acquisition_act_num_fac'];
		$this->text_fac_ref_fou = $msg['acquisition_fac_ref_fou'];
		$this->text_num_cde = $msg['acquisition_act_num_cde'];
	}
	
	protected function _init_pos_tot() {
		$pos_tot = explode(',', $this->get_parameter_value('pos_tot'));
		//Insertion de la valeur 0 pour la position Y inexistant dans le paramétrage
		array_splice($pos_tot, 1, 0, array('0'));
		$this->_init_position('tot', $pos_tot);
	}
	
	protected function _init_tab() {
		global $acquisition_pdffac_tab_fac;
		
		$pos_tab = explode(',', $acquisition_pdffac_tab_fac);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
	}
	
	public function doLettre($id_bibli, $id_fac) {
		global $msg,$pmb_pdf_font;
		global $acquisition_gestion_tva;
		
		//On récupère les infos de la facture
		$this->id_acte = $id_fac;
		$fac = $this->get_acte();
		
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
	
		//Affichage adresse facturation
		$this->display_invoicing();
	
		//Affichage numero facture et numero commande
		$numero = str_replace('!!numero!!', $fac->numero, $this->text_num);
		$numero = str_replace('!!date!!', formatdate($fac->date_acte), $numero);
		$numero.= "\n".$this->text_num_cde." ".$cde->numero."\n";
		$numero.= $this->text_fac_ref_fou." ".$fac->reference;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->SetXY($this->x_num, $this->y_num);
		$this->PDF->MultiCell($this->l_num, $this->h_num, $numero, 0, 'L', 0);
		$this->PDF->Ln();
	
		//Affichage lignes facture
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
		$this->w_lib = round($w*40/100);
		$this->x_qte = $this->x_lib + $this->w_lib;
		$this->w_qte = round($w*10/100); 
		$this->x_pri = $this->x_qte + $this->w_qte;
		$this->w_pri = round($w*10/100);
		$this->x_dat = $this->x_pri + $this->w_pri;
		$this->w_dat = round($w*20/100);
	
		if ($acquisition_gestion_tva) $this->prix.= $msg['acquisition_act_tab_priht']."\n".$msg['acquisition_tva']."\n".$msg['acquisition_rem']; 
		else $this->prix.= " ".$msg['acquisition_act_tab_prittc']."\n".$msg['acquisition_rem'];	
		$this->tot_ht = 0;
		$this->tot_tva = 0;
		$this->tot_ttc = 0;
	
		$this->doEntete();
	
		$this->doLines();
		
		$this->y = $this->PDF->SetY($this->y);
	
		//affichage des montants ht, ttc, tva	
		$this->PDF->Ln();
		$this->y = $this->PDF->GetY();
		if ($acquisition_gestion_tva) $this->h = $this->h_tot * 3;
		else $this->h = $this->h_tot;
		$this->s = $this->y + $this->h;
	
		if ($this->s > ($this->hauteur_page-$this->marge_bas)){
			$this->PDF->AddPage();
			$this->PDF->SetXY($this->x_tot, $this->marge_haut);
			$this->y = $this->PDF->GetY(); 
		}
		if ($acquisition_gestion_tva){
			$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_total_ht'], 1, 0, 'L',0);
			$this->PDF->Cell($this->l_tot, $this->h_tot, number_format(round($this->tot_ht, 2),2,'.','')." ".$fac->devise, 1, 1, 'R',0);
			$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_tva'], 1, 0, 'L',0);
			$this->PDF->Cell($this->l_tot, $this->h_tot, number_format(round($this->tot_tva, 2),2,'.','')." ".$fac->devise, 1, 1,'R',0);	 		 	
		}
	
		$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_total_ttc'], 1, 0, 'L',0);
		$this->PDF->Cell($this->l_tot, $this->h_tot, number_format(round($this->tot_ttc, 2),2,'.','')." ".$fac->devise, 1, 1, 'R',0);	 	
	
		$this->PDF->SetAutoPageBreak(true, $this->marge_bas);
		$this->PDF->SetX($this->marge_gauche);
		$this->PDF->Ln();
	
		$this->PDF->SetFontSize($this->fs);
	
		$this->PDF->OutPut();			

	}
	
	//Entete de tableau
	public function doEntete() {
		global $msg;

		$this->h = $this->h_tab * max( 	$this->PDF->NbLines($this->w_code, $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code']),
				$this->PDF->NbLines($this->w_lib,$msg['acquisition_act_tab_lib']),
				$this->PDF->NbLines($this->w_qte, $msg['acquisition_act_tab_qte']),
				$this->PDF->NbLines($this->w_pri, $this->prix),
				$this->PDF->NbLines($this->w_dat, $msg['acquisition_num_cp_compta']) );
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
		$this->PDF->SetXY($this->x_pri, $this->y);
		$this->PDF->Rect($this->x_pri, $this->y, $this->w_pri, $this->h, 'FD');
		$this->PDF->MultiCell($this->w_pri, $this->h_tab, $this->prix, 0, 'L');
		$this->PDF->SetXY($this->x_dat, $this->y);
		$this->PDF->Rect($this->x_dat, $this->y, $this->w_dat, $this->h, 'FD');
		$this->PDF->MultiCell($this->w_dat, $this->h_tab, $msg['acquisition_num_cp_compta'], 0, 'L');
		$this->y = $this->y+$this->h;
	}
	
	public function doLines() {
		global $acquisition_gestion_tva;
		
		$fac = $this->get_acte();
		
		$lignes = actes::getLignes($this->id_acte);
		while (($row = pmb_mysql_fetch_object($lignes))) {
			$typ = new types_produits($row->num_type);
			$col1 = $typ->libelle."\n".$row->code;
			if ($row->num_rubrique) {
				$rub = new rubriques($row->num_rubrique);
				$col5 = $rub->num_cp_compta;
			} else {
				$col5 = '';
			}
			$col4 = number_format($row->prix, 2,'.','')." ".$fac->devise."\n".number_format($row->tva,2,'.','')." %\n".number_format($row->remise,2,'.','')." %";
			
			$this->h = $this->h_tab * max( 	$this->PDF->NbLines($this->w_code, $col1),
					$this->PDF->NbLines($this->w_lib, $row->libelle),
					$this->PDF->NbLines($this->w_qte, $row->nb),
					$this->PDF->NbLines($this->w_pri, $col4),
					$this->PDF->NbLines($this->w_dat, $col5) );
			
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
			$this->PDF->MultiCell($this->w_qte, $this->h_tab, $row->nb, 0, 'R');
			$this->PDF->SetXY($this->x_pri, $this->y);
			$this->PDF->Rect($this->x_pri, $this->y, $this->w_pri, $this->h);
			$this->PDF->MultiCell($this->w_pri, $this->h_tab, $col4, 0, 'R');
			$this->PDF->SetXY($this->x_dat, $this->y);
			$this->PDF->Rect($this->x_dat, $this->y, $this->w_dat, $this->h);
			$this->PDF->MultiCell($this->w_dat, $this->h_tab, $col5, 0, 'R');
			$this->y = $this->y+$this->h;
			
			//calcul des montants ht, ttc, tva
			if ($acquisition_gestion_tva) {
				$lig_ht = $row->nb * $row->prix * (1-($row->remise/100)) ;
				$this->tot_ht = $this->tot_ht + $lig_ht;
				$this->tot_tva = $this->tot_tva + ($lig_ht*($row->tva/100) );
				$this->tot_ttc = $this->tot_ht + $this->tot_tva;
			} else {
				$lig_ttc = $row->nb * $row->prix * (1-($row->remise/100)) ;
				$this->tot_ttc = $this->tot_ttc + $lig_ttc;
			}
		}
	}
}

class lettre_invoice_factory {

	public static function make() {
		global $acquisition_pdffac_print, $base_path;

		$className = 'lettre_invoice_PDF';
		if (file_exists("$base_path/classes/pdf/accounting/".$acquisition_pdffac_print.".class.php")) {
			require_once("$base_path/classes/pdf/accounting/".$acquisition_pdffac_print.".class.php");
			$className = $acquisition_pdffac_print;
		}
		return new $className();
	}
}