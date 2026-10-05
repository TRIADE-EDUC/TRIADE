<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_text_i18n.class.php,v 1.2 2024/01/18 13:31:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_text_i18n extends custom_fields {
	
    protected static function get_chk_values($name) {
        global ${$name};
        global ${$name}, ${$name."_langs"};
        $val=${$name};
        $langs = (${$name."_langs"});
        $final_value = array();
        if (isset($val) && is_array($val)) {
            foreach ($val as $key => $value) {
                if ($value) {
                    $final_value[] = $value."|||".($langs[$key] ? $langs[$key] : '');
                }
            }
        }
        return $final_value;
    }
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $langue_doc = get_langue_doc();
        $value=format_output($field,$value);
        if (!$value) {
            $value=array();
        }
        $formatted_values = array();
        foreach ($value as $val) {
            $exploded_val = explode("|||", $val);
            $formatted_values[] = $exploded_val[0]." ".($exploded_val[1] ? "(".$langue_doc[$exploded_val[1]].")" : '');
        }
        
        if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) {
            $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
        }
        if($field["OPTIONS"][0]["ISHTML"][0]["value"]){
            return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$formatted_values), "withoutHTML" =>implode($pmb_perso_sep,$formatted_values));
        }else{
            return implode($pmb_perso_sep,$formatted_values);
        }
    }
    
    public static function aff($field,&$check_scripts) {
        global $charset, $base_path;
        global $msg;
        
        $langue_doc = get_langue_doc();
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        $afield_name = $field["ID"];
        $ret = "";
        $count = 0;
        if (!$values) {
            if(isset($options['DEFAULT_LANG'][0]['value']) && $options['DEFAULT_LANG'][0]['value']) {
                $values = array("|||".$options['DEFAULT_LANG'][0]['value']);
            } else {
                $values = array("");
            }
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= get_js_function_dnd('text_i18n', $field['NAME']);
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_text_i18n_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
        }
        foreach ($values as $value) {
            $exploded_value = explode("|||", $value);
            $display_temp ="<input id=\"".$field['NAME']."_".$count."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[]\" data-form-name='".$field["NAME"]."_' value=\"".htmlentities($exploded_value[0],ENT_QUOTES,$charset)."\">";
            $display_temp.="&nbsp;".$msg['param_perso_lang_select']." : <input id=\"".$field['NAME']."_lang_".$count."\" class=\"saisie-10emr\" type=\"text\" value=\"".(isset($exploded_value[1]) && $exploded_value[1] ? htmlentities($langue_doc[$exploded_value[1]],ENT_QUOTES,$charset) : '')."\" autfield=\"".$field['NAME']."_lang_code_".$count."\" completion=\"langue\" autocomplete=\"off\" data-form-name='".$field["NAME"]."_lang_' >";
            $display_temp.="<input class=\"bouton\" type=\"button\" value=\"...\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$field['NAME']."_lang_code_".$count."&p2=".$field['NAME']."_lang_".$count."', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
            $display_temp.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$field['NAME']."_lang_".$count.".value=''; this.form.".$field['NAME']."_lang_code_".$count.".value=''; \" value=\"X\">";
            $display_temp.="<input id=\"".$field['NAME']."_lang_code_".$count."\" data-form-name='".$field["NAME"]."_lang_code_' type=\"hidden\" value=\"".(isset($exploded_value[1]) && $exploded_value[1] ? htmlentities($exploded_value[1], ENT_QUOTES, $charset) : '')."\" name=\"".$field['NAME']."_langs[".$count."]\">";
            if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
                $button_add = '';
                if(end($values) == $value) {
                    $button_add='<input id="button_add_'.$field['NAME'].'_'.$field['ID'].'" class="bouton" type="button" value="+" onclick="add_custom_text_i18n_(\''.$field['ID'].'\', \''.addslashes($field['NAME']).'\','.addslashes($options['SIZE'][0]['value']).','.addslashes($options['MAXSIZE'][0]['value']).')"/>';
                }
                $ret.=get_block_dnd('text_i18n', $field['NAME'], $count, $display_temp.$button_add, $exploded_value[0]);
            } else {
                $ret.=$display_temp."<br />";
            }
            $count++;
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.='<input id="customfield_text_i18n_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.$count.'">';
            $ret .= '<div id="spaceformorecustomfieldtexti18n_'.$afield_name.'"></div>';
            $ret .= get_custom_dnd_on_add();
            $ret.="<script>
			function add_custom_text_i18n_(field_id, field_name, field_size, field_maxlen) {
		        var count = document.getElementById('customfield_text_i18n_'+field_id).value;
                
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfieldtexti18n_'+field_id, 'customfield_text_i18n_'+field_name, count);
				var buttonAdd = document.getElementById('button_add_'+ field_name + '_' + field_id);
                
				var text = document.createElement('input');
				text.setAttribute('id', field_name + '_' + count);
		        text.setAttribute('name',field_name+'[' + count + ']');
		        text.setAttribute('type','text');
		        text.setAttribute('value','');
		        text.setAttribute('size',field_size);
		        text.setAttribute('maxlength',field_maxlen);
                
				var lang_label = document.createTextNode(' ".$msg['param_perso_lang_select']." : ');
				    
				var lang = document.createElement('input');
				lang.setAttribute('id', field_name + '_lang_' + count);
				lang.setAttribute('class', 'saisie-10emr');
				lang.setAttribute('type', 'text');
				lang.setAttribute('value', \"".(isset($exploded_value[1]) && $exploded_value[1] ? htmlentities($langue_doc[$exploded_value[1]],ENT_QUOTES,$charset) : '')."\");
				lang.setAttribute('autfield', field_name + '_lang_code_' + count);
				lang.setAttribute('completion', 'langue');
				lang.setAttribute('autocomplete', 'off');
				    
				var select = document.createElement('input');
				select.setAttribute('class', 'bouton');
				select.setAttribute('type', 'button');
				select.setAttribute('value', '...');
				select.addEventListener('click', function(){
					openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=' + field_name + '_lang_code_' + count + '&p2=' + field_name + '_lang_' + count, 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes');
				}, false);
					    
				var del = document.createElement('input');
				del.setAttribute('class', 'bouton');
				del.setAttribute('type', 'button');
				del.setAttribute('value', 'X');
				del.addEventListener('click', function(){
					document.getElementById(field_name + '_lang_' + count).value=''; document.getElementById(field_name + '_lang_code_' + count).value='';
				}, false);
					    
				var lang_code = document.createElement('input');
				lang_code.setAttribute('id', field_name + '_lang_code_' + count);
				lang_code.setAttribute('type', 'hidden');
				lang_code.setAttribute('value', \"".(isset($exploded_value[1]) && $exploded_value[1] ? $exploded_value[1] : '')."\");
				lang_code.setAttribute('name', field_name + '_langs[' + count + ']');
				    
		        var space_br=document.createElement('br');
				    
				document.getElementById(node_dnd_id).appendChild(text);
				document.getElementById(node_dnd_id).appendChild(document.createTextNode(' '));
				document.getElementById(node_dnd_id).appendChild(lang_label);
				document.getElementById(node_dnd_id).appendChild(lang);
				document.getElementById(node_dnd_id).appendChild(select);
				document.getElementById(node_dnd_id).appendChild(del);
				document.getElementById(node_dnd_id).appendChild(buttonAdd);
				document.getElementById(node_dnd_id).appendChild(lang_code);
				document.getElementById(node_dnd_id).appendChild(space_br);
				    
				document.getElementById('customfield_text_i18n_'+field_id).value = document.getElementById('customfield_text_i18n_'+field_id).value * 1 + 1;
				ajax_pack_element(lang);
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
        global $msg;
        global $base_path;
        
        $langue_doc = get_langue_doc();
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if(!is_array($values)) {
            $values = array(
                'text' => '',
                'lang' => ''
            );
        }
        $ret="<input id=\"".$varname."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" name=\"".$varname."[0][text]\" value=\"".htmlentities($values[0]['text'],ENT_QUOTES,$charset)."\">";
        $ret.="&nbsp;".$msg['param_perso_lang_select']." : <input id=\"".$varname."_lang\" class=\"saisie-10emr\" type=\"text\" value=\"".($values[0]['lang'] ? htmlentities($langue_doc[$values[0]['lang']],ENT_QUOTES,$charset) : '')."\" autfield=\"".$varname."_lang_code\" completion=\"langue\" autocomplete=\"off\" >";
        $ret.="<input class=\"bouton\" type=\"button\" value=\"".$msg['parcourir']."\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$varname."_lang_code&p2=".$varname."_lang', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
        $ret.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$varname."_lang.value=''; this.form.".$varname."_lang_code.value=''; \" value=\"".$msg['raz']."\">";
        $ret.="<input id=\"".$varname."_lang_code\" type=\"hidden\" value=\"".($values[0]['lang'] ? htmlentities($values[0]['lang'], ENT_QUOTES, $charset) : '')."\" name=\"".$varname."[0][lang]\">";
        return $ret;
    }
}