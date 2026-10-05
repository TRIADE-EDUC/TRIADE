<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2019/09/19 14:10:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $type, $id;

// page de switch recherche notice

// inclusions principales
require_once("$class_path/search_perso.class.php");

switch ($type) {
    case 'EXPL':
        $search_p= new search_perso($id, 'EXPL');
        break;
    case 'RECORDS':
    default:
        $search_p= new search_perso($id, 'RECORDS');
        break;
}
$search_p->proceed();