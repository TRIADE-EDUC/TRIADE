<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_concept_navigator.class.php,v 1.1 2018-10-08 16:41:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");

class selector_concept_navigator extends selector {
    
    public function __construct(){
        parent::__construct();
        $this->objects_type = 'concepts';
    }
    
    public function proceed() {
        global $msg;
        // tout se passe dans le JS 
        return '<h3>'.$msg['selector_tab_navigate'].'</h3>';
    }
    
}