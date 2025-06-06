<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_article.class.php,v 1.6 2018-08-24 08:44:59 plmrozowski Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_article extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<h3>{{title}}</h3>
<p>{{resume}}</p>
<img src='{{logo.large}}' alt=''/>
<p>{{content}}</p>";
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_common_datasource_article();
		return $datasource->get_format_data_structure();
	}
}