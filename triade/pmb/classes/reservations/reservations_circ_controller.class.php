<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reservations_circ_controller.class.php,v 1.3 2022/09/28 12:37:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/reservations/reservations_controller.class.php");

class reservations_circ_controller extends reservations_controller {
	
	protected static $list_ui_class_name = 'list_reservations_circ_ui';
	
	protected static $ancre;
	
	protected static $msg_a_pointer;
	
	public static function set_ancre($ancre) {
		static::$ancre = $ancre;
	}
	
	public static function set_msg_a_pointer($msg_a_pointer) {
		static::$msg_a_pointer = $msg_a_pointer;
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $f_loc, $pmb_lecteurs_localises, $deflt_resas_location;
		
		if(!isset($f_loc) || $f_loc=="") {
			if ($pmb_lecteurs_localises){
				$f_loc = $deflt_resas_location;
			} else {
				$f_loc = 0;
			}
		}
		$list_ui_instance = new static::$list_ui_class_name(array('id_notice' => 0, 'id_bulletin' => 0, 'id_empr' => 0, 'resa_state' => 'encours', 'f_loc' => $f_loc));
		if(!empty(static::$ancre)) {
			$list_ui_instance->set_ancre(static::$ancre);
		}
		return $list_ui_instance;
	}
	
	public static function proceed($id=0) {
		global $msg;
		global $sub, $action;
		global $dest;
		global $pmb_lecteurs_localises, $deflt_resas_location;
		global $resa_liste_jscript_GESTION_INFO_GESTION, $f_loc;
		
		$id = intval($id);
		switch ($action) {
			case 'edit':
			case 'save':
			case 'delete':
			case 'list_save':
			case 'list_delete':
			case 'dataset_edit':
			case 'dataset_save':
			case 'dataset_apply':
			case 'dataset_delete':
				break;
			default:
				if(!isset($f_loc) || $f_loc=="") {
					if ($pmb_lecteurs_localises){
						$f_loc = $deflt_resas_location;
					} else {
						$f_loc = 0;
					}
				}
				switch($dest) {
					case "TABLEAU":
						break;
					case "TABLEAUHTML":
						break;
					case "TABLEAUCSV":
						break;
					default:
						get_cb_expl("", $msg[661], $msg['resa_pointage_doc'], "./circ.php?categ=listeresa&sub=$sub&action=valide_cb&f_loc=$f_loc");
						
						//un message à afficher
						if(!empty(static::$msg_a_pointer)) {
							print static::$msg_a_pointer;
						}
						//on affiche la liste
						echo $resa_liste_jscript_GESTION_INFO_GESTION;
						break;
				}
		}
		parent::proceed($id);
	}
	
}