<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thresholds.class.php,v 1.5 2022/02/21 08:12:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/entites.class.php");
require_once($class_path."/threshold.class.php");

class thresholds {
	
	/**
	 * Etablissement associé
	 * @var entites
	 */
	protected $entity;
	
	/**
	 * Tableau de seuils
	 */
	protected $thresholds;
	
	public function __construct($num_entity=0) {
		$this->entity = null;
		$this->thresholds = array();
		$num_entity = intval($num_entity);
		if($num_entity) {
			$this->entity = new entites($num_entity);
			$this->fetch_data();
		}
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		$query = 'select id_threshold from thresholds where threshold_num_entity = '.$this->entity->id_entite.' order by threshold_amount';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->thresholds[] = new threshold($row->id_threshold);
			}
		}
	}
	
	public function get_data() {
		$data = array();
		foreach($this->thresholds as $threshold) {
			$data[] = $threshold->get_data();
		}
		return $data;
	}
	
	public function get_json_data() {
		return json_encode(encoding_normalize::utf8_normalize($this->get_data()));
	}
	
	public function get_threshold_from_price($ht_price='0.00', $ttc_price='0.00') {
		$thresholds = array_reverse($this->thresholds);
		foreach($thresholds as $threshold) {
			if((!$threshold->get_amount_tax_included() & ($threshold->get_amount() <= $ht_price)) || ($threshold->get_amount_tax_included() & ($threshold->get_amount() <= $ttc_price))) {
				return $threshold; 
			}
		}
		return false;
	}
	
	public function get_entity() {
		return $this->entity;
	}
	
	public function get_thresholds() {
		return $this->thresholds;
	}
}