<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_music_form.class.php,v 1.2.6.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_marc_list.class.php");
require_once($class_path."/marc_table.class.php");

class selector_music_form extends selector_marc_list {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	protected function get_marc_list_instance() {
		global $s_music_form;
		if (!count($s_music_form)) {
			$s_music_form = new marc_list('music_form');
		}
		return $s_music_form;
	}
}
?>