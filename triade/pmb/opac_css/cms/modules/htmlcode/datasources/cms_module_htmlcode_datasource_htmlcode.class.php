<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_htmlcode_datasource_htmlcode.class.php,v 1.1 2012/06/01 15:21:48 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_htmlcode_datasource_htmlcode extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		return $this->format_text($this->msg['cms_module_root_no_parameters']);
	}
	
}