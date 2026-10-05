<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: accounting_devis_controller.class.php,v 1.4 2022/06/29 08:38:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/accounting/accounting_controller.class.php");

class accounting_devis_controller extends accounting_controller {
	
	protected static $list_ui_class_name = 'list_accounting_devis_ui';
	
	public static function proceed($id=0) {
        global $action;
        global $chk, $sugchk, $by_mail;
        
        switch($action) {
            case 'list':
                entites::setSessionBibliId(static::$id_bibli);
                show_list_dev(static::$id_bibli);
                break;
            case 'modif':
                show_dev(static::$id_bibli, static::$id_acte);
                break;
            case 'delete' :
                actes::delete(static::$id_acte);
                liens_actes::delete(static::$id_acte);
                show_list_dev(static::$id_bibli);
                break;
            case 'update' :
                update_dev();
                show_list_dev(static::$id_bibli);
                break;
            case 'from_sug' :
                show_list_biblio_from_sug($chk);
                break;
            case 'from_sug_next' :
                show_dev_from_sug(static::$id_bibli, $sugchk);
                break;
            case 'duplicate' :
                duplicate_dev(static::$id_bibli, static::$id_acte);
                break;
            case 'list_delete' :
                list_accounting_devis_ui::run_action_list('delete');
                show_list_dev(static::$id_bibli);
                break;
            case 'list_rec':
                list_accounting_devis_ui::run_action_list('rec');
                show_list_dev(static::$id_bibli);
                break;
            case 'list_arc':
                list_accounting_devis_ui::run_action_list('arc');
                show_list_dev(static::$id_bibli);
                break;
            case 'print' :
                print_dev(static::$id_bibli, static::$id_acte, $by_mail);
                show_list_dev(static::$id_bibli);
                break;
            default:
            	if(entites::is_selected_biblio('show_list_dev') == false) {
            		print entites::show_list_biblio('show_list_dev');
            	} else {
            		parent::proceed($id);
            	}
                break;
        }
	}
}