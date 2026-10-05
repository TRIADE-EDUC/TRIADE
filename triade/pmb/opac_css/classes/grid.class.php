<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: grid.class.php,v 1.1 2017/01/06 16:10:49 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe d'une grille

require_once($class_path."/encoding_normalize.class.php");

class grid {
	
	/**
	 * Type de grille
	 * @var string
	 */
	protected $type = '';
	
	/**
	 * Filtres de la grille <=> Elements pivots
	 * @var string
	 */
	protected $filter = '';
	
	/**
	 * Flag
	 * @var boolean
	 */
	protected $status = false;

	/**
	 * Données de la grille
	 * @var string
	 */
	protected $data = '';
	
	/**
	 * Constructeur
	 * @param string $type
	 * @param string $filter
	 */
	public function __construct($type='', $filter='') {
		$this->type = $type;
		$this->filter = $filter;
		$this->fetch_data();
	}

	/**
	 * Données de la grille
	 */
	protected function fetch_data() {
		$query = 'select grid_generic_data from grids_generic
			where grid_generic_type="'.addslashes($this->type).'"
			and grid_generic_filter="'.addslashes($this->filter).'"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$this->status = true;
			$this->data = pmb_mysql_result($result,0);
		}
	}
	
	public function save() {
		
		if($this->status) {
			$query = 'update grids_generic set';
		} else {
			$query = 'insert into grids_generic set';
		}
		$query .= '
			grid_generic_type = "'.addslashes($this->type).'",
			grid_generic_filter = "'.addslashes($this->filter).'",
			grid_generic_data = "'.addslashes(json_encode($this->data)).'"
		';
		if($this->status) {
			$query .= '
				where grid_generic_type="'.addslashes($this->type).'"
				and	grid_generic_filter="'.addslashes($this->filter).'"
			';
		}
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		}
		return false;
		
	}
      
	/**
	 * Suppression de la grille
	 * @return boolean
	 */
	public function delete() {
	}
	
	public static function permute_backbone($backbone_values){
		if(count($backbone_values) > 1){
			$newFirstLevel = array();
			for($i=0 ; $i<count($backbone_values[0]) ; $i++){
				for($j=0 ; $j<count($backbone_values[1]) ; $j++){
					 $newFirstLevel[] = $backbone_values[0][$i].'_'.$backbone_values[1][$j];
				}
			}
			array_splice($backbone_values, 0, 2, array($newFirstLevel));
			return self::permute_backbone($backbone_values);
		}
		return $backbone_values;
	}
	
	public static function json_response($status, $datas = '') {
		return encoding_normalize::json_encode(array('status'=> $status, 'datas'=> $datas));
	}
	
	public static function proceed($datas) {
		global $action;
		
		switch ($action) {
			case "save":
				$datas = json_decode(encoding_normalize::utf8_normalize(stripslashes($datas)));
				if(is_object($datas)){
					if(!$datas->genericType){
						print grid::json_response(false);
						return false;
					}
					if($datas->all_backbones){
						$flag = true;
						$backbones_values = grid::permute_backbone($datas->backbone_table);
						foreach($backbones_values[0] as $permutation){
							$grid = new grid($datas->genericType, $permutation);
							$grid->set_data($datas->zones);
							$flag = $grid->save();
						}
					}else{
						$grid = new grid($datas->genericType, $datas->genericSign);
						$grid->set_data($datas->zones);
						$flag = $grid->save();
					}
					if($flag){
						print grid::json_response(true);
						return true;
					}
					return false;
				}else{
					print grid::json_response(false);
				}
				break;
			case "get_datas":
				$datas = json_decode(stripslashes($datas));
				if(is_object($datas)){
					$grid = new grid($datas->genericType, $datas->genericSign);
					print grid::json_response($grid->get_status(), $grid->get_data());
				} else {
					print grid::json_response(false);
				}
				break;
			default:
				ajax_http_send_error("404 Not Found","Invalid command : ".$action);
				break;
		}
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
	
	public function get_filter() {
		return $this->filter;
	}
	
	public function set_filter($filter) {
		$this->filter = $filter;
	}
	
	public function get_status() {
		return $this->status;
	}
	
	public function set_status($status) {
		$this->status = $status;
	}
	
	public function get_data() {
		return $this->data;
	}
	
	public function set_data($data) {
		$this->data = $data;
	}

}
