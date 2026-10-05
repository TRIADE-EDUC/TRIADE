<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_selector_interesting.class.php,v 1.1 2022/05/03 09:30:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_selector_interesting extends cms_module_common_selector {
	
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_form() {
		$form = parent::get_form();
		$form .= "
			<div class='row'>
                <div class='colonne3'>
                    &nbsp;
                </div>
                <div class='colonne-suite'>
                    " . $this->format_text($this->msg['cms_module_itemslist_selector_interesting_yes']) . "
                 </div>
            </div>";
		
		return $form;
	}
}