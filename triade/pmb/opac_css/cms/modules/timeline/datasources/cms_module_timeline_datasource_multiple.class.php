<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_timeline_datasource_multiple.class.php,v 1.4 2017-10-17 10:22:11 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_timeline_datasource_multiple extends cms_module_common_datasource_multiple {
	
	
	public function get_available_datasources(){
		return array(
			'cms_module_timeline_datasource_records',
			'cms_module_timeline_datasource_works',
			'cms_module_timeline_datasource_authors',
			'cms_module_timeline_datasource_articles'
		);
	}
	
	public function get_datas(){
		$datas = parent::get_datas();
		$this->debug($datas,CMS_DEBUG_MODE_CONSOLE);
		return $datas;
	}
}