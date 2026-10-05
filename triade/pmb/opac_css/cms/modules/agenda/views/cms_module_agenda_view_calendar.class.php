<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_calendar.class.php,v 1.29.2.2 2025/01/21 15:29:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_view_calendar extends cms_module_common_view{
	
	public function __construct($id=0){
		$this->use_dojo=true;
		parent::__construct($id);
	}

	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_calendar_nb_displayed_events_under'>".$this->format_text($this->msg['cms_module_agenda_view_calendar_nb_displayed_events_under'])."</label>
			</div>
			<div class='colonne-suite'>
				<input type='text' name='cms_module_agenda_view_calendar_nb_displayed_events_under' value='".$this->format_text($this->parameters['nb_displayed_events_under'])."'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_calendar_link_event'>".$this->format_text($this->msg['cms_module_agenda_view_calendar_link_event'])."</label>
			</div>
			<div class='colonne-suite'>
				".$this->get_constructor_link_form("event")."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_calendar_link_eventslist'>".$this->format_text($this->msg['cms_module_agenda_view_calendar_link_eventslist'])."</label>
			</div>
			<div class='colonne-suite'>
				".$this->get_constructor_link_form("eventslist")."
			</div>
		</div>
		";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_agenda_view_calendar_nb_displayed_events_under;
		$this->save_constructor_link_form("event");
		$this->save_constructor_link_form("eventslist");
		$this->parameters['nb_displayed_events_under'] = (int) $cms_module_agenda_view_calendar_nb_displayed_events_under;
		return parent::save_form();
	}
	
	public function get_headers($datas=array()){

		$headers = parent::get_headers($datas);
		$headers[] = "
		<script>
			require(['dijit/dijit']);
		</script>";		
		$headers[] = "
		<script>
			require(['dijit/Calendar']);
		</script>";
		// AR - 10/02/21 ; L'appel au JS ne sert à rien, il est vide, c'est un mécanisme prévu mais non utilisé
		//$headers[] = "<script src='".$this->get_ajax_link(array('do' => "get_js"), 'js')."'/>";
		$css_file = $this->get_css_file();
		if($css_file) {
			$headers[] = "<link rel='stylesheet' type='text/css' href='".$css_file."'/>";
		}
		return $headers;
	}
	
	public function render($datas){
		$html_to_display = "<div id='cms_module_calendar_".$this->id."'></div>";
		$legend ="";
		$styles = array();
		$events = array();
		$event_list= "";
		
		if($this->parameters>0 && !empty($datas['events']) && is_countable($datas['events']) && count($datas['events'])){
			$legend ="<div class='row'>";
			$event_list= "
		<ul class='cms_module_agenda_view_calendar_eventslist'>";
			$nb_displayed=0;
			$date_time = mktime(0,0,0);
			$calendar = array();
			foreach($datas['events'] as $event){
			    $event->id_event = $event->id;
			    $event->event_title = $event->get_title();
				if(!empty($event->event_start)){
 					$events[] = $event;
					if(!in_array($event->calendar,$calendar)){
						$calendar[] = $event->calendar;
						$legend.="
							<div style='float:left;'>
								<div style='float:left;width:1em;height:1em;background-color:".$event->color."'></div>
								<div style='float:left;'>&nbsp;".$this->format_text($event->calendar)."&nbsp;&nbsp;</div>
							</div>";
					}
					$styles[$event->id_type] = $event->color;
					if($nb_displayed<$this->parameters['nb_displayed_events_under'] && ($event->event_start['time']>= $date_time || $event->event_end['time']>= $date_time)){
						$event_list.="
				<li><a href='".$this->get_constructed_link("event",$event->id)."' title='".$this->format_text($event->calendar)."' alt='".$this->format_text($event->title)."'><span class='cms_module_agenda_event_".$event->id_type."'>".$this->get_date_to_display($event->event_start['format_value'],$event->event_end['format_value'])."</span> : ".$this->format_text($event->title)."</a></li>";
						$nb_displayed++;
					}
				}
			}
			$event_list.= "
		</ul>";
			$legend.="</div><div class='row'></div>";
		}
		
		$html_to_display.="
			<style>
		";

		if(is_array($styles) && count($styles)){
			foreach($styles as $id =>$color){
				$html_to_display.="
						#".$this->get_module_dom_id()." td.cms_module_agenda_event_".$id." {
							background : ".$color.";
						}
						#".$this->get_module_dom_id()." .cms_module_agenda_view_calendar_eventslist .cms_module_agenda_event_".$id." {
							color : ".$color.";
						}
				";
			}
		}
		$html_to_display.="
			</style>
		";
		$html_to_display.=$legend.$event_list;
		
		$json_events = encoding_normalize::json_encode(encoding_normalize::utf8_normalize($events));
		if (empty($json_events)) {
		    $json_events =  array();
		}
		
		$link_single_event = $this->get_constructed_link("event","!!id!!");
		if (empty($link_single_event)) {
		    $link_single_event =  "";
		}
		
		$link_events = $this->get_constructed_link("eventslist","!!date!!");
		if (empty($link_events)) {
		    $link_events =  "";
		}
		
		$html_to_display.="
		<script>
            require([
                'apps/pmb/cms/CmsCalendar',
                'dojo/ready',
                'dojo/domReady!'
            ], function(Calendar, ready){
                ready(function() {
                    var calendar = new Calendar({
                        events: ". $json_events .",
                        singleEventLink: '". $link_single_event."',
                        eventsLink: '". $link_events ."'
                    }, 'cms_module_calendar_".$this->id."')
                    calendar.startup();
                });
            });
		</script>
		";
		return $html_to_display;
	}
	
	public function execute_ajax(){
		return [];
	}
	
	
	public function get_css_file() {
		
		$css_content = "
#".$this->get_module_dom_id()." td.cms_module_agenda_event_day {
	background : green;		
}
#".$this->get_module_dom_id()." ul.cms_module_agenda_view_calendar_eventslist li {
	display : block;
}

#".$this->get_module_dom_id()." ul.cms_module_agenda_view_calendar_eventslist li a {
	display : inline;
	background : none;
	border : none;
	color : inherit !important;
}
";
		return $this->make_tmp_file($css_content, '.css', true);
	}
				
	
	protected function get_date_to_display($start,$end){
		$display = "";
		if($start){
			if($end && $start != $end){
				
				$display.= "du ".$start." au ".$end;
			}else{
				$display.=$start;
			}
		}else{
		
		}
		return $display;
	}
}
