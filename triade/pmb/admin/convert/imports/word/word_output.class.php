<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: word_output.class.php,v 1.2 2019/07/12 10:25:27 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_output.class.php");

class word_output extends convert_output {
	public function _get_header_($output_params) {
		$r="";
		$f_rtf=@fopen("imports/word/".$output_params['RTFTEMPLATE'][0]['value'],"r");
		while (!feof($f_rtf)) {
			$line=fgets($f_rtf,4096);
			if (strpos($line,"!!START!!")===false) {
				$r.=$line;
			} else break;
		}
		fclose($f_rtf);
	    return $r;
	}
	
	public function _get_footer_($output_params) {
		$r="";
		$f_rtf=@fopen("imports/word/".$output_params['RTFTEMPLATE'][0]['value'],"r");
		while (!feof($f_rtf)) {
			$line=fgets($f_rtf,4096);
			if (strpos($line,"!!STOP!!")!==false) {
				break;
			}
		}
		while (!feof($f_rtf)) {
			$line=fgets($f_rtf,4096);
			$r.=$line;
		}
		fclose($f_rtf);
	    return $r;
	}
}
?>