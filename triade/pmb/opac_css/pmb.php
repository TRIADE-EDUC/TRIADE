<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb.php,v 1.2 2019-05-13 07:53:01 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if(!empty($hash) && !empty($url) && !empty($id)) {
	require_once($class_path."/campaigns/campaigns_controller.class.php");
	campaigns_controller::proceed($hash, $url, $id);
} elseif(!empty($hash) && !empty($url)) {
	if(!isset($from)) $from = '';
	if($hash == md5($url.$from)) {
		//Enregistrement du log
		global $pmb_logs_activate;
		if($pmb_logs_activate){
			global $log;
			$log->add_log('num_session',session_id());
			$log->save();
		}
		header('Location: '.$url);
	}
}
