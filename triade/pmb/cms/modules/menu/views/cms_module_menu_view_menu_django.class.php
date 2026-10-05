<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_menu_view_menu_django.class.php,v 1.5 2014/11/17 17:00:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_menu_view_menu_django extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<!-- Attention, avec un template Django, vous devez gérer la profondeur de votre menu dans le template, il n'existe pas de récursivité avec Django -->
<ul class='cms_menu cms_menu_deep0'>
	{% for item in items %}
		<li {% if item.current %} class='cms_menu_current'{% endif %}>
			{% if item.link %}
				<a href='{{item.link}}'>{{item.title}}</a>
			{% else %}
				{{item.title}}
			{% endif %}
			<!-- Voici un exemple pour la profondeur dans les menus, à répéter autant de fois que de niveaux.. -->
			{% if item.children %}
				<ul class='cms_menu cms_menu_deep1'>
					{% for children1 in item.children %}
						<li {% if children1.current %} class='cms_menu_current'{% endif %}>
							{% if children1.link %}
								<a href='{{children1.link}}'>{{children1.title}}</a>
							{% else %}
								{{children1.title}}
							{% endif %}
							{% if children1.children %}
								<ul class='cms_menu cms_menu_deep2'>
									{% for children2 in children1.children %}
										<li {% if children2.current %} class='cms_menu_current'{% endif %}>
											{% if children2.link %}
												<a href='{{children2.link}}'>{{children2.title}}</a>
											{% else %}
												{{children2.title}}
											{% endif %}
										</li>
									{% endfor %}
								</ul>
							{% endif %}
						</li>
					{% endfor %}
				</ul>
			{% endif %}
		</li>
	{% endfor %}
</ul>";
	}
	
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		if(!is_array($datas['items'])){
			$datas['items']=array();
		}
		foreach($datas['items'] as $key => $item){
			if($item['link'] == $_SERVER['HTTP_REFERER']){
				$datas['items'][$key]['current'] = true;
			}
		}
		//on rappelle le tout...
		return parent::render($datas);
	}
	
	public function get_format_data_structure(){
		$menu = new cms_module_menu_datasource_menu();
		$format = $menu->get_format_data_structure();
		$format[0]['children'][] = array(
			'var' => "items[i].current",
			'desc' => $this->msg['cms_module_menu_view_menu_django']
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}