<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_catalog_abts_form.class.php,v 1.1.4.5 2025/04/28 10:00:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_catalog_abts_form extends interface_catalog_form {
	
    protected $serial_id;
    
    protected $abt_status;
	
    protected function get_action_cancel_label() {
        global $msg;
        
        switch ($this->table_name) {
            case 'abts_abts':
                return $msg['bt_retour'];
            default:
                return parent::get_action_cancel_label();
        }
        
    }
    
    protected function get_cancel_action() {
        switch ($this->table_name) {
            case 'abts_abts':
                return "./catalog.php?categ=serials&sub=view&serial_id=".$this->serial_id."&view=abon";
            default:
                return parent::get_cancel_action();
        }
    }
    
    protected function get_submit_action() {
        switch ($this->table_name) {
            case 'abts_abts':
                return $this->get_url_base()."&act=update&abt_id=".$this->object_id;
            default:
                return parent::get_submit_action();
        }
    }
    
    protected function get_display_submit_action() {
        switch ($this->table_name) {
            case 'abts_abts':
                return "<input type='submit' class='bouton' name='save_button' id='save_button' value='".$this->get_action_save_label()."' onClick=\"this.form.action='".$this->get_submit_action()."';if(test_form(this.form)==true) this.form.submit();else return false;\" />";
            default:
                return parent::get_display_submit_action();
        }
    }
    
    protected function get_action_duplicate_label() {
        global $msg;
        
        switch ($this->table_name) {
            case 'abts_abts':
                return $msg['abts_abonnements_copy_abonnement'];
            default:
                return parent::get_action_duplicate_label();
        }
    }
    
    protected function get_display_duplicate_action() {
        switch ($this->table_name) {
            case 'abts_abts':
                return $this->get_display_action('duplicate_button', $this->get_action_duplicate_label(), ['function' => "duplique(this,event);"]);
            default:
                return parent::get_display_duplicate_action();
        }
    }
    
    protected function get_display_action($name, $label, $event=[], $attrs=[]) {
        global $msg, $charset;
        
        switch ($name) {
            case 'gen':
                return "<input type='submit' class='bouton' value='".htmlentities($label, ENT_QUOTES, $charset)."' onClick=\"if(confirm('".addslashes(str_replace("\"","&quot;",$msg['abonnements_confirm_gen_grille']))."')){this.form.action='".$event['location']."';if(test_form(this.form)==true) this.form.submit();else return false;} else return false;\"/>";
            case 'prolonge':
                return "<input type='submit' class='bouton' value='".htmlentities($label, ENT_QUOTES, $charset)."' onClick=\"this.form.action='".$event['location']."';if(test_form(this.form)==true) this.form.submit();else return false;\"/>";
            case 'raz':
                return "<input type='submit' class='bouton' value='".htmlentities($label, ENT_QUOTES, $charset)."' onClick=\"if(confirm('".$msg['confirm_raz_grille']."')){this.form.action='".$event['location']."';if(test_form(this.form)==true) this.form.submit();else return false;} else return false;\"/>";
            default:
                return parent::get_display_action($name, $label, $event, $attrs);
        }
    }
    
    protected function get_delete_action() {
        switch ($this->table_name) {
            case 'abts_abts':
                return $this->get_url_base()."&act=del&abt_id=".$this->object_id;
            default:
                return parent::get_delete_action();
        }
    }
    
	protected function get_display_label() {
	    global $msg;
	    
	    // la propriete label peut contenir du HTML
	    switch ($this->table_name) {
	        case 'abts_abts':
	            if ($this->object_id) {
	                return "
                    <div class='row'>
        				<div class='left'>
        					<h3>".$this->label."</h3>
        				</div>
        				<div class='right'>
        					<label for='abts_status' class='etiquette'>".$msg['empr_statut_menu']."</label>&nbsp;
                    		".abts_status::get_form_for($this->abt_status)."&nbsp;
        				</div>
        			</div>
                    <div class='row'></div>";
	            } else {
	                return "<h3>".$this->label."</h3>";
	            }
	            break;
	        default:
                return parent::get_display_label();
	    }
	}
	
	public function set_serial_id($serial_id) {
	    $this->serial_id = intval($serial_id);
	    return $this;
	}
	
	public function set_abt_status($abt_status) {
	    $this->abt_status = intval($abt_status);
	    return $this;
	}
	
	public function get_url_base() {
	    return parent::get_url_base().(!empty($this->serial_id) ? "&serial_id=".$this->serial_id : "");
	}
}