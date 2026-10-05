<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rtf_factory.class.php,v 1.4 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class pmb2RTF extends PHPRtfLite {
	
	function to_utf8($string){
		global $charset;
		
		if($charset != 'utf-8'){
			return encoding_normalize::utf8_normalize($string);
		}
		return $string;
	}
	
}


class rtf_factory {
	
	public static function make() {
		
		return new pmb2RTF();
	}
}