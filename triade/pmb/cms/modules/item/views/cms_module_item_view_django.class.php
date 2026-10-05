<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_item_view_django.class.php,v 1.2 2016/02/12 10:13:45 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_item_view_django extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
    {% if watch.logo_url %}
    <img src='{{item.logo_url}}' alt='{{item.title}}'/>
    {% endif %}
    <a href='{{item.url}}' title='Source' target='_blank'>
        <h3>{{item.title}}</h3>
    </a>
    <blockquote>{{item.publication_date}} / {{item.source.title}}</blockquote>
    <blockquote>{{item.summary}}</blockquote>
    <blockquote>{{item.content}}</blockquote>
</div>";
	}
	
	public function render($datas){
		$render_datas = array();
		$render_datas['item'] = array();
		$render_datas['item'] = $datas;
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_item_datasource_item();
		$datas = $datasource->get_format_data_structure();
		
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}

}