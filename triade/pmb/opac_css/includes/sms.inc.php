<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sms.inc.php,v 1.1 2023/07/06 14:57:01 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;

require_once "$class_path/sms.class.php";

/**
 * @param number $type Type de sms (0 = retard, 1 = resa, 2 = animations)
 * @param number $level Niveau de retard
 * @param string $to_tel Téléphone destinataire
 * @param string $message Contenu du SMS
 * 
 * @return boolean
 */
function send_sms($type = 0, $level = 1, $to_tel = '', $message = '') {
	global $empr_sms_activation;

	$ret = false;
	if (empty($to_tel) || empty($message)) {
	    return $ret;
	}
	
	$tab_sms_activation = explode(',', $empr_sms_activation);
	if (is_array($tab_sms_activation)) {
		switch ($type) {
			case 0:
			    if ($level > 0 && $level < 4 && $tab_sms_activation[$level-1] == 1) {
			        $ret = true;
			    }
				break;
			case 1:
			    if ($tab_sms_activation[3] == 1) {
			        $ret = true;
			    }
				break;
			case 2:
			    if ($tab_sms_activation[4] == 1) {
			        $ret = true;
			    }
			    break;
			default:
				break;
		} 
	}
	
	if ($ret) {
		$ret = false;
		$sms = sms_factory::make();
		if (is_object($sms)) {
			$ret = $sms->send_sms($to_tel, $message);
		}
	}
	
	return $ret;
}