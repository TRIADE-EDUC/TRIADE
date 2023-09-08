<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sparql_view_django.class.php,v 1.1 2013-09-26 10:15:57 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sparql_view_django extends cms_module_common_view_django {

	
	public function render($datas){
		for($i=0 ; $i<count($datas['result']) ; $i++){
			foreach($datas['result'][$i] as $key => $value){
				if(strpos($key," ") !== false){
					$datas['result'][$i][str_replace(" ","_",$key)] = $value;
					unset($datas['result'][$i][$key]);
				}
				$datas['result'][$i][$key] = $this->charset_normalize($value,"utf-8");
			}
		}
		return parent::render($datas);
	}
}