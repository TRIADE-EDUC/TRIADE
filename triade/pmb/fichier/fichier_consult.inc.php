<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fichier_consult.inc.php,v 1.6 2020/06/16 12:12:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $idfiche;

require_once($class_path."/fichier/fiches_controller.class.php");

fiches_controller::proceed($idfiche);