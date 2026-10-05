<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_date_box.class.php,v 1.2 2024/01/18 13:31:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_date_box extends custom_fields {
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $return = "";
        $format_value = format_output($field,$value);
        if (!$value) {
            $value = array();
        }
        foreach ($value as $key => $val) {
            if ($val == "0000-00-00") {
                $val = "";
            }
            if ($val) {
                if ($return) $return .= $pmb_perso_sep;
                $return .= $format_value[$key];
            }
        }
        return $return;
    }
    
    public static function aff($field,&$check_scripts) {
        global $msg;
        
        $values = ($field['VALUES'] ? $field['VALUES'] : array(""));
        $options = $field['OPTIONS'][0];
        $afield_name = $field["ID"];
        $count = 0;
        $ret = "";
        foreach ($values as $value) {
            $d=explode("-",$value);
            $val = "";
            
            $checked_date = false;
            if(!empty($d[0]) && !empty($d[1]) && !empty($d[2])) {
                $checked_date = @checkdate($d[1],$d[2],$d[0]);
            }
            if ((!$checked_date)&&(!isset($options["DEFAULT_TODAY"][0]["value"]) || !$options["DEFAULT_TODAY"][0]["value"])) {
                if(!$flag_id_origine) { //on est en création
                    $val = date("Y-m-d",time());
                }
            } elseif ((!$checked_date)&&(isset($options["DEFAULT_TODAY"][0]["value"]) && $options["DEFAULT_TODAY"][0]["value"])) {
                $val = "";
            } else {
                $val = $value;
            }
            
            $ret .= "<div>
				<input type='text' style='width: 10em;' name='".$field['NAME']."[]' id='".$field['NAME']."_val_".$count."' value='".$val."'
						data-dojo-type='dijit/form/DateTextBox' constraints=\"{datePattern:'".getDojoPattern($msg['format_date'])."'}\" required='false' />
				<input class='bouton' type='button' value='X' onClick='empty_dojo_calendar_by_id(\"".$field['NAME']."_val_".$count."\");'/>";
            if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
                $ret .= '<input class="bouton" type="button" value="+" onclick="add_custom_date_box_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\',\''.(!$options["DEFAULT_TODAY"][0]["value"] ? formatdate(date("Ymd",time())).'\',\''.date("Y-m-d",time()) : '').'\')">';
                $ret .= '</div>';
                $count++;
        }
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= '<input id="customfield_date_box_'.$afield_name.'" type="hidden" name="customfield_date_box_'.$afield_name.'" value="'.$count.'">';
            $ret .= '<div id="spaceformorecustomfielddatebox_'.$afield_name.'"></div>';
            $ret .= get_custom_dnd_on_add();
            $ret .= "<script>
			function add_custom_date_box_(field_id, field_name, value, value_popup) {
				var count = document.getElementById('customfield_date_box_'+field_id).value;
                
				var val = document.createElement('input');
				val.setAttribute('name', field_name + '[]');
				val.setAttribute('id', field_name + '_val_' + count);
		        val.setAttribute('data-dojo-type','dijit/form/DateTextBox');
				val.setAttribute('type','text');
				val.setAttribute('style','width: 10em;');
				if (value) {
		        	val.setAttribute('value',value_popup);
				} else {
					val.setAttribute('value','');
				}
				var del = document.createElement('input');
				del.setAttribute('type', 'button');
		        del.setAttribute('class','bouton');
		        del.setAttribute('value','X');
				del.addEventListener('click', function() {
					empty_dojo_calendar_by_id(field_name + '_val_' + count);
				}, false);
                
				var br = document.createElement('br');
                
				document.getElementById('spaceformorecustomfielddatebox_'+field_id).appendChild(val);
				document.getElementById('spaceformorecustomfielddatebox_'+field_id).appendChild(document.createTextNode (' '));
				document.getElementById('spaceformorecustomfielddatebox_'+field_id).appendChild(del);
				document.getElementById('spaceformorecustomfielddatebox_'+field_id).appendChild(br);
				document.getElementById('customfield_date_box_'+field_id).value = document.getElementById('customfield_date_box_'+field_id).value * 1 + 1;
                
				dojo.parser.parse('spaceformorecustomfielddatebox_'+field_id);
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
        
        $values=$field['VALUES'];
        $ret="
		<div id='".$varname."_start_part[]' style='display: inline-block;'>";
        if(!isset($field['OP'])) $field['OP'] = '';
        switch ($field['OP']) {
            case 'LESS_THAN_DAYS':
            case 'MORE_THAN_DAYS':
                $ret.="<input type='text' style='width: 10em;' name='".$varname."[]' id='".$varname."[]' value='".$values[0]."' /> ".htmlentities($msg['days'], ENT_QUOTES, $charset);
                break;
            default:
                $val='';
                if (!empty($values[0])) {
                    $d=explode("-",$values[0]);
                    if(!empty($d[0]) && !empty($d[1]) && !empty($d[2])) {
                        if (@checkdate($d[1],$d[2],$d[0])) {
                            $val=$values[0];
                        }
                    }
                }
                $ret.="<input type='text' style='width: 10em;' name='".$varname."[]' id='".$varname."[]' value='".$val."'  data-dojo-type='dijit/form/DateTextBox' constraints=\"{datePattern:'".getDojoPattern($msg['format_date'])."'}\" required='false' />";
                break;
        }
        $ret.="
		</div>";
        
        $values=$field['VALUES1'];
        $val='';
        if (!empty($values[0])) {
            $d=explode("-",$values[0]);
            if(!empty($d[0]) && !empty($d[1]) && !empty($d[2])) {
                if (@checkdate($d[1],$d[2],$d[0])) {
                    $val=$values[0];
                }
            }
        }
        $ret.="
		<div id='".$varname."_end_part[]' style='display: inline-block;'>
			 - <input type='text' style='width: 10em;' name='".$varname."_1[]' id='".$varname."_1[]' value='".$val."'  data-dojo-type='dijit/form/DateTextBox' constraints=\"{datePattern:'".getDojoPattern($msg['format_date'])."'}\" required='false' />
		</div>";
        return $ret;
    }
    
    public static function get_formatted_label_aff_filter($label) {
        return formatdate($label);
    }
}