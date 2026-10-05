<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_form.class.php,v 1.4.8.2 2024/06/28 14:12:42 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$include_path/templates/empr_form.tpl.php");

class empr_form {
    
    protected $empr_fields;
    protected $active;
    protected $replace_with_mail;
    
    public function get_form() {
        global $empr_form_row;
        global $charset;
        global $empr_username_with_mail;
        
        $html = $this->get_template();
        
        $search = array(
            "!!empr_renewal_form_field_code!!",
            "!!empr_renewal_form_fieldname!!",
            "!!empr_renewal_form_display!!",
            "!!empr_renewal_form_mandatory!!",
            "!!empr_renewal_form_alterable!!",
            "!!empr_renewal_form_explanation!!",
            "!!empr_renewal_form_force_mandatory!!",
        );
        
        $this->get_empr_fields();
        //$this->sort_empr_fields();
        $form_rows = '';
        foreach ($this->empr_fields as $empr_field_code => $options) {
            $replace = array(
                htmlentities($empr_field_code, ENT_QUOTES, $charset),
                htmlentities($options['field_label'], ENT_QUOTES, $charset),
                ($options['display'] ? "checked='checked'" : ""),
                ($options['mandatory'] || $options['force_mandatory'] ? "checked='checked'" : ""),
                ($options['alterable'] ? "checked='checked'" : ""),
                htmlentities($options['explanation'], ENT_QUOTES, $charset),
                ($options['force_mandatory'] ? "readonly='readonly' onclick='return false;'" : ""),
            );
            $form_rows.= str_replace($search, $replace, $empr_form_row);
        }
        $html = str_replace('!!empr_renewal_form_rows!!', $form_rows, $html);

        $checked = '';
        if (intval($empr_username_with_mail) === 1) {
	        $checked = 'checked="checked"';
	    }
        $html = str_replace('!!replace_username_with_mail!!', $checked, $html);
        
        return $html;
    }
    
    protected function get_template() {
        global $empr_renewal_form;
        return $empr_renewal_form;
    }
    
    public function get_from_form() {
        global $renewal_activate, $replace_username_with_mail;
        
        $this->active = (intval($renewal_activate) ? 1 : 0);
        $this->replace_with_mail = (intval($replace_username_with_mail) ? 1 : 0);

        $this->get_empr_fields();
        $empr_fields_codes = array_keys($this->empr_fields);
        foreach($empr_fields_codes as $empr_field_code) {
            global ${$empr_field_code};
            $this->empr_fields[$empr_field_code]['display'] = (!empty(${$empr_field_code}['display']) ? 1 : 0);
            $this->empr_fields[$empr_field_code]['mandatory'] = (!empty(${$empr_field_code}['mandatory']) || $this->empr_fields[$empr_field_code]['force_mandatory'] ? 1 : 0);
            $this->empr_fields[$empr_field_code]['alterable'] = (!empty(${$empr_field_code}['alterable']) ? 1 : 0);
            $this->empr_fields[$empr_field_code]['explanation'] = stripslashes(${$empr_field_code}['explanation']);
        }
    }
    
    public function save() {
        global $empr_renewal_activate, $empr_username_with_mail;
        
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
        
        $query = "UPDATE parametres SET valeur_param=" . $this->active . " WHERE type_param='empr' and sstype_param='renewal_activate' ";
        pmb_mysql_query($query);
        $empr_renewal_activate = $this->active;

        $query = "UPDATE parametres SET valeur_param=" . $this->replace_with_mail . " WHERE type_param='empr' and sstype_param='username_with_mail' ";
        pmb_mysql_query($query);
        $empr_username_with_mail = $this->replace_with_mail;

        return true;
    }
    
    public function get_empr_fields() {
        global $msg, $empr_birthdate_optional;
        
        if (!empty($this->empr_fields)) {
            return $this->empr_fields;
        }
        
        $this->empr_fields = array(
            'empr_nom' => array('field_label' => $msg['67'], 'display' => 1, 'mandatory' => 1, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 1),
            'empr_prenom' => array('field_label' => $msg['68'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_adr1' => array('field_label' => $msg['69'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_adr2' => array('field_label' => $msg['70'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_cp' => array('field_label' => $msg['71'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_ville' => array('field_label' => $msg['72'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_pays' => array('field_label' => $msg['146'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_mail' => array('field_label' => $msg['58'], 'display' => 1, 'mandatory' => 1, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 1),
            'empr_tel1' => array('field_label' => $msg['73'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_tel2' => array('field_label' => $msg['73tel2'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_prof' => array('field_label' => $msg['74'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_year' => array('field_label' => $msg['75'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => ($empr_birthdate_optional ? 0 : 1)),
            'empr_categ' => array('field_label' => $msg['59'], 'display' => 0, 'mandatory' => 1, 'alterable' => 0, 'explanation' => '', 'force_mandatory' => 1),
            'empr_codestat' => array('field_label' => $msg['60'], 'display' => 0, 'mandatory' => 1, 'alterable' => 0, 'explanation' => '', 'force_mandatory' => 1),
            'empr_sexe' => array('field_label' => $msg['125'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_login' => array('field_label' => $msg['empr_login'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_lang' => array('field_label' => $msg['empr_langue_opac'], 'display' => 1, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
            'empr_location' => array('field_label' => $msg['empr_location'], 'display' => 0, 'mandatory' => 1, 'alterable' => 0, 'explanation' => '', 'force_mandatory' => 1),
            'empr_msg' => array('field_label' => $msg['empr_msg'], 'display' => 0, 'mandatory' => 0, 'alterable' => 1, 'explanation' => '', 'force_mandatory' => 0),
        );
        
        $pperso = new parametres_perso('empr');
        foreach ($pperso->t_fields as $field) {
            $this->empr_fields[$field['NAME']] = array(
                'field_label' => $field['TITRE'],
                'display' => $field['OPAC_SHOW'],
                'mandatory' => $field['MANDATORY'],
                'alterable' => ($field['MANDATORY'] ? 0 : 1),
                'explanation' => '',
                'force_mandatory' => $field['MANDATORY'],
            );
        }
        
        $query = $this->get_empr_fields_query();
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if (isset($this->empr_fields[$row['empr_renewal_form_field_code']])) {
                    $this->empr_fields[$row['empr_renewal_form_field_code']]['display'] = $row['empr_renewal_form_field_display'];
                    $this->empr_fields[$row['empr_renewal_form_field_code']]['mandatory'] = $row['empr_renewal_form_field_mandatory'];
                    $this->empr_fields[$row['empr_renewal_form_field_code']]['alterable'] = $row['empr_renewal_form_field_alterable'];
                    $this->empr_fields[$row['empr_renewal_form_field_code']]['explanation'] = $row['empr_renewal_form_field_explanation'];
                }
            }
        }
        
        return $this->empr_fields;
    }
    
    protected function get_empr_fields_query() {
        $query = "SELECT empr_renewal_form_field_code, empr_renewal_form_field_display, empr_renewal_form_field_mandatory, empr_renewal_form_field_alterable, empr_renewal_form_field_explanation
				FROM empr_renewal_form_fields";
        return $query;
    }
    
    protected function sort_empr_fields() {
        uasort($this->empr_fields, function($a, $b) {
            if ($a['field_label'] < $b['field_label']) {
                return -1;
            }
            if ($a['field_label'] > $b['field_label']) {
                return 1;
            }
            return 0;
        });
    }
}