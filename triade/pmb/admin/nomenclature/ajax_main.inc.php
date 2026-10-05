<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2016/01/06 15:05:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/nomenclature/nomenclature_instrument.class.php");

switch($sub):
	case "instrument" :
		switch($action) {
			case "create":
				$return = nomenclature_instrument::create();
				print encoding_normalize::json_encode($return);
				break;
		}
		break;
	case "forms" :
		switch($action) {
			case "get_form":
				switch($form){
					case "nomenclature_instrument_form_tpl":
						print nomenclature_instrument::get_dialog_form();
						break;
				}
				break;
		}
		break;
	default:
		ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
		break;		
endswitch;	
