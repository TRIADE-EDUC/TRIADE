<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.14 2022/03/11 09:26:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $quoi, $idemprcaddie;

require_once("$class_path/classementGen.class.php") ;
require_once($class_path."/empr_caddie_procs.class.php");
require_once ($include_path."/templates/empr_cart.tpl.php");

empr_caddie_controller::proceed_module_gestion($quoi, $idemprcaddie);