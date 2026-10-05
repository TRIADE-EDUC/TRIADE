<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_sections_type_section.class.php,v 1.1 2019/03/19 14:38:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class docwatch_selector_sections_type_section extends docwatch_selector_editorial_type {
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->docwatch_selector_editorial_type="section";
	}
}
