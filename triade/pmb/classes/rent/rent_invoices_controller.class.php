<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoices_controller.class.php,v 1.1 2021/04/10 13:50:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/rent/rent_controller.class.php");

class rent_invoices_controller extends rent_controller {
	
    protected static $model_class_name = 'rent_invoice';
    
	protected static $list_ui_class_name = 'list_rent_invoices_ui';
	
	public static function proceed($id=0) {
	    global $action, $msg;
	    global $rent_accounts_ui_selected_objects, $rent_invoices_ui_selected_objects;
	    
	    switch($action) {
	        case 'create_from_accounts' :
	            $created = false;
	            if(!empty($rent_accounts_ui_selected_objects)) {
	                $accounts = $rent_accounts_ui_selected_objects;
	                $created = rent_invoices::create_from_accounts($accounts);
	            }
	            $list_ui_instance = static::get_list_ui_instance();
	            if(!$created) {
	                $list_ui_instance->set_messages($msg['acquisition_account_cant_invoice_create'].'<br /><br />');
	            }
	            print $list_ui_instance->get_display_list();
	            break;
	        case 'validate' :
	            if(!empty($rent_invoices_ui_selected_objects)) {
	                $invoices = $rent_invoices_ui_selected_objects;
	                rent_invoices::validate($invoices);
	            }
	            $list_ui_instance = static::get_list_ui_instance();
	            print $list_ui_instance->get_display_list();
	            break;
	        default:
	            parent::proceed($id);
	            break;
	    }
	}
	
}