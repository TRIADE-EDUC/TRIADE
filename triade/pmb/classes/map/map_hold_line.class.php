<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold_line.class.php,v 1.2.20.1 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
require_once($class_path . "/map/map_hold.class.php");

/**
 * class map_hold_line
 * Classe représentant une ligne
 */
class map_hold_line extends map_hold {

    /**
     *
     * @return string
     * @access public
     */
    public function get_hold_type() {
        return '';
    }
}
