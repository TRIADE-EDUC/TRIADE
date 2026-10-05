<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_simple_circ.inc.php,v 1.3 2021/12/08 15:59:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $start_date, $end_date;

require_once("$class_path/simple_circ.class.php");

$simple_circ= new simple_circ($start_date,$end_date);
//$simple_circ= new simple_circ("2013-09-01","2013-11-03");
print $simple_circ->get_display();