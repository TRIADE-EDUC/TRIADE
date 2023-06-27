<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_caddie_procs.class.php,v 1.1 2017-05-06 12:03:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/caddie_procs.class.php");

// définition de la classe de gestion des procédures de paniers

class authorities_caddie_procs extends caddie_procs {
	
	static $module = 'autorites';
	static $table = 'authorities_caddie_procs';
}