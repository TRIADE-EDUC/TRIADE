<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_PDF.class.php,v 1.19.2.5 2024/12/04 08:55:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/parameters_subst.class.php");

class lettre_PDF {
	
	public $PDF;
	public $orient_page = 'P';			//Orientation page (P=portrait, L=paysage)
	public $largeur_page = 210;			//Largeur de page
	public $hauteur_page = 297;			//Hauteur de page
	public $unit = 'mm';				//Unite 
	public $marge_haut = 10;			//Marge haut
	public $marge_bas = 20;				//Marge bas
	public $marge_droite = 10;			//Marge droite
	public $marge_gauche = 10;			//Marge gauche
	public $w = 190;					//Largeur utile page
	public $font = 'Helvetica';			//Police
	public $fs = 10;					//Taille police
	public $y_footer = 8;				//Distance footer / bas de page
	public $h_footer = 8;				//Hauteur de la ligne
	public $fs_footer = 10;				//Taille police footer
	
	protected static $instances = array();
	protected static $language = '';
	protected static $languages_messages = array();
	protected static $languages_specifics_globals = array();
	
	public function __construct() {
	    $this->_substitution_parameters();
		$this->_init();
		$this->_open();
	}
	
	protected function _substitution_parameters() {
	    global $include_path;
	    global $deflt2docs_location;
	    
	    //Globalisons tout d'abord les paramètres communs à toutes les localisations
	    if (file_exists($include_path."/parameters_subst/pdf_per_localisations_subst.xml")){
	        $parameter_subst = new parameters_subst($include_path."/parameters_subst/pdf_per_localisations_subst.xml", 0);
	    } else {
	        $parameter_subst = new parameters_subst($include_path."/parameters_subst/pdf_per_localisations.xml", 0);
	    }
	    $parameter_subst->extract();
	    
	    if(isset($deflt2docs_location)) {
	        if (file_exists($include_path."/parameters_subst/pdf_per_localisations_subst.xml")){
	            $parameter_subst = new parameters_subst($include_path."/parameters_subst/pdf_per_localisations_subst.xml", $deflt2docs_location);
	        } else {
	            $parameter_subst = new parameters_subst($include_path."/parameters_subst/pdf_per_localisations.xml", $deflt2docs_location);
	        }
	        $parameter_subst->extract();
	    }
	}
	
	protected function _init_default_parameters() {
		
	}
	
	protected function _init_default_positions() {
	
	}
	
	protected function _init() {
		global $pmb_pdf_font;
		
		$this->_init_PDF();
		
		$this->_init_marges();
		
		$this->w = $this->largeur_page-$this->marge_gauche-$this->marge_droite;
		
		$this->font = $pmb_pdf_font;
		if($this->get_parameter_value('text_size')) {
			$this->fs = $this->get_parameter_value('text_size');
		}
		$this->_init_default_parameters();
		$this->_init_default_positions();
		
		$pos_footer = explode(',', $this->get_parameter_value('pos_footer'));
		if(count($pos_footer) == 3) {
			$this->PDF->y_footer = $pos_footer[0];
			$this->PDF->h_footer = $pos_footer[1];
			$this->PDF->fs_footer = $pos_footer[2];
		} elseif(count($pos_footer) == 2) { //parametres acquisition
			$this->PDF->y_footer = $pos_footer[0];
			$this->PDF->h_footer = $pos_footer[1];
			$this->PDF->fs_footer = $pos_footer[1];
		} else {
			$this->PDF->y_footer=$this->y_footer;
			$this->PDF->h_footer=$this->h_footer;
			$this->PDF->fs_footer=$this->fs_footer;
		}
	}
	
	protected function _open() {
		$this->PDF->Open();
		$this->PDF->SetMargins($this->marge_gauche, $this->marge_haut, $this->marge_droite);
		$this->PDF->setFont($this->font);
		
		$this->PDF->footer_type=1;
		if(!empty($this->get_parameter_value('footer'))) {
			$this->PDF->msg_footer = $this->get_parameter_value('footer')."\n";
			$this->PDF->footer_type=3;
		}
	}
	
	protected function get_parameter_id($type_param, $sstype_param) {
	    $query = "SELECT id_param FROM parametres WHERE type_param='".addslashes($type_param)."' AND sstype_param='".addslashes($sstype_param)."'";
	    return pmb_mysql_result(pmb_mysql_query($query), 0, 'id_param');
	}
	
	protected function get_parameter_value($name) {
	    $parameter_name = static::get_parameter_prefix().'_'.$name;
	    global $$parameter_name;
	    return $$parameter_name;
	}
	
	protected function set_parameter_value($name, $value) {
		$parameter_name = static::get_parameter_prefix().'_'.$name;
		global $$parameter_name;
		$$parameter_name = $value;
	}
	
	protected function set_language($language) {
		global $msg, $lang;
	    
	    if(empty(static::$languages_messages)) {
	    	static::$languages_messages[$lang] = $msg;
	    }
	    if($lang != $language) {
            $msg = static::get_language_messages($language);
            parameter::set_language_parameters($language);
            static::set_language_specifics_globals($language);
	    }
        static::$language = $language;
	}
	
	protected function restaure_language() {
		global $msg, $lang;
	    
	    if(!empty(static::$languages_messages[$lang])) {
	    	$msg = static::$languages_messages[$lang];
	    }
	    parameter::set_language_parameters($lang);
	    static::set_language_specifics_globals($lang);
	}
	
	protected function _init_marges() {
		$marges_page = $this->get_parameter_value('marges_page');
		if(!empty($marges_page)) {
			$marges_page = explode(',', $marges_page);
			if (!empty($marges_page[0])) $this->marge_haut = $marges_page[0];
			if (!empty($marges_page[1])) $this->marge_bas = $marges_page[1];
			if (!empty($marges_page[2])) $this->marge_droite = $marges_page[2];
			if (!empty($marges_page[3])) $this->marge_gauche = $marges_page[3];
		} else {
			$marge_page_droite = $this->get_parameter_value('marge_page_droite');
			if (!empty($marge_page_droite)) $this->marge_droite = $marge_page_droite;
			$marge_page_gauche = $this->get_parameter_value('marge_page_gauche');
			if (!empty($marge_page_gauche)) $this->marge_gauche = $marge_page_gauche;
		}
	}
	
	protected function _init_position($name, $position=array()) {
		if (isset($position[0]) && $position[0]) $this->{"x_".$name} = $position[0];
		if (isset($position[1]) && $position[1]) $this->{"y_".$name} = $position[1];
		if (isset($position[2]) && $position[2]) $this->{"l_".$name} = $position[2];
		if (isset($position[3]) && $position[3]) $this->{"h_".$name} = $position[3];
		if (isset($position[4]) && $position[4]) $this->{"fs_".$name} = $position[4];
	}
	
	protected function get_position_values($name) {
		$values = array();
		if (isset($this->{"x_".$name})) $values[0] = $this->{"x_".$name};
		if (isset($this->{"y_".$name})) $values[1] = $this->{"y_".$name};
		if (isset($this->{"l_".$name})) $values[2] = $this->{"l_".$name};
		if (isset($this->{"h_".$name})) $values[3] = $this->{"h_".$name};
		if (isset($this->{"fs_".$name})) $values[4] = $this->{"fs_".$name};
		return $values;
	}
	
	protected function ln_multiCell() {
		$this->PDF->Ln();
	}
	
	protected function display_multiCell($w, $h, $txt, $border=0, $align='J', $fill=0) {
	    if(strpos($txt, '<br />') !== false || strpos($txt, '<br/>') !== false) {
	        if(strpos($txt, '<br />') !== false) {
	            $sections = explode('<br />', $txt);
	        } else {
	            $sections = explode('<br/>', $txt);
	        }
			foreach ($sections as $section) {
				$this->PDF->multiCell($w, $h, $section, $border, $align, $fill);
				$this->ln_multiCell();
			}
		} else {
			$this->PDF->multiCell($w, $h, $txt , $border, $align, $fill);
		}
	}
	
	protected function display_parameter_multiCell($name) {
		$this->display_multiCell($this->{"l_".$name}, $this->{"h_".$name}, $this->get_parameter_value($name));
	}
	
	public function getLettre($format=0,$name='lettre.pdf') {
		if (!$format) {
			return $this->PDF->OutPut();
		} else {
			return $this->PDF->OutPut($name,'S');
		}
	}
	
	public function getFileName() {
		return $this->filename;
	}
	
	protected static function get_parameter_prefix() {
	    return '';
	}
	
	public function reset_default_positions() {
		$this->_init_default_positions();
	}
	
	protected static function get_parameter_name($name) {
		return static::get_parameter_prefix().'_'.$name;
	}
	
	public static function get_instance($group='') {
	    global $msg, $charset;
	    global $base_path, $class_path, $include_path;
	    
	    $className = static::class;
	    if(!isset(static::$instances[$className])) {
	    	$print_parameter = static::get_parameter_name('print');
	    	global ${$print_parameter};
		    if($group) {
		        if(!empty(${$print_parameter}) && file_exists($class_path."/pdf/".$group."/".${$print_parameter}.".class.php")) {
		            require_once($class_path."/pdf/".$group."/".${$print_parameter}.".class.php");
		            $className = ${$print_parameter};
		        } else {
		            require_once($class_path."/pdf/".$group."/".$className.".class.php");
		        }
		    } else {
		        if(!empty(${$print_parameter}) && file_exists($class_path."/pdf/".${$print_parameter}.".class.php")) {
		            require_once($class_path."/pdf/".${$print_parameter}.".class.php");
		            $className = ${$print_parameter};
		        } else {
		            require_once($class_path."/pdf/".$className.".class.php");
		        }
		    }
		    static::$instances[$className] = new $className();
	    } else {
	    	//Ré-initialisation des positions pour démarrer une nouvelle page
	    	static::$instances[$className]->reset_default_positions();
	    }
	    return static::$instances[$className];
	}
	
	public static function get_language_messages($language) {
	    global $include_path;
	    
	    if(!isset(static::$languages_messages[$language])) {
	        $messages_instance = new XMLlist($include_path."/messages/".$language.".xml");
	        $messages_instance->analyser();
	        static::$languages_messages[$language] = $messages_instance->table;
	    }
	    return static::$languages_messages[$language];
	}
	
	public static function get_language_specifics_globals($language) {
	    global $lang;
	    global $deflt2docs_location, $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_town;
	    
	    if(!isset(static::$languages_specifics_globals[$lang])) {
	        static::$languages_specifics_globals[$lang] = [];
	        static::$languages_specifics_globals[$lang]['biblio_name'] = $biblio_name;
	        static::$languages_specifics_globals[$lang]['biblio_adr1'] = $biblio_adr1;
	        static::$languages_specifics_globals[$lang]['biblio_adr2'] = $biblio_adr2;
	        static::$languages_specifics_globals[$lang]['biblio_town'] = $biblio_town;
	    }
	    if(!isset(static::$languages_specifics_globals[$language])) {
	        static::$languages_specifics_globals[$language] = [];
	        static::$languages_specifics_globals[$language]['biblio_name'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'name', $biblio_name, $language);
	        static::$languages_specifics_globals[$language]['biblio_adr1'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'adr1', $biblio_adr1, $language);
	        static::$languages_specifics_globals[$language]['biblio_adr2'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'adr2', $biblio_adr2, $language);
	        static::$languages_specifics_globals[$language]['biblio_town'] = translation::get_translated_text($deflt2docs_location, 'docs_location', 'town', $biblio_town, $language);
	    }
	    return static::$languages_specifics_globals[$language];
	}
	
	public static function set_language_specifics_globals($language) {
	    
	    $globals = static::get_language_specifics_globals($language);
	    if(!empty($globals)) {
	        foreach ($globals as $global_name=>$global_value) {
	            global ${$global_name};
	            ${$global_name} = $global_value;
	        }
	    }
	}
}