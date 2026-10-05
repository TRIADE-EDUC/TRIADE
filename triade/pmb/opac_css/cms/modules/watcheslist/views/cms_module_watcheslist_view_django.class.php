<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_view_django.class.php,v 1.5.2.1 2025/01/17 10:40:44 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watcheslist_view_django extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for watch in watches %}
<h3>{{watch.title}}</h3>
{% if watch.logo.exists %}
<img src='{{watch.logo.vign}}' alt=''/>
{% else %}			
<img src='{{watch.logo_url}}' alt=''/>
{% endif %}
<div>{{watch.desc}}</div>
{% endfor %}
</div>";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_watcheslist_view_link'>".$this->format_text($this->msg['cms_module_watcheslist_view_django_build_watch_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("watch");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("watch");
		return parent::save_form();
	}
	
	public function render($datas){
	    if (is_countable($datas['watches'])) {
    		for($i=0 ; $i<count($datas['watches']) ; $i++){
    			$datas['watches'][$i]['link'] = $this->get_constructed_link('watch',$datas['watches'][$i]['id']);
    		}
	    }
		return parent::render($datas);
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_watcheslist_datasource_watches();
		$datas = $datasource->get_format_data_structure();
	
		$datas[0]['children'][] = array(
				'var' => "watches[i].watch.link",
				'desc'=> $this->msg['cms_module_watcheslist_view_django_watch_link_desc']
		);
	
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}