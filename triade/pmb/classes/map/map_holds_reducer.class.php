<?php

// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_holds_reducer.class.php,v 1.17.8.1 2025/01/31 09:50:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once($class_path . "/map/map_hold_point.class.php");

class map_holds_reducer {

    /**
     * Emprise de la carte
     * @var map_hold_polygon
     * @access protected
     */
    protected $map_hold;

    /**
     * Emprises des éléments à afficher sur la carte
     * 
     * @access protected
     */
    protected $holds;

    /**
     * Box des emprises des éléments à afficher sur la carte
     *
     * @access protected
     */
    protected $holds_box;
    protected $map_area;
    protected $clusters;
    protected $map_distance;
    protected $displayed_holds;
    protected $clustered_holds;

    /**
     * Constructeur
     *  @param map_hold_polygon map_hold Emprise courante de la carte
     *  @param Array() ids Liste des identifiants des objets
     *  @param int hold_max Nombre maximum d'emprise présentes sur une couche de la carte

     * @return void
     */
    public function __construct($map_hold, $holds) {
        $this->map_hold = $map_hold;
        $this->holds = $holds;
        $this->clusters = array();
        if(is_object($this->map_hold)) {
        	$this->init();
        	$this->map_hold->get_coords();
        }
    }

    public function init() {
        global $pmb_map_hold_distance;
        
        $this->map_hold->get_coords();

        $query = "select Area(geomfromtext('" . $this->map_hold->get_wkt() . "')) as area";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $this->map_area = $row->area;
        }
        $map_coords = $this->map_hold->get_coords();
        /**
         * La fonction SQL enveloppe renvoi une emprise normalisée telle que: POLYGON((MINX MINY, MAXX MINY, MAXX MAXY, MINX MAXY, MINX MINY))
         * La distance mini se trouve donc entre les points 0 et 3 (le point 4 étant le point de fermeture du polygone)
         */
        if(is_object($map_coords[0]) && is_object($map_coords[3])) {
        	$this->map_distance = (sqrt(pow(($map_coords[0]->get_decimal_long() - $map_coords[3]->get_decimal_long()), 2) + pow(($map_coords[0]->get_decimal_lat() - $map_coords[3]->get_decimal_lat()), 2))) / $pmb_map_hold_distance;
        }
    }

    /**
     * Retourne les emprises correspondantes à la réduction
     *  @param int hold_max Nombre maximum d'emprise présentes sur une couche de la carte

     * @return void
     */
    public function get_reduction() {

        global $pmb_map_hold_ratio_max, $pmb_map_hold_ratio_min;
// 		$high_val = $pmb_map_hold_ratio_max;
// 		$low_val = $pmb_map_hold_ratio_min;


        $this->dicho($pmb_map_hold_ratio_min, $pmb_map_hold_ratio_max);

        foreach ($this->clustered_holds as $key => $hold) {
            $clustered = false;
            if (!count($this->clusters)) {
                $this->clusters[$key] = $hold;
            } else {
                foreach ($this->clusters as $holdC) {
                    if ($this->shouldCluster($holdC, $hold)) {
                        if (is_array($holdC->get_num_object())) {
                            $num_obj = $holdC->get_num_object();
                            if (!in_array($hold->get_num_object(), $num_obj)) {
                                $num_obj[] = $hold->get_num_object();
                                $holdC->set_num_object($num_obj);
                            }
                        } else {
                            if ($holdC->get_num_object() != $hold->get_num_object()) {
                                $holdC->set_num_object(array($holdC->get_num_object(), $hold->get_num_object()));
                            }
                        }
                        $clustered = true;
                        break;
                    }
                }
                if (!$clustered) {
                    $this->clusters[$key] = $hold;
                }
            }
        }
        //
        foreach ($this->clusters as $key => $hold) {
            $wkt = $hold->get_center();
            $this->clusters[$key] = new map_hold_point("point", $hold->get_num_object());
            $this->clusters[$key]->set_wkt($wkt);
        }

        //
        //var_dump(count($displayed_holds));
        uasort($this->displayed_holds, array(static::class, 'cmp_area'));
        return array_merge($this->displayed_holds, $this->clusters);
        //return $this->displayed_holds;
    }

    public function get_occupation_percentage($hold) {
        return ($hold->get_normalized_bbox_area() / $this->map_area) * 100;
    }

    public function get_point_center($hold) {
        $query = "select AsText(Centroid(geomfromtext('" . $hold->get_wkt() . "'))) as center";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $hold_wkt = $row->center;
            return $hold_wkt;
        }
    }

    public function shouldCluster($cluster, $hold) {
        $coords_cluster = explode(" ", substr($cluster->get_center(), strpos($cluster->get_center(), "(") + 1, -1));
        $coords_hold = explode(" ", substr($hold->get_center(), strpos($hold->get_center(), "(") + 1, -1));

        $distance = sqrt(pow(($coords_cluster[0] - $coords_hold[0]), 2) + pow(($coords_cluster[1] - $coords_hold[1]), 2));
        return ($distance <= $this->map_distance);
    }

    public function check_wkt($holds, $wkt_string) {
        foreach ($holds as $num => $hold) {
            if ($hold->get_wkt() == $wkt_string) {
                return $num;
            }
        }
        return false;
    }

    public function simplify_polygon($wkt) {
        $parts = explode(',', $wkt);
        $new = "";
        $start = array_shift($parts);
        $stop = array_pop($parts);
        for ($j = 0; $j <= count($parts); $j++) {
            if ($j % 2)
                $new.= "," . $parts[$j];
        }
        if (!(count($parts) % 2)) {
            $new.=",";
        }
        $wkt = $start . $new . $stop;
        return $wkt;
    }

    public function dicho($min, $max) {
        global $pmb_map_max_holds, $pmb_map_hold_ratio_min;
        $params = explode(",", $pmb_map_max_holds);
        if ($params[1] == "1" && count($this->holds) > $params[0]) {
            $min = $max;
        }
        //print " min ".$min." max ".$max;
        $nb_emprise_min = $this->calc_empr($min);
        $nb_emprise_max = $this->calc_empr($max);
        if ($nb_emprise_max >= $params[0]) {
            //Avec le seuil max, on a déjà trop d'emprise, on ne continue pas.
            return;
        }
        if ($nb_emprise_min <= $params[0]) {
            $this->calc_empr($min);
            //On a un nombre convenable d'emprise avec le seuil min, on s'arrête
            return;
        }

        $mid = (($max - $min) / 2) + $min;
        if ((($max - $min) / 2) <= $pmb_map_hold_ratio_min) {
            $this->calc_empr($mid);
            //On s'arrête car le seuil est inférieur au seuil mini
            return;
        }
        $nb_emprise_seuil = $this->calc_empr($mid);
        if ($nb_emprise_seuil < $params[0]) {
            return $this->dicho($min, $mid);
        } else if ($nb_emprise_seuil == $params[0]) {
            return;
        } else {
            return $this->dicho($mid, $max);
        }
    }

    public function calc_empr($seuil_min) {
        $this->displayed_holds = array();
        $this->clusters = array();
        $this->clustered_holds=$this->holds;
        return array();    
        
        /*
        $this->displayed_holds = array();
        $this->clustered_holds = array();
        $this->clusters = array();

        foreach ($this->holds as $key => $hold) {
            if ($this->get_occupation_percentage($hold) > $seuil_min) {//ces emprises doivent être affichées
                if ($this->get_occupation_percentage($hold) < $pmb_map_hold_ratio_max) {
                    $existant_key = $this->check_wkt($this->displayed_holds, $hold->get_wkt());
                    if ($existant_key != false) {
                        if (is_array($this->displayed_holds[$existant_key]->get_num_object())) {
                            $num_obj = $this->displayed_holds[$existant_key]->get_num_object();
                            $num_obj[] = $hold->get_num_object();
                            $this->displayed_holds[$existant_key]->set_num_object($num_obj);
                        } else {
                            $this->displayed_holds[$existant_key]->set_num_object(array($this->displayed_holds[$existant_key]->get_num_object(), $hold->get_num_object()));
                        }
                    } else {
                        $this->displayed_holds[$key] = $hold;
                        if (count($this->displayed_holds[$key]->get_wkt()) > 1000 && $this->displayed_holds[$key]->get_hold_type() == "POLYGON") {
                            $this->displayed_holds[$key]->set_wkt($this->simplify_polygon($this->displayed_holds[$key]->get_wkt()));
                        }
                    }
                }//Trop grandes non affichées
            } else {//celles ci doivent etre reduite a un point
                $this->clustered_holds[$key] = $hold;
            }
        }
        return count($this->displayed_holds);
         
         */
    }

    public static function cmp_area($a, $b) {
        if ($a->get_normalized_bbox_area() == $b->get_normalized_bbox_area()) {
            return 0;
        }
        return ($a->get_normalized_bbox_area() > $b->get_normalized_bbox_area()) ? -1 : 1;
    }
}
