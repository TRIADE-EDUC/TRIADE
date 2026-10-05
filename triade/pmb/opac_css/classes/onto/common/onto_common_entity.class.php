<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_entity.class.php,v 1.2.4.1.2.1 2025/03/07 15:33:04 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

/**
 * class onto_common_class
 * Permet de représenter une instance d'une entité d'une ontologie
 */
class onto_common_entity
{

    protected $uri = "";

    /**
     *
     * @var onto_handler
     */
    protected $handler;

    protected static $prefix;

    protected $data = [];

    protected $isbd = '';

    protected $detail = '';

    protected $type = '';

    protected $infos = [];

    protected $item;

    protected $displayLabelProperty = '';

    protected $uid = "";

    public function __construct(string $uri, onto_handler $handler)
    {
        $this->handler = $handler;
        $this->uri = $uri;
    }

    public function get_data()
    {
        return $this->data;
    }

    protected function fetch()
    {
        $this->data['uri'] = $this->uri;
        $this->data['id'] = onto_common_uri::get_id($this->uri);
        $query = 'select * where { <' . $this->uri . '> ?p ?o }';
        $this->handler->data_query($query);
        if ($this->handler->data_num_rows()) {
            $assertions = $this->handler->data_result();
            foreach ($assertions as $assertion) {
                $this->handle_assertion($assertion);
            }
        }
        $this->item = $this->handler->get_item_instance($this->uri);
    }

    private function handle_assertion($assertion)
    {
        $name = $this->handler->get_pmb_name($assertion->p);
        if (empty($this->data[$name])) {
            $this->data[$name] = [];
        }
        // On traite les cas particulier ici
        switch ($assertion->p) {
            // Le type, on peut en avoir plusieurs, mais le pmb_name est bien suffisant
            case 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type':
                $type = $this->handler->get_pmb_name($assertion->o);
                $typeLabel = $this->handler->get_class_label($assertion->o);
                if ($type != "") {
                    $this->data[$name] = array_merge($this->data[$name], [
                        $type
                    ]);
                    if (empty($this->data['typeLabel'])) {
                        $this->data['typeLabel'] = [];
                    }
                    $this->data['typeLabel'] = array_merge($this->data['typeLabel'], [
                        $typeLabel
                    ]);
                    $this->data['typeLabel'] = array_unique($this->data['typeLabel']);
                    $this->data[$name] = array_unique($this->data[$name]);
                    $this->displayLabelProperty = $this->handler->get_display_label($assertion->o);
                    $this->type = $assertion->o;
                }
                return;
        }
        $element = [];
        switch ($assertion->o_type) {
            case 'uri':
                // on a besoin d'aller chercher le pmb_name de la property...
                $query = 'select ?type where { <' . $assertion->o . '> rdf:type ?type }';
                $this->handler->data_query($query);
                if ($this->handler->data_num_rows()) {
                    $types = $this->handler->data_result();
                    $type_uri = $types[0]->type;
                } else {
                    $this->handler->onto_query($query);
                    if ($this->handler->onto_num_rows()) {
                        $types = $this->handler->onto_result();
                        $type_uri = $types[0]->type;
                    } else {
                        // var_dump($this->handler->get_onto_name(),$this->handler->get_data_label($assertion->o));
                        // var_dump("flop => ".$query);
                    }
                }
                $classname = onto_common_entity::get_entity_class_name($this->handler->get_pmb_name($type_uri), $this->handler->get_onto_name());
                $element[] = new $classname($assertion->o, $this->handler);
                break;
            default:
                $element[$assertion->o_lang][] = $assertion->o;
                break;
        }

        $this->data[$name] = array_merge($this->data[$name], $element);
    }

    public function get_isbd()
    {
        if ($this->isbd !== '') {
            return $this->isbd;
        }
        $query = "select ?isbd where { <" . $this->type . "> pmb_onto:isbd ?isbd }";
        $this->handler->onto_query($query);
        if ($this->handler->onto_num_rows()) {
            $results = $this->handler->onto_result();
            $h2o = H2o::parseString($results[0]->isbd);
        } else {
            $filepath = $this->get_template_filepath('isbd');
            if ($filepath !== false) {
                $h2o = H2o_collection::get_instance($filepath);
            }
        }
        if (is_object($h2o)) {
            $this->isbd = trim($h2o->render($this->get_context_render()));
            return $this->isbd;
        }
        // Pas de template, on affiche le label...
        $this->isbd = trim($this->data[$this->handler->get_pmb_name($this->displayLabelProperty)][0]);
        return $this->isbd;
    }

    public function get_detail()
    {
        if ($this->detail !== '') {
            return $this->detail;
        }
        $filepath = $this->get_template_filepath('detail');
        if ($filepath !== false) {
            $h2o = H2o_collection::get_instance($filepath);
            $this->detail = $h2o->render($this->get_context_render());
        }
        return $this->detail;
    }

    public function get_template_filepath($type = '')
    {
        $filepath = $this->find_template($this->handler->get_onto_name(), $type);
        if (false === $filepath) {
            $filepath = $this->find_template('common', $type);
        }
        return $filepath;
    }

    protected function find_template($where, $type)
    {
        global $include_path;
        $template_path = $include_path . '/templates/ontologies/' . $where . '/';
        if (! empty($type)) {
            $template_path .= "$type/";
        }

        $entity = $this->handler->get_pmb_name($this->type);

        $filepath = $template_path . $entity . '_subst.html';
        if (file_exists($filepath)) {
            return $filepath;
        }
        $filepath = $template_path . $entity . '.html';
        if (file_exists($filepath)) {
            return $filepath;
        }
        $filepath = $template_path . 'entity.html';
        if (file_exists($filepath)) {
            return $filepath;
        }
        return false;
    }

    public function get_displayLabel()
    {
        $displayLabels = $this->data[$this->handler->get_pmb_name($this->displayLabelProperty)];
        $displayLabel = self::get_translation($displayLabels);
        return $displayLabel[0];
    }

    public function __get($name)
    {
        if ($name == 'uri') {
            return $this->uri;
        }
        if (empty($this->data)) {
            $this->fetch();
        }

        if (method_exists($this, 'get_' . $name)) {
            return $this->{'get_' . $name}();
        }
        if (isset($this->data[$name])) {
            return self::get_translation($this->data[$name]);
        }
        return false;
    }

    public static function get_translation($data)
    {
        if (! is_array($data)) {
            return $data;
        }
        // Si c'est pas un tableau associatif, on renvoie directement la valeur
        if (is_array($data) && ! array_diff_key($data, array_keys(array_keys($data)))) {
            return $data;
        }
        global $lang;
        $rdflang = substr($lang, 0, 2);
        // On a des infos dans la langue de l'interface, on met de suite ce qu'il faut...
        if (! empty($data[$rdflang])) {
            return $data[$rdflang];
        }
        // On a rien dans la langue courante, on prend la première !
        foreach ($data as $values) {
            return $values;
        }
    }

    public static function get_entity_class_exists($class_name,$onto_name = '')
    {
        global $class_path;

        if (file_exists($class_path."/onto/".$onto_name."/".$class_name.".class.php") && class_exists($class_name)) {
            return true;
        }
        return false;
    }

    public static function get_entity_class_name($class_name, $onto_name = "")
    {
        if ($onto_name == '') {
            $onto_name = 'common';
        }
        // On commence par le plus précis...
        $classname = "onto_" . $onto_name . "_entity_" . $class_name;
        if (static::get_entity_class_exists($classname, $onto_name)) {
            return $classname;
        }
        // On est encore la, on regarde la classe générique avec l'onto_name
        $classname = "onto_" . $onto_name . "_entity";
        if (static::get_entity_class_exists($classname, $onto_name)) {
            return $classname;
        }
        // En principe, ca ne peut pas arriver
        if ($onto_name == 'common') {
            return false;
        }
        // On regarde dans le common !
        return self::get_entity_class_name($class_name);
    }

    public function get_infos()
    {
        global $msg;
        if (! empty($this->infos)) {
            return $this->infos;
        }
        foreach ($this->data as $property => $values) {
            switch ($property) {
                case "uri":
                case "id":
                case "type":
                case "typeLabel":
                    break;
                case "pmbdatatype":
                    $vals = [];
                    foreach (self::get_translation($values) as $value) {
                        if (! empty(onto_ontopmb_datatype_pmbdatatype_selector::$options[$value->uri])) {
                            $vals[] = $msg[onto_ontopmb_datatype_pmbdatatype_selector::$options[$value->uri]];
                        }
                    }
                    $this->infos[$property] = [
                        'label' => $this->handler->get_label($property),
                        'values' => $vals
                    ];
                    break;
                default:
                    $vals = [];
                    foreach (self::get_translation($values) as $value) {
                        if (is_object($value)) {
                            $vals[] = [
                                'object' => $value
                            ];
                        } else {
                            $vals[] = $value;
                        }
                    }
                    $this->infos[$property] = [
                        'label' => $this->handler->get_label($property),
                        'values' => $vals
                    ];
                    break;
            }
        }
        return $this->infos;
    }

    public function get_item()
    {
        return $this->item;
    }

    protected function get_context_render()
    {
        global $ontology_id;
        return [
                'entity' => $this,
                'get_vars' => $_GET,
                'post_vars' => $_POST,
                'base_url' => "./index.php?lvl=onto_see&ontology_id=".$ontology_id
        ];
    }

    public function get_context_parameters()
    {
        return $this->context_parameters;
    }

    public function set_context_parameters($context_parameters = array())
    {
        $this->context_parameters = $context_parameters;
    }

    public function add_context_parameter($key, $value)
    {
        $this->context_parameters[$key] = $value;
    }

    public function delete_context_parameter($key)
    {
        unset($this->context_parameters[$key]);
    }

    public function get_uid()
    {
        if (! empty($this->uid)) {
            return $this->uid;
        }
        $this->uid = 'onto_' . md5(microtime(true));
        return $this->uid;
    }
}