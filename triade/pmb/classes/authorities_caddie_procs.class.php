<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_caddie_procs.class.php,v 1.2.12.1 2025/03/20 08:37:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/caddie_procs.class.php");

// définition de la classe de gestion des procédures de paniers

class authorities_caddie_procs extends caddie_procs {
	
	public static $module = 'autorites';
	public static $table = 'authorities_caddie_procs';
	
	protected static function get_interface_form_instance() {
	    return new interface_autorites_form('maj_proc');
	}
}