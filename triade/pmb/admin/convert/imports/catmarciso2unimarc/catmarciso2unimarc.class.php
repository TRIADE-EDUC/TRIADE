<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: catmarciso2unimarc.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/marc_table.class.php");
require_once("$include_path/isbn.inc.php");
require_once($base_path."/admin/convert/convert.class.php");

class catmarciso2unimarc extends convert {

	protected static function cut_header($notice, $s, $islast, $isfirst, $param_path) {
		global $ISO_decode_do_not_decode;
		$ISO_decode_do_not_decode=true;
		if ($notice) {
			if (substr($notice,0,2)=="\r\n")
					$data=substr($notice,2);
			else $data=$notice;
		} else {	
				$error="Registre buit";
		}
		if ((!$data)&&(!$error)) $error="Dernière notice : ne pas tenir compte !";
		if (!$error) $r['VALID'] = true; else $r['VALID']=false;
		$r['ERROR'] = $error;
		$r['DATA'] = $data;
		return $r;
	}
}
