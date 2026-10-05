<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_barcodes_PDF.class.php,v 1.2 2021/09/21 11:30:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/lettre_PDF.class.php");

class lettre_barcodes_PDF extends lettre_PDF {
	
	protected $barcodes;
	
    protected static function get_parameter_prefix() {
        return "";
    }
		
    protected function _init_PDF() {
    	global $fpdf;
    	global $CBG_NBR_X_CELLS, $CBG_NBR_Y_CELLS, $ORIENTATION;
    	
    	$nom_classe=$fpdf."_Etiquette";
    	$this->PDF = new $nom_classe($CBG_NBR_X_CELLS, $CBG_NBR_Y_CELLS, $ORIENTATION);
    }
    
	protected function _init_default_positions() {
		global $CBG_TOP_MARGIN, $CBG_BOTTOM_MARGIN, $CBG_LEFT_MARGIN, $CBG_RIGHT_MARGIN;
		global $CBG_INNER_TOP_MARGIN, $CBG_INNER_BOTTOM_MARGIN, $CBG_INNER_LEFT_MARGIN, $CBG_INNER_RIGHT_MARGIN;
		global $CBG_TEXT_HEIGHT, $CBG_TEXT_FONT_SIZE, $CBG_CB_TEXT_SIZE, $CBG_CB_RES;
		
		// marges, mesures en mm
		define("CBG_LEFT_MARGIN",        $CBG_LEFT_MARGIN);
		define("CBG_RIGHT_MARGIN",       $CBG_RIGHT_MARGIN);
		define("CBG_TOP_MARGIN",         $CBG_TOP_MARGIN);
		define("CBG_BOTTOM_MARGIN",      $CBG_BOTTOM_MARGIN);
		
		// marges intérieures du bord de l'étiquette au code barre, mesures en mm
		define("CBG_INNER_LEFT_MARGIN",   $CBG_INNER_LEFT_MARGIN);
		define("CBG_INNER_RIGHT_MARGIN",  $CBG_INNER_RIGHT_MARGIN);
		define("CBG_INNER_TOP_MARGIN",    $CBG_INNER_TOP_MARGIN);
		define("CBG_INNER_BOTTOM_MARGIN", $CBG_INNER_BOTTOM_MARGIN);
		
// 		// place allouée au nom de la bibliothèque, mesure en mm
		define("CBG_TEXT_HEIGHT",         $CBG_TEXT_HEIGHT);
		// Taille de la police, en points
		define("CBG_TEXT_FONT_SIZE",      $CBG_TEXT_FONT_SIZE);
// 		// Taille du texte du code-barre, 1 : le plus petit ; 5 : le plus grand
		define("CBG_CB_TEXT_SIZE",        $CBG_CB_TEXT_SIZE);
// 		// Résolution du code barre. Si vous augmentez ce paramètre, il faudra peut-être
// 		// augmenter la taille de la police. Une valeur faible produit un fichier moins volumineux
		define("CBG_CB_RES",              $CBG_CB_RES);
		// l'apparence du code barre dépend étroitement de la résolution et de la taille du texte
	}
	
	protected function doStick($cb) {
		global $bibli_name;
		
		$cbwidth = $this->PDF->GetStickWidth() - CBG_INNER_LEFT_MARGIN - CBG_INNER_RIGHT_MARGIN;
		$cbheight = $this->PDF->GetStickHeight() - CBG_INNER_TOP_MARGIN - CBG_INNER_BOTTOM_MARGIN -  CBG_TEXT_HEIGHT ;
		// if ($bibli_name != '') {
		$cbheight -= CBG_TEXT_HEIGHT;
		// }
		
		// texte
		if ($bibli_name != "") {
			$this->PDF->SetXY($this->PDF->GetStickX(), $this->PDF->GetStickY() + CBG_INNER_TOP_MARGIN);
			$this->PDF->Cell($this->PDF->GetStickWidth(), CBG_TEXT_HEIGHT, stripslashes($bibli_name), 0, 0, 'C');
			
		}
		
		// code barre
		$x = $this->PDF->GetStickX() + CBG_INNER_LEFT_MARGIN;
		$y = $this->PDF->GetStickY() + CBG_INNER_TOP_MARGIN;
		if ($bibli_name != "") {
			$y += CBG_TEXT_HEIGHT;
		}
		$this->PDF->DrawBarcode($cb, $x, $y, $cbwidth, $cbheight, 'c39');
		
		// code barre en clair ( il faut desactiver son affichage ds le fichier class/barecode.php par define("BCS_DRAW_TEXT"      ,  0); au lieu de 128
		$this->PDF->SetXY($this->PDF->GetStickX(), $this->PDF->GetStickY() + CBG_INNER_TOP_MARGIN + CBG_TEXT_HEIGHT + $cbheight);
		$this->PDF->Cell($this->PDF->GetStickWidth(), CBG_TEXT_HEIGHT, $cb, 0, 0, 'C');
	}
	
	public function doLettre() {
		global $pmb_pdf_font;
		
		$this->PDF->SetPageMargins(CBG_TOP_MARGIN, CBG_BOTTOM_MARGIN, CBG_LEFT_MARGIN, CBG_RIGHT_MARGIN);
		$this->PDF->SetFont($pmb_pdf_font, '', CBG_TEXT_FONT_SIZE);
		$this->PDF->SetCBFontSize(CBG_CB_TEXT_SIZE);
		$this->PDF->SetCBXRes(CBG_CB_RES);
		$this->PDF->SetCBStyle(BCS_ALIGN_CENTER | BCS_BORDER | BCS_DRAW_TEXT);
		
		foreach ($this->barcodes as $cb) {
			// Ajoute une étiquette
			$this->PDF->AddStick();
			
			$this->doStick($cb);
		}				
	}
	
	public function set_barcodes($barcodes) {
		$this->barcodes = $barcodes;
	}
}