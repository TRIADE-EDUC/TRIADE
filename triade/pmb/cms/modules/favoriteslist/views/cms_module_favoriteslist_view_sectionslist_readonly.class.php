<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_favoriteslist_view_sectionslist_readonly.class.php,v 1.2 2022/07/19 09:50:48 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_favoriteslist_view_sectionslist_readonly extends cms_module_common_view_sectionslist{
	
    private $additional_data = [];
    
    public function render($datas){
        $this->additional_data = [
            "save_link" => $this->get_ajax_link([]),
            "display_mode" => $datas["display_mode"] ?? "readonly",
            "item" => $datas["item"] ?? 0,
            "entities" => $datas["entities"] ?? [],
        ];
        //on rappelle le tout...
        $html = parent::render($datas["ids"]);
        
        return $html;
    }
    
    protected function additional_data() {
        return $this->additional_data;
    }
    
    public function execute_ajax() {
        global $favoriteslist_data;
        
        $view_data = json_decode(stripslashes($favoriteslist_data), true);
        
        if (!isset($_SESSION['favorites_lists'])) {
            $_SESSION['favorites_lists'] = [];
        }
        $_SESSION['favorites_lists'][$view_data["item"]] = $view_data["checked_entities"];
        
        $response = array(
            'content' => "",
            'content-type' => "text/html"
        );
        return $response;
    }
}