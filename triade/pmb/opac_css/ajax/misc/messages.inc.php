<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: messages.inc.php,v 1.1 2015-04-07 15:20:48 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/encoding_normalize.class.php");

switch($action){
	case 'get_messages':
		if($group){
			if($messages->table_js[$group]){
				$array_message_retourne = array();
				foreach($messages->table_js[$group] as $key => $value){
					$array_message_retourne[] = array("code"=>$key, "message"=>$value, "group"=>$group);
				}
				print encoding_normalize::json_encode($array_message_retourne);
			}else{
				print encoding_normalize::json_encode(array());
			}
		}
		break;
}