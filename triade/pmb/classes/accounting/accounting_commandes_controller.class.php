<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: accounting_commandes_controller.class.php,v 1.4 2022/06/29 08:38:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/accounting/accounting_controller.class.php");

class accounting_commandes_controller extends accounting_controller {
	
	protected static $list_ui_class_name = 'list_accounting_commandes_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $statut;
		
		$filters = array();
		if(!empty($statut)) {
			$filters['status'] = $statut;
		}
		return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
	
	public static function proceed($id=0) {
        global $action;
        global $id_dev, $chk, $sugchk, $by_mail;
        global $statut; //A conserver car elle doit être utilisée ailleurs 
        
	    switch($action) {
	        case 'list':
	            entites::setSessionBibliId(static::$id_bibli);
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'modif':
	            if(!static::$id_acte && !static::$id_exercice) {
	                $url = "./acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli=".static::$id_bibli."&id_cde=0";
	                show_list_exercices(static::$id_bibli, 'show_cde', $url, 0);
	            } else {
	                show_cde(static::$id_bibli, static::$id_exercice, static::$id_acte);
	            }
	            break;
	        case 'delete' :
	            actes::delete(static::$id_acte);
	            liens_actes::delete(static::$id_acte);
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'update' :
	        case 'valid' :
	            update_cde();
	            $statut = STA_ACT_ENC;
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'from_devis' :
	            if (!static::$id_exercice) {
	                $url = "./acquisition.php?categ=ach&sub=cmde&action=from_devis&id_bibli=".static::$id_bibli."&id_dev=".$id_dev;
	                show_list_exercices(static::$id_bibli, 'show_cde_from_dev', $url, $id_dev);
	            } else {
	                show_cde_from_dev(static::$id_bibli, static::$id_exercice, $id_dev);
	            }
	            break;
	        case 'from_sug' :
	            show_list_biblio_from_sug($chk);
	            break;
	        case 'from_sug_next' :
	            show_cde_from_sug(static::$id_bibli, static::$id_exercice, $sugchk);
	            break;
	        case 'sold':
	            sold_cde();
	            $statut = STA_ACT_REC;
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'arc':
	            arc_cde();
	            $statut = STA_ACT_ARC;
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'duplicate' :
	            duplicate_cde(static::$id_bibli, static::$id_acte);
	            break;
	        case 'list_valid':
	            list_accounting_commandes_ui::run_action_list('valid');
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'list_delete' :
	            list_accounting_commandes_ui::run_action_list('delete');
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'list_arc':
	            list_accounting_commandes_ui::run_action_list('arc');
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'list_sold':
	            list_accounting_commandes_ui::run_action_list('sold');
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        case 'print' :
	            print_cde(static::$id_bibli, static::$id_acte, $by_mail);
	            show_list_cde(static::$id_bibli, static::$id_exercice);
	            break;
	        default:
	        	if(entites::is_selected_biblio('show_list_cde') == false) {
	        		print entites::show_list_biblio('show_list_cde');
	        	} else {
	        		parent::proceed($id);
	        	}
	            break;
	    }
	    
	}
}