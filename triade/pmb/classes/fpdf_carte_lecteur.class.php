<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fpdf_carte_lecteur.class.php,v 1.7 2017/02/01 09:54:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if (! defined('FPDF_ETIQUETTE_CLASS')) {
define('FPDF_ETIQUETTE_CLASS', 1);
if (! defined('FPDF_FONTPATH')) {
	define('FPDF_FONTPATH','font/');
}
define('FPDF_CB_TEMPPATH', './temp/');
//require('fpdf.class.php');

include('barcode/barcode.php');
include('barcode/c128aobject.php');
include('barcode/c128bobject.php');
include('barcode/c128cobject.php');
include('barcode/c39object.php');
include('barcode/i25object.php');

class FPDF_Etiquette extends FPDF
{
	// private properties
	
	// infos planche d'étiquettes
	public $topMargin;         // Marge du haut de la planche d'étiquettes
	public $bottomMargin;      // Marge du bas de la planche d'étiquettes
	public $leftMargin;        // Marge de gauche de la planche d'étiquettes
	public $rightMargin;       // Marge de droite de la planche d'étiquettes
	
	public $xSticksPadding;    // Espacement horizontal entre 2 étiquettes
	public $ySticksPadding;    // Espacement vertical entre 2 étiquettes
	
	public $nbrXSticks;        // Nombre d'étiquettes en largeur
	public $nbrYSticks;        // Nombre d'étiquettes en hauteur
	
	public $stickTopMargin;    // Marge intérieure haut de l'étiquette
	public $stickBottomMargin; // Marge intérieure bas de l'étiquette
	public $stickLeftMargin;   // Marge intérieure gauche de l'étiquette
	public $stickRightMargin;  // Marge intérieure droite de l'étiquette
	
	public $xStick;            // Position courante de l'étiquette (unité : étiquette)
	public $yStick;            // Position courante de l'étiquette (unité : étiquette)
	public $nbrSticks;         // Nombre de sticks ajouté avec AddStick
	
	// infos code barre
	public $cbXRes;            // Résolution du code barres
	public $cbFontSize;        // Taille de la police du code barre
	public $cbStyle;           // Style du code barre
	
	/****************************************************************************
	*                                                                           *
	*                              Public methods                               *
	*                                                                           *
	****************************************************************************/
	public function __construct($nbrXSticks, $nbrYSticks, $orientation='P',$unit='mm',$format='A4')
	{
		parent::__construct($orientation, $unit, $format);
	
		// Initialisation des propriétés
		$this->nbrXSticks = $nbrXSticks;
		$this->nbrYSticks = $nbrYSticks;
		$this->nbrSticks = 0;
	
		// par défaut, prend toute la feuille
		// Marges de la planche
		$this->SetPageMargins(0, 0, 0, 0);
		// Ecart entre les étiquettes
		$this->SetSticksPadding(0, 0);
		// Marge intérieure des étiquettes
		$this->SetSticksMargins(5, 5, 5, 5);
	
		// infos code barres
		$this->SetCBFontSize(3);
		$this->SetCBXRes(1);
	
		// autres
		$this->SetAutoPageBreak(false);
	}
	
	public function SetPageMargins($top, $bottom, $left, $right)
	{
		$this->topMargin=$top;
		$this->bottomMargin=$bottom;
		$this->leftMargin=$left;
		$this->rightMargin=$right;
	}
	
	public function SetSticksPadding($xPadding, $yPadding)
	{
		$this->xSticksPadding = $xPadding;
		$this->ySticksPadding = $yPadding;
	}
	
	public function SetSticksMargins($top,$bottom, $left, $right)
	{
		$this->stickTopMargin=$top;
		$this->stickBottomMargin=$bottom;
		$this->stickLeftMargin=$left;
		$this->stickRightMargin=$right;
	}
	
	public function SetCBFontSize($size)
	{
		if ($size > 5)
			$size = 5;
		elseif ($size < 1)
			$size = 1;
		$this->cbFontSize = $size;
	}
	
	public function SetCBXRes($xres)
	{
		if ($xres < 1)
			$xres = 1;
		elseif ($xres > 3)
			$xres = 3;
		$this->cbXRes = $xres;
	}
	
	public function SetCBStyle($style)
	{
		$this->cbStyle = $style;
	}
	
	public function GetStickWidth()
	{
		return ($this->w - ($this->leftMargin + $this->rightMargin) - (($this->nbrXSticks-1) * $this->xSticksPadding) )  /  $this->nbrXSticks;
	}
	
	public function GetStickHeight()
	{
		return ($this->h - ($this->topMargin + $this->bottomMargin) - (($this->nbrYSticks-1) * $this->ySticksPadding) )  /  $this->nbrYSticks;
	}
	
	public function AddStick()
	{
		if ($this->nbrSticks == 0)
		{
			$this->xStick = 0;
			$this->yStick = 0;
			$this->AddPage();
		}
		else
		{
			$this->xStick++;
			if ($this->xStick >= $this->nbrXSticks)
			{
				$this->yStick++;
				$this->xStick = 0;
				if ($this->yStick >= $this->nbrYSticks)
				{
					$this->AddPage();
					$this->yStick = 0;
				}
			}
		}
	
		$this->nbrSticks++;
	}
	
	public function GetStickX()
	{
		return $this->leftMargin + (($this->w - ($this->leftMargin + $this->rightMargin)) / $this->nbrXSticks) * $this->xStick;
	}
	
	public function GetStickY()
	{
		return $this->topMargin + (($this->h - ($this->topMargin + $this->bottomMargin)) / $this->nbrYSticks) * $this->yStick;
	}
	
	public function GetNbrSticks()
	{
		return $this->nbrSticks;
	}
	
	public function DrawBarcode($cb, $x,$y, $w,$h, $type='')
	{
		$type = strToLower($type);
		$len = strlen($cb);
	
		// calcule la largeur du code barre en pixels
		switch ($type)
		{
			case 'c128a' :
			case 'c128b' :
			case 'c128c' :
				$width = (35 + $len*11)*$this->cbXRes;
				break;
			case 'i25' :
				$width = (8 + $len*7)*$this->cbXRes;
				break;
			case 'c39' :
			default :
				$width = (($len+2)*12 + $len+1)*$this->cbXRes;
				break;
		}
		// calcule la hauteur en pixels à partir de la largeur
		$height = ($width * $h) / $w;
	
		// crée le code barre
		switch ($type)
		{
			case 'c128a' :
				$cbi = new C128AObject($width, $height, $this->cbStyle, "$cb");
				break;
			case 'c128b' :
				$cbi = new C128BObject($width, $height, $this->cbStyle, "$cb");
				break;
			case 'c128c' :
				$cbi = new C128CObject($width, $height, $this->cbStyle, "$cb");
				break;
			case 'i25' :
				$cbi = new I25Object($width, $height, $this->cbStyle, "$cb");
				break;
			case 'c39' :
			default :
				$cbi = new C39Object($width, $height, $this->cbStyle, "$cb");
				break;
		}
	
		// dessine et incorpore au pdf.
		$cbi->SetFont($this->cbFontSize);
		$cbi->DrawObject($this->cbXRes);
		$filename = FPDF_CB_TEMPPATH."cb".time().$cb;
		$cbi->SaveTo($filename);
		$cbi->DestroyObject();
		$this->Image($filename, $x, $y, $w, $h, "png");
		unlink($filename);
	}

} // fin de la classe FPDF_Etiquette

//Pour version unicode. il faudrait pouvoir ne changer que le extends UFPDF

class UFPDF_Etiquette extends UFPDF
{
	// private properties
	
	// infos planche d'étiquettes
	public $topMargin;         // Marge du haut de la planche d'étiquettes
	public $bottomMargin;      // Marge du bas de la planche d'étiquettes
	public $leftMargin;        // Marge de gauche de la planche d'étiquettes
	public $rightMargin;       // Marge de droite de la planche d'étiquettes
	
	public $xSticksPadding;    // Espacement horizontal entre 2 étiquettes
	public $ySticksPadding;    // Espacement vertical entre 2 étiquettes
	
	public $nbrXSticks;        // Nombre d'étiquettes en largeur
	public $nbrYSticks;        // Nombre d'étiquettes en hauteur
	
	public $stickTopMargin;    // Marge intérieure haut de l'étiquette
	public $stickBottomMargin; // Marge intérieure bas de l'étiquette
	public $stickLeftMargin;   // Marge intérieure gauche de l'étiquette
	public $stickRightMargin;  // Marge intérieure droite de l'étiquette
	
	public $xStick;            // Position courante de l'étiquette (unité : étiquette)
	public $yStick;            // Position courante de l'étiquette (unité : étiquette)
	public $nbrSticks;         // Nombre de sticks ajouté avec AddStick
	
	// infos code barre
	public $cbXRes;            // Résolution du code barres
	public $cbFontSize;        // Taille de la police du code barre
	public $cbStyle;           // Style du code barre
	
	/****************************************************************************
	*                                                                           *
	*                              Public methods                               *
	*                                                                           *
	****************************************************************************/
	public function __construct($nbrXSticks, $nbrYSticks, $orientation='P',$unit='mm',$format='A4')
	{
		parent::__construct($orientation, $unit, $format);
	
		// Initialisation des propriétés
		$this->nbrXSticks = $nbrXSticks;
		$this->nbrYSticks = $nbrYSticks;
		$this->nbrSticks = 0;
	
		// par défaut, prend toute la feuille
		// Marges de la planche
		$this->SetPageMargins(0, 0, 0, 0);
		// Ecart entre les étiquettes
		$this->SetSticksPadding(0, 0);
		// Marge intérieure des étiquettes
		$this->SetSticksMargins(5, 5, 5, 5);
	
		// infos code barres
		$this->SetCBFontSize(3);
		$this->SetCBXRes(1);
	
		// autres
		$this->SetAutoPageBreak(false);
	}
	
	public function SetPageMargins($top, $bottom, $left, $right)
	{
		$this->topMargin=$top;
		$this->bottomMargin=$bottom;
		$this->leftMargin=$left;
		$this->rightMargin=$right;
	}
	
	public function SetSticksPadding($xPadding, $yPadding)
	{
		$this->xSticksPadding = $xPadding;
		$this->ySticksPadding = $yPadding;
	}
	
	public function SetSticksMargins($top,$bottom, $left, $right)
	{
		$this->stickTopMargin=$top;
		$this->stickBottomMargin=$bottom;
		$this->stickLeftMargin=$left;
		$this->stickRightMargin=$right;
	}
	
	public function SetCBFontSize($size)
	{
		if ($size > 5)
			$size = 5;
		elseif ($size < 1)
			$size = 1;
		$this->cbFontSize = $size;
	}
	
	public function SetCBXRes($xres)
	{
		if ($xres < 1)
			$xres = 1;
		elseif ($xres > 3)
			$xres = 3;
		$this->cbXRes = $xres;
	}
	
	public function SetCBStyle($style)
	{
		$this->cbStyle = $style;
	}
	
	public function GetStickWidth()
	{
		return ($this->w - ($this->leftMargin + $this->rightMargin) - (($this->nbrXSticks-1) * $this->xSticksPadding) )  /  $this->nbrXSticks;
	}
	
	public function GetStickHeight()
	{
		return ($this->h - ($this->topMargin + $this->bottomMargin) - (($this->nbrYSticks-1) * $this->ySticksPadding) )  /  $this->nbrYSticks;
	}
	
	public function AddStick()
	{
		if ($this->nbrSticks == 0)
		{
			$this->xStick = 0;
			$this->yStick = 0;
			$this->AddPage();
		}
		else
		{
			$this->xStick++;
			if ($this->xStick >= $this->nbrXSticks)
			{
				$this->yStick++;
				$this->xStick = 0;
				if ($this->yStick >= $this->nbrYSticks)
				{
					$this->AddPage();
					$this->yStick = 0;
				}
			}
		}
	
		$this->nbrSticks++;
	}
	
	public function GetStickX()
	{
		return $this->leftMargin + (($this->w - ($this->leftMargin + $this->rightMargin)) / $this->nbrXSticks) * $this->xStick;
	}
	
	public function GetStickY()
	{
		return $this->topMargin + (($this->h - ($this->topMargin + $this->bottomMargin)) / $this->nbrYSticks) * $this->yStick;
	}
	
	public function GetNbrSticks()
	{
		return $this->nbrSticks;
	}
	
	public function DrawBarcode($cb, $x,$y, $w,$h, $type='')
	{
		$type = strToLower($type);
		$len = strlen($cb);
	
		// calcule la largeur du code barre en pixels
		switch ($type)
		{
			case 'c128a' :
			case 'c128b' :
			case 'c128c' :
				$width = (35 + $len*11)*$this->cbXRes;
				break;
			case 'i25' :
				$width = (8 + $len*7)*$this->cbXRes;
				break;
			case 'c39' :
			default :
				$width = (($len+2)*12 + $len+1)*$this->cbXRes;
				break;
		}
		// calcule la hauteur en pixels à partir de la largeur
		$height = ($width * $h) / $w;
	
		// crée le code barre
		switch ($type)
		{
			case 'c128a' :
				$cbi = new C128AObject($width, $height, $this->cbStyle, "$cb");
				break;
			case 'c128b' :
				$cbi = new C128BObject($width, $height, $this->cbStyle, "$cb");
				break;
			case 'c128c' :
				$cbi = new C128CObject($width, $height, $this->cbStyle, "$cb");
				break;
			case 'i25' :
				$cbi = new I25Object($width, $height, $this->cbStyle, "$cb");
				break;
			case 'c39' :
			default :
				$cbi = new C39Object($width, $height, $this->cbStyle, "$cb");
				break;
		}
	
		// dessine et incorpore au pdf.
		$cbi->SetFont($this->cbFontSize);
		$cbi->DrawObject($this->cbXRes);
		$filename = FPDF_CB_TEMPPATH."cb".time().$cb;
		$cbi->SaveTo($filename);
		$cbi->DestroyObject();
		$this->Image($filename, $x, $y, $w, $h, "png");
		unlink($filename);
	}

} // fin de la classe FPDF_Etiquette

} // fin de définition de FPDF_ETIQUETTE_CLASS
