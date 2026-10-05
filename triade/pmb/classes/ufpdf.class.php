<?php
/**
 * UFPDF, Unicode Free PDF generator
 * Version:  0.1
 *           based on FPDF 1.52 by Olivier PLATHEY
 * Date:     2004-09-01
 * Author:   Steven Wittens <steven@acko.net>
 * License:  GPL
 *
 * UFPDF is a modification of FPDF to support Unicode through UTF-8.
 * @package UFPDF
 * @see fpdf.php
 * @see reportpdf.php
 * @version $Id: ufpdf.class.php,v 1.24.6.1 2024/12/06 13:08:32 dbellamy Exp $
 */

// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ufpdf.class.php,v 1.24.6.1 2024/12/06 13:08:32 dbellamy Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

if(!class_exists('UFPDF')) {
define('UFPDF_VERSION','0.1');
if (! defined('FPDF_FONTPATH')) {
	define('FPDF_FONTPATH',$class_path.'/font/');
}
if (file_exists('fpdf.php')) include_once 'fpdf.php';
else if (file_exists('ufpdf/fpdf.php')) include_once 'ufpdf/fpdf.php';

/**
 * Main UFPDF class for creating Unicode PDF documents
 *
 * derives from FPDF class
 * @see FPDF
 */
class UFPDF extends FPDF
{
	public $embed_fonts;
	public $arabicforms = array();
	public $arabiclettersbefore = array();
	public $arabiclettersafter = array();
	public $arabicneutral = array();
	
	//Standard fonts

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
public function __construct($orientation='P',$unit='mm',$size='A4')
{
	$this->embed_fonts = true;
  parent::__construct($orientation, $unit, $size);
  // création des arrays pour traitement de l'arabe
  // hamza 0621
  $this->arabicforms[chr(0xD8).chr(0xA1)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x80),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x80),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x80),
  													'final' => chr(0xEF).chr(0xBA).chr(0x80));
  // alef with madda 0622??
  $this->arabicforms[chr(0xD8).chr(0xA2)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x81),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x81),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x82),
  													'final' => chr(0xEF).chr(0xBA).chr(0x82));
  // alef with hamza 0623
  $this->arabicforms[chr(0xD8).chr(0xA3)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x83),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x83),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x84),
  													'final' => chr(0xEF).chr(0xBA).chr(0x84));
  // Waw with hamza 0624
  $this->arabicforms[chr(0xD8).chr(0xA4)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x85),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x85),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x86),
  													'final' => chr(0xEF).chr(0xBA).chr(0x86));
  // alef with hamza below 0625
  $this->arabicforms[chr(0xD8).chr(0xA5)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x87),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x87),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x88),
  													'final' => chr(0xEF).chr(0xBA).chr(0x88));
  // Yah with hamza 0626
  $this->arabicforms[chr(0xD8).chr(0xA6)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x89),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x8B),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x8C),
  													'final' => chr(0xEF).chr(0xBA).chr(0x8A));
  // Alef 0627
  $this->arabicforms[chr(0xD8).chr(0xA7)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x8D),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x8D),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x8E),
  													'final' => chr(0xEF).chr(0xBA).chr(0x8E));
  // beh 0628
  $this->arabicforms[chr(0xD8).chr(0xA8)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x8F),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x91),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x92),
  													'final' => chr(0xEF).chr(0xBA).chr(0x90));
  // teh marbuta 0629
  $this->arabicforms[chr(0xD8).chr(0xA9)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x93),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x93),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x94),
  													'final' => chr(0xEF).chr(0xBA).chr(0x94));
  // teh 062A
  $this->arabicforms[chr(0xD8).chr(0xAA)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x95),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x97),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x98),
  													'final' => chr(0xEF).chr(0xBA).chr(0x96));
  // theh 062B
  $this->arabicforms[chr(0xD8).chr(0xAB)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x99),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x9B),
  													'medial' => chr(0xEF).chr(0xBA).chr(0x9C),
  													'final' => chr(0xEF).chr(0xBA).chr(0x9A));
  // Jeem 062C
  $this->arabicforms[chr(0xD8).chr(0xAC)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0x9D),
  													'initial' => chr(0xEF).chr(0xBA).chr(0x9F),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xA0),
  													'final' => chr(0xEF).chr(0xBA).chr(0x9E));
  // Hah 062D
  $this->arabicforms[chr(0xD8).chr(0xAD)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xA1),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xA3),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xA4),
  													'final' => chr(0xEF).chr(0xBA).chr(0xA2));
  // KHah 062E
  $this->arabicforms[chr(0xD8).chr(0xAE)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xA5),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xA7),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xA8),
  													'final' => chr(0xEF).chr(0xBA).chr(0xA6));
  // dal 062F
  $this->arabicforms[chr(0xD8).chr(0xAF)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xA9),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xA9),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xAA),
  													'final' => chr(0xEF).chr(0xBA).chr(0xAA));
  // thal 0630
  $this->arabicforms[chr(0xD8).chr(0xB0)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xAB),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xAB),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xAC),
  													'final' => chr(0xEF).chr(0xBA).chr(0xAC));
  // reh 0631
  $this->arabicforms[chr(0xD8).chr(0xB1)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xAD),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xAD),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xAE),
  													'final' => chr(0xEF).chr(0xBA).chr(0xAE));
  // zain 0632
  $this->arabicforms[chr(0xD8).chr(0xB2)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xAF),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xAF),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xB0),
  													'final' => chr(0xEF).chr(0xBA).chr(0xB0));
  // seen 0633
  $this->arabicforms[chr(0xD8).chr(0xB3)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xB1),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xB3),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xB4),
  													'final' => chr(0xEF).chr(0xBA).chr(0xB2));
  // sheen 0634
  $this->arabicforms[chr(0xD8).chr(0xB4)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xB5),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xB7),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xB8),
  													'final' => chr(0xEF).chr(0xBA).chr(0xB6));
  // sad 0635
  $this->arabicforms[chr(0xD8).chr(0xB5)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xB9),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xBB),
  													'medial' => chr(0xEF).chr(0xBA).chr(0xBC),
  													'final' => chr(0xEF).chr(0xBA).chr(0xBA));
  // dad 0636
  $this->arabicforms[chr(0xD8).chr(0xB6)] = array('isolated' => chr(0xEF).chr(0xBA).chr(0xBD),
  													'initial' => chr(0xEF).chr(0xBA).chr(0xBF),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x80),
  													'final' => chr(0xEF).chr(0xBA).chr(0xBE));
  // tah 0637
  $this->arabicforms[chr(0xD8).chr(0xB7)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x81),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x83),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x84),
  													'final' => chr(0xEF).chr(0xBB).chr(0x82));
  // zah 0638
  $this->arabicforms[chr(0xD8).chr(0xB8)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x85),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x87),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x88),
  													'final' => chr(0xEF).chr(0xBB).chr(0x86));
  // ain 0639
  $this->arabicforms[chr(0xD8).chr(0xB9)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x89),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x8B),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x8C),
  													'final' => chr(0xEF).chr(0xBB).chr(0x8A));
  // ghain 063A
  $this->arabicforms[chr(0xD8).chr(0xBA)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x8D),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x8F),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x90),
  													'final' => chr(0xEF).chr(0xBB).chr(0x8E));
  // feh 0641 
  $this->arabicforms[chr(0xD9).chr(0x81)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x91),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x93),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x94),
  													'final' => chr(0xEF).chr(0xBB).chr(0x92));
  // qaf 0642 
  $this->arabicforms[chr(0xD9).chr(0x82)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x95),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x97),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x98),
  													'final' => chr(0xEF).chr(0xBB).chr(0x96));
  // kaf 0643 
  $this->arabicforms[chr(0xD9).chr(0x83)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x99),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x9B),
  													'medial' => chr(0xEF).chr(0xBB).chr(0x9C),
  													'final' => chr(0xEF).chr(0xBB).chr(0x9A));
  // lam 0644 
  $this->arabicforms[chr(0xD9).chr(0x84)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0x9D),
  													'initial' => chr(0xEF).chr(0xBB).chr(0x9F),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xA0),
  													'final' => chr(0xEF).chr(0xBB).chr(0x9E));
  // meem 0645 
  $this->arabicforms[chr(0xD9).chr(0x85)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0xA1),
  													'initial' => chr(0xEF).chr(0xBB).chr(0xA3),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xA4),
  													'final' => chr(0xEF).chr(0xBB).chr(0xA2));
  // noon 0646 
  $this->arabicforms[chr(0xD9).chr(0x86)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0xA5),
  													'initial' => chr(0xEF).chr(0xBB).chr(0xA7),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xA8),
  													'final' => chr(0xEF).chr(0xBB).chr(0xA6));
  // hah 0647 
  $this->arabicforms[chr(0xD9).chr(0x87)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0xA9),
  													'initial' => chr(0xEF).chr(0xBB).chr(0xAB),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xAC),
  													'final' => chr(0xEF).chr(0xBB).chr(0xAA));
  // waw 0648 
  $this->arabicforms[chr(0xD9).chr(0x88)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0xAD),
  													'initial' => chr(0xEF).chr(0xBB).chr(0xAD),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xAE),
  													'final' => chr(0xEF).chr(0xBB).chr(0xAE));
  // alef maksura 0649 
  $this->arabicforms[chr(0xD9).chr(0x89)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0xAF),
  													'initial' => chr(0xEF).chr(0xBB).chr(0xAF),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xB0),
  													'final' => chr(0xEF).chr(0xBB).chr(0xB0));
  // Yeh 064A 0xD9 0x8A
  $this->arabicforms[chr(0xD9).chr(0x8A)] = array('isolated' => chr(0xEF).chr(0xBB).chr(0xB1),
  													'initial' => chr(0xEF).chr(0xBB).chr(0xB3),
  													'medial' => chr(0xEF).chr(0xBB).chr(0xB4),
  													'final' => chr(0xEF).chr(0xBB).chr(0xB2));
  												

  // letter without link before - hamza 											
  $this->arabiclettersbefore = array(chr(0xD8).chr(0xA1));
  // letter without link after  - hamza - alef with madda - alef with hamza - waw with hamza 
  //								alef with hamza below - alef - dal - thal 				
  //								reh - zain - waw								
  $this->arabiclettersafter = array(chr(0xD8).chr(0xA1),chr(0xD8).chr(0xA2),chr(0xD8).chr(0xA3),chr(0xD8).chr(0xA4)
  								,chr(0xD8).chr(0xA5),chr(0xD8).chr(0xA7),chr(0xD8).chr(0xA9),chr(0xD8).chr(0xAF),chr(0xD8).chr(0xB0)
  								,chr(0xD8).chr(0xB1),chr(0xD8).chr(0xB2),chr(0xD9).chr(0x88)) ;
  // what should be part of arabic sentence 
  $this->arabicneutral = array(' ',1,2,3,4,5,6,7,8,9,0,'(',')',',',);
  
   
}

public function gethtmlentitiesdecode() {
  	global $charset;
	
	$trans=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT | ENT_HTML401,$charset);
	$this->htmlentitiesdecode = array();
	foreach($trans as $k => $v)
	{
          $this->htmlentitiesdecode[$v] = $k;
	}
	$this->htmlentitiesdecode["&nbsp;"] = " ";
	$this->htmlentitiesdecode["&shy;"] = "-";
}

public function SetEmbedFonts($embed)
{
	$this->embed_fonts = $embed;
}

public function GetStringWidth($s)
{
  //Get width of a string in the current font
  $s = (string)$s;
  $codepoints=$this->utf8_to_codepoints($s);
  $cw=&$this->CurrentFont['cw'];
  $w=0;
  //print " [$s]";
  foreach($codepoints as $cp) {
	//print "[$cp]";
    if (isset($cw[$cp])) {
	    $w+=$cw[$cp];
    }
    else if (isset($cw[ord($cp)])) {
	    $w+=$cw[ord($cp)];
    }
    else if (isset($cw[chr($cp)])) {
	    $w+=$cw[chr($cp)];
    }
    else $w+=500;
    //-- adjust width for incorrect hebrew chars
    //if ($cp>1480 && $cp < 1550) $w -= $cw[$cp]/1.8;
  }
  return $w*$this->FontSize/1000;
}

public function AddFont($family,$style='',$file='')
{
    global $type, $ctg, $desc, $up, $ut, $cw, $originalsize, $size1, $size2;
  //Add a TrueType or Type1 font
  $family=strtolower($family);
  if($file=='')
  	$file=str_replace(' ','',$family).strtolower($style).'.php';
  if($family=='arial')
    $family='helvetica';
  $style=strtoupper($style);
  if($style=='IB')
    $style='BI';
  $fontkey = $family.$style;
  if(isset($this->fonts[$family.$style]))
    $this->Error('Font already added: '.$family.' '.$style);
  $info = $this->_loadfont($file);
  $info['i'] = count($this->fonts)+1;
  if(!empty($info['file']))
  {
  	// Embedded font
  	if($info['type']=='TrueTypeUnicode')
  		$this->FontFiles[$info['file']] = array('length1'=>$info['originalsize']);
	else
		$this->FontFiles[$info['file']] = array('length1'=>$info['size1'], 'length2'=>$info['size2']);
  }
  $this->fonts[$fontkey] = $info;
}

public function Text($x,$y,$txt)
{
  if(!isset($this->CurrentFont))
	  $this->Error('No font has been set');
  //Output a string
  $txt = strtr($txt, $this->htmlentitiesdecode);
  $txt = str_replace('&euro;','â‚¬',$txt);
  
  
  $s=sprintf('BT %.2F %.2F Td %s Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escapetext($txt));
  if($this->underline and $txt!='')
    $s.=' '.$this->_dounderline($x,$y,$txt);
  if($this->ColorFlag)
    $s='q '.$this->TextColor.' '.$s.' Q';
  $this->_out($s);
}

public function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=false,$link='')
{
    $w = intval($w);
    $h = intval($h);
    $ln = intval($ln);
    
  //Output a cell
  $txt = strtr($txt, $this->htmlentitiesdecode);
  $txt = str_replace('&euro;','â‚¬',$txt);
  
  $k=$this->k;
  if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
  {
    //Automatic page break
    $x=$this->x;
    $ws=$this->ws;
    if($ws>0)
    {
      $this->ws=0;
      $this->_out('0 Tw');
    }
    $this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
    $this->x=$x;
    if($ws>0)
    {
      $this->ws=$ws;
      $this->_out(sprintf('%.3F Tw',$ws*$k));
    }
  }
  if($w==0)
    $w=$this->w-$this->rMargin-$this->x;
  $s='';
  if($fill || $border==1)
  {
  	if($fill)
      $op=($border==1) ? 'B' : 'f';
    else
      $op='S';
    $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
  }
  if(is_string($border))
  {
    $x=$this->x;
    $y=$this->y;
    if(is_int(strpos($border,'L')))
      $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
    if(is_int(strpos($border,'T')))
      $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
    if(is_int(strpos($border,'R')))
      $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    if(is_int(strpos($border,'B')))
      $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
  }
  if($txt!='')
  {
    $width = $this->GetStringWidth($txt);
    if($align=='R')
      $dx=$w-$this->cMargin-$width;
    elseif($align=='C')
      $dx=($w-$width)/2;
    else
      $dx=$this->cMargin;
    if($this->ColorFlag)
      $s.='q '.$this->TextColor.' ';
    $txtstring=$this->_escapetext($txt);
    $s.=sprintf('BT %.2F %.2F Td %s Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txtstring);
    if($this->underline)
      $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
    if($this->ColorFlag)
      $s.=' Q';
    if($link)
      $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$width,$this->FontSize,$link);
  }
  if($s)
    $this->_out($s);
  $this->lasth=$h;
  if($ln>0)
  {
    //Go to next line
    $this->y+=$h;
    if($ln==1)
      $this->x=$this->lMargin;
  }
  else
    $this->x+=$w;
}

public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) 
{
    $w = intval($w);
    $h = intval($h);
    
	global $charset;
	//Output text with automatic or explicit line breaks
	$cw = &$this->CurrentFont['cw'];

	if($w == 0) {
		$w = $this->w - $this->rMargin - $this->x;
	}

	$wmax = ($w - 2 * $this->cMargin);

    $txt = strtr($txt, $this->htmlentitiesdecode);
	$txt = str_replace('&euro;','â‚¬',$txt);
	$s = str_replace("\r", '', $txt); // remove carriage returns
	$nb = strlen($s);

	$b=0;
	if($border) {
		if($border==1) {
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		} else {
			$b2='';
			if(strpos($border,'L')!==false) {
				$b2.='L';
			}
			if(strpos($border,'R')!==false) {
				$b2.='R';
			}
			$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;
	while($i<$nb) {
		//Get next character
	    $c = substr($s, $i, 1);
		if(preg_match("/[\n]/u", $c)) {
			//Explicit line break
			if($this->ws > 0) {
				$this->ws = 0;
				$this->_out('0 Tw');
			}
			$this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2) {
				$b = $b2;
			}
			continue;
		}
		if(preg_match("/[ ]/u", $c)) {
			$sep = $i;
			$ls = $l;
			$ns++;
		}

		$l = $this->GetStringWidth(substr($s, $j, $i-$j));

		if($l > $wmax) {
			//Automatic line break
			if($sep == -1) {
				if($i == $j) {
					$i++;
				}
				if($this->ws > 0) {
					$this->ws = 0;
					$this->_out('0 Tw');
				}
				$this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
			}
			else {
				if($align=='J') {
					$this->ws = ($ns>1) ? ($wmax-$ls)/($ns-1) : 0;
					$this->_out(sprintf('%.3f Tw', $this->ws * $this->k));
				}
				$this->Cell($w, $h, substr($s, $j, $sep-$j), $b, 2, $align, $fill);
				$i = $sep + 1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && ($nl==2)) {
				$b=$b2;
			}
		}
		else {
			$i++;
		}
	}
	//Last chunk
	if($this->ws>0) {
		$this->ws=0;
		$this->_out('0 Tw');
	}
	if($border && is_int(strpos($border,'B'))) {
		$b.='B';
	}
	$this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
	$this->x=$this->lMargin;
}

public function Write($h, $txt, $link='') {

	//Output text in flowing mode
	$cw = &$this->CurrentFont['cw'];
	$w = $this->w - $this->rMargin - $this->x;
	$wmax = ($w - 2 * $this->cMargin);

	$txt = strtr($txt, $this->htmlentitiesdecode);
	$txt = str_replace('&euro;','â‚¬',$txt);
			
	$s = str_replace("\r", '', $txt);
	$nb = strlen($s);

	// handle single space character
	if(($nb==1) && preg_match("/[ ]/u", $s)) {
		$this->x += $this->GetStringWidth($s);
		return;
	}

	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb) {
		//Get next character
	    $c=substr($s, $i, 1);
		if(preg_match("/[\n]/u", $c)) {
			//Explicit line break
			$this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', false, $link);
			$i++;
			$sep = -1;
			$j = $i;
			$l = 0;
			if($nl == 1) {
				$this->x = $this->lMargin;
				$w = $this->w - $this->rMargin - $this->x;
				$wmax = ($w - 2 * $this->cMargin);
			}
			$nl++;
			continue;
		}
		if(preg_match("/[ ]/u", $c)) {
			$sep= $i;
		}

		$l = $this->GetStringWidth(substr($s, $j, $i-$j));

		if($l > $wmax) {
			//Automatic line break
			if($sep == -1) {
				if($this->x > $this->lMargin) {
					//Move to next line
					$this->x = $this->lMargin;
					$this->y += $h;
					$w=$this->w - $this->rMargin - $this->x;
					$wmax=($w - 2 * $this->cMargin);
					$i++;
					$nl++;
					continue;
				}
				if($i==$j) {
					$i++;
				}
				$this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', false, $link);
			}
			else {
				$this->Cell($w, $h, substr($s, $j, $sep-$j), 0, 2, '', false, $link);
				$i=$sep+1;
			}
			$sep = -1;
			$j = $i;
			$l = 0;
			if($nl==1) {
				$this->x = $this->lMargin;
				$w = $this->w - $this->rMargin - $this->x;
				$wmax = ($w - 2 * $this->cMargin);
			}
			$nl++;
		}
		else {
			$i++;
		}
	}
	//Last chunk
	if($i!=$j) {
		$this->Cell($l / 1000 * $this->FontSize, $h, substr($s, $j), 0, 0, '', false, $link);
	}

	$this->x += $this->GetStringWidth(substr($s, $j, $i-$j));
}


public function AliasNbPages($alias='{nb}')
{
	//Define an alias for total number of pages
	$this->AliasNbPages=$this->utf8_to_utf16be($alias,false);
}


/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/

public function _puttruetypeunicode($font) {
  //Type0 Font
  $this->_newobj();
  $this->_out('<</Type /Font');
  $this->_out('/Subtype /Type0');
  $this->_out('/BaseFont /'. $font['name'] );
  $this->_out('/Encoding /Identity-H');
  $this->_out('/DescendantFonts ['. ($this->n + 1) .' 0 R]');
  $this->_out('>>');
  $this->_out('endobj');

  //CIDFont
  $this->_newobj();
  $this->_out('<</Type /Font');
  $this->_out('/Subtype /CIDFontType2');
  $this->_out('/BaseFont /'. $font['name']);
  $this->_out('/CIDSystemInfo <</Registry (Adobe) /Ordering (UCS) /Supplement 0>>');
  $this->_out('/FontDescriptor '. ($this->n + 1) .' 0 R');
  $c = 0;
  $widths = "";
  foreach ($font['cw'] as $i => $w) {
    $widths .= $i .' ['. $w.'] ';
  }
  $this->_out('/W ['. $widths .']');
  $this->_out('/CIDToGIDMap '. ($this->n + 2) .' 0 R');
  $this->_out('>>');
  $this->_out('endobj');

  //Font descriptor
  $this->_newobj();
  $this->_out('<</Type /FontDescriptor');
  $this->_out('/FontName /'.$font['name']);
  $s = "";
  foreach ($font['desc'] as $k => $v) {
    $s .= ' /'. $k .' '. $v;
  }
  if ($font['file']) {
		$s .= ' /FontFile2 '. $this->FontFiles[$font['file']]['n'] .' 0 R';
  }
  $this->_out($s);
  $this->_out('>>');
  $this->_out('endobj');

  //Embed CIDToGIDMap
  $this->_newobj();
  if(defined('FPDF_FONTPATH'))
    $file=FPDF_FONTPATH.$font['ctg'];
  else
    $file=$font['ctg'];
  $size=filesize($file);
  if(!$size)
    $this->Error('Font file not found');
  $this->_out('<</Length '.$size);
	if(substr($file,-2) == '.z')
    $this->_out('/Filter /FlateDecode');
  $this->_out('>>');
  $f = fopen($file,'rb');
  $this->_putstream(fread($f,$size));
  fclose($f);
  $this->_out('endobj');
}

protected function _textstring($s)
{
 	
  //Convert to UTF-16BE
  $s = $this->utf8_to_utf16be($s);
  //Escape necessary characters
  return '('. strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\')) .')';
}

public function _escapetext($s)
{

$newstring = '';
$keepforlater = '';
$toreverse = '';
// version beta 
// a voir : mettre les caracteres faisant partis de la phrase (parenthese, chiffres,virgules...)
// peut etre mettre des caracteres neutres début de mots et d'autres fin de mot (exemple des parentheses)'
// traiter le lam alef

  // arabic : arabic is written left to right and letter change shapes depending 
  // of their palce in the word. 
  // So you need to reverse the sentence and replace the letter
  // by their presentation form.
  // 	do it only if you find arabic charcaters
    if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}]/u', $s)) {
  	  $i=0;
	  while($i< strlen($s)) {
	  	$char = substr($s, $i, 1);
	  	// arabic character, take the second byte.
	  	if (($char == chr(0xD8)) or ($char == chr(0xD9))) {
	  		$i++;
	  		$char .= substr($s, $i, 1);
	  	}
	  	// we have to have three characters to see what's before and what follow the letter.
	  	if (!$char1) $char1 = $char;
	  	elseif (!$char2) $char2 = $char;
	  	elseif (!$char3) $char3 = $char;
	  	else {
	  		$char1 = $char2;
	  		$char2 = $char3;
	  		$char3 = $char;
	  	}
	  	// First character
	  	if (($char1) and (!$char2) and (!$char3)) {
	  	}
	  	elseif (($char1) and ($char2) and (!$char3)){
	  		// if it's an arabic char 
			if ($this->arabicforms[$char1] ) {
				// if $char1 don't link with following letter, we're done.
				if (in_array($char1,$this->arabiclettersafter)) {
					$toreverse = $this->arabicforms[$char1]['isolated'];			
				}
				else {
				// Char1 link with next and char2 link before char 1 -> initial form
					if 	(in_array($char2,$this->arabiclettersafter)===false) {
							$toreverse = $this->arabicforms[$char1]['initial'];			
					}
					// char2 dont link before char1 -> isolated form
					else {
						$toreverse = $this->arabicforms[$char1]['isolated'];			
					}
				}
			}
			// this is a character not arabic, but can be part of an arabic sentence
			// let keep it and see what we'll do later.
			elseif (in_array($char1, $this->arabicneutral)) { 
				$keepforlater = $char1;
			}
			else {
				$newstring = $char1;
			}
	  	}
	  	// other letters
	  	else { 
	  		// if it's an arabic char 
			if ($this->arabicforms[$char2] ) {
			//echo "je suis arabe<br />";
				// if $char1 arabic and don't link with following letter
				if (in_array($char1,$this->arabiclettersafter)) {
					//if $char3 arabic and don't link before
					if (in_array($char3,$this->arabiclettersbefore)) {
						$toreverse = $this->arabicforms[$char2]['isolated'] . $toreverse;
					}
					// char3 arabic and link before 
					elseif ($this->arabicforms[$char3]) {
						$toreverse = $this->arabicforms[$char2]['initial']. $toreverse;
					}
					else {
						// char 3 is not arabic -> isolated form
						$toreverse = $this->arabicforms[$char2]['isolated']. $toreverse;
					}
				}
				// char1 arabic et link after
				elseif ($this->arabicforms[$char1]) {
					//if $char3 arabic and don't link before
					if (in_array($char3,$this->arabiclettersbefore)) {
						$toreverse = $this->arabicforms[$char2]['final'] . $toreverse;
					}
					// char3 arabic and link before 
					elseif ($this->arabicforms[$char3]) {
						$toreverse = $this->arabicforms[$char2]['medial']. $toreverse;
					}
					else {
						// char 3 is not arabic -> isolated form
						$toreverse = $this->arabicforms[$char2]['final']. $toreverse;
					}
				}
				// char1 pas arabe
				else {
					if (in_array($char1, $this->arabicneutral)) {
				//		echo "char1 neutre<br />";
						// je recupere ce que j'ai mis de cote
						
						$toreverse = $keepforlater.$toreverse ;
						$keepforlater = '';
					}
					//if $char3 arabic and don't link before
					if (in_array($char3,$this->arabiclettersbefore)) {
						$toreverse = $this->arabicforms[$char2]['isolated'] . $toreverse;
					}
					// char3 arabic and link before 
					elseif ($this->arabicforms[$char3]) {
						$toreverse = $this->arabicforms[$char2]['initial']. $toreverse;
					}
					else {
						// char 3 is not arabic -> isolated form
						$toreverse = $this->arabicforms[$char2]['isolated']. $toreverse;
					}
				}
			}
			elseif (in_array($char2, $this->arabicneutral)) {
			//	echo "je suis neutre<br />"; 
				$keepforlater = $keepforlater. $char2;
			}
			else {
		//		echo "je suis autre";
				$newstring = $newstring.$toreverse.$keepforlater.$char2;
				$keepforlater = '';
				$toreverse = '';
			}
	  	}
	  //	echo "nouvelle-" .$newstring."-<br />";
	  //	echo "arabe-" .$toreverse."-<br />";
	  //	echo "neutres-".$keepforlater."-<br />";
	  	$i++;
  	}
	// if it's an arabic char 
	if ($this->arabicforms[$char3] ) {
		// if $char2 arabic and don't link with following letter
		if (in_array($char2,$this->arabiclettersafter)) {
			$toreverse = $this->arabicforms[$char3]['isolated'] . $toreverse;
		}
		// char2 arabic et link after
		elseif ($this->arabicforms[$char2]) {
			$toreverse = $this->arabicforms[$char3]['final'] . $toreverse;
		}
		// char2 pas arabe
		else {
			if (in_array($char2, $this->arabicneutral)) {
				// je recupere ce que j'ai mis de cote
				$toreverse = $keepforlater.$toreverse ;
				$keepforlater = ' ';
			}
			$toreverse = $this->arabicforms[$char2]['isolated'] . $toreverse;
		}
		$newstring = $newstring.$keepforlater.$toreverse;
   	}
	elseif (in_array($char3, $this->arabicneutral)) { 
			$keepforlater = $keepforlater. $char3;
			$newstring = $newstring.$keepforlater.$toreverse;
			$keepforlater = '';
			$toreverse = '';
	}
	else {
		$newstring = $newstring.$toreverse.$keepforlater.$char3;
		$keepforlater = '';
		$toreverse = '';
	}
  	//print "nouvelle" .$newstring."<br />";
  	//print "arabe" .$toreverse."<br />";
  	//print "neutres".$keepforlater."<br />";
  $s = $newstring;
  }
  //Convert to UTF-16BE
  $s = $this->utf8_to_utf16be($s, false);
  //Escape necessary characters
  return '('. strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\')) .')';
}

protected function _putinfo()
{
	$this->metadata['Producer'] = 'UFPDF '.UFPDF_VERSION;
	$this->metadata['CreationDate'] = 'D:'.@date('YmdHis');
	foreach($this->metadata as $key=>$value)
		$this->_put('/'.$key.' '.$this->_textstring($value));
}

// UTF-8 to UTF-16BE conversion.
// Correctly handles all illegal UTF-8 sequences.
public function utf8_to_utf16be(&$txt, $bom = true) {
	if (!$this->embed_fonts) return $txt;
  $l = strlen($txt);
  $txt .= " ";
  $out = $bom ? "\xFE\xFF" : '';
  for ($i = 0; $i < $l; ++$i) {
      $c = ord(substr($txt, $i, 1));
    // ASCII
    if ($c < 0x80) {
        $out .= "\x00". substr($txt, $i, 1);
    }
    // Lost continuation byte
    else if ($c < 0xC0) {
      $out .= "\xFF\xFD";
      continue;
    }
    // Multibyte sequence leading byte
    else {
      if ($c < 0xE0) {
        $s = 2;
      }
      else if ($c < 0xF0) {
        $s = 3;
      }
      else if ($c < 0xF8) {
        $s = 4;
      }
      // 5/6 byte sequences not possible for Unicode.
      else {
        $out .= "\xFF\xFD";
        while (ord(substr($txt, ($i + 1), 1)) >= 0x80 && ord(substr($txt, ($i + 1), 1)) < 0xC0) { ++$i; }
        continue;
      }

      $q = array($c);
      // Fetch rest of sequence
      while (ord(substr($txt, ($i + 1), 1)) >= 0x80 && ord(substr($txt, ($i + 1), 1)) < 0xC0) { ++$i; $q[] = ord(substr($txt, $i, 1)); }

      // Check length
      if (count($q) != $s) {
        $out .= "\xFF\xFD";
        continue;
      }

      switch ($s) {
        case 2:
          $cp = (($q[0] ^ 0xC0) << 6) | ($q[1] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x80) {
            $out .= "\xFF\xFD";
          }
          else {
            $out .= chr($cp >> 8);
            $out .= chr($cp & 0xFF);
          }
          break;
        case 3:
          $cp = (($q[0] ^ 0xE0) << 12) | (($q[1] ^ 0x80) << 6) | ($q[2] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x800) {
            $out .= "\xFF\xFD";
          }
          // Check for UTF-8 encoded surrogates (caused by a bad UTF-8 encoder)
          else if ($c > 0xD800 && $c < 0xDFFF) {
            $out .= "\xFF\xFD";
          }
          else {
            $out .= chr($cp >> 8);
            $out .= chr($cp & 0xFF);
          }
          break;
        case 4:
          $cp = (($q[0] ^ 0xF0) << 18) | (($q[1] ^ 0x80) << 12) | (($q[2] ^ 0x80) << 6) | ($q[3] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x10000) {
            $out .= "\xFF\xFD";
          }
          // Outside of the Unicode range
          else if ($cp >= 0x10FFFF) {
            $out .= "\xFF\xFD";
          }
          else {
            // Use surrogates
            $cp -= 0x10000;
            $s1 = 0xD800 | ($cp >> 10);
            $s2 = 0xDC00 | ($cp & 0x3FF);

            $out .= chr($s1 >> 8);
            $out .= chr($s1 & 0xFF);
            $out .= chr($s2 >> 8);
            $out .= chr($s2 & 0xFF);
          }
          break;
      }
    }
  }
  return $out;
}

// UTF-8 to codepoint array conversion.
// Correctly handles all illegal UTF-8 sequences.
public function utf8_to_codepoints(&$txt) {
  $l = strlen($txt);
  $txt .= " ";
  $out = array();
  for ($i = 0; $i < $l; ++$i) {
      $c = ord(substr($txt, $i, 1));
    // ASCII
    if ($c < 0x80) {
        $out[] = ord(substr($txt, $i, 1));
    }
    // Lost continuation byte
    else if ($c < 0xC0) {
      $out[] = 0xFFFD;
      continue;
    }
    // Multibyte sequence leading byte
    else {
      if ($c < 0xE0) {
        $s = 2;
      }
      else if ($c < 0xF0) {
        $s = 3;
      }
      else if ($c < 0xF8) {
        $s = 4;
      }
      // 5/6 byte sequences not possible for Unicode.
      else {
        $out[] = 0xFFFD;
        while (ord(substr($txt, ($i + 1), 1)) >= 0x80 && ord(substr($txt, ($i + 1), 1)) < 0xC0) { ++$i; }
        continue;
      }

      $q = array($c);
      // Fetch rest of sequence
      while (ord(substr($txt, ($i + 1), 1)) >= 0x80 && ord(substr($txt, ($i + 1), 1)) < 0xC0) { ++$i; $q[] = ord(substr($txt, $i, 1)); }

      // Check length
      if (count($q) != $s) {
        $out[] = 0xFFFD;
        continue;
      }

      switch ($s) {
        case 2:
          $cp = (($q[0] ^ 0xC0) << 6) | ($q[1] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x80) {
            $out[] = 0xFFFD;
          }
          else {
            $out[] = $cp;
          }
          break;
        case 3:
          $cp = (($q[0] ^ 0xE0) << 12) | (($q[1] ^ 0x80) << 6) | ($q[2] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x800) {
            $out[] = 0xFFFD;
          }
          // Check for UTF-8 encoded surrogates (caused by a bad UTF-8 encoder)
          else if ($c > 0xD800 && $c < 0xDFFF) {
            $out[] = 0xFFFD;
          }
          else {
            $out[] = $cp;
          }
          break;
        case 4:
          $cp = (($q[0] ^ 0xF0) << 18) | (($q[1] ^ 0x80) << 12) | (($q[2] ^ 0x80) << 6) | ($q[3] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x10000) {
            $out[] = 0xFFFD;
          }
          // Outside of the Unicode range
          else if ($cp >= 0x10FFFF) {
            $out[] = 0xFFFD;
          }
          else {
            $out[] = $cp;
          }
          break;
      }
    }
  }
  return $out;
}

public function NbLines($w,$txt) {
    //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax = ($w - 2 * $this->cMargin);
	$txt = str_replace('&euro;','â‚¬',$txt);
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);

	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb) {
		$c=$s[$i];
		if(preg_match("/[\n]/u", $c)) {
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if(preg_match("/[ ]/u", $c)) {
			$sep = $i;
		}
        $l = $this->GetStringWidth(substr($s, $j, $i-$j));
        if($l>$wmax) {
            if($sep==-1) {
            	if($i==$j)
                 	$i++;
            } else {
                $i=$sep+1;
            }
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        } else {
			$i++;	        	
        }
    }
    return $nl;
}

//End of class
}

}
?>