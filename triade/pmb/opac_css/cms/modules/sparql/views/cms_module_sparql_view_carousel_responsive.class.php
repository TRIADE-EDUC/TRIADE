<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sparql_view_carousel_responsive.class.php,v 1.1 2014/11/17 15:46:51 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_sparql_view_carousel_responsive extends cms_module_common_view_carousel_responsive{
	
	public function render($datas){
		$datas = array();
		$datas['records']=$data['result'];
		return parent::render($datas);
	}
}