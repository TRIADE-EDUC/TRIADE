<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_rss.class.php,v 1.6 2023/12/07 15:02:47 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_rss extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<h2>{{title}}</h2>
<p>{{description}}</p>
{% for item in items %}
<div>
<h4><a href='{{item.link}}' target='_blank'>{{item.title}}</a></h4>
<p>{{item.description}}</p>
</div>
{% endfor %}";
	}
	
	public function get_format_data_structure(){
		$rss = new cms_module_common_datasource_rss();
		return array_merge($rss->get_format_data_structure(),parent::get_format_data_structure());
	}
}