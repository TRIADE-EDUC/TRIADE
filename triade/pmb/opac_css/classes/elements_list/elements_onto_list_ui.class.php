<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_onto_list_ui.class.php,v 1.1 2023/02/07 15:31:40 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'éléments d'une ontologie
 *
 * @author dgoron
 *        
 */
class elements_onto_list_ui extends elements_list_ui
{

    protected $parent_path = array();
    /**
     * 
     * @var onto_ontology
     */
    protected $ontology;
    /**
     *
     * @var ontology
     */
    protected $onto;

    public function set_ontology(onto_ontology $ontology)
    {
        $this->ontology = $ontology;
    }

    protected function generate_elements_list()
    {
        $elements_list = '';
        $recherche_ajax_mode = 0;
        $nb = 0;
        if (is_array($this->contents)) {
            foreach ($this->contents as $element_id) {
                if (! in_array($element_id, $this->parent_path)) {
                    $this->parent_path[] = $element_id;
                    if (! $recherche_ajax_mode && ($nb ++ > 5)) {
                        $recherche_ajax_mode = 1;
                    }
                    $elements_list .= $this->generate_element($element_id, $recherche_ajax_mode);
                    array_pop($this->parent_path);
                }
            }
        }
        return $elements_list;
    }
    
    protected function get_onto()
    {
        if(!empty($this->onto)){
            return $this->onto;
        }
        $this->onto = ontologies::get_ontology_by_pmbname($this->ontology->name);
        return $this->onto;
    }

    protected function generate_element($element_id, $recherche_ajax_mode = 0)
    {
        $element_uri = onto_common_uri::get_uri($element_id);
        $query = 'select ?type where { <'.$element_uri.'> rdf:type ?type }';
        $results = $this->get_onto()->exec_data_query($query);
        if(!empty($results)){
            $type = $results[0]->type;
        }
        $classname = onto_common_entity::get_entity_class_name($this->onto->get_handler()->get_pmb_name($type),$this->ontology->name);
       
        $entity = new $classname($element_uri,$this->onto->get_handler());
        $this->add_context_parameter('element_id', $element_id);
        $entity->set_context_parameters($this->get_context_parameters());
        $template_path = $entity->get_template_filepath('list');
        $context = array(
            'list_element' => $entity,
            'base_url' => './index.php?lvl=onto_see&ontology_id='.$this->onto->get_id()
        );
        return static::render($template_path, $context, $this->get_context_parameters());
    }
}