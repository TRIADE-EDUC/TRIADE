<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_records.class.php,v 1.7 2020/12/17 13:54:55 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/notice.class.php");

class vedette_records extends vedette_element{
	
	protected $type = TYPE_NOTICE;

	public function set_vedette_element_from_database(){
		$this->entity = new notice($this->id);
		$this->isbd = notice::get_notice_title($this->id);
	}
	
	public function get_link_see(){
		return str_replace("!!type!!", "records", $this->get_generic_link());
	}
}
