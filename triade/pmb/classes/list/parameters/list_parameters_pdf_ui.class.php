<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_parameters_pdf_ui.class.php,v 1.5.2.1 2024/10/01 15:38:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_parameters_pdf_ui extends list_parameters_ui {
	
	public function init_filters($filters=array()) {
		$filters['types_param'] = static::get_types_param();
		parent::init_filters($filters);
	}
	
	public static function get_types_param() {
	    return array(
	        'acquisition_pdfliv',
	        'acquisition_pdffac',
	        'pdflettreloansgroup',
	        'pdflettreretard',
	        'pdflettreloans',
	        'pdflettreticket',
	        'pdflettreresa',
	        'pdflettreadhesion',
	        'acquisition_pdfsug',
	    );
	}
	
	public static function get_sstypes_param_is_translated() {
	    return array(
	        '1title_list', '2title_list', '3title_list',
	        'before_list', '1before_list', '2before_list', '3before_list',
	        'after_list', '1after_list', '2after_list', '3after_list',
	        'fdp', '1fdp', '2fdp', '3fdp',
	        'madame_monsieur', '1madame_monsieur', '2madame_monsieur', '3madame_monsieur',
	    );
	}
}