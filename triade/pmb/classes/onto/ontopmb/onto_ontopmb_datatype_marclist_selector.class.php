<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_marclist_selector.class.php,v 1.1 2017/05/30 13:30:17 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';




/**
 * class onto_common_datatype_resource_selector
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_ontopmb_datatype_marclist_selector  extends onto_common_datatype {
	public static $options=array(
		'country' 			=> "parperso_marclist_option_country",
		'lang'  			=> "parperso_marclist_option_lang",
		'doctype' 			=> "parperso_marclist_option_doctype",
		'function' 			=> "parperso_marclist_option_function",
		'section_995'		=> "parperso_marclist_option_section_995",
		'typdoc_995'		=> "parperso_marclist_option_typdoc_995",
		'codstatdoc_995'	=> "parperso_marclist_option_codstatdoc_995",
		'nivbiblio'			=> "parperso_marclist_option_nivbiblio",
		'music_form'		=> "parperso_marclist_option_music_form",
		'music_key'			=> "parperso_marclist_option_music_key",
	);
	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	/**
	 *
	 * @access public
	 */

	public function check_value(){
		if (is_string($this->value)) return true;
		return false;
	}
	
	public function get_value(){
		return $this->value;
	}
	
	public function get_raw_value(){
		return $this->value;
	}
	
	public function get_formated_value(){
		return $this->value;
	}

} // end of onto_common_datatype_resource_selector
