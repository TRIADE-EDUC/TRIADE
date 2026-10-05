<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_rsslist.class.php,v 1.2 2021/04/29 13:49:16 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_rsslist extends cms_module_common_view_django {
	
	public function __construct($id = 0) {
		parent::__construct($id);
		
		$this->default_template = "
{% for rss in rsslist %}
    <h2>{{ rss.title }}</h2>
    <p>{{ rss.description }}</p>
    {% for item in rss.items %}
        <blockquote>
            <h4><a href='{{ item.link }}' target='_blank'>{{ item.title }}</a></h4>
            <p>{{ item.description.0 }}</p>
        </blockquote>
    {% endfor %}
{% endfor %}";
	}
	
	public function get_format_data_structure() {
	    $rss = new cms_module_common_datasource_rsslist();
	    
	    return array_merge($rss->get_format_data_structure(), parent::get_format_data_structure());
	}
}