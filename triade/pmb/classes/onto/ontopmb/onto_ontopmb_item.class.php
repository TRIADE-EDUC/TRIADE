<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_item.class.php,v 1.1 2015-08-10 23:16:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/ontopmb/onto_ontopmb_item.tpl.php');

class onto_ontopmb_item extends onto_common_item {
	
	
	public function replace_temp_uri(){
		if(onto_common_uri::is_temp_uri($this->get_uri())){
			$this->uri = onto_common_uri::replace_temp_uri($this->get_uri(),$this->onto_class->uri,$this->onto_class->get_base_uri()."#".$this->onto_class->pmb_name);
		}
	}
}