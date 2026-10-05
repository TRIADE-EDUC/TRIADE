<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_rsslist.class.php,v 1.2 2021/04/29 13:49:16 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_rsslist extends cms_module_common_module {
	
	public function __construct($id = 0) {
		$this->module_path = str_replace(basename(__FILE__), "", __FILE__);
		parent::__construct($id);
		if (empty($this->id)) {
			$this->modcache = "no_cache";
		}
	}
}