<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.8 2021/04/07 12:31:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");

global $categ;

switch ($categ) {
    case 'animations':
        include('./animations/animations/animations.inc.php');
        break;
    case 'registration':
        include('./animations/animations/registration.inc.php');
        break;
    case 'mailing':
        include('./animations/animations/mailing.inc.php');
        break;
    case 'dashboard':
        include('./dashboard/ajax_main.inc.php');
        break;
    default:
        break;
}