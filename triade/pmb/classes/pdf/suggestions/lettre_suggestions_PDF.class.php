<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_suggestions_PDF.class.php,v 1.4 2022/09/20 08:13:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/lettre_PDF.class.php");
require_once($class_path.'/suggestions.class.php');
require_once($class_path.'/suggestions_origine.class.php');
require_once($class_path.'/suggestions_map.class.php');
require_once($class_path.'/analyse_query.class.php');

class lettre_suggestions_PDF extends lettre_PDF {
	
    public $fs = 8;
    
    public $x_titre = 10;				//Distance titre / bord gauche de page
    public $y_titre = 10;				//Distance titre / bord haut de page
    public $l_titre = 100;				//Largeur titre
    public $h_titre = 10;				//Hauteur titre
    public $fs_titre = 16;				//Police titre
	public $x_date = 170;				//Distance date / bord gauche de page
	public $y_date = 10;				//Distance date / bord haut de page
	public $l_date = 0;				//Largeur date
	public $h_date = 6;				//Hauteur date
	public $fs_date = 8;				//Taille police date
	public $h_tab = 5;					//Hauteur de ligne table acte
	public $fs_tab = 8;				//Taille police table acte
	public $x_tab = 10;				//position table acte / bord gauche page 
	public $y_tab = 10;				//position table acte / haut page sur pages 2 et + 
	public $y_footer = 15;				//Distance footer / bas de page
	public $fs_footer = 8;				//Taille police footer
	public $y = 0;
	public $h = 0;
	public $s = 0;
	public $h_header = 0;
	
	protected static function get_parameter_prefix() {
	    return 'acquisition_pdfsug';
	}
    
	protected function _init() {
		parent::_init();
		
		$this->_init_pos_titre();
		
		$this->_init_pos_date();
		
		$this->_init_tab();
		$this->x_tab = $this->marge_gauche;
		$this->y_tab = $this->marge_haut;
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
	
	protected function _init_pos_titre() {
	    $pos_titre = explode(',', $this->get_parameter_value('pos_titre'));
	    $this->_init_position('titre', $pos_titre);
	}
	
	protected function _init_pos_date() {
		$pos_date = explode(',', $this->get_parameter_value('pos_date'));
		$this->_init_position('date', $pos_date);
	}
	
	protected function _init_tab() {
	    global $acquisition_pdfsug_tab_sug;
	    
	    $pos_tab = explode(',', $acquisition_pdfsug_tab_sug);
	    if ($pos_tab[0]) $this->h_tab = $pos_tab[0];
	    if ($pos_tab[1]) $this->fs_tab = $pos_tab[1];
	}
		
	protected function display_date() {
		$this->PDF->setFontSize($this->fs_date);
		$this->PDF->SetXY($this->x_date, $this->y_date);
		$this->PDF->Cell($this->l_date, $this->h_date, formatdate(today()), 0, 0, 'L', 0);
	}
	
	protected function display_titre() {
	    global $msg;
	    global $user_input, $origine_id, $type_origine;
	    
	    $us=stripslashes($user_input);
	    $titre ="";
	    if($origine_id){
	        if (is_array($origine_id) && count($origine_id) && is_array($type_origine) && count($type_origine)) {
	            $nom="";
	            foreach($origine_id as $k=>$v) {
	                if ($v) {
	                    if($type_origine[$k]){
	                        $req = "select concat(empr_prenom,' ',empr_nom) as nom from empr where id_empr='".$origine_id[$k]."'";
	                    }else{
	                        $req = "select concat(prenom,' ',nom) as nom from users where userid='".$origine_id[$k]."'";
	                    }
	                    $res_empr = pmb_mysql_query($req);
	                    if($res_empr && pmb_mysql_num_rows($res_empr)){
	                        $empr = pmb_mysql_fetch_object($res_empr);
	                        if($nom)$nom.=", ";
	                        $nom.=$empr->nom;
	                    }
	                }
	            }
	            if($nom){
	                $titre =  sprintf($msg['acquisition_sug_list_origine'],$nom);
	            }
	        }else{
	            if($type_origine){
	                $req = "select concat(empr_prenom,' ',empr_nom) as nom from empr where id_empr='".$origine_id."'";
	            }else{
	                $req = "select concat(prenom,' ',nom) as nom from users where userid='".$origine_id."'";
	            }
	            $res_empr = pmb_mysql_query($req);
	            if($res_empr && pmb_mysql_num_rows($res_empr)){
	                $empr = pmb_mysql_fetch_object($res_empr);
	                $titre =  sprintf($msg['acquisition_sug_list_origine'],$empr->nom);
	            }
	        }
	    }
	    if(!$titre){
	        $titre =  $msg['acquisition_sug_list'].$us;
	    }
	    
	    $this->PDF->setFontSize($this->fs_titre);
	    $this->PDF->SetXY($this->x_titre, $this->y_titre);
	    $this->PDF->Cell($this->l_titre, $this->h_titre, $titre, 0, 0, 'L', 0);
	}

	public function doLettre() {
	    global $msg,$pmb_pdf_font;
	    global $selected_objects;
	    global $x_dat,$x_tit,$x_edi,$x_aut,$x_sta,$x_cat;
	    global $w_dat,$w_tit,$w_edi,$w_aut,$w_sta,$w_cat;
	    
	    $this->PDF->addPage();
	    
	    $this->PDF->setFont($pmb_pdf_font);
	    
	    //Affichage date
	    $this->display_date();
	    
	    //Affichage titre
	    $this->display_titre();
	    
	    
	    //Affichage lignes suggestions
	    $this->PDF->SetAutoPageBreak(false);
	    $this->PDF->AliasNbPages();
	    
	    $this->PDF->SetFontSize($this->fs_tab);
	    $this->PDF->SetFillColor(230);
	    $this->PDF->Ln();
	    $this->y = $this->PDF->GetY();
	    $this->PDF->SetXY($this->x_tab,$this->y);
	    
	    $x_dat =  $this->x_tab;
	    $w_dat = round($this->w*10/100);
	    $x_tit = $x_dat + $w_dat;
	    $w_tit = round($this->w*30/100);
	    $x_edi = $x_tit + $w_tit;
	    $w_edi = round($this->w*20/100);
	    $x_aut = $x_edi + $w_edi;
	    $w_aut = round($this->w*20/100);
	    $x_sta = $x_aut + $w_aut;
	    $w_sta = round($this->w*10/100);
	    $x_cat = $x_sta + $w_sta;
	    $w_cat = round($this->w*10/100);
	    
	    
	    $this->doEntete();
	    
	    if(!empty($selected_objects)) {
	        $query = "select * from suggestions where id_suggestion IN (".addslashes($selected_objects).")";
    	    $res = pmb_mysql_query($query);
    	    
    	    $sug_map = new suggestions_map();
    	    while ($row = pmb_mysql_fetch_object($res)){
    	        $lib_statut = $sug_map->getPdfComment($row->statut);
    	        
    	        
    	        if(!$row->num_notice) $lib_cat='';
    	        else $lib_cat='X';
    	        
    	        
    	        $this->h = $this->h_tab * max( 	$this->PDF->NbLines($w_dat, $row->date_creation),
    	            $this->PDF->NbLines($w_tit, $row->titre),
    	            $this->PDF->NbLines($w_edi, $row->editeur),
    	            $this->PDF->NbLines($w_aut, $row->auteur),
    	            $this->PDF->NbLines($w_sta, $lib_statut),
    	            $this->PDF->NbLines($w_cat, $lib_cat) );
    	        
    	        $this->s = $this->y+$this->h;
    	        if ($this->s > ($this->hauteur_page-$this->marge_bas)){
    	            
    	            $this->PDF->AddPage();
    	            $this->PDF->SetXY($this->x_tab, $this->y_tab);
    	            $this->y = $this->PDF->GetY();
    	            $this->doEntete();
    	            
    	        }
    	        $this->PDF->SetXY($x_dat, $this->y);
    	        $this->PDF->Rect($x_dat, $this->y, $w_dat, $this->h);
    	        $this->PDF->MultiCell($w_dat, $this->h_tab, $row->date_creation, 0, 'L');
    	        $this->PDF->SetXY($x_tit, $this->y);
    	        $this->PDF->Rect($x_tit, $this->y, $w_tit, $this->h);
    	        $this->PDF->MultiCell($w_tit, $this->h_tab, $row->titre, 0, 'L');
    	        $this->PDF->SetXY($x_edi, $this->y);
    	        $this->PDF->Rect($x_edi, $this->y, $w_edi, $this->h);
    	        $this->PDF->MultiCell($w_edi, $this->h_tab, $row->editeur, 0, 'L');
    	        $this->PDF->SetXY($x_aut, $this->y);
    	        $this->PDF->Rect($x_aut, $this->y, $w_aut, $this->h);
    	        $this->PDF->MultiCell($w_aut, $this->h_tab, $row->auteur, 0, 'L');
    	        $this->PDF->SetXY($x_sta, $this->y);
    	        $this->PDF->Rect($x_sta, $this->y, $w_sta, $this->h);
    	        $this->PDF->MultiCell($w_sta, $this->h_tab, $lib_statut, 0, 'L');
    	        $this->PDF->SetXY($x_cat, $this->y);
    	        $this->PDF->Rect($x_cat, $this->y, $w_cat, $this->h);
    	        $this->PDF->MultiCell($w_cat, $this->h_tab, $lib_cat, 0, 'L');
    	        $this->y = $this->y+$this->h;
    	        
    	    }
	    }
	    $this->y = $this->PDF->SetY($this->y);
	    
	    $this->PDF->SetAutoPageBreak(true, $this->marge_bas);
	    $this->PDF->SetX($this->marge_gauche);
	    $this->PDF->Ln();
	    
	    $this->PDF->OutPut();
	}
	
	//Entete de tableau
	public function doEntete() {
	    global $msg;
	    global $x_dat,$x_tit,$x_edi,$x_aut,$x_sta,$x_cat;
	    global $w_dat,$w_tit,$w_edi,$w_aut,$w_sta,$w_cat;
	    
	    $this->h_header = $this->h_tab * max( 	$this->PDF->NbLines($w_dat, $msg['acquisition_sug_dat_cre']),
	        $this->PDF->NbLines($w_tit,$msg['acquisition_sug_tit']),
	        $this->PDF->NbLines($w_edi, $msg['acquisition_sug_edi']),
	        $this->PDF->NbLines($w_aut, $msg['acquisition_sug_aut']),
	        $this->PDF->NbLines($w_sta, $msg['acquisition_sug_etat']),
	        $this->PDF->NbLines($w_cat, $msg['acquisition_sug_iscat'])
	        );
	    $s = $this->y+$this->h_header;
	    if ($s > ($this->hauteur_page-$this->marge_bas)){
	        
	        $this->PDF->AddPage();
	        $this->PDF->SetXY($this->x_tab, $this->y_tab);
	        $this->y = $this->PDF->GetY();
	        
	    }
	    $this->PDF->SetXY($x_dat, $this->y);
	    $this->PDF->Rect($x_dat, $this->y, $w_dat, $this->h_header, 'FD');
	    $this->PDF->MultiCell($w_dat, $this->h_tab, $msg['acquisition_sug_dat_cre'], 0, 'L');
	    $this->PDF->SetXY($x_tit, $this->y);
	    $this->PDF->Rect($x_tit, $this->y, $w_tit, $this->h_header, 'FD');
	    $this->PDF->MultiCell($w_tit, $this->h_tab, $msg['acquisition_sug_tit'], 0, 'L');
	    $this->PDF->SetXY($x_edi, $this->y);
	    $this->PDF->Rect($x_edi, $this->y, $w_edi, $this->h_header, 'FD');
	    $this->PDF->MultiCell($w_edi, $this->h_tab, $msg['acquisition_sug_edi'], 0, 'L');
	    $this->PDF->SetXY($x_aut, $this->y);
	    $this->PDF->Rect($x_aut, $this->y, $w_aut, $this->h_header, 'FD');
	    $this->PDF->MultiCell($w_aut, $this->h_tab, $msg['acquisition_sug_aut'], 0, 'L');
	    $this->PDF->SetXY($x_sta, $this->y);
	    $this->PDF->Rect($x_sta, $this->y, $w_sta, $this->h_header, 'FD');
	    $this->PDF->MultiCell($w_sta, $this->h_tab, $msg['acquisition_sug_etat'], 0, 'L');
	    $this->PDF->SetXY($x_cat, $this->y);
	    $this->PDF->Rect($x_cat, $this->y, $w_cat, $this->h_header, 'FD');
	    $this->PDF->MultiCell($w_cat, $this->h_tab, $msg['acquisition_sug_iscat'], 0, 'L');
	    $this->y = $this->y+$this->h_header;
	}
}