<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loans_edition_controller.class.php,v 1.3 2023/09/20 08:08:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/loans/loans_controller.class.php");

class loans_edition_controller extends loans_controller {
	
	protected static $list_ui_class_name = 'list_loans_edition_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $sub;
		
		switch($sub) {
		    case 'retard' :
		        $overwrite_filters = array('pret_retour_end' => date('Y-m-d'), 'short_loan_flag' => 0, 'pret_date_end' => '', 'pret_retour_start' => '');
		        $overwrite_applied_sort = array('by' => 'empr');
		        break;
		    case 'retard_par_date' :
		        $overwrite_filters = array('pret_retour_end' => date('Y-m-d'), 'short_loan_flag' => 0, 'pret_date_end' => '', 'pret_retour_start' => '');
		        $overwrite_applied_sort = array('by' => 'pret_retour_empr');
		        break;
		    case 'short_loans':
		        $overwrite_filters = array('pret_retour_end' => '', 'short_loan_flag' => 1, 'pret_date_end' => '', 'pret_retour_start' => '');
		        $overwrite_applied_sort = array('by' => 'pret_retour');
		        break;
		    case 'unreturned_short_loans' :
		        $overwrite_filters = array('pret_retour_end' => '', 'short_loan_flag' => 1, 'pret_date_end' => date('Y-m-d'), 'pret_retour_start' => date('Y-m-d'));
		        $overwrite_applied_sort = array('by' => 'pret_retour');
		        break;
		    case 'overdue_short_loans' :
		        $overwrite_filters = array('pret_retour_end' => date('Y-m-d'), 'short_loan_flag' => 1, 'pret_date_end' => '', 'pret_retour_start' => '');
		        $overwrite_applied_sort = array('by' => 'pret_retour');
		        break;
		    case 'archives' :
		        static::$list_ui_class_name = 'list_loans_archives_edition_ui';
		        break;
		    default:
		        $overwrite_filters = array('pret_retour_end' => '', 'short_loan_flag' => 0, 'pret_date_end' => '', 'pret_retour_start' => '');
		        $overwrite_applied_sort = array('by' => 'pret_retour');
		        break;
		}
		$filters = array_merge($filters, $overwrite_filters);
		$applied_sort = array_merge($applied_sort, $overwrite_applied_sort);
		return parent::get_list_ui_instance($filters, $pager, $applied_sort);
	}
	
	public static function proceed($id=0) {
		global $dest, $action;
		
		parent::proceed($id);
		switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				break;
			default:
				//impression/emails (on est dans le cas retards/retards par date)
			    if ($action == "print_all" || $action == "print") {
			        if ($action == "print_all") {
			            $pager = array('all_on_page' => true);
			        } else {
			            $pager = array();
			        }
					$list_ui_instance = static::get_list_ui_instance(array(), $pager);
					$list_ui_instance->print_relances();
				}
				break;
		}
	}
}