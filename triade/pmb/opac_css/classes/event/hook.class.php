<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: hook.class.php,v 1.1 2016/09/02 07:34:24 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

interface hook_interface {
	public static function get_subcriptions();	
}

class hook implements hook_interface {
	
	/**
	 * Retourne un tableau de collable par type/sous-type
	 * array('type' => array(
	 * 	'sub_type' => array(array("hook_class","callback")),
	 *  'sub_type2' => array(array("hook_class","callback2")),
	 * ));
	 * @return array()
	 */
	public static function get_subcriptions(){
// 		return array('type' => array(
// 				'sub_type' => array(array("hook_class","callback")),
// 				'sub_type' => array(array("hook_class","callback2")),
// 				'sub_type' => array(array("hook_class","callback3")),
// 				'sub_type' => array(array("hook_class","callback4"))
// 		));
		return array();	
	}
	
	public static function requires(){
		return array();
	}
}