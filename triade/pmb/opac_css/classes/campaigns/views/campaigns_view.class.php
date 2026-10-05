<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaigns_view.class.php,v 1.5 2022/02/18 09:55:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class campaigns_view {
	
	protected $type;
	
	/**
	 * Instances de stats
	 * @var campaign_stats
	 */
	protected static $campaign_stats;
	
	/**
	 * Instances de campagnes
	 * @var campaign
	 */
	protected $campaigns;
	
	/**
	 * Instances des vues dérivées
	 * @var campaigns_view
	 */
	protected static $instances;
	
	public function __construct($type='') {
		$this->type = $type;
		if($type) {
			$this->type = $type;
		} else {
		    $this->type = ucfirst(str_replace('campaign_view_', '', static::class));
		}
	}
	
	public function add($campaign) {
		$this->campaigns[$campaign->get_id()] = $campaign; 
	}
	
	public function set_campaigns($campaigns) {
		$this->campaigns = $campaigns;
	}
	
	public function get_display_summary() {
		global $msg, $charset;
		
		$display = "
		<table>
			<tr class='center'>
				<th>".htmlentities($msg['campaign_label'], ENT_QUOTES, $charset)."</th>
				<th>".htmlentities($msg['campaign_date'], ENT_QUOTES, $charset)."</th>
				<th>".htmlentities($msg['campaign_view_recipients_number'], ENT_QUOTES, $charset)."</th>
				<th>".htmlentities($msg['campaign_view_opening_rate'], ENT_QUOTES, $charset)."</th>
				<th>".htmlentities($msg['campaign_view_clicks_rate'], ENT_QUOTES, $charset)."</th>
			</tr>";
		foreach ($this->campaigns as $campaign) {
			$display .= "
			<tr class='center'>
				<td>".$campaign->get_label()."</td>
				<td>".$campaign->get_formatted_date()."</td>
				<td>".static::get_campaign_stats($campaign->get_id())->get_recipients_number()."</td>
				<td>".static::get_campaign_stats($campaign->get_id())->get_opening_rate()."</td>
				<td>".static::get_campaign_stats($campaign->get_id())->get_clicks_rate()."</td>
			</tr>";
		}
		$display .= "</table>";
		return $display;
	}
	
	public function get_campaign_charting($node_id) {
		global $msg;
		
		$campaign_charting = new campaign_charting(static::class.'_'.strtolower($this->type).'_'.$node_id);
		$campaign_charting->set_type($this->type);
		if(isset($msg['campaigns_view_'.strtolower($this->type).'_'.$node_id])) {
			$campaign_charting->set_title($msg['campaigns_view_'.$this->type.'_'.$node_id]);
		} else {
			$campaign_charting->set_title($msg['campaigns_view_'.$node_id]);
		}
		return $campaign_charting;
	}
		
	/**
	 * Nombre d'ouverture et de clics par campagnes
	 */
	public function get_opening_and_clicks() {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('opening_and_clicks');
		
		$recipients_number = array();
		$opening_by_campaigns = array();
		$clicks_by_campaigns = array();
		$x_labels = array();
		$i = 1;
		foreach ($this->campaigns as $campaign) {
			$recipients_number[$campaign->get_id()] = static::get_campaign_stats($campaign->get_id())->get_recipients_number();
			$opening = static::get_campaign_stats($campaign->get_id())->get_opening(true);
			$opening_by_campaigns[$campaign->get_id()] = array(
					'y' => $opening/$recipients_number[$campaign->get_id()]*100,
					'tooltip' => $opening.'/'.$recipients_number[$campaign->get_id()]
			);
			$clicks = static::get_campaign_stats($campaign->get_id())->get_clicks(true);
			$clicks_by_campaigns[$campaign->get_id()] = array(
					'y' => $clicks/$recipients_number[$campaign->get_id()]*100,
					'tooltip' => $clicks.'/'.$recipients_number[$campaign->get_id()]
			);
			$x_labels[] = array('value'=> $i, 'text' => $campaign->get_dated_label());
			$i++;
		}
		$campaign_charting->add_serie_data($msg['campaign_view_opening_rate'], array_values($opening_by_campaigns), array('fill' => '#da6abc'));
		$campaign_charting->add_serie_data($msg['campaign_view_clicks_rate'], array_values($clicks_by_campaigns));
		$campaign_charting->set_x_axis(array('labels' => $x_labels));
		$campaign_charting->set_y_axis(array('majorTickStep' => 25, 'title' => $msg['campaigns_view_rate']));

		return $campaign_charting->render();
	}
	
	/**
	 * Nombre d'ouvertures par stats destinataires
	 */
	public function get_opening_by_recipients($element) {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('opening_by_recipients_'.$element);

		$total_opening = array();
		$x_labels = array();
		$i = 1;
		$series = array();
		$possibles_values = campaign_recipients::get_possible_values_of_field(array_keys($this->campaigns), $element);
		
		foreach ($this->campaigns as $campaign) {
			foreach ($possibles_values as $possible_value) {
				$series[$possible_value][$campaign->get_id()] = array();
			}
			$total_opening[$campaign->get_id()] = static::get_campaign_stats($campaign->get_id())->get_opening(true);
			$opening_by_recipient = static::get_campaign_stats($campaign->get_id())->get_opening_by_recipients($element, true);
			foreach ($opening_by_recipient as $recipient => $value) {
				$series[$recipient][$campaign->get_id()] = array(
						'y' => $value/$total_opening[$campaign->get_id()]*100,
						'tooltip' => $value.'/'.$total_opening[$campaign->get_id()]
				);
			}
			$x_labels[] = array('value'=> $i, 'text' => $campaign->get_dated_label());
			$i++;
		}
		foreach ($series as $recipient => $opening) {
			$campaign_charting->add_serie_data($recipient, array_values($opening));
		}
		$campaign_charting->set_x_axis(array('labels' => $x_labels));
		$campaign_charting->set_y_axis(array('majorTickStep' => 25, 'title' => $msg['campaigns_view_rate']));
		return $campaign_charting->render();
	}
	
	/**
	 * Nombre de clics par stats destinataires
	 */
	public function get_clicks_by_recipients($element) {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('clicks_by_recipients_'.$element);
	
		$total_clicks = array();
		$x_labels = array();
		$i = 1;
		$series = array();
		$possibles_values = campaign_recipients::get_possible_values_of_field(array_keys($this->campaigns), $element);
	
		foreach ($this->campaigns as $campaign) {
			foreach ($possibles_values as $possible_value) {
				$series[$possible_value][$campaign->get_id()] = array();
			}
			$total_clicks[$campaign->get_id()] = static::get_campaign_stats($campaign->get_id())->get_clicks(true);
			$clicks_by_recipient = static::get_campaign_stats($campaign->get_id())->get_clicks_by_recipients($element, true);
			foreach ($clicks_by_recipient as $recipient => $value) {
				$series[$recipient][$campaign->get_id()] = array(
						'y' => $value/$total_clicks[$campaign->get_id()]*100,
						'tooltip' => $value.'/'.$total_clicks[$campaign->get_id()]
				);
			}
			$x_labels[] = array('value'=> $i, 'text' => $campaign->get_dated_label());
			$i++;
		}
		foreach ($series as $recipient => $clicks) {
			$campaign_charting->add_serie_data($recipient, array_values($clicks));
		}
		$campaign_charting->set_x_axis(array('labels' => $x_labels));
		$campaign_charting->set_y_axis(array('majorTickStep' => 25, 'title' => $msg['campaigns_view_rate']));
		return $campaign_charting->render();
	}
	
	/**
	 * 
	 * @param int $campaign_id
	 * @return campaign_stats
	 */
	public function get_campaign_stats($campaign_id) {
		if(!isset(static::$campaign_stats[$campaign_id])) {
			static::$campaign_stats[$campaign_id] = new campaign_stats($campaign_id);
		}
		return static::$campaign_stats[$campaign_id];
	}
	
	/**
	 * 
	 * @param string $name
	 * @return campaigns_view
	 */
	public function get_instance($name) {
		global $class_path;
		
		$name = lcfirst($name);
		if(!isset(static::$instances[$name])) {
			$class_name = 'campaigns_view_'.$name;
			$filename = $class_path."/campaigns/views/".$class_name.".class.php";
			if(file_exists($filename)) {
				require_once($filename);
				static::$instances[$name] = new $class_name();
			} else {
				static::$instances[$name] = new campaigns_view(ucfirst($name));
			}
		}
		static::$instances[$name]->set_campaigns($this->campaigns);
		return static::$instances[$name];
	}
}