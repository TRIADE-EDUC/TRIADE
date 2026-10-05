<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_date_inter.class.php,v 1.2 2024/01/18 13:31:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_date_inter extends custom_fields {
	
    protected static function get_chk_values($name) {
        global ${$name};
        $val=${$name};
        $value = array();
        if (is_array($val)) {
            foreach($val as $interval){
                if(!$interval['time_begin']) {
                    $interval['time_begin'] = '00:00';
                }
                if(!$interval['time_end']) {
                    $interval['time_end'] = '23:59';
                }
                if($interval['date_begin'] && $interval['date_end']) {
                    $timestamp_begin = strtotime($interval['date_begin'] . ' ' . $interval['time_begin']);
                    $timestamp_end = strtotime($interval['date_end'] . ' ' . $interval['time_end']);
                    
                    if ($timestamp_begin > $timestamp_end) {
                        $value[] = $timestamp_end."|".$timestamp_begin;
                    } else {
                        $value[] = $timestamp_begin."|".$timestamp_end;
                    }
                }
            }
        }
        return $value;
    }
    
    public static function val($field, $value) {
        global $pmb_perso_sep, $msg;
        
        $values=format_output($field,$value);
        $return = "";
        for ($i=0;$i<count($values);$i++){
            $val = explode("|",$values[$i]);
            
            if(empty($val[0]) || empty($val[1])) continue;
            $timestamp_begin = $val[0];
            $timestamp_end = $val[1];
            if ($return) {
                $return .= " " . $pmb_perso_sep . " ";
            }
            $return .= date($msg['1005']." H:i",$timestamp_begin) . " - " . date($msg['1005'] . " H:i",$timestamp_end);
        }
        return $return;
    }
    
    public static function aff($field,&$check_scripts) {
        global $msg;
        
        $values = ($field['VALUES'] ? $field['VALUES'] : array(""));
        $options=$field['OPTIONS'][0];
        $afield_name = $field["ID"];
        $count = 0;
        $ret = "";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.= get_js_function_dnd('date_inter', $field['NAME']);
            $ret.= '<input class="bouton" type="button" value="+" onclick="add_custom_date_inter_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\',\''.$options["DEFAULT_TODAY"][0]["value"].'\')">';
        }
        foreach ($values as $value) {
            $timestamp_begin = '';
            $timestamp_end = '';
            $dates = explode("|",$value);
            if (isset($dates[0])) $timestamp_begin = $dates[0];
            if (isset($dates[1])) $timestamp_end = $dates[1];
            
            if (!$timestamp_begin && !$timestamp_end && !$options["DEFAULT_TODAY"][0]["value"]) {
                $time = time();
                $date_begin = date("Y-m-d",$time);
                $date_end = date("Y-m-d",$time);
                $time_begin = null;
                $time_end = null;
            } elseif (!$timestamp_begin && !$timestamp_end && $options["DEFAULT_TODAY"][0]["value"]) {
                $date_begin = null;
                $date_end = null;
                $time_begin = null;
                $time_end = null;
            } else {
                $date_begin = date("Y-m-d",$timestamp_begin);
                $date_end = date("Y-m-d",$timestamp_end);
                $time_begin = "T".date("H:i",$timestamp_begin);
                $time_end = "T".date("H:i",$timestamp_end);
            }
            $display_temp = get_input_date_time_inter($field['NAME']."[".$count."]", $field['NAME']."_".$count, $date_begin, $time_begin, $date_end, $time_end, $field['MANDATORY']);
            if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
                $button_add = '';
                if (end($values) == $value) {
                    $button_add = '<input id="button_add_'.$field['NAME'].'" class="bouton" type="button" value="+" onclick="add_custom_date_inter_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\',\''.$options["DEFAULT_TODAY"][0]["value"].'\')">';
                }
                $ret.= get_block_dnd('date_inter', $field['NAME'], $count, $display_temp.$button_add, $date_begin);
            } else {
                $ret.= $display_temp;
            }
            $count++;
        }
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= '<input id="customfield_date_inter_'.$afield_name.'" type="hidden" name="customfield_date_inter_'.$afield_name.'" value="'.$count.'">';
            $ret .= '<div id="spaceformorecustomfielddateinter_'.$afield_name.'"></div>';
            $ret .= get_custom_dnd_on_add();
            $ret .= "
        <script>
			function add_custom_date_inter_(field_id, field_name, today) {
				var count = document.getElementById('customfield_date_inter_'+field_id).value;
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfielddateinter_'+field_id, 'customfield_date_inter_'+field_name, count);
                get_input_date_time_inter_js(document.getElementById(node_dnd_id), field_name + '[' + count + ']', field_name + '_' + count, today, 'date_begin', 'date_end');
                document.getElementById('customfield_date_inter_'+field_id).value = document.getElementById('customfield_date_inter_'+field_id).value * 1 + 1;
                dojo.parser.parse(node_dnd_id);
			}
		</script>";
        }
        if ($field['MANDATORY'] == 1) {
            $caller = get_form_name();
            $check_scripts.= "if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $msg;
        
        $timestamp_begin = '';
        $timestamp_end = '';
        $values=$field['VALUES'];
        if(!empty($values[0])) {
            $dates = explode("|",$values[0]);
            if (isset($dates[0])) $timestamp_begin = $dates[0];
            if (isset($dates[1])) $timestamp_end = $dates[1];
        }
        if (!$timestamp_begin && !$timestamp_end) {
            $time = time();
            $date_begin = date("Y-m-d",$time);
            $date_end = date("Y-m-d",$time);
            $time_begin = date("H:i",$time);
            $time_end = date("H:i",$time);
        } else {
            $date_begin = date("Y-m-d",$timestamp_begin);
            $date_end = date("Y-m-d",$timestamp_end);
            $time_begin = date("H:i",$timestamp_begin);
            $time_end = date("H:i",$timestamp_end);
        }
        $ret = "<div>
					<label>".$msg['resa_planning_date_debut']."</label>
					<input type='date' id='".$varname."_date_begin' name='".$varname."[date_begin]' value='".$date_begin."' />
					<input type='time' id='".$varname."_time_begin' name='".$varname."[time_begin]' value='T".$time_begin."'\" />
					<label>".$msg['resa_planning_date_fin']."</label>
					<input type='date' id='".$varname."_date_end' name='".$varname."[date_end]' value='".$date_end."' />
					<input type='time' id='".$varname."_time_end' name='".$varname."[time_end]' value='T".$time_end."' />
			</div>";
        return $ret;
    }
    
    public static function get_formatted_label_aff_filter($label) {
        return formatdate($label);
    }
}