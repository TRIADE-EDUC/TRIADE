<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts_edition_controller.class.php,v 1.2 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/lists_controller.class.php");
require_once($class_path."/list/transferts/list_transferts_edition_ui.class.php");

class transferts_edition_controller extends lists_controller {
	
	protected static $model_class_name = 'transferts_edition';
	
	protected static $list_ui_class_name = 'list_transferts_edition_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $sub;
		
		switch($sub) {
			case "validation":
				return new static::$list_ui_class_name(array('etat_transfert' => 0, 'etat_demande' => 0), array(), array('by' => 'cote'));
			case "envoi":
				return new static::$list_ui_class_name(array('etat_transfert' => 0, 'etat_demande' => 1), array(), array('by' => 'cote'));
			case "retours":
				return new static::$list_ui_class_name(array('etat_transfert' => 0, 'etat_demande' => 3, 'type_transfert' => 1), array(), array('by' => 'cote'));
			case "reception":
				return new static::$list_ui_class_name(array('etat_transfert' => 0, 'etat_demande' => 2), array(), array('by' => 'cote'));
		}
	}
}