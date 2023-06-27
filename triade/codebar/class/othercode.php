<?php
//////////////////////////////////////////////////////////////////////
// othercode.php
//--------------------------------------------------------------------
//
// Sub-Class - othercode
//
// Other Codes
// Starting with a bar and altern to space, bar, ...
// 0 is the smallest
//
//--------------------------------------------------------------------
// Revision History
// V1.00	17 jun	2004	Jean-Sebastien Goupil
//--------------------------------------------------------------------
// Copyright (C) Jean-Sebastien Goupil
// http://other.lookstrike.com/barcode/
//--------------------------------------------------------------------
//////////////////////////////////////////////////////////////////////
if(!defined('IN_CB'))die("You are not allowed to access to this page.");

class othercode extends BarCode {
	protected $text;
	protected $textfont;
	private $a1;

	public function othercode($maxHeight,FColor $color1,FColor $color2,$res,$text,$textfont,$a1="") {
		BarCode::BarCode($maxHeight,$color1,$color2,$res);
		$this->setText($text);
		$this->textfont = $textfont;
		$this->a1 = $a1;
	}
	public function setText($text) {
		$this->text = $text;
	}
	public function draw($im) {
		$this->DrawChar($im,$this->text,1);
		$this->lastX = $this->positionX;
		$this->lastY = $this->maxHeight;
		$this->DrawText($im);
	}
	protected function DrawText($im) {
		if($this->textfont != 0 && $this->a1!="") {
			$bar_color = (is_null($this->color1))?NULL:$this->color1->allocate($im);
			if(!is_null($bar_color)) {
				$xPosition = ($this->positionX / 2) - (strlen($this->a1)/2)*imagefontwidth($this->textfont);
				$text_color = (is_null($this->color1))?NULL:$this->color1->allocate($im);
				imagestring($im,$this->textfont,$xPosition,$this->maxHeight,$this->a1,$text_color);
			}
			$this->lastY = $this->maxHeight + imagefontheight($this->textfont);
		}
	}
};
?>