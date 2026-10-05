<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_abts_PDF.class.php,v 1.1 2019/08/02 10:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/lettre_PDF.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/coordonnees.class.php");
require_once($class_path."/serial_display.class.php");

class lettre_abts_PDF extends lettre_PDF {
	
    protected $liste_rel;
    
    protected function _init_PDF() {
        if(!empty($this->get_parameter_value('format_page'))) {
            $this->PDF = pdf_factory::make($this->get_parameter_value('format_page'), $this->unit, array($this->get_parameter_value('largeur_page'), $this->get_parameter_value('hauteur_page')));
        } else {
            $this->PDF = pdf_factory::make('P', 'mm', 'A4');
        }
    }
	
	protected function display_biblio_info($x=0, $y=0, $short=0) {
        global $msg;
	    global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_phone, $biblio_email;
	    
	    //Affichage Bibli / date édition
	    $this->PDF->setFontSize(12);
	    $this->PDF->Cell(150,4,encoding_normalize::utf8_normalize($biblio_name),0);
	    $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($msg['fpdf_edite']." ".formatdate(date("Y-m-d",time()))),0);
	    $this->PDF->Ln();
	    $this->PDF->setFontSize(10);
	    if($biblio_adr1){
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($biblio_adr1),0);
	        $this->PDF->Ln();
	    }
	    if($biblio_adr2){
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($biblio_adr2),0);
	        $this->PDF->Ln();
	    }
	    if($biblio_cp || $biblio_town){
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize(trim($biblio_cp." ".$biblio_town)),0);
	        $this->PDF->Ln();
	    }
	    if($biblio_phone){
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($biblio_phone),0);
	        $this->PDF->Ln();
	    }
	    if($biblio_email){
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($biblio_email),0);
	        $this->PDF->Ln();
	    }
	}
		
	protected function display_supplier($id_fournisseur) {
	    global $msg;
	    
	    $retraitFournisseur=100;
	    
	    $fou = new entites($id_fournisseur);
	    $coord_fou = entites::get_coordonnees($id_fournisseur,1);
	    $coord_fou = pmb_mysql_fetch_object($coord_fou);
	    if($fou->raison_sociale != '') {
	        $libelleFou = $fou->raison_sociale;
	    } else {
	        $libelleFou = $coord_fou->libelle;
	    }
	    $this->PDF->setFontSize(12);
	    $this->PDF->Cell($retraitFournisseur,4,"",0);
	    $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($libelleFou),0);
	    $this->PDF->Ln();
	    $this->PDF->setFontSize(10);
	    if($coord_fou->adr1){
	        $this->PDF->Cell($retraitFournisseur,4,"",0);
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($coord_fou->adr1),0);
	        $this->PDF->Ln();
	    }
	    if($coord_fou->adr2){
	        $this->PDF->Cell($retraitFournisseur,4,"",0);
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($coord_fou->adr2),0);
	        $this->PDF->Ln();
	    }
	    if($coord_fou->cp){
	        $this->PDF->Cell($retraitFournisseur,4,"",0);
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($coord_fou->cp),0);
	        $this->PDF->Ln();
	    }
	    if($coord_fou->ville){
	        $this->PDF->Cell($retraitFournisseur,4,"",0);
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($coord_fou->ville),0);
	        $this->PDF->Ln();
	    }
	    if($coord_fou->contact!=''){
	        $this->PDF->Cell($retraitFournisseur,4,"",0);
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($msg['acquisition_act_formule']." ".$coord_fou->contact),0);
	        $this->PDF->Ln();
	    }
	}
	
	public function doLettre() {
	    global $msg;
	    
	    foreach($this->liste_rel as $id_fournisseur =>$info_fournisseur ){
	        //Nouvelle page
	        $this->PDF->addPage();
	        
	        $this->display_biblio_info();
	        //Fournisseur
	        if($id_fournisseur){
	            //Affichage fournisseur
	            $this->PDF->Ln();
	            $this->PDF->Ln();
	            $this->display_supplier($id_fournisseur);
	        }
	        
	        $this->PDF->Ln();
	        $this->PDF->Ln();
	        $this->PDF->Ln();
	        $this->PDF->Ln();
	        $this->PDF->Ln();
	        $this->PDF->setFontSize(10);
	        $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($msg["abts_gestion_retard_lettre_monsieur"]),0);
	        $this->PDF->Ln();
	        $this->PDF->Ln();
	        $this->PDF->Ln();
	        
	        foreach($info_fournisseur as $num_notice =>$info_notice ){
	            $perio= new serial_display($num_notice);
	            $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($perio->notice->tit1),0);
	            $this->PDF->Ln();
	            $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize("________________________________________"),0);
	            $this->PDF->Ln();
	            $this->PDF->Ln();
	            foreach($info_notice as $abt_num => $info_abt){
	                foreach($info_abt as $rel_id => $rel_info){
	                    $this->PDF->SetFont('','U');
	                    $this->PDF->Cell(20,4,encoding_normalize::utf8_normalize($rel_info["rel_libelle_numero"]." :"),0,0);
	                    $this->PDF->SetFont('','');
	                    $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize(formatdate($rel_info["rel_date_parution"])),0);
	                    $this->PDF->Ln();
	                    $this->PDF->Cell(0,4,encoding_normalize::utf8_normalize($rel_info["rel_comment_gestion"]),0);
	                    $this->PDF->Ln();
	                }
	            }
	        }
	    }
	    $this->PDF->OutPut();
	}

	public function set_liste_rel($liste_rel) {
	    $this->liste_rel = $liste_rel;
	}
}