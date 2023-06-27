<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_calendar_django.class.php,v 1.10 2019-04-10 10:49:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_view_calendar_django extends cms_module_common_view_django{
	private $dojo_theme="tundra";
	
	public function __construct($id=0){
		$this->use_dojo=true;
		parent::__construct($id);
		$this->default_template = "
<div>
<h3>Titre</h3>
{{calendar}}
{% for legend in legends %}
<div style='float:left;'>
<div style='float:left;width:1em;height:1em;background-color:{{legend.color}}'></div>
<div style='float:left;'>&nbsp;{{legend.calendar}}&nbsp;&nbsp;</div>
</div>
{% endfor %}				
{% for event in events %}
<h3>
{% if event.event_start.format_value %}
 {% if event.event_end.format_value %}
du {{event.event_start.format_value}} au {{event.event_end.format_value}}
 {% else %}
le {{event.event_start.format_value}}
 {% endif %}
{% endif%} : {{event.title}}
</h3>
<blockquote>
<img src='{{event.logo.large}}' alt=''/>
<p>{{event.resume}}<br/><a href='{{event.link}}'>plus d'infos...<a/></p>
</blockquote>
{% endfor %}
</div>";
	}

	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_calendar_django_nb_displayed_events_under'>".$this->format_text($this->msg['cms_module_agenda_view_calendar_django_nb_displayed_events_under'])."</label>
			</div>
			<div class='colonne-suite'>
				<input type='text' name='cms_module_agenda_view_calendar_django_nb_displayed_events_under' value='".$this->format_text($this->parameters['nb_displayed_events_under'])."'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_calendar_django_link_event'>".$this->format_text($this->msg['cms_module_agenda_view_calendar_django_link_event'])."</label>
			</div>
			<div class='colonne-suite'>
				".$this->get_constructor_link_form("event")."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_view_calendar_django_link_eventslist'>".$this->format_text($this->msg['cms_module_agenda_view_calendar_django_link_eventslist'])."</label>
			</div>
			<div class='colonne-suite'>
				".$this->get_constructor_link_form("eventslist")."
			</div>
		</div>
		";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		global $cms_module_agenda_view_calendar_django_nb_displayed_events_under;
		$this->save_constructor_link_form("event");
		$this->save_constructor_link_form("eventslist");
		$this->parameters['nb_displayed_events_under'] = $cms_module_agenda_view_calendar_django_nb_displayed_events_under+0;
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
		$render_datas = array();
		$render_datas['legends'] = array();
		$render_datas['events'] = array();
		$nb_displayed=0;
		$styles = array();
		$calendar = array();
		$events = array();
		if(count($datas['events'])){
			foreach($datas['events'] as $event){
				if(isset($event['event_start']) && $event['event_start']){
					$events[] =$event;
					$styles[$event['id_type']] = array("color" => $event['color'], "calendar" => $this->format_text($event['calendar']));
					if($nb_displayed<$this->parameters['nb_displayed_events_under']) {
						$event['link'] = $this->get_constructed_link("article",$event['id']);
						$render_datas['events'][]=$event;
						$nb_displayed++;
					}
				}
			}
		}
		
		$html_to_display = "
		<div id='cms_module_calendar_".$this->id."' data-dojo-props='onChange : cms_module_agenda_highlight_events,getClassForDate:cms_module_agenda_get_class_day'; dojoType='dijit.Calendar' style='width:100%;'></div>";
		
		$html_to_display.="
			<style>
		";
		
		foreach($styles as $id =>$style){
			$html_to_display.="
				#".$this->get_module_dom_id()." td.cms_module_agenda_event_".$id." {
					background : ".$style["color"].";
				}
				#".$this->get_module_dom_id()." .cms_module_agenda_view_calendar_eventslist .cms_module_agenda_event_".$id." {
					color : ".$style["color"].";
				}
		";
		}
		$html_to_display.="
			</style>
		";
			
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
		$render_datas['calendar'] = $html_to_display;
		$render_datas['legends'] = $styles;
		
		//on rappelle le tout...
		return parent::render($render_datas);

	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_agenda_datasource_agenda();
		$format_data = $datasource->get_format_data_structure("eventslist");
		$format_data[0]['children'][] = array(
				'var' => "events[i].link",
				'desc'=> $this->msg['cms_module_agenda_view_calendar_django_link_desc']
		);
		$format_data[] = array(
				'var' => "calendar",
				'desc'=> $this->msg['cms_module_agenda_view_calendar_django_calendar_desc']
		);
		$format_data[] = array(
				'var' => "legends",
				'desc'=> $this->msg['cms_module_agenda_view_calendar_django_legends_desc'],
				'children' => array(
						array(
								'var' => "legends[i].calendar",
								'desc'=> $this->msg['cms_module_agenda_view_calendar_django_legend_calendar_desc']
						),
						array(
								'var' => "legends[i].color",
								'desc'=> $this->msg['cms_module_agenda_view_calendar_django_legend_color_desc']
						)
					)
		);
		$format_data = array_merge($format_data,parent::get_format_data_structure());
		return $format_data;
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
