<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_universe_form.class.php,v 1.1.6.1 2025/03/06 08:40:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

global $class_path;
require_once($class_path . '/interface/admin/interface_admin_form.class.php');

class interface_admin_universe_form extends interface_admin_form
{
    protected function get_display_duplicate_action()
	{
	    return $this->get_display_action('duplicate_button', $this->get_action_duplicate_label());
	}
    
    public function get_display_ajax()
	{
		global $current_module;

		$display = "
		<form class='form-" . $current_module . "' id='" . $this->name . "' name='" . $this->name . "'  method='post' action=\"" . $this->get_url_base() . "&action=save&id=" . $this->object_id . "\" >
			" . $this->get_display_label() . "	
			<div class='form-contenu'>
				" . $this->content_form . "
			</div>	
			<div class='row'>	
				<div class='left'>
                    ".$this->get_display_action('cancel_button', $this->get_action_cancel_label())."
					<input type='submit' class='bouton' name='save_button' id='save_button' value='" . $this->get_action_save_label() . "' />
					" . $this->get_display_duplicate_action() . "
				</div>
				<div class='right'>
					" . ($this->object_id ? $this->get_display_action('delete_button', $this->get_action_delete_label()) : "") . "
				</div>
			</div>
		<div class='row'></div>
		</form>";
		if (isset($this->table_name) && $this->table_name) {
			$translation = new translation($this->object_id, $this->table_name);
			$display .= $translation->connect($this->name);
		}
		if (isset($this->field_focus) && $this->field_focus) {
			$display .= "<script type='text/javascript'>document.forms['" . $this->name . "'].elements['" . $this->field_focus . "'].focus();</script>";
		}
		return $display;
	}
}