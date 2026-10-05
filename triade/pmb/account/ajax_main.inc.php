<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.5 2024/01/31 13:09:33 dgoron Exp $

use Pmb\MFA\Controller\MFAMailController;
use Pmb\MFA\Controller\MFAOtpController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $action, $object_type, $class_path, $PMBuserid;

//En fonction de $categ, il inclut les fichiers correspondants
switch($categ) {
	case 'lists':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'lists');
				break;
		}
		break;
	case 'tabs':
		switch($action) {
			case "list":
				require_once "$class_path/tabs/tabs_controller.class.php";
				tabs_controller::proceed_ajax($object_type, 'tabs');
				break;
		}
		break;
	case 'modules':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'modules');
				break;
		}
		break;
	case 'selectors':
		switch($action) {
			case "list":
				require_once "$class_path/selectors/selectors_controller.class.php";
				selectors_controller::proceed_ajax($object_type, 'selectors');
				break;
		}
		break;
	case 'logs':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'logs');
				break;
		}
		break;
	case 'authentication':
	    global $security_mfa_active, $secret_code, $code;
		$response = false;

		if($security_mfa_active) {
		    switch($action) {
		        case "send_mail":
		            $mfa_mail = (new MFAMailController())->getData('GESTION');
		            $mail_user = mail_user_mfa::get_instance();
		            $mail_user->set_mfa_mail($mfa_mail);
					$mail_user->set_temp_mfa_secret_code(base32_upper_encode($secret_code));
		            $mail_user->set_mail_to_id($PMBuserid);
		            $response = $mail_user->send_mail() ? 1 : 0;
		            break;
				
		            // case "send_sms":
		            // 	$mfa_mail = (new MFASmsController())->getData('GESTION');
		            
		            // 	$phone = user::get_param($PMBuserid, "phone");
		            
		            // 	$sms_user = sms_factory::make();
		            // 	$sms_user->send_sms();
		            
		            // 	break;

				case "check_initialization":
					$mfa_otp = (new MFAOtpController())->getData("GESTION");

					$mfa_totp = new mfa_totp();
					$mfa_totp->set_hash_method($mfa_otp->hashMethod);
					$mfa_totp->set_life_time($mfa_otp->lifetime);
					$mfa_totp->set_length_code($mfa_otp->lengthCode);
					$response = $mfa_totp->check_totp(base32_upper_decode($secret_code), $code, 2) ? 1 : 0;
					break;
		    }
		}

		ajax_http_send_response($response);
		break;
	case 'facettes':
	    global $categ, $object_type, $filters;
	    
	    $is_external = false;
	    $temporary_variable_filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
	    if(!isset($temporary_variable_filters['type'])) $temporary_variable_filters['type'] = 'notices';
	    if('notices_externes' == $temporary_variable_filters['type']) $is_external = true;
	    facettes_gestion_controller::set_object_id(0);
	    facettes_gestion_controller::set_type($temporary_variable_filters['type']);
	    facettes_gestion_controller::set_is_external($is_external);
	    facettes_gestion_controller::proceed_ajax($object_type, 'configuration/'.$categ);
	    break;
	default:
		break;
}
