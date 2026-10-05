<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_circ_note_ex_form.class.php,v 1.1.4.2 2025/05/30 12:00:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/circ/interface_circ_form.class.php');

class interface_circ_note_ex_form extends interface_circ_form {
	
    protected function get_display_label() {
        global $msg;
        global $cb;
        
        return "
            <h3>".$this->label."</h3>
            <b>$msg[232] : <strong>".stripslashes($cb)."</strong></b>";
    }
    
	protected function get_cancel_action() {
	    global $cb;
	    return $this->get_url_base()."&form_cb_expl=".$cb;
	}
	
	protected function get_submit_action() {
	    global $cb;
	    return $this->get_url_base()."&cb=".rawurlencode(stripslashes($cb))."&id=".$this->object_id."&action=submit";
	}
	
	protected function get_display_submit_action() {
        return "<input type='submit' class='bouton' name='save_button' id='save_button' value='".$this->get_action_save_label()."' />";
	}
	
	protected function get_display_delete_action() {
	    return "";
	}
}