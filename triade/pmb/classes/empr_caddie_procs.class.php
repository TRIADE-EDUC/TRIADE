<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie_procs.class.php,v 1.3.12.1 2025/03/20 08:37:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/caddie_procs.class.php");

// définition de la classe de gestion des procédures de paniers

class empr_caddie_procs extends caddie_procs {
	
	public static $module = 'circ';
	public static $table = 'empr_caddie_procs';
	
	protected static function get_interface_form_instance() {
	    return new interface_circ_form('maj_proc');
	}
	
	public static function get_parameters_remote() {
		$allowed_proc_types = array("PEMPS", "PEMPA");
		$types_selectaction = array(
				"PEMPA" => 'ACTION',
				"PEMPS" => 'SELECT');
		$testable_types = array(
				"PEMPS" => true,
				"PEMPA" => true
		);
		$type_titles = array(
				"PEMPS" => "remote_procedures_circ_select",
				"PEMPA" => "remote_procedures_circ_action"
		);
		return array(
				'allowed_proc_types' => $allowed_proc_types,
				'types_selectaction' => $types_selectaction,
				'testable_types' => $testable_types,
				'type_titles' => $type_titles
		);
	}
	
	public static function get_display_remote_lists() {
		static::get_display_remote_list("PEMPS");
		static::get_display_remote_list("PEMPA");
	}
}