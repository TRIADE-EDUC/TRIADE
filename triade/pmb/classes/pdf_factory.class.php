<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pdf_factory.class.php,v 1.9.8.2 2025/04/17 15:08:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/fpdf.class.php");
require_once("$class_path/ufpdf.class.php");

class pmb2FPDF extends FPDF {

	public $footer_type=0;
	public $y_footer; //Distance footer / bas de page
	public $h_footer;
	public $fs_footer;
	public $msg_footer = '';
	public $align_footer = 'C';
	public $npage = 1;
	public $display_npage = true; 
	
	public function Footer() {
		if(empty($this->y_footer)) {
			$this->y_footer = 1.5;
		}
		if(empty($this->h_footer)) {
			if(!empty($this->fs_footer)) {
				$this->h_footer = $this->fs_footer;
			} else {
				$this->h_footer = 0;
			}
		}
		if(!empty($this->fs_footer)) {
			$this->SetFont($this->FontFamily, '', $this->fs_footer);
		}
		switch ($this->footer_type) {
			case '1' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->h_footer,$this->msg_footer.($this->display_npage ? $this->PageNo().(!empty($this->AliasNbPages) ? ' / '.$this->AliasNbPages : '') : ''),0,0,$this->align_footer);
	    		$this->npage++;
				break;
			case '2' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->h_footer,$this->msg_footer.($this->display_npage ? $this->npage : ''),0,0,$this->align_footer);
	    		$this->npage++;
				break;
			case '3' :
	    		$this->SetY(-$this->y_footer);
	    		$this->MultiCell(0,$this->h_footer,$this->msg_footer.($this->display_npage ? $this->PageNo().' / '.$this->AliasNbPages : ''),0,$this->align_footer);
	    		$this->npage++;
				break;
			default :
			case '0';
				break;
		}
	}
}

class pmb2UFPDF extends UFPDF {
	
	public $footer_type=0;
	public $y_footer;
	public $h_footer;
	public $fs_footer;
	public $msg_footer = '';
	public $align_footer = 'C';
	public $npage = 1;
	public $display_npage = true;
	
	public function Footer() {
		if(empty($this->y_footer)) {
			$this->y_footer = 1.5;
		}
		if(empty($this->h_footer)) {
			if(!empty($this->fs_footer)) {
				$this->h_footer = $this->fs_footer;
			} else {
				$this->h_footer = 0;
			}
		}
		if(!empty($this->fs_footer)) {
			$this->SetFont($this->FontFamily, '', $this->fs_footer);
		}
		switch ($this->footer_type) {
			case '1' :
				$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->h_footer,$this->msg_footer.($this->display_npage ? $this->npage : ''),0,0,$this->align_footer);
	    		$this->npage++;
				break;
			case '2' :
	    		$this->SetY(-$this->y_footer);
	    		$this->Cell(0,$this->h_footer,$this->msg_footer.($this->display_npage ? $this->npage : ''),0,0,$this->align_footer);
	    		$this->npage++;
				break;
			case '3' :
	    		$this->SetY(-$this->y_footer);
	    		$this->MultiCell(0,$this->h_footer,$this->msg_footer.($this->display_npage ? $this->npage : ''),0,$this->align_footer);
	    		$this->npage++;
				break;
			default :
			case '0';
				break;
		}
	}
}


class pdf_factory {
	
	public static function make($orientation='P', $unit='mm', $format='A4') {
		
		global $charset;
		
		$className = 'pmb2FPDF';
		if($charset=='utf-8') {
			$className = 'pmb2UFPDF';
		}		
		return new $className($orientation, $unit, $format);
	}
}

