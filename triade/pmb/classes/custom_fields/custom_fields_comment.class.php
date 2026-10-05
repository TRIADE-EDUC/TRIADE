<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_comment.class.php,v 1.2.4.1 2025/02/12 09:43:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_comment extends custom_fields {
	
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $value=format_output($field,$value);
        if (!$value) {
            $value=array();
        }
        
        if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) {
            $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
        }
        if($field["OPTIONS"][0]["ISHTML"][0]["value"]) {
            return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
        }else{
            return implode($pmb_perso_sep,$value);
        }
    }
    
    public static function aff($field,&$check_scripts) {
        global $charset;
        global $msg;
        
        $options = $field['OPTIONS'][0];
        $values = $field['VALUES'];
        $afield_name = $field["ID"];
        $ret = "";
        
        $count = 0;
        if (!$values) {
            $values = array("");
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= get_js_function_dnd('comment', $field['NAME']);
            $ret .= '<span style="vertical-align:top"><input class="bouton" type="button" value="+" onclick="add_custom_comment_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\','.$options['ROWS'][0]['value'].','.$options['COLS'][0]['value'].','.$options['MAXSIZE'][0]['value'].'); "/></span>';
        }
        foreach ($values as $avalues) {
            $display_temp = "<textarea id=\"".$field['NAME']."_".$count."\" cols=\"".$options['COLS'][0]['value']."\"  rows=\"".$options['ROWS'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[]\" data-form-name='".$field['NAME']."_' wrap=virtual>".htmlentities($avalues,ENT_QUOTES,$charset)."</textarea>";
            if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
                $button_add = '';
                if(end($values) == $avalues) {
                    $button_add='<input id="button_add_'.$field['NAME'].'_'.$field['ID'].'" class="bouton" type="button" value="+" onclick="add_custom_comment_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\','.$options['ROWS'][0]['value'].','.$options['COLS'][0]['value'].','.$options['MAXSIZE'][0]['value'].')"/>';
                }
                $ret.=get_block_dnd('comment', $field['NAME'], $count, $display_temp.$button_add, $avalues);
            } else {
                $ret.=$display_temp."<br />";
            }
            $ret.="<br / >";
            $count++;
        }
        
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.='<input id="customfield_text_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.(count($values)).'">';
            $ret .= '<div id="spaceformorecustomfieldtext_'.$afield_name.'"></div>';
            $ret.=get_custom_dnd_on_add();
            $ret.="<script>
		var cpt = $count;
		
		function add_custom_comment_(field_id, field_name, field_rows, field_cols, field_maxsize) {
				document.getElementById('customfield_text_'+field_id).value = document.getElementById('customfield_text_'+field_id).value * 1 + 1;
				count = document.getElementById('customfield_text_'+field_id).value;
				
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfieldtext_'+field_id, 'customfield_comment_'+field_name, (count-1));
				var	buttonAdd = document.getElementById('button_add_' + field_name + '_' + field_id);
				
				f_aut0 = document.createElement('textarea');
				f_aut0.setAttribute('name',field_name+'[]');
				f_aut0.setAttribute('id',field_name+'_'+cpt);
				f_aut0.setAttribute('cols',field_cols);
				f_aut0.setAttribute('rows',field_rows);
				f_aut0.setAttribute('maxlength',field_maxsize);
				f_aut0.setAttribute('wrap','virtual');
				
				space=document.createElement('br');
				
				document.getElementById(node_dnd_id).appendChild(f_aut0);
				if (buttonAdd) document.getElementById(node_dnd_id).appendChild(buttonAdd);
				document.getElementById(node_dnd_id).appendChild(space);
				cpt++;
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
        $ret="<textarea id=\"".$varname."\" cols=\"".$options['COLS'][0]['value']."\"  rows=\"".$options['ROWS'][0]['value']."\" name=\"".$varname."[]\" wrap=virtual>".htmlentities($values[0],ENT_QUOTES,$charset)."</textarea>";
        return $ret;
    }
    
    public static function get_formatted_label_aff_filter($label) {
        return cutlongwords($label);
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $varname;
    }
}