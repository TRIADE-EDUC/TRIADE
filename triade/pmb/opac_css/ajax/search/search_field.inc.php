<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_field.inc.php,v 1.1 2021/11/19 09:56:51 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;

switch ($sub) {
    case "special":
        global $n, $type;
        $search = new search();
        print $search->get_special_field($type, $n);
        break;
}

