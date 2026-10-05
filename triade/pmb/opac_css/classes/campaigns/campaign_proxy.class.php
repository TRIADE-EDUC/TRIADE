<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_proxy.class.php,v 1.6 2023/03/02 13:23:54 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/campaigns/campaign_recipient.class.php");

class campaign_proxy {
	
	public function __construct() {
	}
	
	protected static function get_builded_url($recipient_hash, $url, $id) {
		global $opac_url_base;
	
		return $opac_url_base."pmb.php?hash=".urlencode(static::gen_hash($recipient_hash, $url))."&url=".urlencode($url)."&id=".$id;
	}
	
	public static function replace_links($html, $recipient_hash, $id) {
		$matches = array();
		$links = preg_match_all('/<a [^>]*href=[\'"]([^\'"]+)[\'"]/i', $html, $matches);
		if(count($matches[1])) {
			for($j=0; $j<count($matches[1]); $j++) {
				if(substr($matches[1][$j], 0, 1) != '#') {
					$html = str_replace("'".$matches[1][$j]."'", "'".static::get_builded_url($recipient_hash, $matches[1][$j], $id)."'", $html);
					$html = str_replace('"'.$matches[1][$j].'"', '"'.static::get_builded_url($recipient_hash, $matches[1][$j], $id).'"', $html);
				}
			}
		}
		return $html;
	}
	
	public static function proxyfication($campaign_recipient, $corps) {
		$proxified_content = '';
		
		$proxified_content = static::replace_links($corps, $campaign_recipient->get_hash(), $campaign_recipient->get_id());
		$proxified_content .= static::get_white_pixel($campaign_recipient);
		return $proxified_content;
	}
	
	protected static function get_white_pixel($campaign_recipient) {
		global $opac_url_base;
		
		$image_url = $opac_url_base."pixel.php";
		return "<img src='".static::get_builded_url($campaign_recipient->get_hash(), $image_url, $campaign_recipient->get_id())."' alt='' />";
	}
	
	protected static function gen_hash($recipient_hash, $url) {
	    global $opac_empr_password_salt;
	    if ('' == $opac_empr_password_salt) {
	        password::gen_salt_base();
	    }
	    return md5("{$opac_empr_password_salt}_{$recipient_hash}_{$url}");
	}
	
	public static function check($hash, $url, $id) {
		$campaign_recipient = new campaign_recipient($id);
	
		if($hash == static::gen_hash($campaign_recipient->get_hash(), $url)) {
			return true;
		}
		return false;
	}
	
	public static function redirect($url) {
		header('Location: '.html_entity_decode($url));
	}
}