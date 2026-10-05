<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_charte.class.php,v 1.2 2019/11/27 15:36:54 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_charte extends cms_module_common_module {
	
	public function __construct($id=0){
		$this->module_path = str_replace(basename(__FILE__),"",__FILE__);
		parent::__construct($id);
	}
	
	protected function get_modcache_choices(){
	    return array(
	        array(
	            'value' => "no_cache",
	            'name' => $this->msg['cms_module_common_module_no_cache']
	        )
	    );
	}
}