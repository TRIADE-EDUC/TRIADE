<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_card_PDF.class.php,v 1.1.10.1 2024/06/06 13:51:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/reader/lettre_reader_PDF.class.php");
require_once($class_path."/emprunteur.class.php");

class lettre_reader_card_PDF extends lettre_reader_PDF {
	
    protected static function get_parameter_prefix() {
        return "pdfcartelecteur";
    }
		
    protected function _init_PDF() {
    	global $fpdf;
    	
    	define("CBG_NBR_X_CELLS",        4);     // Nombre d'étiquettes en largeur sur la page
    	define("CBG_NBR_Y_CELLS",        19);     // Nombre d'étiquettes en hauteur
    	
    	$nom_classe=$fpdf."_Etiquette";
    	$this->PDF = new $nom_classe(CBG_NBR_X_CELLS, CBG_NBR_Y_CELLS);
    }
    
	protected function _init_default_positions() {
		// marges, mesures en mm
		define("CBG_LEFT_MARGIN",        6);
		define("CBG_RIGHT_MARGIN",       6);
		define("CBG_TOP_MARGIN",         13);
		define("CBG_BOTTOM_MARGIN",      13);
		
		// marges intérieures du bord de l'étiquette au code barre, mesures en mm
		define("CBG_INNER_LEFT_MARGIN",   4);
		define("CBG_INNER_RIGHT_MARGIN",  4);
		define("CBG_INNER_TOP_MARGIN",    1);
		define("CBG_INNER_BOTTOM_MARGIN", 1);
		
		// place allouée au nom de la bibliothèque, mesure en mm
		define("CBG_TEXT_HEIGHT",         2);
		// Taille de la police, en points
		define("CBG_TEXT_FONT_SIZE",      6);
		// Taille du texte du code-barre, 1 : le plus petit ; 5 : le plus grand
		define("CBG_CB_TEXT_SIZE",        3);
		// Résolution du code barre. Si vous augmentez ce paramètre, il faudra peut-être
		// augmenter la taille de la police. Une valeur faible produit un fichier moins volumineux
		define("CBG_CB_RES",              5);
		// l'apparence du code barre dépend étroitement de la résolution et de la taille du texte
	}
	
	public function doLettre($id_empr) {
		global $msg;
		
		//Génération de la lettre dans la langue du lecteur
		$this->set_language(emprunteur::get_lang_empr($id_empr));
		$this->PDF->addPage();
		$this->PDF->SetPageMargins(CBG_TOP_MARGIN, CBG_BOTTOM_MARGIN, CBG_LEFT_MARGIN, CBG_RIGHT_MARGIN);
		
		$requete = "SELECT id_empr, empr_cb, empr_nom, empr_prenom, empr_date_adhesion, empr_date_expiration, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration FROM empr WHERE id_empr='$id_empr' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$empr = pmb_mysql_fetch_object($res);
		
		$pos_x = $this->get_parameter_value('pos_h');
		$pos_y = $this->get_parameter_value('pos_v');
		$biblio_name = $this->get_parameter_value('biblio_name');
		
		$this->PDF->SetFont($this->font, '', 14);
		$this->PDF->SetXY(($pos_x+40 - $this->get_parameter_value('largeur_nom')/2), $pos_y);
		$this->PDF->MultiCell($this->get_parameter_value('largeur_nom'), 7, $empr->empr_prenom." ".$empr->empr_nom, 0, "C", 0);
		
		$largeur_carteno = 70;
		$this->PDF->SetFont($this->font, '', 10);
		$this->PDF->SetXY(($pos_x+40 - $largeur_carteno/2), $pos_y+30);
		$this->PDF->MultiCell($largeur_carteno, 8, $this->get_parameter_value('carteno')." ".$empr->empr_cb, 0, "C", 0);
		
		if($this->get_parameter_value('valabledu') != '' || $this->get_parameter_value('valableau') != '') {
			$largeur_valable = 70;
			$this->PDF->SetFont($this->font, '', 10);
			$this->PDF->SetXY(($pos_x+40 - $largeur_valable/2), $pos_y+35);
			$this->PDF->MultiCell($largeur_valable, 8, $this->get_parameter_value('valabledu')." ".$empr->aff_empr_date_adhesion." ".$this->get_parameter_value('valableau')." ".$empr->aff_empr_date_expiration, 0, "C", 0);
		}
		
		$xpos = $pos_x + 16 ;
		$ypos = $pos_y+16 ;
		// code barre
		$this->PDF->SetFont($this->font, '', CBG_TEXT_FONT_SIZE);
		$this->PDF->SetCBFontSize(CBG_CB_TEXT_SIZE);
		$this->PDF->SetCBXRes(CBG_CB_RES);
		$this->PDF->SetCBStyle(BCS_ALIGN_CENTER | BCS_BORDER | BCS_DRAW_TEXT);
		$cbwidth = $this->PDF->GetStickWidth() - CBG_INNER_LEFT_MARGIN - CBG_INNER_RIGHT_MARGIN;
		$cbheight = $this->PDF->GetStickHeight() - CBG_INNER_TOP_MARGIN - CBG_INNER_BOTTOM_MARGIN;
		if ($biblio_name != '') $cbheight -= CBG_TEXT_HEIGHT;
		if ($biblio_name != "") {
			$this->PDF->SetXY($xpos, $ypos + CBG_INNER_BOTTOM_MARGIN);
			$this->PDF->Cell($this->PDF->GetStickWidth(), CBG_TEXT_HEIGHT, $biblio_name, 0, 0, 'C');
		}
		$x = $xpos + CBG_INNER_LEFT_MARGIN;
		$y = $ypos + CBG_INNER_TOP_MARGIN;
		if ($biblio_name != "") {
			$y += CBG_TEXT_HEIGHT;
		}
		$this->PDF->DrawBarcode($empr->empr_cb, $x, $y, $cbwidth, $cbheight, 'c39');
		
		$this->PDF->SetLineWidth(1);
		$this->PDF->Rect($pos_x+10, $pos_y+14, 60, 17, "D");
		
		//Restauration de la langue de l'interface
		$this->restaure_language();
	}	
}