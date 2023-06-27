<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_calendar.class.php,v 1.17 2019-06-13 15:26:51 btafforeau Exp $

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
		global $lang;
		$headers = parent::get_headers($datas);
		$headers[] = "
		<script type='text/javascript'>
			require(['dijit/dijit']);
		</script>";		
		$headers[] = "
		<script type='text/javascript'>
			require(['dijit/Calendar']);
		</script>";
		$headers[] = "<script type='text/javascript' src='".$this->get_ajax_link(array('do' => "get_js"))."'/>";
		$headers[] = "<link rel='stylesheet' type='text/css' href='".$this->get_ajax_link(array('do' => "get_css"))."'/>";
		return $headers;
	}
	
	public function render($datas){
		$html_to_display = "
		<div id='cms_module_calendar_".$this->id."' data-dojo-props='onChange : cms_module_agenda_highlight_events,getClassForDate:cms_module_agenda_get_class_day'; dojoType='dijit.Calendar' style='width:100%;'></div>";
		$legend ="";
		$styles = array();
		$events = array();
		$event_list= "";
		if($this->parameters>0 && count($datas['events'])){
			$legend ="<div class='row'>";
			$event_list= "
		<ul class='cms_module_agenda_view_calendar_eventslist'>";
			$nb_displayed=0;
			$date_time = mktime(0,0,0);
			$calendar = array();
			foreach($datas['events'] as $event){
				if(isset($event['event_start']) && $event['event_start']){
 					$events[] =$event;
					if(!in_array($event['calendar'],$calendar)){
						$calendar[] = $event['calendar'];
						$legend.="
							<div style='float:left;'>
								<div style='float:left;width:1em;height:1em;background-color:".$event['color']."'></div>
								<div style='float:left;'>&nbsp;".$this->format_text($event['calendar'])."&nbsp;&nbsp;</div>
							</div>";
					}
					$styles[$event['id_type']] = $event['color'];
					if($nb_displayed<$this->parameters['nb_displayed_events_under'] && ($event['event_start']['time']>= $date_time || $event['event_end']['time']>= $date_time)){
						$event_list.="
				<li><a href='".$this->get_constructed_link("event",$event['id'])."' title='".$this->format_text($event['calendar'])."' alt='".$this->format_text($event['title'])."'><span class='cms_module_agenda_event_".$event['id_type']."'>".$this->get_date_to_display($event['event_start']['format_value'],$event['event_end']['format_value'])."</span> : ".$this->format_text($event['title'])."</a></li>";
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
		
			
		$html_to_display.="
		<script type='text/javascript'>
			var events = ".json_encode($this->utf8_encode($events)).";	
			
			function cms_module_agenda_get_class_day(date,locale){
				var classname='';
				dojo.forEach(events,function (event){
						start_day = new Date(event['event_start']['time']*1000);
						start_day.setHours(1,0,0,0);
						if(event['event_end']){
							end_day = new Date(event['event_end']['time']*1000);
							end_day.setHours(1,0,0,0);
						}else end_day = false;
						if((date.valueOf()>=start_day.valueOf() && (end_day && date.valueOf()<=end_day.valueOf())) || date.valueOf()==start_day.valueOf()){
							if (classname.indexOf('cms_module_agenda_event_'+event.id_type) === -1) classname+='cms_module_agenda_event_'+event.id_type;
							if (classname) {
								classname+= ' ';
								if(classname.indexOf('cms_module_agenda_multiple_events') === -1) {
									classname+=' cms_module_agenda_multiple_events ';
								}
							}
						}
				});
				return classname;
			}
			
			function cms_module_agenda_highlight_events(value){
				if(value){
					require(['dojo/date'],function(date){
						var current_events = new Array();
						dojo.forEach(events,function (event){
							start_day = new Date(event['event_start']['time']*1000);
							if(event['event_end']){
								end_day = new Date(event['event_end']['time']*1000);
							}else end_day = false;
							//juste une date ou dates debut et fin
							if(date.difference(value, start_day, 'day') == 0 || (start_day && end_day && date.difference(value, start_day, 'day') <= 0 && date.difference(value, end_day, 'day') >= 0 )){
								current_events.push(event);
							}
							start_day = end_day = false;
						});
						if(current_events.length == 1){
							//un seul evenement sur la journee, on l'affiche directement
							var link = '".$this->get_constructed_link("event","!!id!!")."';
							document.location = link.replace('!!id!!',current_events[0]['id']);
						}else if (current_events.length > 1){
							//plusieurs evenements, on affiche la liste...
							var month = value.getMonth()+1;
							var day =value.getDate();
							var day = value.getFullYear()+'-'+(month >9 ? month : '0'+month)+'-'+(day > 9 ? day : '0'+day);
							var link = '".$this->get_constructed_link("eventslist","!!date!!")."';
							document.location = link.replace('!!date!!',day);
						}
					});
				}
			}
		</script>
		";
		return $html_to_display;
	}
	
	public function execute_ajax(){
		$response = array();
		global $do;
		switch ($do){
			case "get_css" :
				$response['content-type'] = "text/css";
				$response['content'] = "
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
				
				break;			
			case "get_js" :
				$response['content-type'] = "application/javascript";
				$response['content'] = "";
				break;		
		}
		return $response;
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
