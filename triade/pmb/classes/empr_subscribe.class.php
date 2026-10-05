<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_subscribe.class.php,v 1.2 2019/11/08 11:00:21 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class empr_subscribe extends empr_form {
    
    protected function get_template() {
        global $empr_subscribe_form;
        return $empr_subscribe_form;
    }
    
    public function save() {
        if (empty($this->empr_fields)) {
            return false;
        }
        pmb_mysql_query("TRUNCATE TABLE empr_renewal_form_fields");
        
        $values = array();
        foreach ($this->empr_fields as $empr_field_code => $options) {
            $values[] = "('".addslashes($empr_field_code)."', ".$options['display'].", ".$options['mandatory'].", ".$options['alterable'].", '".addslashes($options['explanation'])."')";
        }
        $query = "INSERT INTO empr_renewal_form_fields (empr_renewal_form_field_code, empr_renewal_form_field_display, empr_renewal_form_field_mandatory, empr_renewal_form_field_alterable, empr_renewal_form_field_explanation)
			VALUES ".implode(',', $values);
        pmb_mysql_query($query);
        return true;
    }
    
    protected function get_empr_fields_query() {
        $query = "SELECT empr_renewal_form_field_code, empr_renewal_form_field_display, empr_renewal_form_field_mandatory, empr_renewal_form_field_alterable, empr_renewal_form_field_explanation
				FROM empr_renewal_form_fields";
        return $query;
    }
}