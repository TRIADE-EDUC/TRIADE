<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_charting.class.php,v 1.4 2018/03/08 17:29:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/campaigns/campaign_charting.tpl.php");

class campaign_charting {
		
	protected $node_id;
	
	protected $series_data;
	
	protected $type;
	
	protected $x_axis;
	
	protected $y_axis;
	
	protected $min;
	
	protected $title;
	
	public function __construct($node_id) {
		$this->node_id = $node_id;
		$this->min = 0;
	}
	
	public function add_serie_data($label, $values, $styles=array()) {
		$this->series_data[] = array('label' => $label, 'values' => $values, 'styles' => $styles);
	}
	
	public function set_series_data($series_data) {
		$this->series_data = $series_data;
		return $this;
	}
	
	public function render() {
		global $charset;
		global $campaign_charting_commons, $campaign_charting_axis;
		
		$charting_view = $campaign_charting_commons;
		$charting_view = str_replace('!!chartType!!', $this->type, $charting_view);
		$charting_view = str_replace('!!nodeId!!', $this->node_id, $charting_view);
		$charting_view = str_replace('!!legendNodeId!!', $this->node_id.'Legend', $charting_view);
		$charting_view = str_replace('!!title!!', (isset($this->title) ? htmlentities($this->title, ENT_QUOTES, $charset) : ''), $charting_view);
		$charting_view = str_replace('!!seriesData!!', encoding_normalize::json_encode($this->series_data), $charting_view);
		
		$axis = '';
		if ($this->type != 'Pie') {
			$axis = $campaign_charting_axis;
			$axis = str_replace('!!xAxis!!', encoding_normalize::json_encode($this->get_x_axis()), $axis);
			$axis = str_replace('!!yAxis!!', encoding_normalize::json_encode($this->get_y_axis()), $axis);
			
			$axis = str_replace('!!xAxisLabels!!', encoding_normalize::json_encode($this->x_axis['labels']), $axis);
		}
		$charting_view = str_replace('!!chartAxis!!', $axis, $charting_view);
		
		$charting_view = str_replace('!!min!!', $this->min, $charting_view);
		return $charting_view;
	}
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function get_x_axis() {
		if(!isset($this->x_axis)) {
			$this->x_axis = array(
					'majorTickStep' => 1,
					'majorLabels' => true,
					'minorTicks' => false
			);
		}
		return $this->x_axis;
	}
	
	public function set_x_axis($x_axis) {
		$this->get_x_axis();
		if(is_array($x_axis)) {
			foreach($x_axis as $property=>$value) {
				$this->x_axis[$property] = $value;
			}
		}
		return $this;
	}
	
	public function get_y_axis() {
		if(!isset($this->y_axis)) {
			$this->y_axis = array(
					'majorTickStep' => 1,
					'majorLabels' => true,
					'minorTicks' => false,
					'min' => 0,
					'vertical' => true,
					'fixLower' => 'major',
					'fixUpper' => 'major'
			);
		}
		return $this->y_axis;
	}
	
	public function set_y_axis($y_axis) {
		$this->get_y_axis();
		if(is_array($y_axis)) {
			foreach($y_axis as $property=>$value) {
				$this->y_axis[$property] = $value;
			}
		}
		return $this;
	}
	
	public function set_min($min) {
		$this->min = $min;
		return $this;
	}
	
	public function set_title($title) {
		$this->title = $title;
		return $this;
	}
}