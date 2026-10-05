<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_progress_bar.class.php,v 1.3 2023/03/21 09:52:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/progress_bar.class.php");

class scheduler_progress_bar extends progress_bar {
	public $percent='0';
	
	//Constructeur.	 $text
	public function __construct($percent='0') {
		
		$this->html_id = parent::$nb_instance;
		$this->percent= $percent;
		if($this->percent == '0.00' || $this->percent == '100.00') {
			$this->percent = round($this->percent);
		}
			
		parent::$nb_instance++;
	}
	
	public function get_display(){
        $display = "
		<div id='progress_bar_".$this->html_id."' class='scheduler_progress_bar'>
			<img id='progress_".$this->html_id."' class='scheduler_progress_bar_img' src='".get_url_icon('jauge.png')."' style='width:".$this->percent."%;' />
			<div class='scheduler_progress_bar_percent' style='".(!empty($this->percent) ? 'font-weight:bold;' : '')."'>
				<span id='progress_text_".$this->html_id."'></span>".$this->percent."%
				<span id='progress_percent_".$this->html_id."'></span>
			</div>
		</div>";
        flush();
        return $display;
    }
}
