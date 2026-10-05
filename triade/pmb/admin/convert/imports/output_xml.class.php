<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: output_xml.class.php,v 1.1 2018/07/25 06:19:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_output.class.php");

class output_xml extends convert_output {
	public function _get_header_($output_params) {
		global $charset;
	
		$tmp_charset = $charset;
		if (isset($output_params["CHARSET"]))
			$tmp_charset = $output_params["CHARSET"];
	
			$r="<?xml version=\"1.0\" encoding=\"$tmp_charset\"?>\n";
			$r.="<".$output_params['ROOTELEMENT'][0]['value'].">\n";
			if(isset($output_params['ADDHEADER'][0]['value'])) {
				$r.=$output_params['ADDHEADER'][0]['value'];
			}
			return $r;
	}
	
	public function _get_footer_($output_params) {
		$r="";
		if(isset($output_params['ADDFOOTER'][0]['value'])) {
			$r.=$output_params['ADDFOOTER'][0]['value'];
		}
		return $r."</".$output_params['ROOTELEMENT'][0]['value'].">";
	}
}

?>