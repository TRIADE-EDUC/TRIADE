<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2021/03/11 08:07:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");

global $categ;

switch ($categ) {
    case 'registration':
        include('./animations/animations/registration.inc.php');
        break;
    default:
        break;
}