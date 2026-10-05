<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_controller.class.php,v 1.2 2019/04/12 13:46:16 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/vedette/vedette_composee.class.php';
require_once $class_path.'/vedette/vedettes_ui.class.php';

class vedette_controller {
    
    private $params;
        
    public function __construct($params) {
        if (count($params)) {
            $this->params = $params;
        }
    }

	public function proceed() {

    }
    
    public function proceed_ajax() {
        if (isset($this->params['action'])) {
            switch($this->params['action']) {
                case 'get_grammar_form':
                    print $this->get_grammar_form();
                    break;
                default:
                    break;
            }
        }
    }
    
    private function get_grammar_form() {
        $html = '';
        if (!empty($this->params['name'])) {
            $vedette = new vedette_composee(0, $this->params['name']);
            $html = encoding_normalize::utf8_normalize(vedettes_ui::get_grammar_form($vedette, $this->params['property_name'], 0, $this->params['instance_name'], 'http://www.w3.org/2000/01/rdf-schema#Literal'));
        }
        return $html;
    }
}
