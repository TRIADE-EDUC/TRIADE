<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origins.inc.php,v 1.1 2011-12-20 13:12:45 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/origins.class.php");
require_once($include_path."/templates/origin.tpl.php");

$origins = new origins_authorities();
$origins->proceed();