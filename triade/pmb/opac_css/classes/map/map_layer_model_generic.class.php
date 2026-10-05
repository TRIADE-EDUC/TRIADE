<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_layer_model_generic.class.php,v 1.2 2019/09/20 14:04:21 btafforeau Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
require_once ($class_path . "/map/map_layer_model.class.php");

class map_layer_model_generic extends map_layer_model
{

    /**
     * Nom du layer affiché sur la carte
     *
     * @var string
     * @access protected
     */
    protected $name;
    
    /**
     * Valeur hexadécimale de l'emprise
     *
     * @var string
     * @access protected
     */
    protected $color = "";

    /**
     * Requête récupérant les informations des différentes emprises
     *
     * @var string
     * @access protected
     */
    protected $query;

    /**
     * Type de données du layer (authority, record..)
     *
     * @var string
     * @access protected
     */
    protected $type_record;

    /**
     * Constructeur
     *
     * @return void
     * @access public
     */
    public function __construct($ids = [], $type = '')
    {
        $this->ids = $ids;
        $this->type = $type;
        if ($this->ids) {
            $this->fetch_datas();
        }
    }

    public function set_query($query)
    {
        $this->query = $query;
    }
    
    public function get_query()
    {
        return $this->query;
    }

    /**
     * Ajout de la couleur du layer
     *
     * @param string $color
     *            Couleur du layer
     * @return void
     */
    public function set_color($color)
    {
        $this->color = $color;
    }

    public function set_layer_model_name($name)
    {
        $this->name = $name;
    }

    public function get_layer_model_type()
    {
        return "generic";
    }

    public function get_layer_model_name()
    {
        return $this->name;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function set_type_record($type)
    {
        $this->type_record = $type;
    }

    public function get_type_record()
    {
        return $this->type_record;
    }

    public function get_holds()
    {
        $linked_ids = array();
        $result = pmb_mysql_query($this->query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                if (empty($linked_ids[$row->map_emprise_id])) {
                    $linked_ids[$row->map_emprise_id]['map_emprise_obj_num'] = $row->map_emprise_obj_num;
                    $linked_ids[$row->map_emprise_id]['map'] = $row->map;
                    $linked_ids[$row->map_emprise_id]['bbox_area'] = $row->bbox_area;
                    $linked_ids[$row->map_emprise_id]['center'] = $row->center;
                }
                $linked_ids[$row->map_emprise_id]['ids'][] = $row->num_object;
            }
            foreach ($linked_ids as $id_emprise => $data) {
                $geometric = strtolower(substr($data['map'], 0, strpos($data['map'], "(")));
                $hold_class = "map_hold_" . $geometric;
                if (class_exists($hold_class)) {
                    $emprise = new $hold_class("concept", $data['map_emprise_obj_num'], $data['map']);
                    $emprise->set_normalized_bbox_area($data['bbox_area']);
                    $emprise->set_center($data['center']);
                    $emprise->set_record($data['ids']);
                    $emprise->set_color($this->color);
                    $this->holds[$id_emprise] = $emprise;
                }
            }
        }
        return $this->holds;
    }
}
