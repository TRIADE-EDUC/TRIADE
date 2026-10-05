<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_category_ui.class.php,v 1.1 2018/01/17 15:01:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/frbr/cataloging/frbr_cataloging_category.tpl.php");

/**
 * class frbr_cataloging_category_ui
 * 
 */

class frbr_cataloging_category_ui{

	/** Aggregations: */

	/** Compositions: */

	/** Fonctions: */
	
	public static function get_form(){
		global $frbr_cataloging_category_form_tpl;
		$form = $frbr_cataloging_category_form_tpl;
		return $form;
	}

} // end of frbr_cataloging_category_ui
