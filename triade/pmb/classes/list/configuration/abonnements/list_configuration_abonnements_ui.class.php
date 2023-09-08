<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_abonnements_ui.class.php,v 1.4 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/list_configuration_ui.class.php");

class list_configuration_abonnements_ui extends list_configuration_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'admin';
		static::$categ = 'abonnements';
		static::$sub = str_replace(array('list_configuration_abonnements_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
}