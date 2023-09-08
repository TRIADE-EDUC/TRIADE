<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sms.inc.php,v 1.3 2011-12-28 11:14:15 pmbs Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/sms.class.php") ;

if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

/*
 *	$type 	= type de sms (0=retard,1=resa)
 *	$level 	= niveau de retard
 *	$to_tel = telephone destinataire
 *	$message
 */
function send_sms($type=0, $level=1, $to_tel='', $message='') {

	global $empr_sms_activation;

	$ret=false;
	if (!$to_tel || !$message) return $ret;
	$tab_sms_activation=explode(',',$empr_sms_activation);
	if (is_array($tab_sms_activation)) {
		switch ($type) {
			case 0 :
				if ( $level>0 && $level<4 && $tab_sms_activation[$level-1]==1) $ret=true;
				break;
			case 1 :
				if ($tab_sms_activation[3]==1) $ret=true;
				break;
			default :
				break;
		} 
	}
	if ($ret) {
		$ret=false;
		$sms = sms_factory::make();
		if (is_object($sms)) {
			$ret=$sms->send_sms($to_tel, $message);
		}
	}
	return $ret ;
}
