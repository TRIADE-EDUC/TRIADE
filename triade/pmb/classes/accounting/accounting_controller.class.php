<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: accounting_controller.class.php,v 1.3 2020/11/05 09:39:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class accounting_controller extends lists_controller {
	
	protected static $model_class_name = 'actes';
	
	protected static $list_ui_class_name = 'list_accounting_ui';
	
	protected static $id_bibli;
	
	protected static $id_exercice;
	
	protected static $id_acte;
	
	public static function set_id_bibli($id_bibli) {
	    static::$id_bibli = intval($id_bibli);
	}
	
	public static function set_id_exercice($id_exercice) {
	    static::$id_exercice = intval($id_exercice);
	}
	
	public static function set_id_acte($id_acte) {
	    static::$id_acte = intval($id_acte);
	}
}