<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: apache_functions.inc.php,v 1.2 2015/11/30 09:04:47 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!function_exists('getallheaders')) {
	function getallheaders() {
		foreach($_SERVER as $key=>$value) {
			if (substr($key,0,5)=="HTTP_") {
				$key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
				$out[$key]=$value;
			}else{
				$out[$key]=$value;
			}
		}
		return $out;
	}
}