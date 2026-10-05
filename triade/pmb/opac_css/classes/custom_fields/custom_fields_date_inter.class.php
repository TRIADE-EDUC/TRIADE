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
            
            $timestamp_begin = $val[0];
            $timestamp_end = $val[1];
            if ($return) {
                $return .= " " . $pmb_perso_sep . " ";
            }
            $return .= date($msg['date_format']." H:i",$timestamp_begin) . " - " . date($msg['date_format'] . " H:i",$timestamp_end);
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
                $time_begin = "null";
                $time_end = "null";
            } elseif (!$timestamp_begin && !$timestamp_end && $options["DEFAULT_TODAY"][0]["value"]) {
                $date_begin = "null";
                $date_end = "null";
                $time_begin = "null";
                $time_end = "null";
            } else {
                $date_begin = date("Y-m-d",$timestamp_begin);
                $date_end = date("Y-m-d",$timestamp_end);
                $time_begin = "T".date("H:i",$timestamp_begin);
                $time_end = "T".date("H:i",$timestamp_end);
            }
            $ret .= "<div>
					<label>".$msg['resa_planning_date_debut']."</label>
					<input type='text' id='".$field['NAME']."_".$count."_date_begin' name='".$field['NAME']."[".$count."][date_begin]' value='".$date_begin."' data-dojo-type='dijit/form/DateTextBox'/>
					<input type='text' id='".$field['NAME']."_".$count."_time_begin' name='".$field['NAME']."[".$count."][time_begin]' value='".$time_begin."' data-dojo-type='dijit/form/TimeTextBox' data-dojo-props=\"constraints:{timePattern:'HH:mm',clickableIncrement:'T00:15:00', visibleIncrement: 'T01:00:00',visibleRange: 'T01:00:00'}\"/>
					<label>".$msg['resa_planning_date_fin']."</label>
					<input type='text' id='".$field['NAME']."_".$count."_date_end' name='".$field['NAME']."[".$count."][date_end]' value='".$date_end."' data-dojo-type='dijit/form/DateTextBox'/>
					<input type='text' id='".$field['NAME']."_".$count."_time_end' name='".$field['NAME']."[".$count."][time_end]' value='".$time_end."' data-dojo-type='dijit/form/TimeTextBox' data-dojo-props=\"constraints:{timePattern:'HH:mm',clickableIncrement:'T00:15:00', visibleIncrement: 'T01:00:00',visibleRange: 'T01:00:00'}\"/>
					<input class='bouton' type='button' value='X' onClick='empty_dojo_calendar_by_id(\"".$field['NAME']."_".$count."_date_begin\"); empty_dojo_calendar_by_id(\"".$field['NAME']."_".$count."_time_begin\"); empty_dojo_calendar_by_id(\"".$field['NAME']."_".$count."_date_end\"); empty_dojo_calendar_by_id(\"".$field['NAME']."_".$count."_time_end\");'/>";
            if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count) {
                $ret .= '<input class="bouton" type="button" value="+" onclick="add_custom_date_inter_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\',\''.$options["DEFAULT_TODAY"][0]["value"].'\')">';
            }
            $ret .= '</div>';
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
                
				var label_begin = document.createElement('label');
				label_begin.innerHTML = '".$msg['resa_planning_date_debut']."';
				    
				var date_begin = document.createElement('input');
		        date_begin.setAttribute('id',field_name + '_' + count + '_date_begin');
		        date_begin.setAttribute('type','text');
				    
				var time_begin = document.createElement('input');
				time_begin.setAttribute('type','text');
				time_begin.setAttribute('id',field_name + '_' + count + '_time_begin');
				    
				var label_end = document.createElement('label');
				label_end.innerHTML = '".$msg['resa_planning_date_fin']."';
				    
				var date_end = document.createElement('input');
		        date_end.setAttribute('id',field_name + '_' + count + '_date_end');
		        date_end.setAttribute('type','text');
				    
				var time_end = document.createElement('input');
				time_end.setAttribute('type','text');
				time_end.setAttribute('id',field_name + '_' + count + '_time_end');
				    
				    
				var del = document.createElement('input');
				del.setAttribute('type', 'button');
		        del.setAttribute('class','bouton');
		        del.setAttribute('value','X');
				del.addEventListener('click', function() {
					require(['dijit/registry'], function(registry) {
						empty_dojo_calendar_by_id(field_name + '_' + count + '_date_begin');
						empty_dojo_calendar_by_id(field_name + '_' + count + '_time_begin');
						empty_dojo_calendar_by_id(field_name + '_' + count + '_date_end');
						empty_dojo_calendar_by_id(field_name + '_' + count + '_time_end');
					});
				}, false);
				    
				var br = document.createElement('br');
				    
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(label_begin);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(date_begin);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(time_begin);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(label_end);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(date_end);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(time_end);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(del);
				document.getElementById('spaceformorecustomfielddateinter_'+field_id).appendChild(br);
				document.getElementById('customfield_date_inter_'+field_id).value = document.getElementById('customfield_date_inter_'+field_id).value * 1 + 1;
				    
				var date = new Date();
				if (today) {
					date = null;
				}
				    
				require(['dijit/form/TimeTextBox', 'dijit/form/DateTextBox'], function(TimeTextBox,DateTextBox){
					new DateTextBox({value : date, name : field_name + '[' + count + '][date_begin]'},field_name + '_' + count + '_date_begin').startup();
				    
					new TimeTextBox({value: null,
						name : field_name + '[' + count + '][time_begin]',
						constraints : {
							timePattern:'HH:mm',
							clickableIncrement:'T00:15:00',
							visibleIncrement: 'T01:00:00',
							visibleRange: 'T01:00:00'
						}
					},field_name + '_' + count + '_time_begin').startup();
				    
					new DateTextBox({value : date, name : field_name + '[' + count + '][date_end]'},field_name + '_' + count + '_date_end').startup();
				    
					new TimeTextBox({value : null,
						name : field_name + '[' + count + '][time_end]',
						constraints : {
							timePattern:'HH:mm',
							clickableIncrement:'T00:15:00',
							visibleIncrement: 'T01:00:00',
							visibleRange: 'T01:00:00'
						}
					},field_name + '_' + count + '_time_end').startup();
				});
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
					<input type='text' id='".$varname."_date_begin' name='".$varname."[date_begin]' value='".$date_begin."' data-dojo-type='dijit/form/DateTextBox'/>
					<input type='text' id='".$varname."_time_begin' name='".$varname."[time_begin]' value='T".$time_begin."' data-dojo-type='dijit/form/TimeTextBox' data-dojo-props=\"constraints:{timePattern:'HH:mm',clickableIncrement:'T00:15:00', visibleIncrement: 'T00:15:00',visibleRange: 'T01:00:00'}\"/>
					<label>".$msg['resa_planning_date_fin']."</label>
					<input type='text' id='".$varname."_date_end' name='".$varname."[date_end]' value='".$date_end."' data-dojo-type='dijit/form/DateTextBox'/>
					<input type='text' id='".$varname."_time_end' name='".$varname."[time_end]' value='T".$time_end."' data-dojo-type='dijit/form/TimeTextBox' data-dojo-props=\"constraints:{timePattern:'HH:mm',clickableIncrement:'T00:15:00', visibleIncrement: 'T00:15:00',visibleRange: 'T01:00:00'}\"/>
			</div>";
        return $ret;
    }
    
    public static function get_formatted_label_aff_filter($label) {
        return formatdate($label);
    }
}