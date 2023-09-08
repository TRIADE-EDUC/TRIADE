<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rtf_factory.class.php,v 1.2 2011-08-16 12:17:31 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/rtf/Rtf.php");

class pmb2RTF extends Rtf {
	
	function to_utf8($string){
		global $charset;
		
		if($charset != 'utf-8'){
			return utf8_encode($string);
		}
		return $string;
	}
	
}


class rtf_factory {
	
	public static function make() {
		
		return new pmb2RTF();
	}
}

