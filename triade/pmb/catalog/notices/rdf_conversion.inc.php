<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_conversion.inc.php,v 1.4 2018-09-11 11:33:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($forcage)) $forcage = 0;

require_once($class_path."/rdf_entities_integration/rdf_entities_converter_controller.class.php");

var_dump(rdf_entities_converter_controller::convert($id, "record"));