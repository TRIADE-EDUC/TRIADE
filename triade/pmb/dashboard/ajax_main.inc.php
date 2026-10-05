<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.7 2024/02/28 13:55:55 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $sub, $plugin, $module, $msg;

//En fonction de $categ, il inclut les fichiers correspondants

if($categ === 'plugin') {
	$plugins = plugins::get_instance();
	$file = $plugins->proceed_ajax("dashboard",$plugin,$sub);
	if($file){
		include $file;
	}
}else{
	switch($sub):
		case "save_quick_params":
			if(count($_POST)){
				$class_name="dashboard_module_".$module;
				$save = new $class_name();
				$result = $save->save_quick_params();
				ajax_http_send_response($result);
			}else{
				ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
			}
		break;
		case "save_notification_readed" :
			$query = "select notifications from sessions where SESSID = ".SESSid;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$notifications = pmb_mysql_result($result,0,0);
				if(!$notifications){
					$notifications = array();
				}else{
					$notifications = unserialize($notifications);
				}
				$notifications[$module] = 0;			
				$query = "update sessions set notifications = '".addslashes(serialize($notifications))."' where  SESSID = ".SESSid;
				$result = pmb_mysql_query($query);
				if($result){
					ajax_http_send_response(1);
				}else{
					ajax_http_send_response(0);
				}
			}else{
				ajax_http_send_response(0);
			}
			break;	
		case "save_new_notification" :
			$query = "select notifications from sessions where SESSID = ".SESSid;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$notifications = pmb_mysql_result($result,0,0);
				if(!$notifications){
					$notifications = array();
				}else{
					$notifications = unserialize($notifications);
				}
				$notifications[$module] = 1;
				$query = "update sessions set notifications = '".addslashes(serialize($notifications))."' where  SESSID = ".SESSid;
				$result = pmb_mysql_query($query);
				if($result){
					ajax_http_send_response(1);
				}else{
					ajax_http_send_response(0);
				}
			}else{
				ajax_http_send_response(0);
			}
			break;
		case "get_notifications_state" :
			session_write_close();
			$query = "select notifications from sessions where SESSID = ".SESSid;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$notifications = pmb_mysql_result($result,0,0);
				if(!$notifications){
					$notifications = array();
				}else{
					$notifications = unserialize($notifications);
				}
				if(isset($notifications[$module])){
					ajax_http_send_response($notifications[$module]);
				}else{
					ajax_http_send_response(0);
				}
			}else{
				ajax_http_send_response(0);
			}
			break;		
		default:
			ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
			break;		
	endswitch;	

}