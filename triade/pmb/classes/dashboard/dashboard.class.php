<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard.class.php,v 1.9 2023/05/04 10:16:42 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path, $autoload;
require_once($include_path."/templates/dashboard.tpl.php");

class dashboard {
	private $dashboard_modules = array();
	private $dashboards;
	private $dasboard_to_print = array();
	private $layout; 
	
	public function __construct($dashboard_modules=array()){
		$this->dashboard_modules = $dashboard_modules;
		if(!count($this->dashboard_modules)){
			$this->load_all_modules();
		}
		$this->check_authorization();
	}

	public function render(){
		//On a la liste des modules... On récupère les tableaux de bord...
		$this->dashboards =array();
		foreach($this->dashboard_modules as $module_name){
			$dashboard_classname = "dashboard_module_".$module_name;
			$this->dashboards[] = new $dashboard_classname();
		}
		
		//les différents tableaux
		$this->dasboard_to_print = $context = array();
		foreach($this->dashboards as $dashboard){
			if ($dashboard->module == "dashboard"){
				$context['quick_actions'] = $dashboard->get_quick_params_form();
			}
			$this->dasboard_to_print = array_merge($this->dasboard_to_print,$dashboard->render_infos());
		}	
	
		//on charge le layout !
		$template = $this->load_layout();
		$context['dashboards'] = $this->dasboard_to_print;
 		return h2o($template)->render($context); 
	}
	
	private function render_alerts(){
		foreach($this->dashboards as $dashboard){
			
		}
	}

	private function check_authorization(){
		global $class_path;
		$authorized_modules = array();
		foreach($this->dashboard_modules as $module){
			switch($module){
				case "fichier" :
					global $fiches_active;
					if(SESSrights & DEMANDES_AUTH && $fiches_active && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;				
				case "demandes" :
					global $demandes_active;
					if(SESSrights & DEMANDES_AUTH && $demandes_active && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;				
				case "acquisition" :
					global $acquisition_active;
					if(SESSrights & ACQUISITION_AUTH && $acquisition_active && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;				
				case "catalog" :
					if(SESSrights & CATALOGAGE_AUTH && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;				
				case "circ" :
					if(SESSrights & CIRCULATION_AUTH && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;
				case "dashboard" :
					if(file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module; 
					break;
				case "autorites" :
					if(SESSrights & AUTORITES_AUTH && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;	
				case "cms" :
					global $cms_active;
					if(SESSrights & CMS_AUTH && $cms_active && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;
				case "dsi" :
					global $dsi_active;
					if(SESSrights & DSI_AUTH && $dsi_active && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;	
				case "edit" :
					if(SESSrights & EDIT_AUTH && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;
				case "admin" :
					if(SESSrights & ADMINISTRATION_AUTH && file_exists($class_path."/dashboard/dashboard_module_".$module.".class.php")) $authorized_modules[] = $module;
					break;
			}
		}
		$this->dashboard_modules = $authorized_modules;
	}
	
	private function load_all_modules(){
			$this->dashboard_modules = array();
			$this->dashboard_modules[] = "dashboard";
			$this->dashboard_modules[] = "circ";
			$this->dashboard_modules[] = "catalog";
	 		$this->dashboard_modules[] = "autorites";
			$this->dashboard_modules[] = "edit";
			$this->dashboard_modules[] = "dsi";
			$this->dashboard_modules[] = "acquisition";
			$this->dashboard_modules[] = "demandes";
			$this->dashboard_modules[] = "fichier";
			$this->dashboard_modules[] = "cms";	
			$this->dashboard_modules[] = "admin";
	}
	
	private function choose_layout(){
		$nb_elem = count($this->dasboard_to_print);
		switch ($nb_elem){
			case 1 :
				$this->layout ="layout";
				break;
			case 2 :
				$this->layout ="layout2";
				break;	
			default :
				if($nb_elem>=10 || $nb_elem%4 == 0){
					$this->layout= "layout4";
				}else if ($nb_elem%3 == 0){
					$this->layout= "layout3";
				}else if($nb_elem%4 >= $nb_elem%3){
					$this->layout= "layout4";
				}else{
					$this->layout= "layout3";
				}
				break;				
		}
// 		$this->layout = "layout3";
	}
	
	private function load_layout(){
		global $include_path;
		global $lang;
	
		$this->choose_layout();
		$filepath = $include_path."/dashboard/layouts/".$this->layout;
		if(file_exists($filepath."_subst.xml")){
			$filepath.="_subst";
		}	
		$template = "";
		if(file_exists($filepath.".xml")){
			$xml = new DOMDocument();
			$xml->load($filepath.".xml");
			//langue de référence
			$default_lang = "";
			$xml_template = $xml->getElementsByTagName("template")->item(0);
				
			if($xml_template->hasAttributes()){
				$attributes = $xml_template->attributes;
				for($i=0 ; $i<$attributes->length ; $i++){
					if($attributes->item($i)->nodeName == "default_lang"){
						//dom retourne de l'utf-8 à tous les coups...
						$default_lang = $this->charset_normalize($attributes->item($i)->nodeValue,"utf-8");
						break;
					}
				}
			}
			//on cherche le template qui va bien...
			$html_templates = $xml_template->getElementsByTagName("content");
			$end = false;
			for($i=0 ; $i<$html_templates->length ; $i++){
				if($i == 0 || $html_templates->length == 1){
					$template = $this->charset_normalize($html_templates->item($i)->nodeValue,"utf-8");
				}
				$attributes = $html_templates->item($i)->attributes;
				for($j=0 ; $j<$attributes->length ; $j++){
					if($attributes->item($j)->nodeName == "lang"){
						$current_lang = $this->charset_normalize($attributes->item($j)->nodeValue,"utf-8");
						if($current_lang == $lang){
							$template = $this->charset_normalize($html_templates->item($i)->nodeValue,"utf-8");
							//break(2);
							$end = true;
							break;
						}
					}
					if($attributes->item($j)->nodeName == "default_lang"){
						$current_lang = $this->charset_normalize($attributes->item($j)->nodeValue,"utf-8");
						if($current_lang == $lang){
							$template = $this->charset_normalize($html_templates->item($i)->nodeValue,"utf-8");
							break;
						}
					}
				}
				if($end) break;				
			}
		}
		return $template;
	}
	protected static function charset_normalize($elem,$input_charset){
		global $charset;
		if(is_array($elem)){
			foreach ($elem as $key =>$value){
				$elem[$key] = self::charset_normalize($value,$input_charset);
			}
		}else{
			//PMB dans un autre charset, on converti la chaine...
			$elem = self::clean_cp1252($elem, $input_charset);
			if($charset != $input_charset){
				$elem = iconv($input_charset,$charset,$elem);
			}
		}
		return $elem;
	}
	protected static function clean_cp1252($str,$charset){
		$cp1252_map = array();
		switch($charset){
			case "utf-8" :
				$cp1252_map = array(
				"\x80" => "EUR", /* EURO SIGN */
				"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
				"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
				"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
				"\x85" => "...", /* HORIZONTAL ELLIPSIS */
				"\x86" => "?", /* DAGGER */
				"\x87" => "?", /* DOUBLE DAGGER */
				"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\x89" => "?", /* PER MILLE SIGN */
				"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
				"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
				"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
				"\xe2\x80\x98" => "\x27", /* LEFT SINGLE QUOTATION MARK */
				"\xe2\x80\x99" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
				"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
				"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
				"\x95" => "\b7", /* BULLET */
				"\x96" => "\x20", /* EN DASH */
				"\x97" => "\x20\x20", /* EM DASH */
				"\x98" => "\x7e",   /* SMALL TILDE */
				"\x99" => "?", /* TRADE MARK SIGN */
				"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
				"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
				"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
				"\x9f" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
				);
				break;
			case "iso8859-1" :
			case "iso-8859-1" :
				$cp1252_map = array(
				"\x80" => "EUR", /* EURO SIGN */
				"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
				"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
				"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
				"\x85" => "...", /* HORIZONTAL ELLIPSIS */
				"\x86" => "?", /* DAGGER */
				"\x87" => "?", /* DOUBLE DAGGER */
				"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\x89" => "?", /* PER MILLE SIGN */
				"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
				"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
				"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
				"\x91" => "\x27", /* LEFT SINGLE QUOTATION MARK */
				"\x92" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
				"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
				"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
				"\x95" => "\b7", /* BULLET */
				"\x96" => "\x20", /* EN DASH */
				"\x97" => "\x20\x20", /* EM DASH */
				"\x98" => "\x7e",   /* SMALL TILDE */
				"\x99" => "?", /* TRADE MARK SIGN */
				"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
				"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
				"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
				"\x9f" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
				);
				break;
		}
		return strtr($str, $cp1252_map);
	}
}