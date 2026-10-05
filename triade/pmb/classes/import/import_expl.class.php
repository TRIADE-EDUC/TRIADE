<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_expl.class.php,v 1.5.6.1 2024/09/18 12:34:22 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_entities.class.php");

class import_expl extends import_entities {
	
	public static function get_hidden_form($form_name, $next_action) {
		global $current_module;
		global $filename, $to_file;
		global $recharge, $noticenumber, $j;
		
		$hidden_form = "<form class='form-$current_module' NAME=\"".$form_name."\" METHOD=\"post\" ACTION=\"iimport_expl.php\">";
		$hidden_form .= static::get_input_hidden_variable('name_func');
		$hidden_form .= static::get_input_hidden_text('categ', 'import');
		$hidden_form .= static::get_input_hidden_text('sub', 'import_expl');
		$hidden_form .= static::get_input_hidden_text('action', $next_action);
		$hidden_form .= static::get_input_hidden_variable('book_lender_id');
		$hidden_form .= static::get_input_hidden_variable('book_statut_id');
		$hidden_form .= "<input type='hidden' name='file_submit' value='".($filename ? $filename : $to_file)."' />";
		$hidden_form .= static::get_input_hidden_variable('filename');
		$hidden_form .= static::get_input_hidden_variable('from_file');
		$hidden_form .= static::get_input_hidden_variable('isbn_mandatory');
		$hidden_form .= static::get_input_hidden_variable('isbn_only');
		$hidden_form .= static::get_input_hidden_variable('isbn_dedoublonnage');
		$hidden_form .= static::get_input_hidden_variable('cote_mandatory');
		$hidden_form .= static::get_input_hidden_variable('tdoc_codage');
		$hidden_form .= static::get_input_hidden_variable('statisdoc_codage');
		$hidden_form .= static::get_input_hidden_variable('sdoc_codage');
		$hidden_form .= static::get_input_hidden_variable('statutnot');
		$hidden_form .= static::get_input_hidden_variable('notice_is_new');
		$hidden_form .= static::get_input_hidden_variable('book_location_id');
		if($next_action == 'load') {
			$hidden_form .= "<input type=\"hidden\" name=\"recharge\" value=\"YES\" />";
		} elseif($recharge) {
			$hidden_form .= "<input type=\"hidden\" name=\"recharge\" value=\"$recharge\" />";
		}
		if($noticenumber) {
			$hidden_form .= "<input type=\"hidden\" name=\"noticenumber\" value=\"".($noticenumber+$j)."\" />";
		}
		$hidden_form .= static::get_input_hidden_variable('reste');
		$hidden_form .= static::get_input_hidden_variable('nbtot_notice');
		$hidden_form .= static::get_input_hidden_variable('notice_deja_presente');
		$hidden_form .= static::get_input_hidden_variable('notice_rejetee');
		$hidden_form .= static::get_input_hidden_variable('nb_expl_ignores');
		
		$hidden_form .= static::get_input_hidden_variable('que_faire');
		$hidden_form .= static::get_input_hidden_variable('link_generate');
		$hidden_form .= static::get_input_hidden_variable('authorities_notices');
		$hidden_form .= static::get_input_hidden_variable('authorities_default_origin');
		$hidden_form .= static::get_input_hidden_variable('import_force_notice_is_new');
		$hidden_form .= static::get_input_hidden_variable('import_notice_existing_replace');
		$hidden_form .= static::get_input_hidden_variable('notice_replace_links');
		$hidden_form .= static::get_input_hidden_variable('import_without_index');
		$hidden_form .= static::get_hidden_caddies_form();
		$hidden_form .= "</FORM>";
		return $hidden_form;
	}
	
	public static function get_caddies_form() {
		$caddies_form = static::get_caddie_form('NOTI', 'idcaddie', 'caddie');
		$caddies_form .= static::get_caddie_form('BULL', 'idcaddie', 'caddie');
		$caddies_form .= static::get_caddie_form('EXPL', 'idcaddie', 'caddie');
		return $caddies_form;
	}
	
	public static function get_hidden_caddies_form() {
		$hidden_caddies_form = static::get_input_hidden_caddie_variable('NOTI');
		$hidden_caddies_form .= static::get_input_hidden_caddie_variable('BULL');
		$hidden_caddies_form .= static::get_input_hidden_caddie_variable('EXPL');
		return $hidden_caddies_form;
	}
	
	public static function get_links_caddies() {
		$links_caddies = static::get_link_caddie('NOTI');
		$links_caddies .= static::get_link_caddie('BULL');
		$links_caddies .= static::get_link_caddie('EXPL');
		return $links_caddies;
	}
	
	public static function traite_exemplaires($function_name='') {
		
	}
	
	public static function export_traite_exemplaires($ex=array()) {
	
		$subfields=array();
		$subfields["a"] = $ex -> lender_libelle;
		$subfields["c"] = $ex -> lender_libelle;
		$subfields["f"] = $ex -> expl_cb;
		$subfields["k"] = $ex -> expl_cote;
		$subfields["u"] = $ex -> expl_note;

		if ($ex->statusdoc_codage_import) {
			$subfields["o"] = $ex -> statusdoc_codage_import;
		}
		if ($ex -> tdoc_codage_import) {
			$subfields["r"] = $ex -> tdoc_codage_import;
		} else {
			$subfields["r"] = "uu";
		}
		if ($ex -> sdoc_codage_import) {
			$subfields["q"] = $ex -> sdoc_codage_import;
		} else {
			$subfields["q"] = "u";
		}
	
		global $export996 ;
		$export996['f'] = $ex -> expl_cb ;
		$export996['k'] = $ex -> expl_cote ;
		$export996['u'] = $ex -> expl_note ;
	
		$export996['m'] = substr($ex -> expl_date_depot, 0, 4).substr($ex -> expl_date_depot, 5, 2).substr($ex -> expl_date_depot, 8, 2) ;
		$export996['n'] = substr($ex -> expl_date_retour, 0, 4).substr($ex -> expl_date_retour, 5, 2).substr($ex -> expl_date_retour, 8, 2) ;
	
		$export996['a'] = $ex -> lender_libelle;
		$export996['b'] = $ex -> expl_owner;
	
		$export996['v'] = $ex -> location_libelle;
		$export996['w'] = $ex -> locdoc_codage_import;
	
		$export996['x'] = $ex -> section_libelle;
		$export996['y'] = $ex -> sdoc_codage_import;
	
		$export996['e'] = $ex -> tdoc_libelle;
		$export996['r'] = $ex -> tdoc_codage_import;
	
		$export996['1'] = $ex -> statut_libelle;
		$export996['2'] = $ex -> statusdoc_codage_import;
		$export996['3'] = $ex -> pret_flag;
		
		global $export_traitement_exemplaires ;
		$export996['0'] = $export_traitement_exemplaires ;
	
		return 	$subfields ;

	}
}
