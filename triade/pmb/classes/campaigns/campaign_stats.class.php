<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_stats.class.php,v 1.7 2022/05/05 06:44:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/campaigns/campaign_logs.class.php');
require_once($class_path.'/campaigns/campaign_recipients.class.php');
require_once($class_path.'/encoding_normalize.class.php');

class campaign_stats {
	
	protected $num_campaign;
	
	protected $data;
	
	protected $date;
	
	protected $opening_logs;
	
	protected $clicks_logs;
	
	protected $unsubscribes_logs;
	
	public function __construct($num_campaign=0) {
	    $this->num_campaign = (int) $num_campaign;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$query = "select * from campaigns_stats where campaign_stat_num_campaign = ".$this->num_campaign;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_assoc($result);
			$this->data = encoding_normalize::json_decode($row['campaign_stat_data'], true);
			$this->date = $row['campaign_stat_date'];
		}
	}
	
	protected function init_data() {
		$this->data['opening_rate'] = array();
		$this->data['clicks_rate'] = array();
		$this->data['clicks_by_links'] = array();
		$this->data['clicks_by_links_type'] = array();
	}
	
	public function get_opening_logs() {
		if(!isset($this->opening_logs)) {
			$this->opening_logs = array();
			$campaign_logs = campaign_logs::get_instance($this->num_campaign);
			$logs = $campaign_logs->get_logs();
			foreach ($logs as $log) {
				if(empty($this->opening_logs[$log['num_recipient']])) {
					$this->opening_logs[$log['num_recipient']] = $log;
				}
				if($log['url']['code'] == 33) {
					$this->opening_logs[$log['num_recipient']] = $log;
				}
			}
		}
		return $this->opening_logs;
	}
	
	public function get_clicks_logs() {
		if(!isset($this->clicks_logs)) {
			$this->clicks_logs = array();
			$campaign_logs = campaign_logs::get_instance($this->num_campaign);
			$logs = $campaign_logs->get_logs();
			foreach ($logs as $log) {
				if($log['url']['code'] != 33) {
					$this->clicks_logs[] = $log;
				}
			}
		}
		return $this->clicks_logs;
	}
	
	public function get_opening($unique=false) {
		$opening = 0;
		foreach ($this->get_opening_logs() as $log) {
			if($unique) {
				$opening++;
			} else {
				$opening += count($log['url']['dates']);
			}
		}
		return $opening;
	}
	
	public function get_opening_rate() {
		$opening_rate = '0.00';
		$recipents_number = $this->get_recipients_number();
		if($recipents_number) {
			$opening_rate = $this->get_opening(true)/$recipents_number*100;
		}
		return number_format($opening_rate, 2)."%";
	}
	
	public function get_clicks($unique=false) {
		$clicks = 0;
		$recipients = array();
		foreach ($this->get_clicks_logs() as $log) {
			if(!in_array($log['num_recipient'], $recipients)) {
				$recipients[] = $log['num_recipient'];
				if($unique) {
					$clicks++;
				} else {
					$clicks += count($log['dates']);
				}
			}
		}
		return $clicks;
	}
	
	public function get_clicks_rate() {
		$clicks_rate = '0.00';
		$recipents_number = $this->get_recipients_number();
		if($recipents_number) {
			$clicks_rate = $this->get_clicks(true)/$recipents_number*100;
		}
		return number_format($clicks_rate, 2)."%";
	}
	
	public function get_unsubscribes() {
		$campaign_logs = campaign_logs::get_instance($this->num_campaign);
		$logs = $campaign_logs->get_logs();
		$unsubscribes = 0;
		foreach ($logs as $log) {
// 			if($log['url']['code'] == !!code!!) {
				$unsubscribes++;
// 			}
		}
		return $unsubscribes;
	}
	
	protected function get_hour($timestamp) {
		return date('H', $timestamp);
	}
	
	protected function get_hours() {
		$hours = array();
		for($i = 0; $i < 24; $i++) {
			$hours[] = str_pad($i, 2, 0, STR_PAD_LEFT);
		}
		return $hours;
	}
	
	protected function get_day($timestamp) {
		return date('Y-m-d', $timestamp);
	}
	
	protected function get_days($start, $end) {
		$days = array();
		$day = $start;
		while($day <= $end) {
			$days[] = $day;
			$day = $this->get_day(strtotime($day. ' + 1 day'));
		}
		return $days;
	}
	
	public function get_opening_by_days($start, $end, $unique=false) {
		$details = array();
		$days = $this->get_days($start, $end);
		foreach ($days as $day) {
			$details[$day] = 0;
		}
		foreach ($this->get_opening_logs() as $log) {
			foreach($log['dates'] as $date) {
				$day = $this->get_day(strtotime($date));
				if($day >= $start && $day <= $end) {
					$details[$day]++;
				}
			}
		}
		return $details;
	}
	
	public function get_clicks_by_days($start, $end, $unique=false) {
		$details = array();
		$days = $this->get_days($start, $end);
		foreach ($days as $day) {
			$details[$day] = 0;
		}
		foreach ($this->get_clicks_logs() as $log) {
			foreach($log['dates'] as $date) {
				$day = $this->get_day(strtotime($date));
				if($day >= $start && $day <= $end) {
					$details[$day]++;
				}
			}
		}
		return $details;
	}
	
	public function get_opening_by_hours($start, $end, $unique=false) {
		$details = array();
		$days = $this->get_days($start, $end);
		foreach ($days as $day) {
			$hours = $this->get_hours($day);
			foreach ($hours as $hour) {
				$details[$day][$hour] = 0;
			}
		}
		foreach ($this->get_opening_logs() as $log) {
			foreach($log['dates'] as $date) {
				$day = $this->get_day(strtotime($date));
				$hour = $this->get_hour(strtotime($date));
// 				if($hour >= $start && $hour <= $end) {
					$details[$day][$hour]++;
// 				}
			}
		}
		return $details;
	}
	
	public function get_clicks_by_hours($start, $end, $unique=false) {
		$details = array();
		$days = $this->get_days($start, $end);
		foreach ($days as $day) {
			$hours = $this->get_hours($day);
			foreach ($hours as $hour) {
				$details[$day][$hour] = 0;
			}
		}
		foreach ($this->get_clicks_logs() as $log) {
			foreach($log['dates'] as $date) {
				$day = $this->get_day(strtotime($date));
				$hour = $this->get_hour(strtotime($date));
// 				if($hour >= $start && $hour <= $end) {
					$details[$day][$hour]++;
// 				}
			}
		}
		return $details;
	}
	
	public function get_clicks_by_links($unique=false) {
		$clicks = array();
		foreach ($this->get_clicks_logs() as $log) {
			if(!isset($clicks[$log['url']['link']])) {
				$clicks[$log['url']['link']] = 0;
			}
			if($unique) {
				$clicks[$log['url']['link']]++;
			} else {
				$clicks[$log['url']['link']] += count($log['dates']);
			}
		}
		return $clicks;
	}
	
	public function get_clicks_by_links_type($unique=false) {
		$clicks = array();
		foreach ($this->get_clicks_logs() as $log) {
			if(!isset($clicks[$log['url']['label']])) {
				$clicks[$log['url']['label']] = 0;
			}
			if($unique) {
				$clicks[$log['url']['label']]++;
			} else {
				$clicks[$log['url']['label']] += count($log['dates']);
			}
		}
		return $clicks;
	}
	
	public function get_opening_by_recipients($field_name, $unique=false) {
		$opening = array();
		$campaign_recipients = new campaign_recipients($this->num_campaign);
		foreach ($this->get_opening_logs() as $log) {
			$field_value = $campaign_recipients->get_recipient($log['num_recipient'])->get_detail_label($field_name);
			if(!isset($opening[$field_value])) {
				$opening[$field_value] = 0;
			}
			if($unique) {
				$opening[$field_value] = 1;
			} else {
				$opening[$field_value] += count($log['dates']);
			}
		}
		return $opening;
	}
	
	public function get_clicks_by_recipients($field_name, $unique=false) {
		$clicks = array();
		$campaign_recipients = new campaign_recipients($this->num_campaign);
		foreach ($this->get_clicks_logs() as $log) {
			$field_value = $campaign_recipients->get_recipient($log['num_recipient'])->get_detail_label($field_name);
			if(!isset($clicks[$field_value])) {
				$clicks[$field_value] = 0;
			}
			if($unique) {
				$clicks[$field_value] = 1;
			} else {
				$clicks[$field_value] += count($log['dates']);
			}
		}
		return $clicks;
	}
	
	public function get_recipients_number() {
		$query = "select count(*) from campaigns_recipients where campaign_recipient_num_campaign = '".$this->num_campaign."'";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}
	
	public function get_no_email_sent_number() {
		//Compter le nombre de destinataires dans la file d'attente
		$no_email_sent = 0;
		return $no_email_sent." / ".$this->get_recipients_number();
	}
	
	/**
	 * @return array
	 */
	public function build_data() {
		$this->init_data();
		foreach($this->data as $property=>$data) {
			$method_name = 'get_'.$property;
			$this->data[$property] = $this->{$method_name}();
		}
		return $this->data;
	}
	
	/**
	 * @return string Données au format JSON
	 */
	public function get_json_data() {
		if(!isset($this->data)) {
			$this->build_data();
		}
		return encoding_normalize::json_encode($this->data);
	}
	
	public function save() {
		$query = "select count(*) from campaigns_stats where campaign_stat_num_campaign = ".$this->num_campaign;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_result($result, 0, 0)) {
			$query = 'update campaigns_stats set ';
			$where = 'where campaign_stat_num_campaign= '.$this->num_campaign;
		} else {
			$query = 'insert into campaigns_stats set campaign_stat_num_campaign= '.$this->num_campaign.',';
			$where = '';
		}
		$query .= '
				campaign_stat_data = "'.addslashes(encoding_normalize::json_encode($this->data)).'",
				campaign_stat_date = "'.$this->date.'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		} else {
			return false;
		}
	}
}