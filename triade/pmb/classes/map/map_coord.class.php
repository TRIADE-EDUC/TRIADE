<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_coord.class.php,v 1.10 2016/11/05 14:49:07 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

/**
 * class map_coord
 * 
 */
class map_coord {
    /** Aggregations: */
    /** Compositions: */
    /*     * * Attributes: ** */

    /**
     * Latitude (notation décimale)
     * @access protected
     */
    protected $lat;

    /**
     * Longitude (notation décimale)
     * @access protected
     */
    protected $long;

    /**
     * Constructeur, il utilise les setters de la classe
     *
     * @param float lat Latitude au format degré décimal

     * @param float long Longitude au format degré décimal


     * @return void
     * @access public
     */
    public function __construct($long, $lat) {

        $this->set_lat($lat);
        $this->set_long($long);
    }
// end of member function __construct

    /**
     * 
     *
     * @param float lat Latitude à insérer

     * @return void
     * @access public
     */
    public function set_lat($lat) {

        $this->lat = $lat;
    }
// end of member function set_lat

    /**
     * 
     *
     * @param float long Longitude à insérer

     * @return void
     * @access public
     */
    public function set_long($long) {

        $this->long = $long;
    }
// end of member function set_long

    /**
     * 
     *
     * @param float long Longitude à insérer

     * @param float lat Latitude à insérer

     * @return void
     * @access public
     */
    public function set_coords($long, $lat) {

        $this->set_long($long);
        $this->set_lat($lat);
    }
// end of member function set_coords

    /**
     * Retourne la longitude au format décimal
     *
     * @return float
     * @access public
     */
    public function get_decimal_long() {

        return $this->long;
    }
// end of member function get_decimal_long

    /**
     * Retourne la latitude au format décimal
     *
     * @return float
     * @access public
     */
    public function get_decimal_lat() {

        return $this->lat;
    }
// end of member function get_decimal_lat

    /**
     * Retourne la latitude en degrés minutes secondes
     *
     * @return string
     * @access public
     */
    public function get_sexagesimal_lat() {

        return self::convert_decimal_to_sexagesimal($this->lat);
    }
// end of member function get_sexagesimal_lat

    /**
     * Retourne la longitude en degrés minutes secondes
     *
     * @return string
     * @access public
     */
    public function get_sexagesimal_long() {

        return self::convert_decimal_to_sexagesimal($this->long);
    }
// end of member function get_sexagesimal_long

    /**
     * 
     *
     * @param float value Valeur en degré décimal à  convertir  au format degré°minutes'secondes''

     * @return string
     * @static
     * @access public
     */
    public static function convert_decimal_to_sexagesimal($value) {
        global $msg;

        $neg = false;
        $vars = explode(".", $value);
        $deg = $vars[0];

        if (abs($deg) != $deg) {
            $neg = true;
            $deg = abs($deg);
        }

        if (isset($vars[1])) {
            $tempma = "0." . $vars[1];
            $tempma = $tempma * 3600;
            $min = floor($tempma / 60);
            $sec = $tempma - ($min * 60);
            $sec = round($sec, 1);
            $sec = round($sec);
        } else {
            $min = 0;
            $sec = 0;
        }

        if (round($sec) == 60) {
            $min = $min + 1;
            $sec = 0;
        } else {
            $sec = round($sec);
        }
        if (round($min) == 60) {
            $deg = $deg + 1;
            $min = 0;
        } else {
            $min = round($min);
        }

        if ($neg) {
            $deg = "-" . $deg;
        }
        $dms = $msg["map_coord_format"];
        $dms = str_replace("!!degres!!", $deg, $dms);
        $dms = str_replace("!!minutes!!", $min, $dms);
        $dms = str_replace("!!secondes!!", $sec, $dms);
        //$dms = $deg."°".$min."'".$sec."''";
        return $dms;
    }
// end of member function convert_decimal_to_sexagesimal

    /**
     * 
     *
     * @param string value Valeur en degré°minutes'secondes''à  convertir  au format degré décimal

     * @return float
     * @static
     * @access public
     */
    public static function convert_sexagesimal_to_decimal($value) {

        $dms = str_replace(array("°", "'", "''"), " ", $value);
        $vars = explode(" ", $dms);

        return $vars[0] + ((($vars[1] * 60) + ($vars[2])) / 3600);
    }
// end of member function convert_sexagesimal_to_decimal
}

// end of map_coord
