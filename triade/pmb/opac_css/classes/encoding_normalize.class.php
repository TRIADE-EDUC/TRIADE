<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: encoding_normalize.class.php,v 1.10 2018-06-13 12:41:19 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class encoding_normalize {
	
	protected static function utf8_encode($elem){
		if(is_array($elem)){
			foreach ($elem as $key =>$value){
				$elem[$key] = encoding_normalize::utf8_encode($value);
			}
		}else if(is_object($elem)){
			$elem = encoding_normalize::obj2array($elem);
			$elem = encoding_normalize::utf8_encode($elem);
		}else{
			$elem = utf8_encode($elem);
		}
		return $elem;
	}
	
	public static function utf8_normalize($elem){
		global $charset;
		if($charset != "utf-8"){
			return encoding_normalize::utf8_encode($elem);
		}else{
			return $elem;
		}
	}
	
	
	protected static function obj2array($obj){
		$array = array();
		if(is_object($obj)){
			foreach($obj as $key => $value){
				if(is_object($value)){
					$value = encoding_normalize::obj2array($value);
				}
				$array[$key] = $value;
			}
		}else{
			$array = $obj;
		}
		return $array;
	}
	
	public static function charset_normalize($elem,$input_charset){
		global $charset;
		if(is_array($elem)){
			if(count($elem)) {
				foreach ($elem as $key =>$value){
					$elem[$key] = encoding_normalize::charset_normalize($value,$input_charset);
				}
			}
		}else{
			// Si c'est un numérique on ne fait rien
			if (is_numeric($elem)) {
				return $elem;
			}
			//PMB dans un autre charset, on converti la chaine...
			$elem = self::clean_cp1252($elem, $input_charset);
			if($charset != $input_charset){
				$elem = iconv($input_charset,$charset,$elem);
			}
		}
		return $elem;
	}
	
	public static function json_encode($obj){
		return json_encode(self::utf8_normalize($obj),JSON_HEX_APOS | JSON_HEX_QUOT);
	}
	
	public static function json_decode($obj,$assoc=false){
	    $elem = json_decode($obj,$assoc);
	    foreach ($elem as $key =>$value){
	        $json[encoding_normalize::charset_normalize($key,'utf-8')] = encoding_normalize::charset_normalize($value,'utf-8');
	    }
	    return $json;
	}
	
	public static function clean_cp1252($str,$charset){
		$cp1252_map = array();
		switch($charset){
			case "utf-8" :
				$cp1252_map = array(
					"\xe2\x82\xac" => "EUR", /* EURO SIGN */
					"\xe2\x80\x9a" => "\xc2\xab", /* SINGLE LOW-9 QUOTATION MARK */
					"\xc6\x92" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
					"\xe2\x80\9e" => "\xc2\xab", /* DOUBLE LOW-9 QUOTATION MARK */
					"\xe2\x80\xa6" => "...", /* HORIZONTAL ELLIPSIS */
					"\xe2\x80\xa0" => "?", /* DAGGER */
					"\xe2\x80\xa1" => "?", /* DOUBLE DAGGER */
					"\xcb\x86" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
					"\xe2\x80\xb0" => "?", /* PER MILLE SIGN */
					"\xc5\xa0" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
					"\xe2\x80\xb9" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
					"\xc5\x92" => "OE",   /* LATIN CAPITAL LIGATURE OE */
					"\xc5\xbd" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
					"\xe2\x80\x98" => "\x27", /* LEFT SINGLE QUOTATION MARK */
					"\xe2\x80\x99" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
					"\xe2\x80\x9c" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
					"\xe2\x80\x9d" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
					"\xe2\x80\xa2" => "\xc2\xb7", /* BULLET */
					"\xe2\x80\x93" => "\x20", /* EN DASH */
					"\xe2\x80\x94" => " - ", /* EM DASH */
					"\xcb\x9c" => "\x7e",   /* SMALL TILDE */
					"\xe2\x84\xa2" => "?", /* TRADE MARK SIGN */
					"\xc5\xa1" => "s",   /* LATIN SMALL LETTER S WITH CARON */
					"\xe2\x80\xba" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
					"\xc5\x93" => "oe",   /* LATIN SMALL LIGATURE OE */
					"\xc5\xbe" => "z",   /* LATIN SMALL LETTER Z WITH CARON */
					"\xc5\xb8" => "Y",    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
					"\xe2\x80\xaf" => "", /*  NARROW NO-BREAK SPACE */
					"\xe2\x80\x89" => "", /* THIN SPACE */
				);
				break;
			case "iso8859-1" :
			case "iso-8859-1" :
				$cp1252_map = array(
					"\x80" => "EUR", /* EURO SIGN */
					"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
					"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
					"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
					"\x85" => "...", /* HORIZONTAL ELLIPSIS */
					"\x86" => "?", /* DAGGER */
					"\x87" => "?", /* DOUBLE DAGGER */
					"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
					"\x89" => "?", /* PER MILLE SIGN */
					"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
					"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
					"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
					"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
					"\x91" => "\x27", /* LEFT SINGLE QUOTATION MARK */
					"\x92" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
					"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
					"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
					"\x95" => "\xc2\xb7", /* BULLET */
					"\x96" => "\x20", /* EN DASH */
					"\x97" => "\x20\x20", /* EM DASH */
					"\x98" => "\x7e",   /* SMALL TILDE */
					"\x99" => "?", /* TRADE MARK SIGN */
					"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
					"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
					"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
					"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
					"\x9f" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
				);
				break;
		}
		return strtr($str, $cp1252_map);
	}

	public static function utf8_decode($elem){
		global $charset;
		if($charset != "utf-8"){
			if(is_array($elem)){
				foreach ($elem as $key =>$value){
					$elem[$key] = encoding_normalize::utf8_decode($value);
				}
			}else if(is_object($elem)){
				$elem = encoding_normalize::obj2array($elem);
				$elem = encoding_normalize::utf8_decode($elem);
			}else{
				$elem = utf8_decode($elem);
			}
		}
		return $elem;
	}
}