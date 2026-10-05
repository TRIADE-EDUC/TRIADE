<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaigns_controller.class.php,v 1.2 2022/02/11 10:16:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/campaigns/campaign_logs.class.php");
require_once($class_path."/campaigns/campaign_proxy.class.php");

class campaigns_controller {
	
	public static function proceed($hash, $url, $id) {
		global $opac_url_base;
		
		$id = intval($id);
		if(campaign_proxy::check($hash, $url, $id)) {
			campaign_logs::add($hash, $url, $id);
			campaign_proxy::redirect($url);
			return true;
		}
		campaign_proxy::redirect($opac_url_base);
	}
}