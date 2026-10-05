<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions_relances.class.php,v 1.13 2020/01/09 11:34:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/pdf/accounting/lettre_accounting_PDF.class.php");

require_once("$class_path/rtf_factory.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/types_produits.class.php");

class lettreRelance_PDF extends lettre_accounting_PDF {
	
	public $x_adr_rel = 10;			//Distance adr relance / bord gauche de page
	public $y_adr_rel = 35;			//Distance adr relance / bord haut de page
	public $l_adr_rel = 60;			//Largeur adr relance
	public $h_adr_rel = 5;				//Hauteur adr relance
	public $fs_adr_rel = 10;			//Taille police adr relance
	public $text_adr_tel = '';
	public $text_adr_fax = '';
	public $text_adr_email = '';
	public $x_titre = 10;				//Distance titre / bord gauche de page
	public $y_titre = 90;				//Distance titre / bord haut de page
	public $l_titre = 100;				//Largeur titre
	public $h_titre = 10;				//Hauteur titre
	public $fs_titre = 16;				//Police titre
	public $text_titre = '';
	public $text_num = '';				//Texte commande/devis
	public $text_ech = '';				//Texte date echeance
	public $text_num_ech = '';
	public $x_num_cli = 10;			//Distance num client / bord gauche de page
	public $y_num_cli = 80;			//Distance num client / bord haut de page
	public $l_num_cli = 0;				//Largeur num commande
	public $h_num_cli = 10;			//Hauteur num commande
	public $fs_num_cli = 16;			//Taille police num commande/devis
	public $text_num_cli = '';			//Texte numéro client
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
	public $filename = 'lettre_relance.pdf';
	
	protected function get_parameter_value($name) {
		$parameter_name = 'acquisition_pdfrel_'.$name;
		global $$parameter_name;
		return $$parameter_name;
	}
	
	protected function _init() {
		global $msg;
			
		parent::_init();
		
		$this->_init_pos_adr_rel();
		$this->text_adr_tel = $msg['acquisition_tel'].".";
		$this->text_adr_fax = $msg['acquisition_fax'].".";
		$this->text_adr_email = $msg['acquisition_mail']." :";
		
		$this->_init_pos_titre();
		$this->text_titre = $msg['acquisition_recept_lettre_titre'];
		
		$this->text_num = $msg['acquisition_act_num_cde'];
		$this->text_ech = $msg['acquisition_recept_lettre_ech'];
		
		$this->_init_pos_num_cli();
		$this->text_num_cli = $msg['acquisition_num_cp_client'];
		
		$this->x_col1 =  $this->x_tab;
		$this->w_col1 = round($this->w*17/100);
		$this->txt_header_col1 = $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code'];
		
		$this->x_col2 = $this->x_col1 + $this->w_col1;
		$this->w_col2 = round($this->w*50/100);
		$this->txt_header_col2 = $msg['acquisition_act_tab_lib'];
		
		$this->x_col3 = $this->x_col2 + $this->w_col2;
		$this->w_col3 = round($this->w*11/100);
		$this->txt_header_col3 = $msg['acquisition_qte_cde'];
		
		$this->x_col4 = $this->x_col3 + $this->w_col3;
		$this->w_col4 = round($this->w*11/100);
		$this->txt_header_col4 = $msg['acquisition_qte_liv'];
		
		$this->x_col5 = $this->x_col4 + $this->w_col4;
		$this->w_col5 = round($this->w*11/100);
		$this->txt_header_col5 = $msg['acquisition_act_tab_sol'];
		
	}
	
	protected function _open() {
		global $msg;
		
		parent::_open();
	
		$this->PDF->footer_type=2;
		$this->PDF->msg_footer = $msg['acquisition_act_page'];
	}
	
	protected function _init_pos_adr_rel() {
		$pos_adr_rel = explode(',', $this->get_parameter_value('pos_adr_rel'));
		$this->_init_position('adr_rel', $pos_adr_rel);
	}
	
	protected function _init_pos_titre() {
		$pos_titre = explode(',', $this->get_parameter_value('pos_titre'));
		$this->_init_position('pos_titre', $pos_titre);
	}
	
	protected function _init_pos_num_cli() {
		$pos_num_cli = explode(',', $this->get_parameter_value('pos_num_cli'));
		$this->_init_position('pos_num_cli', $pos_num_cli);
	}
	
	protected function _init_tab() {
		global $acquisition_pdfrel_tab_rel;
	
		$pos_tab = explode(',', $acquisition_pdfrel_tab_rel);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
		$this->x_tab = $this->marge_gauche;
		$this->y_tab = $this->marge_haut;
	}
	
	public function doLettre(&$bib, &$bib_coord, &$fou, &$fou_coord, &$tab_act) {
		foreach($tab_act as $id_act=>$tab_lig) {
			foreach($tab_lig as $id_lig) {
				$this->id_acte = $id_lig;
				break;
			}
		}
		$this->bib = $bib;
		$this->fou = $fou;
		$this->coord_fou = $fou_coord;
		
		$this->h_header = $this->h_tab * max( 	$this->PDF->NbLines($this->w_col1, $this->txt_header_col1 ),
				$this->PDF->NbLines($this->w_col2,$this->txt_header_col2),
				$this->PDF->NbLines($this->w_col3, $this->txt_header_col3),
				$this->PDF->NbLines($this->w_col4, $this->txt_header_col4),
				$this->PDF->NbLines($this->w_col5, $this->txt_header_col5) );
		$this->p_header = false;
		
		$this->PDF->AddPage();
		$this->PDF->npage = 1;
		
		//Affichage logo
		if($bib->logo != '') {
			$this->PDF->Image($bib->logo, $this->x_logo, $this->y_logo, $this->l_logo, $this->h_logo);
		}
		
		//Affichage raison sociale
		$this->display_raison_sociale();
		
		//Affichage date $ville
		$ville_end=stripos($bib_coord->ville,"cedex");	
		if($ville_end!==false) $ville=trim(substr($bib_coord->ville,0,$ville_end));
		else $ville=$bib_coord->ville;
		$date = $ville.$this->sep_ville_date.format_date(today());
		$this->PDF->setFontSize($this->fs_date);
		$this->PDF->SetXY($this->x_date, $this->y_date);
		$this->PDF->Cell($this->l_date, $this->h_date, $date, 0, 0, 'L', 0);
		
		//Affichage coordonnees fournisseur
		$this->display_supplier();
		
		//Affichage adresse bibliotheque
		$adr_rel=''; 
		if($bib_coord->libelle != '') $adr_rel.= $bib_coord->libelle."\n"; 
		if($bib_coord->adr1 != '') $adr_rel.= $bib_coord->adr1."\n";
		if($bib_coord->adr2 != '') $adr_rel.= $bib_coord->adr2."\n";
		if($bib_coord->cp != '') $adr_rel.= $bib_coord->cp." ";
		if($bib_coord->ville != '') $adr_rel.= $bib_coord->ville."\n";
		if($bib_coord->tel1 != '') $adr_rel.= $this->text_adr_tel." ".$bib_coord->tel1."\n";
		if($bib_coord->fax != '') $adr_rel.= $this->text_adr_fax." ".$bib_coord->fax."\n";
		if($bib_coord->email != '') $adr_rel.= $this->text_adr_email." ".$bib_coord->email."\n";
		$this->PDF->setFontSize($this->fs_adr_rel);
		$this->PDF->SetXY($this->x_adr_rel, $this->y_adr_rel);
		$this->PDF->MultiCell($this->l_adr_rel, $this->h_adr_rel, $adr_rel, 1, 'L', 0);
		
		//Affichage numero client
		$numero_cli = $this->text_num_cli." ".$fou->num_cp_client;
		$this->PDF->SetFontSize($this->fs_num_cli);
		$this->PDF->SetXY($this->x_num_cli, $this->y_num_cli);
		$this->PDF->Cell($this->l_num_cli, $this->h_num_cli, $numero_cli, 0, 0, 'L', 0);
		$this->PDF->Ln();
		
		//Affichage titre
		$this->PDF->setFontSize($this->fs_titre);
		$this->PDF->SetXY($this->x_titre, $this->y_titre);
		$this->PDF->Cell($this->l_titre, $this->h_titre, $this->text_titre, 0, 0, 'L', 0);
		
		//Affichage tiret pliage 
		$this->PDF->Line(0,105, 3, 105);
		$this->y=$this->PDF->GetY();
		$this->PDF->Ln();
		$this->PDF->Ln();

		//Affichage texte before
		if ($this->text_before != '') {
			$this->PDF->SetFontSize($this->fs);
			$this->PDF->MultiCell($this->w, $this->h_tab, $this->text_before, 0, 'J', 0);
		}
		
		//Affichage des lignes de relances
		$this->PDF->SetAutoPageBreak(false);
		$this->PDF->AliasNbPages();
	
		$this->PDF->SetFontSize($this->fs_tab);
		$this->PDF->SetFillColor(230);
		$this->y = $this->PDF->GetY();
		$this->PDF->SetXY($this->x_tab,$this->y);
		
		foreach($tab_act as $id_act=>$tab_lig) {
			
			$this->p_header = false;
			$act = new actes($id_act);
			$this->text_num_ech = $this->text_num.' '.$act->numero;
			if ($act->date_ech!='0000-00-00') $this->text_num_ech.= ' '.sprintf($this->text_ech,format_date($act->date_ech));
			
			foreach($tab_lig as $id_lig) {
				
				$lig = new lignes_actes($id_lig);
				$typ = new types_produits($lig->num_type);
				$col1 = $typ->libelle;
				if($lig->code) $col1.= "\n".$lig->code;
				$col2 = $lig->libelle;
				$col3 = $lig->nb;
				$col4 = $lig->getNbDelivered();
				$col5 = $col3-$col4;
				
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
			
			}
		}

		$this->PDF->SetAutoPageBreak(true, $this->marge_bas);
		$this->PDF->SetX($this->marge_gauche);
		$this->PDF->SetY($this->y);
		$this->PDF->Ln();
		$this->PDF->SetFontSize($this->fs);
	
		//Affichage texte after
		$this->PDF->Ln();
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
	
	public function getLettre($format=0,$name='lettre_relance.pdf') {
		if (!$format) {
			return $this->PDF->OutPut();
		} else {
			return $this->PDF->OutPut($name,'S');
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
	
}

class lettreRelance_PDF_factory {
    
    public static function make() {
        
        global $acquisition_pdfrel_print, $base_path;
        $className = 'lettreRelance_PDF';
        if (file_exists("$base_path/classes/$acquisition_pdfrel_print.class.php")) {
            require_once("$base_path/classes/$acquisition_pdfrel_print.class.php");
            $className = $acquisition_pdfrel_print;
        }
        return new $className();
    }
}

class lettreRelance_RTF {
	
	public $RTF;
	public $sect;
	public $orient_page = 'P';			//Orientation page (P=portrait, L=paysage)
	public $largeur_page = 21;			//Largeur de page
	public $hauteur_page = 29.7;		//Hauteur de page
	public $unit = 'cm';				//Unite 
	public $marge_haut = 1;			//Marge haut
	public $marge_bas = 2;				//Marge bas
	public $marge_droite = 1;			//Marge droite
	public $marge_gauche = 1;			//Marge gauche
	public $w = 19;					//Largeur utile page
	public $fonts = array();			//Tableau de polices
	public $font = 'Helvetica';		//Nom police
	public $fs = 10;					//Taille police 
	public $x_logo = 1;				//Distance du logo / bord gauche de page
	public $y_logo = 1;				//Distance du logo / bord haut de page
	public $l_logo = 2;				//Largeur logo
	public $h_logo = 2;				//Hauteur logo
	public $x_raison = 3.5;			//Distance raison sociale / bord gauche de page
	public $y_raison = 1;				//Distance raison sociale / bord haut de page
	public $l_raison = 10;				//Largeur raison sociale
	public $h_raison = 1;				//Hauteur raison sociale
	public $fs_raison = 16;			//Taille police raison sociale
	public $x_date = 15;				//Distance date / bord gauche de page
	public $y_date = 1;				//Distance date / bord haut de page
	public $l_date = 0;				//Largeur date
	public $h_date = 6;				//Hauteur date
	public $fs_date = 8;				//Taille police date
	public $sep_ville_date = '';		//Séparateur entre ville et date
	public $x_adr_rel = 1;				//Distance adr relance / bord gauche de page
	public $y_adr_rel = 3.5;			//Distance adr relance / bord haut de page
	public $l_adr_rel = 6;				//Largeur adr relance
	public $h_adr_rel = 0.5;			//Hauteur adr relance
	public $fs_adr_rel = 10;			//Taille police adr relance
	public $text_adr_tel = '';
	public $text_adr_fax = '';
	public $text_adr_email = '';
	public $x_adr_fou = 10;			//Distance adr fournisseur / bord gauche de page
	public $y_adr_fou = 5.5;			//Distance adr fournisseur / bord haut de page
	public $l_adr_fou = 10;			//Largeur adr fournisseur
	public $h_adr_fou = 0.6;			//Hauteur adr fournisseur
	public $fs_adr_fou = 14;			//Police adr fournisseur
	public $x_titre = 1;				//Distance titre / bord gauche de page
	public $y_titre = 9;				//Distance titre / bord haut de page
	public $l_titre = 10;				//Largeur titre
	public $h_titre = 1;				//Hauteur titre
	public $fs_titre = 16;				//Police titre
	public $text_titre = '';
	public $x_num = 1;					//Distance num commande/devis / bord gauche de page
	public $l_num = 0;					//Largeur num commande
	public $h_num = 1;					//Hauteur num commande
	public $fs_num = 16;				//Taille police num commande/devis
	public $text_num = '';				//Texte commande/devis
	public $text_ech = '';				//Texte date echeance
	public $text_num_ech = '';
	public $x_num_cli = 1;				//Distance num client / bord gauche de page
	public $y_num_cli = 8;				//Distance num client / bord haut de page
	public $l_num_cli = 0;				//Largeur num commande
	public $h_num_cli = 1;				//Hauteur num commande
	public $fs_num_cli = 16;			//Taille police num commande/devis
	public $text_num_cli = '';			//Texte numéro client
	public $text_before = '';			//texte avant table relances
	public $text_after = '';			//texte après table relances
	public $h_tab = 0.5;				//Hauteur de ligne table relance
	public $fs_tab = 10;				//Taille police table relance
	public $x_tab = 1;					//position table relance / bord droit page 
	public $y_tab = 1;					//position table relance / haut page sur pages 2 et + 
	public $x_sign = 1;				//Distance signature / bord gauche de page
	public $l_sign = 6;				//Largeur cellule signature
	public $h_sign = 0.5;				//Hauteur signature
	public $fs_sign = 10;				//Taille police signature
	public $text_sign = '';			//Texte signature
	public $y_footer = 1.5;			//Distance footer / bas de page
	public $fs_footer = 8;				//Taille police footer
	public $msg_footer = '';
	public $x_col1 = '';
	public $w_col1 = '';
	public $txt_header_col1 = '';		//Hauteur entete tableau
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
	public $tab = null;
	public $row = 1;
	public $filename = 'lettre_relance.rtf';
	
	public function __construct() {
		
		global $msg, $pmb_pdf_font;
		global $acquisition_pdfrel_orient_page, $acquisition_pdfrel_text_size, $acquisition_pdfrel_format_page, $acquisition_pdfrel_marges_page;
		global $acquisition_pdfrel_pos_logo, $acquisition_pdfrel_pos_raison, $acquisition_pdfrel_pos_date;
		global $acquisition_pdfrel_pos_adr_rel, $acquisition_pdfrel_pos_adr_fou, $acquisition_pdfrel_pos_num, $acquisition_pdfrel_text_before;
		global $acquisition_pdfrel_text_after, $acquisition_pdfrel_tab_rel, $acquisition_pdfrel_pos_sign, $acquisition_pdfrel_text_sign;
		global $acquisition_pdfrel_pos_footer, $acquisition_pdfrel_pos_titre, $acquisition_pdfrel_pos_num_cli ;
		
		
		$this->RTF = rtf_factory::make();
		if($acquisition_pdfrel_orient_page=='L') $this->RTF->setLandscape();
		
		$format_page = explode('x',$acquisition_pdfrel_format_page);
		if($format_page[0]) $this->largeur_page = $format_page[0] / 10;
		if($format_page[1]) $this->hauteur_page = $format_page[1] / 10;
		$this->RTF->paperHeight = $this->hauteur_page;
		$this->RTF->paperWidth = $this->largeur_page;
		
		$marges_page = explode(',', $acquisition_pdfrel_marges_page);
		if ($marges_page[0]) $this->marge_haut = $marges_page[0] / 10;
		if ($marges_page[1]) $this->marge_bas = $marges_page[1] / 10;
		if ($marges_page[2]) $this->marge_droite = $marges_page[2] / 10;
		if ($marges_page[3]) $this->marge_gauche = $marges_page[3] / 10;
		
		$this->w = $this->largeur_page-$this->marge_droite-$this->marge_gauche;
		
		$this->font = $pmb_pdf_font;
		if($acquisition_pdfrel_text_size) $this->fs = $acquisition_pdfrel_text_size;
		$this->fonts['standard'] = new PHPRtfLite_Font($this->fs, $this->font);
		
		$pos_logo = explode(',', $acquisition_pdfrel_pos_logo);
		if ($pos_logo[0]) $this->x_logo = $pos_logo[0] / 10;
		if ($pos_logo[1]) $this->y_logo = $pos_logo[1] / 10;
		if ($pos_logo[2]) $this->l_logo = $pos_logo[2] / 10;
		if ($pos_logo[3]) $this->h_logo = $pos_logo[3] / 10;

		$pos_raison = explode(',', $acquisition_pdfrel_pos_raison);
		if ($pos_raison[0]) $this->x_raison = $pos_raison[0] / 10;
		if ($pos_raison[1]) $this->y_raison = $pos_raison[1] / 10;
		if ($pos_raison[2]) $this->l_raison = $pos_raison[2] / 10;
		if ($pos_raison[3]) $this->h_raison = $pos_raison[3] / 10;
		if ($pos_raison[4]) $this->fs_raison = $pos_raison[4];
		$this->fonts['raison'] = new PHPRtfLite_Font($this->fs_raison, $this->font);
		
		$pos_date = explode(',', $acquisition_pdfrel_pos_date);
		if ($pos_date[0]) $this->x_date = $pos_date[0] / 10;
		if ($pos_date[1]) $this->y_date = $pos_date[1] / 10;
		if ($pos_date[2]) $this->l_date = $pos_date[2] / 10;
		if ($pos_date[3]) $this->h_date = $pos_date[3] / 10;
		if ($pos_date[4]) $this->fs_date = $pos_date[4];
		$this->fonts['date'] = new PHPRtfLite_Font($this->fs_date, $this->font);
		$this->sep_ville_date = $msg['acquisition_act_sep_ville_date'];
		
		$pos_adr_rel = explode(',', $acquisition_pdfrel_pos_adr_rel);
		if ($pos_adr_rel[0]) $this->x_adr_rel = $pos_adr_rel[0] / 10;
		if ($pos_adr_rel[1]) $this->y_adr_rel = $pos_adr_rel[1] / 10;
		if ($pos_adr_rel[2]) $this->l_adr_rel = $pos_adr_rel[2] / 10;
		if ($pos_adr_rel[3]) $this->h_adr_rel = $pos_adr_rel[3] / 10;
		if ($pos_adr_rel[4]) $this->fs_adr_rel = $pos_adr_rel[4];
		$this->fonts['adr_rel'] = new PHPRtfLite_Font($this->fs_adr_rel, $this->font);
		$this->text_adr_tel = $msg['acquisition_tel'].".";
		$this->text_adr_fax = $msg['acquisition_fax'].".";
		$this->text_adr_email = $msg['acquisition_mail']." :";
		
		$pos_adr_fou = explode(',', $acquisition_pdfrel_pos_adr_fou);
		if ($pos_adr_fou[0]) $this->x_adr_fou = $pos_adr_fou[0] / 10;
		if ($pos_adr_fou[1]) $this->y_adr_fou = $pos_adr_fou[1] / 10;
		if ($pos_adr_fou[2]) $this->l_adr_fou = $pos_adr_fou[2] / 10;
		if ($pos_adr_fou[3]) $this->h_adr_fou = $pos_adr_fou[3] / 10;
		if ($pos_adr_fou[4]) $this->fs_adr_fou = $pos_adr_fou[4];
		$this->fonts['adr_fou'] = new PHPRtfLite_Font($this->fs_adr_fou, $this->font);
		
		$pos_titre = explode(',', $acquisition_pdfrel_pos_titre);
		if ($pos_titre[0]) $this->x_titre = $pos_titre[0] / 10;
		if ($pos_titre[1]) $this->y_titre = $pos_titre[1] / 10;
		if ($pos_titre[2]) $this->l_titre = $pos_titre[2] / 10;
		if ($pos_titre[3]) $this->h_titre = $pos_titre[3] / 10;
		if ($pos_titre[4]) $this->fs_titre = $pos_titre[4];
		$this->fonts['titre'] = new PHPRtfLite_Font($this->fs_titre, $this->font);
		$this->text_titre = $msg['acquisition_recept_lettre_titre'];
		
		$pos_num = explode(',', $acquisition_pdfrel_pos_num);
		if ($pos_num[0]) $this->x_num = $pos_num[0] / 10;
		if ($pos_num[2]) $this->l_num = $pos_num[1] / 10;
		if ($pos_num[3]) $this->h_num = $pos_num[2] / 10;
		if ($pos_num[4]) $this->fs_num = $pos_num[3];
		$this->fonts['num'] = new PHPRtfLite_Font($this->fs_num, $this->font);
		$this->text_num = $msg['acquisition_act_num_cde'];
		$this->text_ech = $msg['acquisition_recept_lettre_ech'];
				
		$pos_num_cli = explode(',', $acquisition_pdfrel_pos_num_cli);
		if ($pos_num_cli[0]) $this->x_num_cli = $pos_num_cli[0] / 10;
		if ($pos_num_cli[0]) $this->x_num_cli = $pos_num_cli[0] / 10;
		if ($pos_num_cli[2]) $this->l_num_cli = $pos_num_cli[1] / 10;
		if ($pos_num_cli[3]) $this->h_num_cli = $pos_num_cli[2] / 10;
		if ($pos_num_cli[4]) $this->fs_num_cli = $pos_num_cli[3];
		$this->fonts['num_cli'] = new PHPRtfLite_Font($this->fs_num_cli, $this->font);
		$this->text_num_cli = $msg['acquisition_num_cp_client'];
		
		$this->text_before = $acquisition_pdfrel_text_before;
		$this->text_after = $acquisition_pdfrel_text_after;
		
		$pos_tab = explode(',', $acquisition_pdfrel_tab_rel);
		if ($pos_tab[0]) $this->h_tab = $pos_tab[0] / 10;
		if ($pos_tab[1]) $this->fs_tab = $pos_tab[1] /10;
		$this->x_tab = $this->marge_gauche;
		$this->y_tab = $this->marge_haut; 
		
		$pos_sign = explode(',', $acquisition_pdfrel_pos_sign);
		if ($pos_sign[0]) $this->x_sign = $pos_sign[0] / 10;
		if ($pos_sign[1]) $this->l_sign = $pos_sign[1] / 10;
		if ($pos_sign[2]) $this->h_sign = $pos_sign[2] / 10;
		if ($pos_sign[3]) $this->fs_sign = $pos_sign[3];
		$this->fonts['sign'] = new PHPRtfLite_Font($this->fs_sign, $this->font);
		
			
		if ($acquisition_pdfrel_text_sign) $this->text_sign = $acquisition_pdfrel_text_sign; 
		else $this->text_sign = $msg['acquisition_act_sign'];
		
		$pos_footer = explode(',', $acquisition_pdfrel_pos_footer);
		if ($pos_footer[0]) $this->PDF->y_footer = $pos_footer[0] / 10;
			else $this->PDF->y_footer=$this->y_footer;
		if ($pos_footer[1]) $this->PDF->fs_footer = $pos_footer[1] / 10;
			else $this->PDF->fs_footer=$this->fs_footer;
		
		$this->x_col1 =  $this->x_tab;
		$this->w_col1 = floor($this->w*20/100);
		$this->txt_header_col1 = $msg['acquisition_act_tab_typ']."\n".$msg['acquisition_act_tab_code'];
		
		$this->x_col2 = $this->x_col1 + $this->w_col1;
		$this->w_col2 = floor($this->w*50/100);
		$this->txt_header_col2 = $msg['acquisition_act_tab_lib'];
		
		$this->x_col3 = $this->x_col2 + $this->w_col2;
		$this->w_col3 = floor(($this->w-$this->w_col1-$this->w_col2)/3);
		$this->txt_header_col3 = $msg['acquisition_qte_cde'];
		
		$this->x_col4 = $this->x_col3 + $this->w_col3;
		$this->w_col4 = floor(($this->w-$this->w_col1-$this->w_col2)/3);
		$this->txt_header_col4 = $msg['acquisition_qte_liv'];
		
		$this->x_col5 = $this->x_col4 + $this->w_col4;
		$this->w_col5 = floor(($this->w-$this->w_col1-$this->w_col2)/3); 
		$this->txt_header_col5 = $msg['acquisition_act_tab_sol'];
		
		$this->RTF->setMargins($this->marge_gauche, $this->marge_haut, $this->marge_droite ,$this->marge_bas);

		$this->RTF->addFooter('all');
		$this->msg_footer = $this->RTF->to_utf8($msg['acquisition_act_page']);
	}
	
	public function doLettre(&$bib, &$bib_coord, &$fou, &$fou_coord, &$tab_act) {
		$this->sect = $this->RTF->addSection();
		//$this->RTF->footers[] = $this->msg_footer; 
		
		$tab1 = $this->sect->addTable();
		$tab1->addRows(1,0);
		$tab1->addColumnsList(array( 	$this->x_raison - $this->x_logo, 
										$this->x_date - $this->x_raison, 
										$this->largeur_page - $this->marge_droite - $this->x_date
									)
								);
		//$this->PDF->npage = 1;
		
		//Affichage logo
		if($bib->logo != '') {
			$par_logo = new PHPRtfLite_ParFormat();
			$tab1->addImageToCell(1, 1, $bib->logo, $par_logo, $this->l_logo, $this->h_logo);		
		}
		
		//Affichage raison sociale
		$raison = $this->RTF->to_utf8($bib->raison_sociale);
		$par_raison = new PHPRtfLite_ParFormat();
		$tab1->writeToCell(1,2,$raison, $this->fonts['raison'], $par_raison);
		
		//Affichage date ville
		$ville_end=stripos($bib_coord->ville,"cedex");	
		if($ville_end!==false) $ville=trim(substr($bib_coord->ville,0,$ville_end));
		else $ville=$bib_coord->ville;
		$date = $ville.$this->sep_ville_date.format_date(today());
		$date = $this->RTF->to_utf8($date);
		$par_ville = new PHPRtfLite_ParFormat();
		$tab1->writeToCell(1,3,$date, $this->fonts['date'], $par_ville);
				
		$this->sect->writeText('', $this->fonts['standard'], new PHPRtfLite_ParFormat());
		
		$tab2 = $this->sect->addTable();
		$tab2->addRows(1,0);
		$tab2->addColumnsList(array( 	$this->l_adr_rel - $this->x_adr_rel, 
										$this->x_adr_fou - $this->l_adr_rel - $this->x_adr_rel,
										$this->largeur_page - $this->x_adr_fou
									)
								);
		
		//Affichage adresse bibliotheque
		$adr_rel=''; 
		if($bib_coord->libelle != '') $adr_rel.= $bib_coord->libelle."\r\n"; 
		if($bib_coord->adr1 != '') $adr_rel.= $bib_coord->adr1."\r\n";
		if($bib_coord->adr2 != '') $adr_rel.= $bib_coord->adr2."\r\n";
		if($bib_coord->cp != '') $adr_rel.= $bib_coord->cp." ";
		if($bib_coord->ville != '') $adr_rel.= $bib_coord->ville."\r\n";
		if($bib_coord->tel1 != '') $adr_rel.= $this->text_adr_tel." ".$bib_coord->tel1."\r\n";
		if($bib_coord->fax != '') $adr_rel.= $this->text_adr_fax." ".$bib_coord->fax."\r\n";
		if($bib_coord->email != '') $adr_rel.= $this->text_adr_email." ".$bib_coord->email."\r\n";
		$adr_rel = $this->RTF->to_utf8($adr_rel);
		$par_adr_rel = new PHPRtfLite_ParFormat();
		$tab2->writeToCell(1,1,$adr_rel, $this->fonts['adr_rel'], $par_adr_rel);
										
		//Affichage coordonnees fournisseur
		//si pas de raison sociale définie, on reprend le libellé
		//si il y a une raison sociale, pas besoin 
		if($fou->raison_sociale != '') {
			$adr_fou = $fou->raison_sociale."\r\n";
		} else { 
			$adr_fou = $coord_fou->libelle."\r\n";
		}
		if($fou_coord->adr1 != '') $adr_fou.= $fou_coord->adr1."\r\n";
		if($fou_coord->adr2 != '') $adr_fou.= $fou_coord->adr2."\r\n";
		if($fou_coord->cp != '') $adr_fou.= $fou_coord->cp." ";
		if($fou_coord->ville != '') $adr_fou.= $fou_coord->ville."\r\n\r\n";
		if ($fou_coord->contact != '') $adr_fou.= $fou_coord->contact;
		$adr_fou = $this->RTF->to_utf8($adr_fou);
		$par_adr_fou = new PHPRtfLite_ParFormat();
		$tab2->writeToCell(1,3,$adr_fou, $this->fonts['adr_fou'], $par_adr_fou);
		
		
		//Affichage numero client
		$numero_cli = $this->RTF->to_utf8($this->text_num_cli." ".$fou->num_cp_client);
		$par_numero_cli = new PHPRtfLite_ParFormat();
		$par_numero_cli->setSpaceAfter(10);
		$this->sect->writeText($numero_cli, $this->fonts['num_cli'], $par_numero_cli);
		
		//Affichage titre
		$text_titre = $this->RTF->to_utf8($this->text_titre);
		$par_titre = new PHPRtfLite_ParFormat();
		$par_titre->setSpaceAfter(10);
		$par_titre->setIndentLeft($this->x_titre - $this->marge_gauche);
		$this->sect->writeText($text_titre, $this->fonts['titre'], $par_titre);

		//Affichage texte before
		if ($this->text_before != '') {
			$text_before = $this->RTF->to_utf8($this->text_before);
			$par_before = new PHPRtfLite_ParFormat();
			$this->sect->writeText($text_before, $this->fonts['standard'], $par_before);
		}
		//Affichage des lignes de relances
		foreach($tab_act as $id_act=>$tab_lig) {
			
			$this->p_header = false;
			$act = new actes($id_act);
			$this->text_num_ech = $this->text_num.' '.$act->numero;
			if ($act->date_ech!='0000-00-00') $this->text_num_ech.= ' '.sprintf($this->text_ech,format_date($act->date_ech));
			$this->doEntete();
			
			foreach($tab_lig as $id_lig) {
				
				$lig = new lignes_actes($id_lig);
				$typ = new types_produits($lig->num_type);
				$col1 = $typ->libelle;
				if($lig->code) $col1.= "\r\n".$lig->code;
				$col2 = $lig->libelle;
				$col3 = $lig->nb;
				$col4 = $lig->getNbDelivered();
				$col5 = $col3-$col4;
				
				$this->tab->addRows(1,0);
				$this->tab->addColumnsList(array( 	$this->w_col1, 
													$this->w_col2,
													$this->w_col3,
													$this->w_col4,
													$this->w_col5
												)
											);
				$border_format = new PHPRtfLite_Border_Format(0.5, "#000000");

				$txt_col1 = $this->RTF->to_utf8($col1);
				$par_col1 = new PHPRtfLite_ParFormat();
				$this->tab->writeToCell($this->row,1,$txt_col1, $this->fonts['standard'], $par_col1);
				
				$txt_col2 = $this->RTF->to_utf8($col2);
				$par_col2 = new PHPRtfLite_ParFormat();
				$this->tab->writeToCell($this->row,2,$txt_col2, $this->fonts['standard'], $par_col2);

				$txt_col3 = $this->RTF->to_utf8($col3);
				$par_col3 = new PHPRtfLite_ParFormat();
				$this->tab->writeToCell($this->row,3,$txt_col3, $this->fonts['standard'], $par_col3);
				
				$txt_col4 = $this->RTF->to_utf8($col4);
				$par_col4 = new PHPRtfLite_ParFormat();
				$this->tab->writeToCell($this->row,4,$txt_col4, $this->fonts['standard'], $par_col4);
				
				$txt_col5 = $this->RTF->to_utf8($col5);
				$par_col5 = new PHPRtfLite_ParFormat();
				$this->tab->writeToCell($this->row,5,$txt_col5, $this->fonts['standard'], $par_col5);
				
				$this->tab->setBordersOfCells($border_format, 1, 1, $this->row, 5);
				
				$this->row++;
			}
		}

		//Affichage texte after
		if ($this->text_after != '') {
			$text_after = $this->RTF->to_utf8($this->text_after);
			$par_after = new PHPRtfLite_ParFormat();
			
			$this->sect->writeText($text_after, $this->fonts['standard'], $par_after);
		}
		
		//Affichage signature
		$text_sign = $this->RTF->to_utf8($this->text_sign);
		$par_sign = new PHPRtfLite_ParFormat();
		$par_sign->setSpaceBefore(10);
		$par_sign->setIndentLeft($this->x_sign - $this->marge_gauche);
		$this->sect->writeText($text_sign, $this->fonts['sign'], $par_sign);
		$this->sect->insertPageBreak();
	}
	
	
	public function getLettre($name='lettre_relance') {	
		
		return $this->RTF->sendRtf($name);
	}	
	
	public function getFileName() {
		return $this->filename;
	}
	
	public function doEntete() {

		$text_num_ech = $this->RTF->to_utf8($this->text_num_ech);
		$par_num_ech = new PHPRtfLite_ParFormat();
		$par_num_ech->setSpaceBefore(10);
		$par_num_ech->setSpaceAfter(10);
		$this->sect->writeText($text_num_ech, $this->fonts['standard'], $par_num_ech);
		
		$this->tab = $this->sect->addTable();
		$this->row=1;
				
		$this->tab->addRows(1,0);
		$this->tab->addColumnsList(array( 	$this->w_col1, 
											$this->w_col2,
											$this->w_col3,
											$this->w_col4,
											$this->w_col5
										)
									);
		$border_format = new PHPRtfLite_Border_Format(0.5, "#000000");
		$txt_header_col1 = $this->RTF->to_utf8($this->txt_header_col1);
		$par_header_col1 = new PHPRtfLite_ParFormat();
		$this->tab->writeToCell($this->row,1,$txt_header_col1, $this->fonts['standard'], $par_header_col1);
		$txt_header_col2 = $this->RTF->to_utf8($this->txt_header_col2);
		$par_header_col2 = new PHPRtfLite_ParFormat();
		$this->tab->writeToCell($this->row,2,$txt_header_col2, $this->fonts['standard'], $par_header_col2);
		$txt_header_col3 = $this->RTF->to_utf8($this->txt_header_col3);
		$par_header_col3 = new PHPRtfLite_ParFormat();
		$this->tab->writeToCell($this->row,3,$txt_header_col3, $this->fonts['standard'], $par_header_col3);
		$txt_header_col4 = $this->RTF->to_utf8($this->txt_header_col4);
		$par_header_col4 = new PHPRtfLite_ParFormat();
		$this->tab->writeToCell($this->row,4,$txt_header_col4, $this->fonts['standard'], $par_header_col4);
		$txt_header_col5 = $this->RTF->to_utf8($this->txt_header_col5);
		$par_header_col5 = new PHPRtfLite_ParFormat();
		$this->tab->writeToCell($this->row,5,$txt_header_col5, $this->fonts['standard'], $par_header_col5);
		$this->tab->setBordersOfCells($border_format, 1, 1, 1, 5);
		$this->tab->setBackgroundOfCells('#D3D3D3', 1, 1, 1, 5); 
		$this->row++;
	}
}