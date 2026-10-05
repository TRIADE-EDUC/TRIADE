<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_services.class.php,v 1.2 2021/07/21 13:40:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/analytics_services/analytics_service.class.php");
require_once($class_path."/encoding_normalize.class.php");

class analytics_services{
	
	protected static $instances = array();
	
	public function __construct(){
	}
		
	public static function get_display_services() {
		$display = "";
		$query = "select id_analytics_service from analytics_services where analytics_service_active = 1";
		$result = pmb_mysql_query($query);
		if($result) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$analytics_service = new analytics_service($row->id_analytics_service);
				$display .= $analytics_service->get_display_service();
			}
		}
		return $display;
	}
	
	public static function is_active($name) {
		global $class_path;

		$services = self::get_services();
		foreach ($services as $service) {
			if($service == $name) {
				return static::get_active_from_name($name);
			}
		}
		return 0;
	}
	
	public static function get_services() {
		global $class_path;
	
		$services = array();
		if(file_exists($class_path.'/analytics_services/services')) {
			$dh = opendir($class_path.'/analytics_services/services');
			while(($service = readdir($dh)) !== false){
				if($service != "." && $service != ".." && $service != "CVS"){
					$services[] = $service;
				}
			}
		}
		return $services;
	}
	
	public static function get_id_from_name($name) {
		$query = "SELECT id_analytics_service FROM analytics_services WHERE analytics_service_name = '".addslashes($name)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'id_analytics_service');
		}
		return 0;
	}
	
	public static function get_active_from_name($name) {
		$query = "SELECT services_analytic_active FROM analytics_services WHERE analytics_service_name = '".addslashes($name)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'services_analytic_active');
		}
		return 0;
	}
	
	public static function get_json_templates() {
		global $class_path, $analytics_service_name;
		
		$templates = array();
		$services = self::get_services();
		foreach ($services as $service) {
			if($service == $analytics_service_name) {
				$class_name = "analytics_service_".$analytics_service_name;
				require_once $class_path.'/analytics_services/services/'.$analytics_service_name.'/'.$class_name.'.class.php';
				$templates['template'] = trim($class_name::get_default_template());
				$templates['consent_template'] = trim($class_name::get_default_consent_template());
			}
		}
		return encoding_normalize::json_encode($templates);
	}
}