<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_html.class.php,v 1.2.4.1 2025/02/12 09:43:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_html extends custom_fields {
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $value=format_output($field, $value);
        if (!$value) {
            $value=array();
        }
        return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
    }
    
    public static function aff($field,&$check_scripts) {
        global $msg;
        global $cms_dojo_plugins_editor;
        
        $values=$field['VALUES'];
        $ret="<input type='hidden' name='".$field['NAME']."[]' value=''/>
	<div data-dojo-type='dijit/Editor' $cms_dojo_plugins_editor	id='".$field['NAME']."' class='saisie-80em' wrap='virtual'>".$values[0]."</div>";
        $check_scripts.= "
	if(document.forms[0].elements['".$field['NAME']."[]']) document.forms[0].elements['".$field['NAME']."[]'].value = dijit.byId('".$field['NAME']."').get('value');";
        if ($field['MANDATORY']==1) {
            $caller = get_form_name();
            $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
        }
        return $ret;
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $field['NAME'];
    }
}