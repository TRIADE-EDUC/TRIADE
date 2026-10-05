<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_graph.class.php,v 1.3 2023/10/24 10:46:25 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_graph extends cms_module_common_module
{

    public function __construct($id = 0)
    {
        $this->module_path = str_replace(basename(__FILE__), "", __FILE__);
        parent::__construct($id);
    }

    public function execute_ajax()
    {
        global $entity_id;

        if (!empty($this->datasource['name']) && class_exists($this->datasource['name'])) {
            $datasource = new $this->datasource['name']($this->datasource['id']);
            $data = $datasource->getDataById($entity_id);
        } else {
            $data = [];
        }

        return [
            "content" => encoding_normalize::json_encode($data),
            "content-type" => "application/json",
        ];
    }
}
