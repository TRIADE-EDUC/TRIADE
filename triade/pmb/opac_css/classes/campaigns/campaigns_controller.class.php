<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaigns_controller.class.php,v 1.1 2018-03-01 16:49:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/campaigns/campaign_logs.class.php");
require_once($class_path."/campaigns/campaign_proxy.class.php");

class campaigns_controller {
	
	public static function proceed($hash, $url, $id) {
		global $opac_url_base;
		
		$id += 0;
		if(campaign_proxy::check($hash, $url, $id)) {
			campaign_logs::add($hash, $url, $id);
			campaign_proxy::redirect($url);
			return true;
		}
		campaign_proxy::redirect($opac_url_base);
	}
}