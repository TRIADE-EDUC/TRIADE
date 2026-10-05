<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watch_view_django.class.php,v 1.5 2023/05/03 15:15:15 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watch_view_django extends cms_module_common_view_django{
	
	public function __construct($id=0){
		global $charset;
		
		parent::__construct($id);
		$this->default_template = "
{% if watch.logo_url %}
<img src='{{watch.logo_url}}' alt='{{watch.title}}'/>
{% endif %}
<h3>{{watch.title}}</h3>
<a href='{{watch.rss_link}}' title='RSS' target='_blank'><img src='".get_url_icon('rss.png')."' alt='RSS'></a>
<blockquote>{{watch.desc}}</blockquote>
<blockquote>Dernière mise à jour le {{watch.last_date}}</blockquote>
";
		if ($charset=="utf-8") {
			$this->default_template = encoding_normalize::utf8_normalize($this->default_template);
		}
	}

	public function render($datas){
		$render_datas = array();
		$render_datas['watch'] = array();
		$render_datas['watch'] = $datas;
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_watch_datasource_watch();
		$datas = $datasource->get_format_data_structure();
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}