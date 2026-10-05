<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_resolve.class.php,v 1.2 2024/01/18 13:31:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_resolve extends custom_fields {
	
	protected static function get_chk_values($name) {
        global ${$name};
        $val = ${$name};
        $value = array();
        if (isset($val['id'])) {
            $nb_vals = count($val['id']);
            for ($i = 0; $i < $nb_vals; $i++) {
                if ($val['id'][$i] != "") {
                    $value[] = $val['id'][$i]."|".$val['resolve'][$i];
                }
            }
        }
        return $value;
    }
    
    public static function val($field, $value) {
        global $charset,$pmb_perso_sep;
        
        $without="";
        $options=$field['OPTIONS'][0];
        $values=format_output($field,$value);
        $ret = "";
        for ($i=0;$i<count($values);$i++){
            $val = explode("|",$values[$i]);
            if(count($val)>1){
                $id =$val[0];
                foreach ($options['RESOLVE'] as $res){
                    if($res['ID'] == $val[1]){
                        $label = $res['LABEL'];
                        $url= $res['value'];
                        break;
                    }
                }
                $link = str_replace("!!id!!",$id,$url);
                if( $ret != "") $ret.= " / ";
                $ret.= htmlentities($label,ENT_QUOTES,$charset)." : $id <a href='$link' target='_blank'><img class='center' src='".get_url_icon("globe.gif")."' alt='$link' title='link'/></a>";
                if($without)$without.=$pmb_perso_sep;
                $without.=$link;
            }else{
                if($without)$without.=$pmb_perso_sep;
                $without.=implode($pmb_perso_sep,$value);
            }
        }
        return array("ishtml" => true, "value"=>$ret,"withoutHTML"=> $without);
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
            $values = array("|");
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= get_js_function_dnd('resolve', $field['NAME']);
            if(!isset($options['MAXSIZE'][0]['value'])) $options['MAXSIZE'][0]['value'] = '';
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_resolve_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
        }
        foreach ($values as $value) {
            $avalues = explode("|",$value);
            $display_temp = "<input id='".$field['NAME']."$count' type='text' size='".$options['SIZE'][0]['value']."' name='".$field['NAME']."[id][]' data-form-name='".$field['NAME']."' value='".htmlentities($avalues[0],ENT_QUOTES,$charset)."'>";
            $display_temp .= "&nbsp;<select id='".$field['NAME']."_select$count'  name='".$field['NAME']."[resolve][]' data-form-name='".$field['NAME']."_select' >";
            foreach($options['RESOLVE'] as $elem){
                $display_temp .= "
			<option value='".$elem['ID']."' ".($avalues[1] == $elem['ID'] ? "selected=selected":"").">".htmlentities($elem['LABEL'],ENT_QUOTES,$charset)."</option>";
            }
            $display_temp .= "</select>";
            if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
                $button_add = '';
                if (end($values) == $value) {
                    $button_add = '<input id="button_add_'.$field['NAME'].'_'.$field['ID'].'" class="bouton" type="button" value="+" onclick="add_custom_resolve_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
                }
                $ret.=get_block_dnd('resolve', $field['NAME'], $count, $display_temp.$button_add, $avalues[0]);
            } else {
                $ret.=$display_temp."<br />";
            }
            $count++;
        }
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.='<input id="customfield_text_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.(count($values)).'">';
            //$ret.='<input class="bouton" type="button" value="+" onclick="add_custom_text_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
            $ret .= '<div id="spaceformorecustomfieldtext_'.$afield_name.'"></div>';
            $ret.=get_custom_dnd_on_add();
            $ret.="<script>
			function add_custom_resolve_(field_id, field_name, field_size, field_maxlen) {
				document.getElementById('customfield_text_'+field_id).value = document.getElementById('customfield_text_'+field_id).value * 1 + 1;
		        count = document.getElementById('customfield_text_'+field_id).value;
                
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfieldtext_'+field_id, 'customfield_resolve_'+field_name, (count-1));
				var buttonAdd = document.getElementById('button_add_' + field_name + '_' + field_id);
                
				f_aut0 = document.createElement('input');
		        f_aut0.setAttribute('name',field_name+'[id][]');
		        f_aut0.setAttribute('id',field_name+(count-1));
		        f_aut0.setAttribute('type','text');
		        f_aut0.setAttribute('size',field_size);
		        f_aut0.setAttribute('maxlen',field_size);
		        f_aut0.setAttribute('value','');
		        space=document.createElement('br');
				var select = document.createElement('select');
				select.setAttribute('name',field_name+'[resolve][]');
				select.setAttribute('id',field_name+'_select'+(count-1));
				";
            foreach($options['RESOLVE'] as $elem){
                $ret.="
				var option = document.createElement('option');
				option.setAttribute('value','".$elem['ID']."');
				var text = document.createTextNode('".$elem['LABEL']."');
				option.appendChild(text);
				select.appendChild(option);
";
            }
            $ret.="
				document.getElementById(node_dnd_id).appendChild(f_aut0);
				document.getElementById(node_dnd_id).appendChild(document.createTextNode(' '));
				document.getElementById(node_dnd_id).appendChild(select);
				if (buttonAdd) document.getElementById(node_dnd_id).appendChild(buttonAdd);
				document.getElementById(node_dnd_id).appendChild(space);
			}
		</script>";
        }
        if ($field['MANDATORY']==1) {
            $caller = get_form_name();
            $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[id][]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
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
        $ret="<input id='".$varname."' type='text' size='".$options['SIZE'][0]['value']."' name='".$varname."[]' value='".htmlentities($values[0],ENT_QUOTES,$charset)."'>";
        return $ret;
    }
}