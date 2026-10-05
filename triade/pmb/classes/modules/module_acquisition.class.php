<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_acquisition.class.php,v 1.2.8.1 2024/07/19 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/modules/module.class.php");

class module_acquisition extends module{
	
    public function proceed_rent(){
        global $sub;
        
        switch($sub) {
            case 'requests':
                rent_requests_controller::proceed($this->object_id);
                break;
            case 'invoices':
                if (SESSrights & ACQUISITION_ACCOUNT_INVOICE_AUTH) {
                    rent_invoices_controller::proceed($this->object_id);
                }
                break;
            case 'accounts':
            default:
                if (SESSrights & ACQUISITION_ACCOUNT_INVOICE_AUTH) {
                    rent_accounts_controller::proceed($this->object_id);
                }
                break;
        }
    }
}