<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold_multilinestring.class.php,v 1.2 2016/11/05 14:49:07 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
require_once($class_path . "/map/map_hold.class.php");

/**
 * class map_hold_multilinestring
 * Classe représentant plusieurs lignes
 */
class map_hold_multilinestring extends map_hold {
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

        return "MULTILINESTRING";
    }

// end of member function get_hold_type

    protected function build_coords() {
        $coords_string = substr($this->wkt, strpos($this->wkt, "(") + 1, -1);
        $coords_multiple_string = substr($coords_string, strpos($coords_string, "(") + 1, -1);
        $lines = explode("),(", $coords_multiple_string);
        foreach ($lines as $line) {
            $coords = explode(",", $line);
            $coords_line = array();
            for ($i = 0; $i < count($coords); $i++) {
                $infos = array();
                $coord = $coords[$i];
                $infos = explode(" ", $coord);
                $coords_line[] = new map_coord($infos[0], $infos[1]);
            }
            $this->coords[] = $coords_line;
        }
        $this->coords_uptodate = true;
    }

    protected function build_wkt() {
        $this->wkt = $this->get_hold_type() . "(";
        $tmp_wkt = "";
        foreach ($this->coords as $line) {
            if ($tmp_wkt == "")
                $tmp_wkt = "(";
            else
                $tmp_wkt .= ",(";
            $tmp_line = "";
            foreach ($line as $coord) {
                if ($tmp_line != "")
                    $tmp_line .= ",";
                $tmp_line.= $coord->get_decimal_lat() . " " . $coord->get_decimal_long();
            }
            $tmp_wkt .= $tmp_line . ")";
        }
        $this->wkt .= $tmp_wkt;
        $this->wkt .= ")";
        $this->wkt_uptodate = true;
    }
}

// end of map_hold_multilinestring