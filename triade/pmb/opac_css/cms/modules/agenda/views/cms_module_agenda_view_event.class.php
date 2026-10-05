<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_event.class.php,v 1.4 2018/08/24 08:44:59 plmrozowski Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_view_event extends cms_module_common_view_article{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<h3>
{% if event_start.format_value %}
 {% if event_end.format_value %}
du {{event_start.format_value}} au {{event_end.format_value}}
 {% else %}
le {{event_start.format_value}}
 {% endif %}
{% endif%} : {{title}}
</h3>
<img src='{{logo.large}}' alt=''/>
<p>{{content}}</p>";
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_agenda_datasource_agenda();
		return $datasource->get_format_data_structure();
	}
}