<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_eventslist.class.php,v 1.9 2022/11/21 14:05:45 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_view_eventslist extends cms_module_common_view_articleslist{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<div>
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
<img src='{{event.logo.large}}'/>
<p>{{event.resume}}<br/><a href='{{event.link}}'>plus d'infos...<a/></p>
</blockquote>
{% endfor %}
</div>";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_articleslist_view_link'>".$this->format_text($this->msg['cms_module_common_view_articleslist_build_article_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("article");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("article");
		return parent::save_form();
	}
	
	protected function get_render_datas($datas) {
		$render_datas = array();
		$render_datas['title'] = "Liste d'évènements";
		$render_datas['events'] = array();
		$articles = array();
		foreach($datas['events'] as $event){
			$event->link = $this->get_constructed_link("article",$event->id);
			$render_datas['events'][]=$event;
			$articles[] = $event->id;
		}
		//on rappelle le tout...
		$parent_render = parent::get_render_datas($articles);
		$render_datas['articles'] = $parent_render['articles'];
		return $render_datas;
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_agenda_datasource_agenda();
		$format_data = $datasource->get_format_data_structure("eventslist");
		$format_data[0]['children'][] = array(
			'var' => "events[i].link",
			'desc'=> $this->msg['cms_module_agenda_view_evenslist_link_desc']
		);
		$format_data = array_merge($format_data, parent::get_format_data_structure());
		return $format_data;
	}
}