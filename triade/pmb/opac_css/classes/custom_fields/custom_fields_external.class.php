<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_external.class.php,v 1.2.4.1 2025/02/12 09:59:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_external extends custom_fields {
    
    public static function val($field, $value) {
        $options=$field['OPTIONS'][0];
        $value=format_output($field,$value);
        //Calcul du libelle
        if ($options["QUERY"][0]["value"]) {
            $rvalues=pmb_mysql_query(str_replace("!!id!!",$value[0],$options["QUERY"][0]["value"]));
            if ($rvalues) {
                return pmb_mysql_result($rvalues,0,0);
            }
        }
        return $value[0];
    }
    
    public static function aff($field,&$check_scripts) {
        global $charset;
        global $msg;
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        //Recherche du libellé
        $vallib=$values[0];
        if ($options["QUERY"][0]["value"]) {
            $rvalues=pmb_mysql_query(str_replace("!!id!!",$values[0],$options["QUERY"][0]["value"]));
            if ($rvalues) {
                $vallib=@pmb_mysql_result($rvalues,0,0);
            }
        }
        $ret="<input id=\"".$field["NAME"]."\" type=\"hidden\" name=\"".$field["NAME"]."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
        if (!$options["HIDE"][0]["value"]) {
            $ret.="<input id=\"".$field["NAME"]."_lib\" type=\"text\" readonly='readonly' size=\"".$options["SIZE"][0]["value"]."\" maxlength=\"".$options["MAXSIZE"][0]["value"]."\" name=\"".$field["NAME"]."_lib[]\" value=\"".htmlentities($vallib,ENT_QUOTES,$charset)."\">";
        }
        $ret.="&nbsp;<input type='button' id='".$field["NAME"]."_button' name='".$field["NAME"]."_button' class='bouton' value='".(($vallib&&($options["HIDE"][0]["value"]))?htmlentities($vallib,ENT_QUOTES,$charset):($options["BUTTONTEXT"][0]["value"]?htmlentities($options["BUTTONTEXT"][0]["value"],ENT_QUOTES,$charset):$msg["parperso_external_browse"]))."' onClick='openPopUp(\"".$options["URL"][0]["value"]."?field_val=".$field["NAME"]."&"."field_lib=".($options["HIDE"][0]["value"]?$field["NAME"]."_button":$field["NAME"]."_lib")."\",\"w_".$field["NAME"]."\",".($options["WIDTH"][0]["value"]?$options["WIDTH"][0]["value"]:"400").",".($options["HEIGHT"][0]["value"]?$options["HEIGHT"][0]["value"]:"600").",-2,-2,\"infobar=no, status=no, scrollbars=yes, menubar=no\");'/>";
        if ($options["DELETE"][0]["value"]) $ret.="&nbsp;<input type='button' class='bouton' value='X' onClick=\"document.getElementById('".$field["NAME"]."').value=''; document.getElementById('".($options["HIDE"][0]["value"]?$field["NAME"]."_button":$field["NAME"]."_lib")."').value='".($options["HIDE"][0]["value"]?($options["BUTTONTEXT"][0]["value"]?htmlentities($options["BUTTONTEXT"][0]["value"],ENT_QUOTES,$charset):$msg["parperso_external_browse"]):"")."';\"/>";
        if ($field["MANDATORY"]==1) {
            $caller = get_form_name();
            $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field["NAME"]."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field["ALIAS"])."\");\n";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $charset;
        global $msg;
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        //Recherche du libellé
        $vallib=$values[0];
        if ($options["QUERY"][0]["value"]) {
            $rvalues=pmb_mysql_query(str_replace("!!id!!",$values[0],$options["QUERY"][0]["value"]));
            if ($rvalues) {
                $vallib = pmb_mysql_result($rvalues,0,0);
            }
        }
        $ret="<input id=\"".$varname."\" type=\"hidden\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
        $ret.="<input id=\"".$varname."_lib\" type=\"text\" name=\"".$varname."_lib[]\" readonly=\"readonly\" value=\"".htmlentities($vallib,ENT_QUOTES,$charset)."\">";
        $ret.="&nbsp;<input type='button' name='".$varname."_button' class='bouton' value='".($options["BUTTONTEXT"][0]["value"]?$options["BUTTONTEXT"][0]["value"]:$msg["parperso_external_browse"])."' onClick='openPopUp(\"".$options["URL"][0]["value"]."?field_val=".$varname."&"."field_lib=".$varname."_lib"."\",\"w_".$varname."\",".($options["WIDTH"][0]["value"]?$options["WIDTH"][0]["value"]:"400").",".($options["HEIGHT"][0]["value"]?$options["HEIGHT"][0]["value"]:"600").",-2,-2,\"\");'/>";
        return $ret;
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $field["NAME"];
    }
}