<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: encoding_normalize.class.php,v 1.20.2.1 2024/08/14 12:39:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if (!defined('UTF16_BIG_ENDIAN_BOM')) {
	define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
}
if (!defined('UTF16_LITTLE_ENDIAN_BOM')) {
	define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
}
if (!defined('UTF8_BOM')) {
	define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
}

class encoding_normalize {
	
	public static function utf8_encode($elem)
	{
		return mb_convert_encoding($elem, 'UTF-8', 'ISO-8859-1');
	}
	
	public static function utf8_normalize($elem)
	{
		global $charset;
		if($charset != "utf-8"){
		    
		    if(is_object($elem)){
		        $elem = encoding_normalize::obj2array($elem);
		    }
		    return mb_convert_encoding($elem, 'UTF-8', 'ISO-8859-1');
			
		}else{
			return $elem;
		}
	}
	
	
	public static function obj2array($obj)
	{
		$array = array();
		if(is_object($obj)){
		    $obj = get_object_vars($obj);
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
	
	public static function charset_normalize($elem,$input_charset)
	{
		global $charset;
		// Si c'est un numérique on ne fait rien
		if (is_numeric($elem) || is_bool($elem)) {
			return $elem;
		}
		if(is_array($elem)){
			if(count($elem)) {
			    $obj = array();
				foreach ($elem as $key =>$value){
				    $obj[encoding_normalize::charset_normalize($key,$input_charset)] = encoding_normalize::charset_normalize($value,$input_charset);
				}
				$elem = $obj;
			}
		} elseif (is_object($elem)) {
		    $object_vars = get_object_vars($elem);
		    $obj = new stdClass();
		    foreach($object_vars as $key => $value) {
		        $obj->{encoding_normalize::charset_normalize($key,$input_charset)} = encoding_normalize::charset_normalize($value,$input_charset);
		    }
		    $elem = $obj;		    
        }else{
			//PMB dans un autre charset, on converti la chaine...
			$elem = self::clean_cp1252($elem, $input_charset);
			if($charset != $input_charset){
			    $str_conv = @iconv($input_charset,$charset,$elem);
			    if ($str_conv !== false) {
			        $elem = $str_conv;
			    }
			}
		}
		return $elem;
	}
	
	public static function json_encode($obj, $options = JSON_HEX_APOS|JSON_HEX_QUOT)
	{
		return json_encode(self::utf8_normalize($obj), $options);
	}
	
	public static function json_decode($obj, $assoc=false)
	{
	    if (empty($obj)) {
	        return;
	    }

	    $elem = json_decode($obj ?? "", $assoc);
	    if (empty($elem)) {
	        return;
	    }

	    return encoding_normalize::charset_normalize($elem, 'utf-8');
	}
	
	public static function clean_cp1252($str,$charset)
	{
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
		return strtr($str ?? "", $cp1252_map);
	}

	public static function utf8_decode($elem)
	{
		global $charset;
		if($charset != "utf-8"){
		    if(is_object($elem)){
		        $elem = encoding_normalize::obj2array($elem);
		    }
		    return mb_convert_encoding($elem, 'ISO-8859-1', 'UTF-8');
		}
		return $elem;
	}
	
	public static function detect_encoding($str='', $list_encodings = null) 
	{

	    if (!isset($list_encodings)) {
	        $list_encodings = mb_list_encodings();
	    }

		$first2 = substr($str, 0, 2);
		$first3 = substr($str, 0, 3);

		if ($first3 == UTF8_BOM) {
			return 'utf-8';
		} elseif ($first2 == UTF16_BIG_ENDIAN_BOM) {
			return 'utf-16be';
		} elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) {
			return 'utf-16le';
		}

		$mbde = mb_detect_encoding($str, $list_encodings, true);
		if ($mbde) {
			return $mbde;
		}
		return false;
	}

	/**
	 * Permet de convertir une chaine avec le bon encodage (global $charset)
	 * Si l'encodage n'a pas fonctionne, retourne la chaine initiale
	 *
	 * @param string $str
	 * @return string
	 */
	public static function convert_encoding($str)
	{
	    global $charset;

	    $encoding = mb_detect_encoding($str, ["UTF-8", "ISO-8859-1"]);
	    if (strtolower($encoding) != $charset) {
	        $convert = mb_convert_encoding($str, $charset, $encoding);
	        return $convert ? $convert : $str;
	    }
	    return $str;
	}

}

