<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_breadcrumb_datasource_page_opac.class.php,v 1.1.2.2 2025/02/25 13:40:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_breadcrumb_datasource_page_opac extends cms_module_common_datasource_list{
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		return array();
		
	}
}