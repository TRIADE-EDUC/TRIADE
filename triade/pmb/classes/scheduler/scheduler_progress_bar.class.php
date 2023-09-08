<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_progress_bar.class.php,v 1.2 2017-11-30 10:00:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/progress_bar.class.php");

class scheduler_progress_bar extends progress_bar {
	public $percent='0';
	
	//Constructeur.	 $text
	public function __construct($percent='0') {
		
		$this->html_id = parent::$nb_instance;
		$this->percent= $percent;
		parent::$nb_instance++;
	}
	
	public function get_display(){
        $display = "<div class='row' id='progress_bar_".$this->html_id."' style='text-align:center; width:80%; border: 1px solid #000000; padding: 3px; z-index:1;'>
	            <div style='text-align:left; width:100%; height:20px;'>
	                <img id='progress_".$this->html_id."' src='".get_url_icon('jauge.png')."' style='width:".$this->percent."%; height:20px'/>
		            
		            <div style='text-align:center; position:relative; top: -25px; z-index:1'>
		                <span id='progress_text_".$this->html_id."'></span>".$this->percent."%
		                <span id='progress_percent_".$this->html_id."'></span>
		            </div>
		    	</div>
	        </div>";
        flush();
        return $display;
    }
}
