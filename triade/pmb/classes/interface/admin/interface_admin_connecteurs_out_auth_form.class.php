<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_connecteurs_out_auth_form.class.php,v 1.1.2.2 2025/03/12 13:49:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_connecteurs_out_auth_form extends interface_admin_form {
	
    protected function get_submit_action_parameters() {
        switch ($this->name) {
            case 'form_outauth_anonymous':
                return $this->get_url_base()."&action=updateanonymous";
            default:
                return parent::get_submit_action_parameters();
        }
        
    }
}