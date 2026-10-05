<?php 
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_page.class.php,v 1.1 2022/12/05 14:00:37 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class onto_common_page {
    
    /**
     * 
     * @var onto_handler
     */
    protected $handler;
    /**
     * @var onto_common_item
     */
    protected $item;
    
    /**
     * @var onto_common_property
     */
    protected $entity;
    
    /**
    * @var onto_param
    */
    protected $params;
    
    public static function get_instance(onto_common_item $item,onto_handler $handler, onto_param $params)
    {
        //TODO Factory
        return new self($item,$handler, $params);
    }
    
    protected function __construct(onto_common_item $item, onto_handler $handler, onto_param $params)
    {
        $this->item = $item;
        $this->handler = $handler;
        $this->params = $params;
        $this->fetchEntity();
    }
    
    
    protected function fetchEntity()
    {
        $entity_classname = onto_common_entity::get_entity_class_name($this->item->get_onto_class_pmb_name(),$this->handler->get_onto_name());
        $this->entity = new $entity_classname($this->item->get_uri(),$this->handler);
    }
    
       
    public function render()
    {
        $filepath = $this->entity->get_template_filepath(); 
        if($filepath !== false){
            $h2o = H2o_collection::get_instance($filepath);
            $context = [
                'entity' => $this->entity,
                'params' => $this->params,
                'get_vars' => $_GET,
                'post_vars' => $_POST,
                        
            ];
            print $h2o->render($context);
        }
    }
}