<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold_point.class.php,v 1.3 2016/11/05 14:49:07 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
require_once($class_path . "/map/map_hold.class.php");

/**
 * class map_hold_point
 * Classe représentant une emprise de type point
 */
class map_hold_point extends map_hold {
    /** Aggregations: */
    /** Compositions: */
    /*     * * Attributes: ** */

    /**
     *
     *
     * @return string
     * @access public
     */
    public function get_hold_type() {

        return "POINT";
    }

// end of member function get_hold_type

    protected function build_coords() {
        $coords_string = substr($this->wkt, strpos($this->wkt, "(") + 1, -1);
        $coords = explode(" ", $coords_string);
        $this->coords[] = new map_coord($coords[0], $coords[1]);
        $this->coords_uptodate = true;
    }

    protected function build_wkt() {
        $this->wkt = $this->get_hold_type() . "(";
        $tmp_wkt = "";
        foreach ($this->coords as $coord) {
            if ($tmp_wkt != "")
                $tmp_wkt .= ",";
            $tmp_wkt.= $coord->get_decimal_lat() . " " . $coord->get_decimal_long();
        }
        $this->wkt.= $tmp_wkt . ")";
        $this->wkt_uptodate = true;
    }
}

// end of map_hold_point