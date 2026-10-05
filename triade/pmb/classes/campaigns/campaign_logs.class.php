<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_logs.class.php,v 1.5 2023/05/04 09:22:14 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/campaigns/campaign_recipient.class.php');

class campaign_logs {
	
	protected $num_campaign;
	
	protected $logs;
	
	protected static $instance;
	
	public function __construct($num_campaign=0) {
	    $this->num_campaign = (int) $num_campaign;
	}
	
	protected function get_url_details($url, $num_recipient) {
		global $opac_url_base;
		
		$parse_format = parse_format::get_instance('consolidation.inc.php');
	
		// On récupère les get
		parse_str(parse_url($url, PHP_URL_QUERY) ?? "", $get);
	
		$parse_format->environnement = array(
				'num_ligne' => $num_recipient,
				'ligne' => array(
						'url_demandee' => $url,
						'get_log' => serialize($get),
						'post_log' => serialize(array())
				)
		);
		$cleaned_url = str_replace($opac_url_base, 'OPAC : ', $url);
		$url_details = array(
				'link' => $cleaned_url,
				'code' => $parse_format->exec_function('type_page', '', '')
		);
		if ($url_details['code'] != 33) {
			$url_details['label'] = $parse_format->exec_function('type_page_lib', '', '');
		}
		return $url_details;
	}
	
	public function get_logs() {
		if(!isset($this->logs)) {
			$query = "select * from campaigns_logs where campaign_log_num_campaign = ".$this->num_campaign." order by campaign_log_hash";
			$result = pmb_mysql_query($query);
			$this->logs = array();
			while($row = pmb_mysql_fetch_assoc($result)) {
				if(!empty($this->logs[$row['campaign_log_hash']])) {
					$dates = $this->logs[$row['campaign_log_hash']]['dates'];
				} else {
					$dates = array();
				}
				$dates[] = $row['campaign_log_date'];
				$this->logs[$row['campaign_log_hash']] = array(
						'num_recipient' => $row['campaign_log_num_recipient'],
						'dates' => $dates,
						'url' => $this->get_url_details($row['campaign_log_url'], $row['campaign_log_num_recipient'])
				);
			}
		}
		return $this->logs;
	}
	
	public static function add($hash, $url, $id) {
		$id = intval($id);
		$campaign_recipient = new campaign_recipient($id);
		$url = static::clean_url($url);
		$query = 'insert into campaigns_logs 
				(campaign_log_num_campaign, campaign_log_num_recipient, campaign_log_hash, campaign_log_url, campaign_log_date)
				VALUES ('.$campaign_recipient->get_num_campaign().', '.$id.', "'.$hash.'", "'.$url.'", "'.date('Y-m-d H:i:s').'")';
		pmb_mysql_query($query);
	}
	
	/**
	 * @param int $num_campaign
	 * @return campaign_logs
	 */
	public static function get_instance($num_campaign) {
		$num_campaign = intval($num_campaign);
		if(!isset(static::$instance[$num_campaign])) {
			static::$instance[$num_campaign] = new campaign_logs($num_campaign);
		}
		return static::$instance[$num_campaign];
	}
	
	protected static function clean_url($url) {
		$url = preg_replace('/\&?(code|emprlogin|date_conex|database)=[^&]*/', '', $url);
		$url = str_replace('?&', '?', $url);
		return $url;
	}
}