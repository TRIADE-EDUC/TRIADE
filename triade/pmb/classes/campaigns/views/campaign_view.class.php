<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_view.class.php,v 1.8 2022/01/19 11:44:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/campaigns/campaign_charting.class.php");
require_once($class_path."/campaigns/campaign_stats.class.php");
require_once($class_path."/campaigns/campaign.class.php");

class campaign_view {
	
	/**
	 * Identifiant de la campagne
	 * @var integer
	 */
	protected $id;
	
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
	protected static $campaigns;
	
	/**
	 * Instances des vues dérivées
	 * @var campaign_view
	 */
	protected static $instances;
	
	public function __construct($id=0, $type='') {
		$this->id = intval($id);
		if($type) {
			$this->type = $type;
		} else {
		    $this->type = ucfirst(str_replace('campaign_view_', '', static::class));
		}
	}
	
	public function get_graphes() {
		return array(
				'get_clicks_by_links',
				'get_opening_and_clicks_by_days',
				'get_clicks_by_links_type'
		);	
	}
	
	public function get_recipients_number() {
		global $charset, $msg;
		
		$html = "<label>".htmlentities($msg['campaign_view_recipients_number'], ENT_QUOTES, $charset)."</label><br />";
		$html.= $this->get_campaign_stats()->get_recipients_number();
		
		return $html;
	}
	
	public function get_opening_rate() {
		global $charset, $msg;
		
		$html = "<label>".htmlentities($msg['campaign_view_opening_rate'], ENT_QUOTES, $charset)."</label><br />";
		$html.= $this->get_campaign_stats()->get_opening_rate(true);
		
		return $html;
	}
	
	public function get_clicks_rate() {
		global $charset, $msg;
		
		$html = "<label>".htmlentities($msg['campaign_view_clicks_rate'], ENT_QUOTES, $charset)."</label><br />";
		$html.= $this->get_campaign_stats()->get_clicks_rate(true);
		
		return $html;
	}
	
	public function get_no_email_sent_number() {
		global $charset, $msg;
		
		$html = "<label>".htmlentities($msg['campaign_view_no_email_sent_number'], ENT_QUOTES, $charset)."</label><br />";
		$html.= $this->get_campaign_stats()->get_no_email_sent_number();
		
		return $html;
	}
	
	public function get_campaign_charting($node_id) {
		global $msg;
		
		$campaign_charting = new campaign_charting(static::class.'_'.strtolower($this->type).'_'.$node_id);
		$campaign_charting->set_type($this->type);
		if(isset($msg['campaign_view_'.strtolower($this->type).'_'.$node_id])) {
			$campaign_charting->set_title($msg['campaign_view_'.$this->type.'_'.$node_id]);
		} else {
			$campaign_charting->set_title($msg['campaign_view_'.$node_id]);
		}
		return $campaign_charting;
	}
	
	/**
	 * Nombre de clics par lien
	 */
	public function get_clicks_by_links() {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('clicks_by_links');
		$clicks_by_links = $this->get_campaign_stats()->get_clicks_by_links();
		$campaign_charting->add_serie_data($msg['campaign_view_clicks_number'], array_values($clicks_by_links));
		$x_labels = array();
		foreach (array_keys($clicks_by_links) as $i=>$label) {
			$x_labels[] = array('value'=> $i+1, 'text' => $label);
		}
		$campaign_charting->set_x_axis(array('labels' => $x_labels, 'maxLabelSize' => 200, 'rotation' => 340));
		return $campaign_charting->render();
	}
	
	/**
	 * Nombre d'ouverture et de clics par jours
	 */
	public function get_opening_and_clicks_by_days($start_date, $end_date) {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('opening_and_clicks_by_days');
		$opening_by_days = $this->get_campaign_stats()->get_opening_by_days($start_date, $end_date);
		$clicks_by_days = $this->get_campaign_stats()->get_clicks_by_days($start_date, $end_date);
		$campaign_charting->add_serie_data($msg['campaign_view_opening_number'], array_values($opening_by_days), array('fill' => '#da6abc'));
		$campaign_charting->add_serie_data($msg['campaign_view_clicks_number'], array_values($clicks_by_days));
		$x_labels = array();
		foreach (array_keys($clicks_by_days) as $i=>$label) {
			$x_labels[] = array('value'=> $i+1, 'text' => formatdate($label));
		}
		$campaign_charting->set_x_axis(array('labels' => $x_labels));
		return $campaign_charting->render();
	}
	
	/**
	 * Nombre d'ouverture et de clics par heures
	 */
	public function get_opening_and_clicks_by_hours($start_date, $end_date, $cumulated=false) {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('opening_and_clicks_by_hours');
		$opening_by_hours = $this->get_campaign_stats()->get_opening_by_hours($start_date, $end_date);
		$x_labels = array();
		$serie_data = array();
		$i = 0;
		foreach ($opening_by_hours as $day => $hours) {
			foreach ($hours as $j=>$hour) {
				$serie_data[] = $hour;
				if($j != '00') {
					$x_labels[] = array('value'=> $i, 'text' => $j.'h');
				} else {
					$x_labels[] = array('value'=> $i, 'text' => formatdate($day));
				}
				$i++;
			}
		}
		if($cumulated) {
			$cumulated_serie_data = array();
			$sum = 0;
			foreach($serie_data as $data) {
				$sum += $data;
				$cumulated_serie_data[] = $sum;
			}
			$campaign_charting->add_serie_data($msg['campaign_view_opening_number'], $cumulated_serie_data, array('color' => '#da6abc'));
		} else {
			$campaign_charting->add_serie_data($msg['campaign_view_opening_number'], $serie_data, array('color' => '#da6abc'));
		}
		
		
		$clicks_by_hours = $this->get_campaign_stats()->get_clicks_by_hours($start_date, $end_date);
		$serie_data = array();
		foreach ($clicks_by_hours as $day => $hours) {
			foreach ($hours as $i=>$hour) {
				$serie_data[] = $hour;
			}
		}
		if($cumulated) {
			$cumulated_serie_data = array();
			$sum = 0;
			foreach($serie_data as $data) {
				$sum += $data;
				$cumulated_serie_data[] = $sum;
			}
			$campaign_charting->add_serie_data($msg['campaign_view_clicks_number'], $cumulated_serie_data);
		} else {
			$campaign_charting->add_serie_data($msg['campaign_view_clicks_number'], $serie_data);
		}
		$campaign_charting->set_x_axis(array('labels' => $x_labels, 'includeZero' => true, 'minorTicks' => true, 'minorTickStep' => 12, 'majorTickStep' => 24));
		return $campaign_charting->render();
	}
	
	/**
	 * Nombre de clics par type de lien
	 */
	public function get_clicks_by_links_type() {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('clicks_by_links_type');
		$clicks_by_links_type = $this->get_campaign_stats()->get_clicks_by_links_type();
		$series_labels = array();
		foreach ($clicks_by_links_type as $label=>$nb_clicks) {
			$series_labels[] = array('y'=> $nb_clicks, 'tooltip' => $label, 'legend' => $label);
		}
		$campaign_charting->add_serie_data($msg['campaign_view_clicks_number'], $series_labels);
		return $campaign_charting->render();
	}
	
	/**
	 * Nombre d'ouvertures par stats destinataires
	 */
	public function get_opening_by_recipients($element) {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('opening_by_recipients_'.$element);
		$opening_by_recipients = $this->get_campaign_stats()->get_opening_by_recipients($element);
		$series_labels = array();
		foreach ($opening_by_recipients as $label=>$nb_opening) {
			$series_labels[] = array('y'=> $nb_opening, 'tooltip' => $label, 'legend' => $label);
		}
		$campaign_charting->add_serie_data($msg['campaign_view_opening_number'], $series_labels);
		return $campaign_charting->render();
	}
	
	/**
	 * Nombre de clics par stats destinataires
	 */
	public function get_clicks_by_recipients($element) {
		global $msg;
	
		$campaign_charting = $this->get_campaign_charting('clicks_by_recipients_'.$element);
		$clicks_by_recipients = $this->get_campaign_stats()->get_clicks_by_recipients($element);
		$series_labels = array();
		foreach ($clicks_by_recipients as $label=>$nb_clicks) {
			$series_labels[] = array('y'=> $nb_clicks, 'tooltip' => $label, 'legend' => $label);
		}
		$campaign_charting->add_serie_data($msg['campaign_view_clicks_number'], $series_labels);
		return $campaign_charting->render();
	}
	
	public function get_campaign_stats() {
		if(!isset(static::$campaign_stats[$this->id])) {
			static::$campaign_stats[$this->id] = new campaign_stats($this->id);
		}
		return static::$campaign_stats[$this->id];
	}
	
	public function get_campaign() {
		if(!isset(static::$campaigns[$this->id])) {
			static::$campaigns[$this->id] = new campaign($this->id);
		}
		return static::$campaigns[$this->id];
	}
	
	/**
	 * 
	 * @param string $name
	 * @return campaign_view
	 */
	public function get_instance($name) {
		global $class_path;
		
		$name = lcfirst($name);
		if(!isset(static::$instances[$name])) {
			$class_name = 'campaign_view_'.$name;
			$filename = $class_path."/campaigns/views/".$class_name.".class.php";
			if(file_exists($filename)) {
				require_once($filename);
				static::$instances[$name] = new $class_name($this->id);
			} else {
				static::$instances[$name] = new campaign_view($this->id, ucfirst($name));
			}
		}
		return static::$instances[$name];
	}
	
	public function format_link($text) {
		return "<a href='".$text."' target='_blank' >".$text."</a>";
	}
}