<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_authperso.class.php,v 1.12.2.1.2.1 2025/04/15 07:31:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_authperso.tpl.php");
require_once($class_path."/entities/entities_authperso_controller.class.php");

class selector_authperso extends selector_authorities {

    public function __construct($user_input=''){
        parent::__construct($user_input);
        $this->objects_type = 'authperso';
    }

    public function proceed() {
        global $mode;
        global $action;
        global $authperso_id;
        global $id_authperso;
        
        $id_authperso=$authperso_id;

        switch($action){
            case 'simple_search':
                $searcher_tabs = $this->get_searcher_tabs_instance();
                $authperso_id = intval($authperso_id);
                if(empty($mode)){
                    $mode = (1000+$authperso_id);
                }
                $searcher_tabs->set_current_mode($mode);
                if(empty($searcher_tabs->get_tab($mode))) {
                    $searcher_tabs->build_default_tab($mode, 'authperso');
                }
                print $this->get_simple_search_form();
                break;
            case 'results_search':
                $searcher_tabs = $this->get_searcher_tabs_instance();
                $authperso_id = intval($authperso_id);
                if(empty($mode)){
                    $mode = (1000+$authperso_id);
                }
                $searcher_tabs->set_current_mode($mode);
                if(empty($searcher_tabs->get_tab($mode))) {
                    $searcher_tabs->build_default_tab($mode, 'authperso');
                }
                ob_start();
                print $this->results_search();
                $results_search = ob_get_contents();
                ob_end_clean();
                print $results_search;
                break;
            default:
                parent::proceed();
        }
    }

    protected function get_form() {
        global $authperso_id;

        $authperso = new authperso($authperso_id);
        return $authperso->get_form_select(0,static::get_base_url());
    }

    protected function get_advanced_form() {
        global $form_display_mode;
        global $authperso_id;

        $entities_controller = $this->get_entities_controller_instance();
        $entities_controller->set_id_authperso($authperso_id);
        $entities_controller->set_url_base(static::get_base_url()."&action=update&form_display_mode=".$form_display_mode);
        $entities_controller->proceed_form();
    }

    protected function get_add_label() {
        global $msg;

        return $msg['authperso_sel_add'];
    }

    protected function save() {
        global $authperso_id;

        $authperso = new authperso($authperso_id);
        $id=$authperso->update_from_form();
        if($authperso->get_cp_error_message()){
            print '<span class="erreur">'.$authperso->get_cp_error_message().'</span>';
        }
        return $id;
    }

    protected function get_authority_instance($authority_id=0, $object_id=0) {
        return new authority($authority_id, $object_id, AUT_TABLE_AUTHPERSO);
    }

    protected function get_display_list() {
        global $authperso_id;
        global $id;
        global $base_url;
        global $type_autorite;
		global $rech_regexp;

        $authperso_id = intval($authperso_id);
        $id = intval($id);
        $type_autorite = intval($type_autorite);
        $base_url = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&type_autorite=".$type_autorite;

        $authperso=new authperso($authperso_id);
		$display_list = $authperso->get_list_selector($id,$this->get_link_pagination(),$this->get_nb_per_page_list());
        return $display_list;
    }

    protected function get_link_pagination() {
        global $rech_regexp;
        global $type_autorite;

        $type_autorite = intval($type_autorite);
        $link = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&type_autorite=".$type_autorite;
        return $link;
    }

    public function get_title() {
        global $msg;
        global $authperso_id;
        
        $name = authpersos::get_name($authperso_id);
        return sprintf($msg["authperso_sel_title"], $name);
    }

    protected function get_entities_controller_instance($id=0) {
        global $authperso_id;
        $entities_authperso_controller = new entities_authperso_controller($id);
        $entities_authperso_controller->set_id_authperso($authperso_id);
        return $entities_authperso_controller;
    }

    public static function get_params_url() {
        global $p3, $p4, $p5, $p6, $authperso_id, $perso_id;

        $params_url = parent::get_params_url();
        $params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "").($authperso_id ? "&authperso_id=".$authperso_id : "").($perso_id ? "&perso_id=".$perso_id : "");
        return $params_url;
    }
}
?>