<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: word.class.php,v 1.2 2022/04/21 07:34:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/convert/convert.class.php");

class word extends convert {
	
	public static function convert_data($notice, $s, $islast, $isfirst, $param_path) {
		global $rtf_pattern;
		global $base_path;
		if ($rtf_pattern == "") {
			$f_rtf = @ fopen($base_path."/admin/convert/imports/$param_path/".$s['RTFTEMPLATE'][0]['value'], "r");
			while (!feof($f_rtf)) {
				$line = fgets($f_rtf, 4096);
				if (strpos($line, "!!START!!") !== false) {
					break;
				}
			}
			//Lecture du pattern
			while (!feof($f_rtf)) {
				$line = fgets($f_rtf, 4096);
				if (strpos($line, "!!STOP!!") === false) {
					$rtf_pattern.= $line;
				} else
					break;
			}
			fclose($f_rtf);
		}
		$t_notice = explode(";", $notice);
		$r_ = str_replace("!!TITLE!!", $t_notice[0], $rtf_pattern);
		$r_ = str_replace("!!AUTHOR!!", $t_notice[1], $r_);
		$r = array();
		$r['VALID'] = true;
		$r['ERROR'] = "";
		$r['DATA'] = $r_;
		return $r;
	}
}
