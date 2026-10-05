<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.10.8.1 2025/03/06 08:09:03 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;
global $class_path, $id_bannette, $id_empr, $id;
global $suite, $i_field, $crit_id,$ss_crit_id;

switch($sub) {
    case 'abo':
    	require_once($class_path."/dsi/bannettes_abo_controller.class.php");
    	bannettes_abo_controller::set_id_empr($id_empr);
    	bannettes_abo_controller::proceed((!empty($id_bannette) ? $id_bannette : $id));
		break;
    case 'pro':
    	require_once($class_path."/dsi/bannettes_controller.class.php");
    	bannettes_controller::proceed($id_bannette);
		break;
    case 'facettes':
    	require_once("$class_path/bannette_facettes.class.php");
    	switch($suite){
    		case "add_facette":
    			$facette = new bannette_facettes($id_bannette);
    			ajax_http_send_response ($facette->add_facette($i_field));
    			break;
    		case "ss_crit":
    			$facette = new bannette_facettes($id_bannette);
    			ajax_http_send_response($facette->add_ss_crit($i_field,$crit_id,$ss_crit_id));
    			break;
    			break;
    		default:
    			//tbd
    			break;
    	}
		break;
    default:
        // include("$include_path/messages/help/$lang/dsi.txt");
        break;
}

