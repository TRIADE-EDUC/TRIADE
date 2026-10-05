<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: isbn.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $code, $fname;

require_once ("$include_path/isbn.inc.php");

if (isset($code)) {
	switch ($fname) {
		case 'getPatterns':
			$tab=array();
			$code = preg_replace('/-|\.| /', '', $code);
			$code = str_replace('x','X',$code);
			//format expurge
			$tab[]=$code; 
			
			if(isEAN($code)) {
				$EAN=$code;
				$isbn = EANtoISBN($code);
				if($isbn) {
					//formatISBN10
					$tab[]=formatISBN($code,10);
					//formatISBN13
					$tab[]=formatISBN($code,13);
				}
			}
			
			if(isISBN($code)) {
				$isbn = formatISBN($code);
				if($isbn) {
					//formatISBN10
					$tab[]=formatISBN($code,10);
					//formatISBN13
					$tab[]=formatISBN($code,13);
					//format EAN
					$tab[]=str_replace('-','',formatISBN($code,13));
				}
			}
			
			if (isISSN($code)) {
				$tab[]=substr($code,0,4).'-'.substr($code,4,4);
			} 
	
			$tab=array_unique($tab);
			ajax_http_send_response(json_encode($tab),'text/html');
			break;
		default:
			break;
	}
}