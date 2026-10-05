<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature_ui.class.php,v 1.6 2015/01/22 14:33:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/nomenclature/nomenclature_nomenclature.class.php");
require_once($class_path."/encoding_normalize.class.php");


/**
 * class nomenclature_nomenclature
 * Représente une nomenclature
 */
class nomenclature_nomenclature_ui {

	protected $nomenclature;
	


	/**
	 * Constructeur
	 *
	 * @return void
	 * @access public
	 */
	public function __construct( ) {
		
	}
	
	public function set_nomenclature($nomenclature){
		$this->nomenclature = $nomenclature;
	}
	
	public function get_form(){
		return "
			<script type='text/javascript' src='./javascript/instru_drag_n_drop.js'></script>
			<script type='text/javascript' src='./javascript/drag_n_drop.js'></script>
			<script type='text/javascript' src='./javascript/ajax.js'></script>
			<script type='text/javascript' >	
				function mis_en_forme_instrument(id){
					var str=document.getElementById(id).value;
					var res = str.split(' - ');
					if(res[0]) document.getElementById(id).value=res[0];
				}
			</script>
			<div id='nomenclature_tutti' data-dojo-type='apps/nomenclature/nomenclature_nomenclature_ui' data-dojo-props='nomenclature_abbr:\"".$this->nomenclature->get_abbreviation()."\",nomenclature_tree:\"".addslashes(json_encode(encoding_normalize::utf8_normalize($this->nomenclature->get_families_tree())))."\",nomenclature_indefinite_character:\"".$this->nomenclature->get_indefinite_character()."\",workshop_tree:\"".addslashes(json_encode(encoding_normalize::utf8_normalize($this->nomenclature->get_workshops_tree())))."\"'></div>";
	}
	
} // end of nomenclature_nomenclature