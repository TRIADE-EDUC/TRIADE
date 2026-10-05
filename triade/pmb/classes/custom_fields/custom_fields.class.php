<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields.class.php,v 1.2.4.4 2025/02/20 15:18:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields {
	
    protected static function get_chk_values($name) {
        global ${$name};
        return ${$name};
    }
    
    protected static function has_chk_mandatory() {
        return false;
    }
    
    public static function chk($field,&$check_message) {
        global $msg;
        
        $name=$field['NAME'];
        global ${$name};
        $val=static::get_chk_values($name);
        
        if(static::has_chk_mandatory()) {
            if ($field['MANDATORY']==1) {
                if ((empty($val))||(!count($val))||((count($val)==1)&&($val[0]==""))) {
                    $check_message=sprintf($msg["parperso_field_is_needed"],$field['ALIAS']);
                    return 0;
                }
            }
        }
        
        $check_datatype_message = "";
        $val_1 = chk_datatype($field, $val, $check_datatype_message);
        if (!empty($check_datatype_message)) {
            $check_message = $check_datatype_message;
            return 0;
        }
        ${$name} = $val_1;
        return 1;
    }
    
//     public static function val($field, $value) {
        
//     }
    
    public static function get_formatted_label_aff_filter($label) {
        return $label;
    }
        
    public static function aff_filter($field,$varname,$multiple) {
        global $charset;
        
        $ret="<select id=\"".$varname."\" name=\"".$varname;
        $ret.="[]";
        $ret.="\" ";
        if ($multiple) $ret.="size=5 multiple";
        $ret.=" data-form-name='".$varname."' >\n";
        
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        $options=$field['OPTIONS'][0];
        if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
            $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\"";
            if ($options['UNSELECT_ITEM'][0]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
            $ret.=">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
        }
        $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
        while ($r=pmb_mysql_fetch_row($resultat)) {
            $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
            $as=array_search($r[0],$values);
            if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
            $ret.=">".htmlentities(static::get_formatted_label_aff_filter($r[0]),ENT_QUOTES,$charset)."</option>\n";
            
        }
        $ret.= "</select>\n";
        return $ret;
    }
    
    public static function get_rgaa_label($field, $varname) {
        return '';
    }
    
    public static function get_button_browse($field, $link) {
        global $msg;
        
        $onclick_event = "openPopUp('".$link."', 'select_perso_".$field['ID']."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')";
//         return "<input type='button' class='bouton' value='...' onclick=\"".$onclick_event."\" />";
        $interface_node_button = new interface_node_button();
        $interface_node_button->set_value($msg['custom_fields_browse_button'])
        ->set_onclick($onclick_event)
        ->set_aria_label(sprintf($msg['custom_fields_browse_aria_label'], $field['ALIAS']));
        return $interface_node_button->get_display();
    }
    
    public static function get_button_add($field, $onclick_event=''){
        global $msg;
        
//         return "<input type='button' class='bouton' value='+' onclick=\"".$onclick_event."\" />";
        $interface_node_button = new interface_node_button();
        $interface_node_button->set_value($msg['custom_fields_add_button'])
        ->set_onclick($onclick_event)
        ->set_aria_label(sprintf($msg['custom_fields_add_aria_label'], $field['ALIAS']));
        return $interface_node_button->get_display();
    }
    
    public static function get_button_raz($field, $onclick_event='') {
        global $msg;
        
//         return "<input type='button' class='bouton' value='X' onclick=\"".$onclick_event."\" />";
        $interface_node_button = new interface_node_button();
        $interface_node_button->set_value($msg['custom_fields_raz_button'])
        ->set_onclick($onclick_event)
        ->set_aria_label(sprintf($msg['custom_fields_raz_aria_label'], $field['ALIAS']));
        return $interface_node_button->get_display();
    }
    
}