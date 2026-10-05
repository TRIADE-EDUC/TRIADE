<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_calendar_django.class.php,v 1.16.2.2 2025/01/21 15:29:49 qvarin Exp $

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
<div>
<img src='{{event.logo.large}}' alt=''/>
<p>{{event.resume}}<br/><a href='{{event.link}}'>plus d'infos...<a/></p>
</div>
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
		$this->parameters['nb_displayed_events_under'] = (int) $cms_module_agenda_view_calendar_django_nb_displayed_events_under;
		return parent::save_form();
	}

	public function get_headers($datas=array()){
		global $lang;
		$headers = parent::get_headers($datas);
		$headers[] = "
		<script>
			require(['dijit/dijit']);
		</script>";
		$headers[] = "
		<script>
			require(['dijit/Calendar']);
		</script>";
		// AR - 09/02/21 : L'appel au JS ne sert à rien, il est vide, c'est un mécanisme prévu mais non utilisé
		//$headers[] = "<script type='text/javascript' src='".$this->get_ajax_link(array('do' => "get_js"), 'js')."'/>";
		$headers[] = "<link rel='stylesheet' type='text/css' href='".$this->get_ajax_link(array('do' => "get_css"), 'css')."'/>";
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
		if(is_countable($datas['events']) && count($datas['events'])){
			foreach($datas['events'] as $event){
			    $event->id_event = $event->id;
			    $event->event_title = $event->get_title();
				if(!empty($event->event_start)){
					$events[] =$event;
					$styles[$event->id_type] = array("color" => $event->color, "calendar" => $this->format_text($event->calendar));
					if($nb_displayed<$this->parameters['nb_displayed_events_under']) {
						$event->link = $this->get_constructed_link("event",$event->id);
						$render_datas['events'][]=$event;
						$nb_displayed++;
					}
				}
			}
		}

		$html_to_display = "
		<div id='cms_module_calendar_".$this->id."' style='width:100%;'></div>";

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
