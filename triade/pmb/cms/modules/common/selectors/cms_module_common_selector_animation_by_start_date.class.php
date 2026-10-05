<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_animation_by_start_date.class.php,v 1.1 2023/09/20 14:52:16 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_animation_by_start_date extends cms_module_common_selector
{

    public function get_form()
    {
        return "
		<div class='row'>
			<div class='colonne3'>
                <label>
                   {$this->format_text($this->msg['cms_module_common_selector_animation_by_start_date_label'])}
				</label>
			</div>
		</div>";
    }

    public function save_form() {
        $this->parameters["start_date_filter"] = true;

        return parent::save_form();
    }

}