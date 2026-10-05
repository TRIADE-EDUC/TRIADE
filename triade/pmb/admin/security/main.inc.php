<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2023/06/19 14:29:17 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $include_path, $lang;
global $data;
global $sub;

switch ($sub) {
    case 'auth':
        if (isset($data)) {
            $data = json_decode(stripslashes($data));
        }
        break;
    default:
        include("$include_path/messages/help/$lang/admin_security.txt");
        break;
}
