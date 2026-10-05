<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_entities.class.php,v 1.4.16.1 2025/03/04 15:50:02 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once $class_path . "/openurl/openurl.class.php";
require_once $class_path . "/openurl/transport/protocole/openurl_transport_http.class.php";

class openurl_entity extends openurl_root
{

    public $descriptors = array();

    // descripteurs de l'entité
    public function addDescriptor($desc)
    {
        $desc->setEntityType($this->type);
        $this->descriptors[] = $desc;
    }

    public function serialize_infos($debug = false)
    {
        $serialized_entity = "";
        foreach ($this->descriptors as $descriptor) {
            $descriptor_serialized = $descriptor->serialize_infos($debug);
            if ($descriptor_serialized != "") {
                if ($serialized_entity != "")
                    $serialized_entity .= "&";
                $serialized_entity .= $descriptor_serialized;
            }
        }
        return $serialized_entity;
    }
}

/*
 * La ressource référencée
 */
class openurl_entity_referent extends openurl_entity
{

    public function __construct()
    {
        $this->type = "referent";
    }

    public function unserialize($infos, $mode)
    {
        global $openurl_map;
        global $class_path;

        $elements = array();
        foreach ($infos as $key => $values) {
            switch ($key) {
                case "rft_id":
                    // descripteurs identifier
                    foreach ($values as $value) {
                        if (preg_match('/([^:]+:[^:]*):.*/', $value, $match)) {
                            $iclass = $openurl_map[strtolower('info:ofi/nam:' . $match[1])]['class'];
                            $ident = new $iclass();
                            $ident->setEntityType($this->type);
                            $ident->unserialize($value);
                            $this->descriptors[] = $ident;
                        }
                    }
                    break;
                case "rft_val_fmt":
                    // By-Value
                    $class = $openurl_map[strtolower($values[0])]['class'];
                    break;
                case "rft_ref_fmt":
                    // By-Reference
                    $content = openurl_transport_http::get($infos["rft_ref"][0]);
                    switch (self::$serialize) {
                        case "kev_mtx":
                        default:
                            $content = openurl_serialize_kev_mtx::unserialize($content);
                            foreach ($content as $key => $value) {
                                $content[$key] = $value[0];
                            }
                            break;
                    }
                    $byref = new $openurl_map[strtolower($values[0])]['class']();
                    $byref->setEntityType($this->type);
                    $byref->unserialize($content, true);
                    $this->descriptors[] = $byref;
                    break;
                case "rft_dat":
                    // Private Data
                    break;
                default:
                    // on colle les éléments du by-value dans un tableau
                    if (substr($key, - 3) != "ref") {
                        $elements[$key] = $values[0];
                    }
                    break;
            }
        }
        if ($class) {
            $byval = new $class();
            $byval->setEntityType($this->type);
            $byval->unserialize($elements);
            $this->descriptors[] = $byval;
        }
    }
}

/*
 * La ressource qui contient la référence
 */
class openurl_entity_referring_entity extends openurl_entity
{

    public function __construct()
    {
        $this->type = "referring_entity";
    }

    public function unserialize($infos, $mode)
    {
        global $openurl_map;
        global $class_path;

        $elements = array();
        foreach ($infos as $key => $values) {
            switch ($key) {
                case "rfe_id":
                    // descripteurs identifier
                    foreach ($values as $value) {
                        if (preg_match('/([^:]+:[^:]*):.*/', $value, $match)) {
                            $iclass = $openurl_map[strtolower('info:ofi/nam:' . $match[1])]['class'];
                            $ident = new $iclass();
                            $ident->setEntityType($this->type);
                            $ident->unserialize($value);
                            $this->descriptors[] = $ident;
                        }
                    }
                    break;
                case "rfe_val_fmt":
                    // By-Value
                    $class = $openurl_map[strtolower($values[0])]['class'];
                    break;
                case "rfe_ref_fmt":
                    // By-Reference
                    break;
                case "rfe_dat":
                    // Private Data
                    break;
                default:
                    // on colle les éléments du by-value dans un tableau
                    $elements[$key] = $values[0];
                    break;
            }
        }
        if ($class) {
            $byval = new $class();
            $byval->setEntityType($this->type);
            $byval->unserialize($elements);
            $this->descriptors[] = $byval;
        }
    }
}

/*
 * La personne demandant le service
 */
class openurl_entity_requester extends openurl_entity
{

    public function __construct()
    {
        $this->type = "requester";
    }

    public function unserialize($infos, $mode)
    {
        foreach ($infos as $key => $values) {
            switch ($key) {
                case "req_id":
                    $class = "openurl_descriptor_identifier_" . $mode . "_" . $this->type;
                    $desc = new $class();
                    $desc->setEntityType($this->type);
                    $desc->unserialize($values);
                    $this->descriptors[] = $desc;
                    break;
            }
        }
    }
}

/*
 * Le type de service demandé
 */
class openurl_entity_service_type extends openurl_entity
{

    public function __construct()
    {
        $this->type = "service_type";
    }

    public function unserialize($infos, $mode)
    {
        global $openurl_map;
        global $class_path;

        $elements = array();
        foreach ($infos as $key => $values) {
            switch ($key) {
                case "svc_val_fmt":
                    // By-Value
                    $class = $openurl_map[strtolower($values)]['class'];
                    break;
                case "svc_ref_fmt":
                    // By-Reference
                    break;
                case "svc_dat":
                    // Private Data
                    break;
                default:
                    // on colle les éléments du by-value dans un tableau
                    $elements[$key] = $values;
                    break;
            }
        }
        if ($class) {
            $byval = new $class();
            $byval->setEntityType($this->type);
            $byval->unserialize($elements);
            $this->descriptors[] = $byval;
        }
    }
}

/*
 * Quel résolveur?
 */
class openurl_entity_resolver extends openurl_entity
{

    public function __construct()
    {
        $this->type = "resolver";
    }

    public function unserialize($infos, $mode)
    {
        foreach ($infos as $key => $values) {
            switch ($key) {
                case "res_id":
                    $class = "openurl_descriptor_identifier_" . $mode . "_" . $this->type;
                    $desc = new $class();
                    $desc->setEntityType($this->type);
                    $desc->unserialize($values);
                    $this->descriptors[] = $desc;
                    break;
            }
        }
    }
}

/*
 * Provenance de la requete
 */
class openurl_entity_referrer extends openurl_entity
{

    public function __construct()
    {
        $this->type = "referrer";
    }

    public function unserialize($infos, $mode)
    {
        foreach ($infos as $key => $values) {
            switch ($key) {
                case "rfr_id":
                    $class = "openurl_descriptor_identifier_" . $mode . "_" . $this->type;
                    $desc = new $class();
                    $desc->setEntityType($this->type);
                    $desc->unserialize($values);
                    $this->descriptors[] = $desc;
                    break;
            }
        }
    }
}