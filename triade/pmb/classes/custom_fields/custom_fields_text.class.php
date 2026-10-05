<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_text.class.php,v 1.2.4.1 2025/02/12 09:43:45 dgoron Exp $

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
        $values=$field['VALUES'];
        $afield_name = $field["ID"];
        $ret = "";
        $count = 0;
        if (!$values) {
            $values = array("");
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= get_js_function_dnd('text', $field['NAME']);
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_text_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
        }
        foreach ($values as $avalues) {
            $display_temp = "<input id=\"".$field['NAME'].$count."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[]\" data-form-name='".$field['NAME']."' value=\"".htmlentities($avalues,ENT_QUOTES,$charset)."\">";
            
            if(!empty($options['REPEATABLE'][0]['value'])) {
                $button_add = '';
                if (end($values) == $avalues) {
                    $button_add = '<input id="button_add_'.$field['NAME'].'_'.$field['ID'].'" class="bouton" type="button" value="+" onclick="add_custom_text_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
                }
                $ret.=get_block_dnd('text', $field['NAME'], $count, $display_temp.$button_add, $avalues);
            } else {
                $ret.=$display_temp."<br />";
            }
            $count++;
        }
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.='<input id="customfield_text_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.(count($values)).'">';
            $ret .= '<div id="spaceformorecustomfieldtext_'.$afield_name.'"></div>';
            $ret.=get_custom_dnd_on_add();
            $ret.="<script>
			function add_custom_text_(field_id, field_name, field_size, field_maxlen) {
				document.getElementById('customfield_text_'+field_id).value = document.getElementById('customfield_text_'+field_id).value * 1 + 1;
		        count = document.getElementById('customfield_text_'+field_id).value;
                
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfieldtext_'+field_id, 'customfield_text_'+field_name, (count-1));
				var buttonAdd = document.getElementById('button_add_'+ field_name + '_' + field_id);
                
				f_aut0 = document.createElement('input');
		        f_aut0.setAttribute('name',field_name+'[]');
		        f_aut0.setAttribute('id',field_name+(count-1));
		        f_aut0.setAttribute('data-form-name',field_name);
		        f_aut0.setAttribute('type','text');
		        f_aut0.setAttribute('size',field_size);
		        f_aut0.setAttribute('maxlength',field_maxlen);
		        f_aut0.setAttribute('value','');
		        space=document.createElement('br');
				document.getElementById(node_dnd_id).appendChild(f_aut0);
				if (buttonAdd) document.getElementById(node_dnd_id).appendChild(buttonAdd);
				document.getElementById(node_dnd_id).appendChild(space);
			}
		</script>";
        }
        if ($field['MANDATORY']==1) {
            $caller = get_form_name();
            $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $charset;
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if(!isset($values[0])) {
            $values[0] = '';
        }
        $ret="<input id=\"".$varname."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
        return $ret;
    }
    
    public static function get_formatted_label_aff_filter($label) {
        return cutlongwords($label);
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $varname;
    }
}