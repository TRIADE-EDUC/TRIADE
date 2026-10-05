<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_system_grid.class.php,v 1.12.4.1 2025/03/19 11:33:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/rent/rent_pricing_system_grid.tpl.php");
require_once($class_path."/rent/rent_pricing_system.class.php");

class rent_pricing_system_grid {
	
	/**
	 * Instance du système de tarification rattaché
	 * @var rent_pricing_system
	 */
	protected $pricing_system;
	
	/**
	 * Grille
	 * @var array
	 */
	protected $grid;
	
	/**
	 * Stockage du tableau des pourcentages
	 * @var array
	 */
	protected $percents;
	
	public function __construct($id_pricing_system) {
	    $id_pricing_system = intval($id_pricing_system);
		$this->pricing_system = new rent_pricing_system($id_pricing_system);
		$this->fetch_data();
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {

		$this->grid = array();
		$query = 'select * from rent_pricing_system_grids where pricing_system_grid_num_system = '.$this->pricing_system->get_id().' order by pricing_system_grid_type, pricing_system_grid_time_start';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->grid[] = array(
						'id' => $row->id_pricing_system_grid,
						'time_start' => $row->pricing_system_grid_time_start,
						'time_end' => $row->pricing_system_grid_time_end,
						'price' => $row->pricing_system_grid_price,
						'type' => $row->pricing_system_grid_type
				);
			}
		}
	}
	
	protected function get_interval_content_form($time_start=0, $time_end=5, $price='', $indice=0) {
	    global $rent_pricing_system_grid_content_form_interval_tpl;
	    
	    $interval_tpl = $rent_pricing_system_grid_content_form_interval_tpl;
	    $interval_tpl = str_replace("!!time_start!!", $time_start, $interval_tpl);
	    $interval_tpl = str_replace("!!time_end!!", $time_end, $interval_tpl);
	    $interval_tpl = str_replace("!!price!!", $price, $interval_tpl);
	    $interval_tpl = str_replace("!!indice!!", $indice, $interval_tpl);
	    return $interval_tpl;
	}
	
	protected function get_intervals_content_form() {
	    global $msg, $charset;
	    
	    $content_form = '';
	    $count_interval = 0;
	    if(count($this->grid)){
	        foreach ($this->grid as $element) {
	            if($element['type'] == 1) {
	                $interval_tpl_temp = $this->get_interval_content_form($element['time_start'], $element['time_end'], $element['price'], $count_interval);
	                if($count_interval) {
	                    $button_raz = "<input class='bouton' type='button' value='".htmlentities($msg['raz'], ENT_QUOTES, $charset)."' onClick=\"pricing_system_grid_delete_interval('pricing_system_grid_interval_".$count_interval."');\" />";
	                    $interval_tpl_temp = str_replace("!!button_raz!!",$button_raz,$interval_tpl_temp);
	                } else {
	                    $interval_tpl_temp = str_replace("!!button_raz!!",'',$interval_tpl_temp);
	                }
	                $content_form .=$interval_tpl_temp;
	                $count_interval++;
	            }
	        }
	    } else {
	        $content_form = $this->get_interval_content_form();
	    }
	    return $content_form;
	}
	
	protected function get_percentage_content_form($percent='', $indice=0) {
	    global $rent_pricing_system_grid_content_form_percent_tpl;
	    
	    $percent_tpl = $rent_pricing_system_grid_content_form_percent_tpl;
	    $percent_tpl = str_replace('!!percent!!', $percent, $percent_tpl);
	    $percent_tpl = str_replace('!!indice!!', $indice, $percent_tpl);
	    return $percent_tpl;
	}
	
	protected function get_percentages_content_form() {
	    $content_form = '';
	    $percents = $this->pricing_system->get_percents();
	    if(is_array($percents) && count($percents)) {
	        foreach ($percents as $indice=>$percent) {
	            $content_form .= $this->get_percentage_content_form($percent, $indice);
	        }
	    } else {
	        $content_form = $this->get_percentage_content_form();
	    }
	    return $content_form;
	}
	
	/**
	 * Contenu du formulaire
	 */
	public function get_content_form(){
	    global $rent_pricing_system_grid_content_form_tpl;
	    
	    $content_form = $rent_pricing_system_grid_content_form_tpl;
	    $count_interval = 0;
	    if(count($this->grid)){
	        foreach ($this->grid as $element) {
	            if($element['type'] == 1) {
	                $count_interval++;
	            }
	            if($element['type'] == 2) {
	                $content_form = str_replace("!!extra_time!!",$element['time_start'], $content_form);
	                $content_form = str_replace("!!extra_price!!", $element['price'], $content_form);
	            }
	            if($element['type'] == 3) {
	                $content_form = str_replace("!!not_used_price!!", $element['price'], $content_form);
	            }
	        }
	    } else {
	        $content_form = str_replace("!!extra_time!!", 15, $content_form);
	        $content_form = str_replace("!!extra_price!!", '', $content_form);
	        $content_form = str_replace("!!not_used_price!!", '', $content_form);
	    }
	    $content_form = str_replace("!!grid_form_interval_tpl!!", $this->get_intervals_content_form(), $content_form);
	    $content_form = str_replace("!!grid_form_percent_tpl!!", $this->get_percentages_content_form(), $content_form);
	    
	    $content_form = str_replace("!!interval_max!!", $count_interval, $content_form);
	    
	    $count_percent = 1;
	    $percents = $this->pricing_system->get_percents();
	    if(is_array($percents) && is_countable($percents) && count($percents)) {
	        $count_percent = count($percents);
	    }
	    $content_form = str_replace("!!percent_max!!", $count_percent, $content_form);
	    
	    return $content_form;
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg;
		global $id_entity;
		global $rent_pricing_system_grid_js_form_tpl;
		
		$form = $rent_pricing_system_grid_js_form_tpl;
		
		
		$interface_form = new interface_admin_acquisition_form('pricing_system_grid_form');
		$interface_form->set_label($msg['pricing_system_grid_form_edit']);
		$interface_form->set_object_id($this->pricing_system->get_id())
		->set_id_entity($id_entity)
		->set_content_form($this->get_content_form())
		->set_table_name('rent_pricing_system_grids');
		$form .= $interface_form->get_display();
		
		return $form;
	}

	/**
	 * Provenance du formulaire
	 */
	public function set_properties_from_form(){
		global $pricing_system_grid_intervals;
		global $pricing_system_grid_extra;
		global $pricing_system_grid_not_used;
		global $pricing_system_grid_percents;
		
		$this->grid = array();
		foreach ($pricing_system_grid_intervals as $interval) {
			if($interval['time_start']*1 !== false && $interval['time_end']*1 !== false) {
				$this->grid[] = array(
						'time_start' => $interval['time_start']*1,
						'time_end' => $interval['time_end']*1,
						'price' => $interval['price'],
						'type' => 1,
				);
			}
		}
		$this->grid[] = array(
				'time_start' => ($pricing_system_grid_extra[0]['time']*1 ? $pricing_system_grid_extra[0]['time']*1 : 0),
				'time_end' => 0,
				'price' => $pricing_system_grid_extra[0]['price'],
				'type' => 2
		);
		$this->grid[] = array(
				'time_start' => 0,
				'time_end' => 0,
				'price' => $pricing_system_grid_not_used[0]['price'],
				'type' => 3
		);
		$percents = array();
		if(is_array($pricing_system_grid_percents) && count($pricing_system_grid_percents)) {
			foreach ($pricing_system_grid_percents as $percent) {
				if($percent != '') {
					$percents[] = $percent;
				}
			}
		}
		$this->pricing_system->set_percents($percents);
	}

	/**
	 * Sauvegarde
	 */
	public function save(){
		$this->delete();
		foreach ($this->grid as $element) {
			$query = 'insert into rent_pricing_system_grids set
					pricing_system_grid_num_system='.$this->pricing_system->get_id().',
					pricing_system_grid_time_start='.$element['time_start'].',
					pricing_system_grid_time_end="'.$element['time_end'].'",
					pricing_system_grid_price="'.$element['price'].'",		
					pricing_system_grid_type='.$element['type'];
			$result = pmb_mysql_query($query);
			if(!$result) {
				return false;
			}
		}
		$this->pricing_system->save_percents();
		return true;
	}
	
	/**
	 * Suppression
	 */
	public function delete(){
		
		if($this->pricing_system->get_id()) {
			$query = "delete from rent_pricing_system_grids where pricing_system_grid_num_system= ".$this->pricing_system->get_id();
			pmb_mysql_query($query);
			return true;
		}
	}
	
	protected function get_display_line($element) {
		global $msg;
		
		$display = "<tr><td class='pricing_system_grid_header align_right'>";
		switch($element['type']) {
			case 1 :
				$display .= $element['time_start']."&apos; ".$msg['pricing_system_grid_to']." ".$element['time_end']."&apos;";
				break;
			case 2 :
				$display .= $element['time_start']." ".$msg['pricing_system_grid_extra_time'];
				break;
			case 3 :
				$display .= $msg['pricing_system_grid_not_used'];
				break;
		}
		$display .= "</td>";
		switch($element['type']) {
			case 1 :
			case 2 :
				foreach ($this->percents as $percent) {
					$display .= "<td class='pricing_system_grid_content align_right'>".number_format(($element['price']*$percent)/100,2,'.','')." &euro;</td>";
				}
				break;
			case 3 :
				$display .= "<td class='pricing_system_grid_content align_right'>".$element['price']." &euro;</td>";
				$display .= "<td class='pricing_system_grid_content' colspan='".(count($this->percents)-1)."'></td>";
				break;
		}
		$display .= "</tr>";
		return $display;
	}
	
	/**
	 * Affichage de la grille
	 */
	public function get_display() {
		global $msg;
		
		$this->percents = $this->pricing_system->get_percents();
		$this->percents = array_merge(array(100), $this->percents);
		$display = "<table style='border:1px solid'>";
		$display .= "<tr><td colspan='".(count($this->percents)+1)."' class='brd center'><b>".$this->pricing_system->get_label()."</b></td></tr>";
		$display .= "<tr><td style='width:10%' class='align_right'><b>".$msg['pricing_system_grid_time_percent']."</b></td>";
		foreach ($this->percents as $percent) {
			$display .= '<td class="pricing_system_grid_header center">'.$percent.'%</td>';
		}
		$display .= "</tr>";
		if(count($this->grid)){
			foreach ($this->grid as $element) {
				$display .= $this->get_display_line($element);
			}
		}
		$display .= "</table>";
		return $display;
	}

	/**
	 * Affichage de la grille dans l'iframe
	 */
	public function get_display_in_layer() {
		$display = '<div class="right"><a href="#" onClick="parent.kill_frame(\'frame_notice_preview\');return false;"><img src="'.get_url_icon('close.gif').'" style="border:0px" class="align_right"></a></div>';
		$display .= $this->get_display();
		return $display;
	}
	
	/**
	 * Valeurs par défaut en création d'un système de tarification
	 */
	public function init_default_grid() {
		$this->grid = 
			array(
				array(
					'time_start' => 0,
					'time_end' => 5,
					'price' => '0.00',
					'type' => 1,
				),
				array(
					'time_start' => 15,
					'time_end' => 0,
					'price' => '0.00',
					'type' => 2,
				),
				array(
						'time_start' => 0,
						'time_end' => 0,
						'price' => '0.00',
						'type' => 3,
				)
			);
	}
	
	public function calc_price_from_time($time=0, $percent='0.00') {
		$price = 0;
		if($time*1) {
			$price = $this->calc_price($time, $percent);	
		}
		return $price;
	}

	public function calc_price_from_percent($percent='0.00', $time=0) {
		$price = 0;
		if($time*1) {
			$price = $this->calc_price($time, $percent);
		}
		return $price;
	}
	
	protected function get_last_interval_grid() {
		
		if(count($this->grid)) {
			$last_interval_grid = array();
			foreach($this->grid as $element) {
				if($element['type'] == 1) {
					$last_interval_grid = $element;
				}
			}
			return $last_interval_grid;
		} else {
			return array(
				'time_start' => 0,
				'time_end' => 0,
				'price' => 0,
				'type' => 1,
			);
		}
	}
	protected function calc_price($time = 0, $percent = 100){
		$price = 0;
		$time = intval($time);
		$defined_price = false;
		foreach($this->grid as $element) {
			if(!$defined_price) {
				switch ($element['type']) {
					case 1 :
						if($time >= $element['time_start']*1) {
							$price = $element['price']*$percent/100;
							if($time < $element['time_end']*1) {
								$defined_price = true;
							}
						}
						break;
					case 2 :
						if($time) {
							$last_interval_element = $this->get_last_interval_grid();
							if($time > $last_interval_element['time_end']*1) {
								if($element['time_start']*1) {
									$nb_extra = ceil(($time-$last_interval_element['time_end']*1)/$element['time_start']*1);									
									if($nb_extra) {
										$price += $element['price']*$percent/100*$nb_extra;
									}
								}
							}
						}
						break;
					case 3 :
						break;
				}
			}
		}
		return number_format($price, 2, '.', '');
	}
	
	public function get_pricing_system() {
		return $this->pricing_system;
	}

	public function get_grid() {
		return $this->grid;
	}
	
	public function set_pricing_system($pricing_system) {
		$this->pricing_system = $pricing_system;
	}
	
	public function set_grid($grid) {
		$this->grid = $grid;
	}
}