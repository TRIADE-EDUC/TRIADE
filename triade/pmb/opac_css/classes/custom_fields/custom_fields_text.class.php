<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_text.class.php,v 1.2.4.1 2025/02/12 09:43:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_text extends custom_fields {
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $value=format_output($field,$value);
        if (!$value) {
            $value=array();
        }
        if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) {
            $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
        }
        if($field["OPTIONS"][0]["ISHTML"][0]["value"]){
            return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
        }else{
            return implode($pmb_perso_sep,$value);
        }
    }
    
    public static function aff($field,&$check_scripts) {
        global $charset;
        global $msg;
        
        $options=$field['OPTIONS'][0];
        $values = array();
        if(!isset($field['VALUES'][0])) {
            $values[0] = '';
        } else {
            $values=$field['VALUES'];
        }
        $ret="<input id=\"".$field['NAME']."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
        if ($field['MANDATORY']==1) {
            $check_scripts.="if (document.forms[0].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $charset;
        
        $values=$field['VALUES'];
        if(!isset($values[0])) {
            $values[0] = '';
        }
        $ret="<input id=\"".$varname."\" type=\"text\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
        return $ret;
    }
    
    public static function get_formatted_label_aff_filter($label) {
        return cutlongwords($label);
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $varname;
    }
}