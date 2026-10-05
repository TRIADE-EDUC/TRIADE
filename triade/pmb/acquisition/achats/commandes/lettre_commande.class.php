<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_commande.class.php,v 1.18 2020/01/23 14:38:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $base_path;

require_once("$class_path/pdf/accounting/lettre_accounting_PDF.class.php");
require_once("$class_path/paiements.class.php");
require_once("$class_path/rubriques.class.php");
require_once("$base_path/acquisition/achats/func_achats.inc.php");
require_once("$class_path/thresholds.class.php");

class lettreCommande_PDF extends lettre_accounting_PDF {
	
	public $x_titre = 10;				//Distance titre / bord gauche de page
	public $y_titre = 90;				//Distance titre / bord haut de page
	public $l_titre = 100;				//Largeur titre
	public $h_titre = 10;				//Hauteur titre
	public $fs_titre = 16;				//Police titre
	public $text_titre = '';
	public $text_num_ech = '';
	public $text_num_cli = '';
	public $w_num = '';
	public $l_num_cli = '';
	public $text_ref = ''; 
	public $x_tot = 10;				//position total / bord gauche de page
	public $l_tot = 40;				//largeur total
	public $h_tot = 5;					//hauteur total
	public $fs_tot = 10;				//Taille police total
	
	public $x_col1 = '';
	public $w_col1 = '';
	public $txt_header_col1 = '';
	public $x_col2 = '';
	public $w_col2 = '';
	public $txt_header_col2 = '';
	public $x_col3 = '';
	public $w_col3 = '';
	public $txt_header_col3 = '';
	public $x_col4 = '';
	public $w_col4 = '';
	public $txt_header_col4 = '';
	public $x_col5 = '';
	public $w_col5 = '';
	public $txt_header_col5 = '';
	
	public $p_header = false;
	public $filename='commande.pdf';
	
	public $tab_mnt = array();
	
	protected static function get_parameter_prefix() {
	    return 'acquisition_pdfcde';
	}
	
	protected function _init_pos_num() {
		global $msg;
		
		parent::_init_pos_num();
		$this->text_num = $msg['acquisition_act_num_cde'];
		$this->text_num_cli = $msg['acquisition_num_cp_client'];
	}
	
	protected function _init_pos_titre() {
		$pos_titre = explode(',', $this->get_parameter_value('pos_titre'));
		$this->_init_position('titre', $pos_titre);
	}
	
	protected function _init_pos_tot() {
		$pos_tot = explode(',', $this->get_parameter_value('pos_tot'));
		//Insertion de la valeur 0 pour la position Y inexistante dans le paramétrage
		array_splice($pos_tot, 1, 0, array('0'));
		$this->_init_position('tot', $pos_tot);
	}
	
	protected function _init_tab() {
		global $acquisition_pdfcde_tab_cde;
	
		$pos_tab = explode(',', $acquisition_pdfcde_tab_cde);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
	}
	
	protected function _init() {
		global $msg;
		global $acquisition_gestion_tva;
			
		parent::_init();
		
		$this->_init_pos_titre();
		$this->text_titre = $msg['acquisition_recept_lettre_titre'];
		
		$this->text_ref = $msg['acquisition_cde_ref_dev'];
		
		$this->_init_pos_tot();
		
		$this->x_col1 =  $this->x_tab;
		$this->w_col1 = round($this->w*20/100);
		$this->txt_header_col1 = $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code'];
		
		$this->x_col2 = $this->x_col1 + $this->w_col1;
		$this->w_col2 = round($this->w*40/100);
		$this->txt_header_col2 = $msg['acquisition_act_tab_lib'];
		
		$this->x_col3 = $this->x_col2 + $this->w_col2;
		$this->w_col3 = round($this->w*10/100); 
		$this->txt_header_col3 = $msg['acquisition_act_tab_qte'];
		
		$this->x_col4 = $this->x_col3 + $this->w_col3;
		$this->w_col4 = round($this->w*10/100); 
		switch($acquisition_gestion_tva) {
			case '1' :
				$this->txt_header_col4 = $msg['acquisition_act_tab_priht']."\n".$msg['acquisition_tva']."\n".$msg['acquisition_rem']; 
				break;
			case '2' :
				$this->txt_header_col4 = $msg['acquisition_act_tab_prittc']."\n".$msg['acquisition_tva']."\n".$msg['acquisition_rem'];
				break;
			default :
				$this->txt_header_col4 = " ".$msg['acquisition_act_tab_prittc']."\n".$msg['acquisition_rem'];
				break;
		}	
		
		$this->x_col5 = $this->x_col4 + $this->w_col4;
		$this->w_col5 = round($this->w*20/100); 
		$this->txt_header_col5 = $msg['acquisition_act_tab_dateliv'];
	}
	
	protected function _open() {
		parent::_open();
		$this->h_header = $this->h_tab * max( 	$this->PDF->NbLines($this->w_col1, $this->txt_header_col1 ),
		$this->PDF->NbLines($this->w_col2,$this->txt_header_col2),
		$this->PDF->NbLines($this->w_col3, $this->txt_header_col3),
		$this->PDF->NbLines($this->w_col4, $this->txt_header_col4),
		$this->PDF->NbLines($this->w_col5, $this->txt_header_col5) );
		$this->p_header = false;
	}
	
	protected function display_date() {
		$ville_end=stripos($this->get_coord_fac()->ville,"cedex");	
		if($ville_end!==false) $ville=trim(substr($this->get_coord_fac()->ville,0,$ville_end));
		else $ville=$this->get_coord_fac()->ville;
		if ($this->get_acte()->date_valid != '0000-00-00') {
			$date = $ville.$this->sep_ville_date.format_date($this->get_acte()->date_valid);
		} else {
			$date = $ville.$this->sep_ville_date.format_date($this->get_acte()->date_acte);
		}
		$this->PDF->setFontSize($this->fs_date);
		$this->PDF->SetXY($this->x_date, $this->y_date);
		$this->PDF->Cell($this->l_date, $this->h_date, $date, 0, 0, 'L', 0);
	}
	
	protected function display_invoicing() {
		global $msg;
		
		$adr_fac = '';
		if($this->text_adr_fac) {
			$adr_fac .= $this->text_adr_fac."\n";
		}
		$adr_fac .= $this->get_invoicing_address();
		if($this->get_bib()->tva) {
			$adr_fac.=$msg["acquisition_tva"].": ".$this->get_bib()->tva."\n";
		}
		$this->PDF->setFontSize($this->fs_adr_fac);
		$this->PDF->SetXY($this->x_adr_fac, $this->y_adr_fac);
		$this->PDF->MultiCell($this->l_adr_fac, $this->h_adr_fac, $adr_fac, 1, 'L', 0);
	}
	
	public function doLettre($id_bibli, $id_cde) {
		global $msg, $acquisition_gestion_tva;
		
		//On récupère les infos de la commande
		$this->id_acte = $id_cde;
		$cde = $this->get_acte();
		
		$this->PDF->AddPage();
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
		$this->display_invoicing();
		
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
		$this->y=$this->PDF->GetY();
		$this->PDF->Ln();
		$this->PDF->Ln();

		//Affichage numero client
		$numero_cli = $this->text_num_cli." ".$this->get_fou()->num_cp_client;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->SetXY($this->x_num, $this->y_num);
		$this->PDF->Cell($this->l_num_cli, $this->h_num, $numero_cli, 0, 0, 'L', 0);
		$this->PDF->Ln();
		
		//Affichage numero commande
		$numero =  $this->text_num.$cde->numero;
		$this->PDF->SetFontSize($this->fs_num);
		$this->PDF->Cell($this->l_num, $this->h_num, $numero, 0, 0, 'L', 0);
		$this->PDF->Ln();
		
		//Affichage reference
		if ($cde->reference != '') {
			$ref = $this->text_ref.$cde->reference;
			$this->PDF->SetFontSize($this->fs);
			$this->PDF->Cell($this->w, $this->h_tab, $ref, 0, 0, 'L', 0);
			$this->PDF->Ln();
			$this->PDF->Ln();
		}
		
		//Affichage texte before + commentaires
		if ($cde->commentaires_i != '') {
			if ($this->text_before != '') $this->text_before.= "\n\n";
			$this->text_before.= $cde->commentaires_i;
		}
		if ($this->text_before != '') {
			$this->PDF->SetFontSize($this->fs);
			$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_before, 0, 'J', 0);
		}
		
		//Affichage lignes commandes
		$this->PDF->SetAutoPageBreak(false);
		$this->PDF->AliasNbPages();
	
		$this->PDF->SetFontSize($this->fs_tab);
		$this->PDF->SetFillColor(230);
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_tab,$this->y);
		
		
		$this->tab_mnt=array();
		$this->doLines();
		
		$this->PDF->SetAutoPageBreak(true, $this->marge_bas);
		$this->PDF->SetX($this->marge_gauche);
		$this->PDF->SetY($this->y);
		$this->PDF->SetFontSize($this->fs);
		$this->PDF->Ln();
	
		//affichage des montants ht, ttc, tva	
		$tab_tot = calc($this->tab_mnt,2);
		$this->y = $this->PDF->GetY();
		if ($acquisition_gestion_tva) $this->h = $this->h_tot * 3;
			else $this->h = $this->h_tot;
		$this->s = $this->y + $this->h;
		
		if ($this->s > ($this->hauteur_page-$this->marge_bas)){
		
			$this->PDF->AddPage();
			$this->PDF->SetXY($this->x_tot, $this->marge_haut);
			$this->y = $this->PDF->GetY(); 
		}
		
		if ($acquisition_gestion_tva) {
			$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_total_ht'], 1, 0, 'L',0);
			$this->PDF->Cell($this->l_tot, $this->h_tot, number_format($tab_tot['ht'],2,'.','' )." ".$cde->devise, 1, 1, 'R',0);
			$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_tva'], 1, 0, 'L',0);
			$this->PDF->Cell($this->l_tot, $this->h_tot, number_format($tab_tot['tva'],2,'.','' )." ".$cde->devise, 1, 1,'R',0);	 		 	
		}
		$this->PDF->Cell($this->l_tot, $this->h_tot, $msg['acquisition_total_ttc'], 1, 0, 'L',0);
		$this->PDF->Cell($this->l_tot, $this->h_tot, number_format($tab_tot['ttc'],2,'.','' )." ".$cde->devise, 1, 1, 'R',0);	 	
		$this->PDF->Ln();
		
		//Affichage conditions de paiement
		$text_paiement = $msg['acquisition_mode_pai'];
		if ($this->get_fou()->num_paiement) {
			$pai = new paiements($this->get_fou()->num_paiement); 
			$text_paiement.= "$pai->libelle";
			$this->PDF->MultiCell($this->w, $this->h_tab, $text_paiement, 0, 'L', 0);
			$this->PDF->Ln();
		}
		
		//Affichage texte after
		if ($this->text_after != '') {
			$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_after, 0, 'J', 0);
			$this->PDF->Ln();		
		}
		
		//Affichage signature
		$this->PDF->Ln();		
		$this->PDF->SetFontSize($this->fs_sign);
		$this->PDF->SetX($this->x_sign);
		
		$thresholds = new thresholds($this->get_bib()->id_entite);
		if($acquisition_gestion_tva) {
			$threshold = $thresholds->get_threshold_from_price($tab_tot['ht'], $tab_tot['ttc']);
		} else {
			$threshold = $thresholds->get_threshold_from_price($tab_tot['ttc'], $tab_tot['ttc']);
		}
		if(is_object($threshold) && $threshold->get_footer()) {
			$this->PDF->MultiCell($this->l_sign, $this->h_sign, $threshold->get_footer(), 0, 'L', 0);
		} else {
			$this->PDF->MultiCell($this->l_sign, $this->h_sign, $this->text_sign, 0, 'L', 0);
		}
	}
	
	//Entete de tableau
	public function doEntete() {
		$this->PDF->SetXY($this->x_num,$this->y);
		$this->PDF->MultiCell($this->w_num, $this->h_num, $this->text_num_ech, 0, 'L');
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_col1, $this->y);
		$this->PDF->Rect($this->x_col1, $this->y, $this->w_col1, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col1, $this->h_tab, $this->txt_header_col1, 0, 'L');
		$this->PDF->SetXY($this->x_col2, $this->y);
		$this->PDF->Rect($this->x_col2, $this->y, $this->w_col2, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col2, $this->h_tab, $this->txt_header_col2, 0, 'L');
		$this->PDF->SetXY($this->x_col3, $this->y);
		$this->PDF->Rect($this->x_col3, $this->y, $this->w_col3, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col3, $this->h_tab, $this->txt_header_col3, 0, 'L');
		$this->PDF->SetXY($this->x_col4, $this->y);
		$this->PDF->Rect($this->x_col4, $this->y, $this->w_col4, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col4, $this->h_tab, $this->txt_header_col4, 0, 'L');
		$this->PDF->SetXY($this->x_col5, $this->y);
		$this->PDF->Rect($this->x_col5, $this->y, $this->w_col5, $this->h_header, 'FD');
		$this->PDF->MultiCell($this->w_col5, $this->h_tab, $this->txt_header_col5, 0, 'L');
	}
	
	public function doLines() {
		global $acquisition_gestion_tva;
		
		$cde = $this->get_acte();
		
		$i=0;
		$lignes = actes::getLignes($this->id_acte);
		while (($row = pmb_mysql_fetch_object($lignes))) {
			
			$typ = new types_produits($row->num_type);
			$col1 = $typ->libelle;
			if($row->code) $col1.= "\n".$row->code;
			$col2 = $row->libelle;
			$col3 = $row->nb;
			$col4 = number_format(round($row->prix, 2),2,'.','' )." ".$cde->devise;
			if ($acquisition_gestion_tva){
				$col4.= "\n".number_format(round($row->tva,2),2,'.','' )." %";
			}
			$col4.= "\n".number_format(round($row->remise,2),2,'.','' )." %";
			$col5='';
			if ($row->date_ech != '0000-00-00') {
				$col5 = formatdate($row->date_ech);
			}
			if($row->num_rubrique) {
				$rub = new rubriques($row->num_rubrique);
				if($rub->num_cp_compta) $col5.= "\n\n".$rub->num_cp_compta;
			}
			
			//Est ce qu'on dépasse ?
			$this->h = $this->h_tab * max( 	$this->PDF->NbLines($this->w_col1, $col1),
					$this->PDF->NbLines($this->w_col2, $col2),
					$this->PDF->NbLines($this->w_col3, $col3),
					$this->PDF->NbLines($this->w_col4, $col4),
					$this->PDF->NbLines($this->w_col5, $col5) );
			$this->s = $this->y+$this->h;
			if(!$this->p_header) $this->s=$this->s + $this->h_header;
			
			//Si oui, chgt page
			if ($this->s > ($this->hauteur_page-$this->marge_bas-$this->fs_footer)){
				$this->PDF->AddPage();
				$this->y = $this->y_tab;
				$this->p_header = false;
			}
			if (!$this->p_header) {
				$this->doEntete();
				$this->y+=$this->h_header;
			}
			$this->p_header = true;
			
			$this->PDF->SetXY($this->x_col1, $this->y);
			$this->PDF->Rect($this->x_col1, $this->y, $this->w_col1, $this->h);
			$this->PDF->MultiCell($this->w_col1, $this->h_tab, $col1, 0, 'L');
			$this->PDF->SetXY($this->x_col2, $this->y);
			$this->PDF->Rect($this->x_col2, $this->y, $this->w_col2, $this->h);
			$this->PDF->MultiCell($this->w_col2, $this->h_tab, $col2, 0, 'L');
			$this->PDF->SetXY($this->x_col3, $this->y);
			$this->PDF->Rect($this->x_col3, $this->y, $this->w_col3, $this->h);
			$this->PDF->MultiCell($this->w_col3, $this->h_tab, $col3, 0, 'R');
			$this->PDF->SetXY($this->x_col4, $this->y);
			$this->PDF->Rect($this->x_col4, $this->y, $this->w_col4, $this->h);
			$this->PDF->MultiCell($this->w_col4, $this->h_tab, $col4, 0, 'R');
			$this->PDF->SetXY($this->x_col5, $this->y);
			$this->PDF->Rect($this->x_col5, $this->y, $this->w_col5, $this->h);
			$this->PDF->MultiCell($this->w_col5, $this->h_tab, $col5, 0, 'R');
			$this->y+= $this->h;
			
			$this->tab_mnt[$i]['q']=$row->nb;
			$this->tab_mnt[$i]['p']=$row->prix;
			$this->tab_mnt[$i]['r']=$row->remise;
			$this->tab_mnt[$i]['t']=$row->tva;
			$i++;
			
		}
	}
}


class lettreCommande_factory {
	
	public static function make() {
		
		global $acquisition_pdfcde_print, $base_path, $class_path;
		$className = 'lettreCommande_PDF';
		if (file_exists("$base_path/acquisition/achats/commandes/$acquisition_pdfcde_print.class.php")) {
			require_once("$base_path/acquisition/achats/commandes/$acquisition_pdfcde_print.class.php");
			$className = $acquisition_pdfcde_print;	
		}
		return new $className();
	}
}