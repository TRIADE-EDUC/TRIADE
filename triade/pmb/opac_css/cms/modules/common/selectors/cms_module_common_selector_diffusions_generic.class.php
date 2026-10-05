<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_diffusions_generic.class.php,v 1.1.4.2 2025/04/11 10:10:10 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_diffusions_generic extends cms_module_common_selector
{
    
    public function __construct($id = 0)
    {
        parent::__construct($id);
        if (!is_array($this->parameters)) {
            $this->parameters = array();
        }
        $this->once_sub_selector = true;
    }
    
    public function get_sub_selectors()
    {
        return array(
            "cms_module_common_selector_diffusions",
            "cms_module_common_selector_type_section",
            "cms_module_common_selector_type_article",
            "cms_module_common_selector_type_article_generic",
            "cms_module_common_selector_type_section_generic"
        );
    }
    
    public function get_value()
    {
        $subSelectorClass = $this->parameters['sub_selector'];
        if ($subSelectorClass) {
            $subSelectorId = $this->get_sub_selector_id($subSelectorClass);
            $subSelector = new $subSelectorClass($subSelectorId);
            
            return $subSelector->get_value();
        }
        return [];
    }
}