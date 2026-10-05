<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_authentication.inc.php,v 1.2 2023/07/13 13:17:02 jparis Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

use Pmb\MFA\Controller\MFAMailController;
use Pmb\MFA\Controller\MFAOtpController;
use Pmb\MFA\Controller\MFASmsController;

global $class_path, $include_path, $sub, $security_mfa_active, $secret_code, $code, $favorite, $otp;

require_once $include_path . "/sms.inc.php";

$response = false;
$id_empr = intval($_SESSION['id_empr_session']);

if($security_mfa_active) {
	if(empty($id_empr)) {
		$id_empr = intval(connexion_empr());
	}
	
	switch($sub){
		case 'send_mail':
			$mfa_mail = (new MFAMailController())->getData('OPAC');
			
			$mail_empr = mail_opac_reader_mfa::get_instance();
			$mail_empr->set_mfa_mail($mfa_mail);
			
			if(!empty($secret_code)) {
				$mail_empr->set_temp_mfa_secret_code(base32_upper_encode($secret_code));
			}

			$mail_empr->set_mail_to_id($id_empr);
			$mail_empr->set_mail_from_id(intval(emprunteur::get_location($id_empr)->id));

			$response = $mail_empr->send_mail();
			break;
			
		case "send_sms":
			$query = "SELECT empr_tel1, empr_sms FROM empr WHERE id_empr = " . $id_empr;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				if(!empty($row->empr_tel1) && $row->empr_sms) {
					 
					$empr_data = new emprunteur_datas($id_empr);
					
					list_patterns_readers_ui::set_emprunteur_datas($empr_data);
					if(!empty($secret_code)) {
						list_patterns_readers_ui::set_temp_mfa_secret_code(base32_upper_encode($secret_code));
					}

					$mfa_sms = (new MFASmsController())->getData('OPAC');
					$text = $mfa_sms->getTranslatedContent();
					$patterns = list_patterns_readers_ui::get_patterns($text);
					$text = str_replace($patterns['search'], $patterns['replace'], $text);

					$sms_user = sms_factory::make();
					if($sms_user->send_sms($row->empr_tel1, $text)) {
						$response = true;
					}
				}
			}
			break;
		
		case "check_totp":
			$mfa_otp = (new MFAOtpController())->getData("OPAC");
	
			$mfa_totp = new mfa_totp();
			$mfa_totp->set_hash_method($mfa_otp->hashMethod);
			$mfa_totp->set_life_time($mfa_otp->lifetime);
			$mfa_totp->set_length_code($mfa_otp->lengthCode);

			$secret_code = emprunteur::get_mfa_secret_code_empr($id_empr);
			$response = $mfa_totp->check_totp(base32_upper_decode($secret_code), $otp, 2);

			break;

		case "initialization":
			if(!empty($_SESSION['id_empr_session'])) {
				$mfa_otp = (new MFAOtpController())->getData("OPAC");
	
				$mfa_totp = new mfa_totp();
				$mfa_totp->set_hash_method($mfa_otp->hashMethod);
				$mfa_totp->set_life_time($mfa_otp->lifetime);
				$mfa_totp->set_length_code($mfa_otp->lengthCode);
	
				$response = $mfa_totp->check_totp(base32_upper_decode($secret_code), $code, 2);
				if($response) {
					$query = "UPDATE empr SET mfa_secret_code = '" . $secret_code . "', mfa_favorite = 'app' WHERE id_empr = " . $id_empr;
					pmb_mysql_query($query);
				}
			}

			break;

		case "reset":
			if(!empty($_SESSION['id_empr_session'])) {
				$query = "UPDATE empr SET mfa_secret_code = NULL, mfa_favorite = NULL WHERE id_empr = " . $id_empr;
				pmb_mysql_query($query);
	
				$response = true;
			}
			break;

		case "save_favorite":
			if(!empty($favorite) && !empty($_SESSION['id_empr_session'])) {
				$query = "UPDATE empr SET mfa_favorite = '" . $favorite . "' WHERE id_empr = " . $id_empr;
				pmb_mysql_query($query);
	
				$response = true;
			}

			break;
	}
}

ajax_http_send_response($response ? "1" : "0");

