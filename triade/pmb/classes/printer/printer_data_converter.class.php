<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: printer_data_converter.class.php,v 1.5 2023/08/28 14:04:12 tsamson Exp $

//if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class printer_data_converter {
	
	public static function convert_to($data=array(), $encoding='') {
		if(method_exists('printer_data_converter', 'convert_to_'.$encoding)) {
			$method_name = 'convert_to_'.$encoding;
			if (is_string($data)) {
				$data = printer_data_converter::$method_name($data);
			}
			if(is_array($data)) {
				foreach($data as $k=>$v) {
					$data[$k] = printer_data_converter::$method_name($v);
				}
				if (empty($data)) {
				    $data = printer_data_converter::$method_name($data);
				}
			}
		}
		return $data;
	}
		
	public static function convert_to_cp850($data) {
		global $charset;
		if(is_string($data)) {
			$data = iconv($charset,'cp850',$data);
		}
		if(is_array($data)) {
			foreach($data as $k=>$v) {
				$data[$k] = printer_data_converter::convert_to_cp850($v);
			}	
		}
		return $data;
	}

	
	public static function convert_to_ci8($data) {
		global $charset;
		$to_ci8 = array(
				"#"=>"\\23",
//				"0"=>"\\30",
				"à"=>"\\40",
				"â"=>"\\5b",
				"ç"=>"\\5c",
				"ê"=>"\\5d",
				"î"=>"\\5e",
				"ô"=>"\\60",
				"é"=>"\\7b",
				"ù"=>"\\7c",
				"è"=>"\\7d",
				"û"=>"\\7e",
		);
		
		if(is_string($data)) {
			if($charset=='utf-8') {
				$data = encoding_normalize::utf8_decode($data); 
			}
			$data=strtr($data,$to_ci8);
		}
		if(is_array($data)) {
			foreach($data as $k=>$v) {
				$data[$k] = printer_data_converter::convert_to_ci8($v);
			}
		}
		return $data;
	}
	
}

