<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_marclist.class.php,v 1.2.4.2 2025/02/20 15:18:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_marclist extends custom_fields {
    
    protected static function has_chk_mandatory() {
        return true;
    }
    
    public static function chk($field,&$check_message) {
        global $msg;
        
        $name=$field['NAME'];
        $options=$field['OPTIONS'][0];
        
        global ${$name};
        if ($options["AUTORITE"][0]["value"]!="yes") {
            $val=${$name};
        } else {
            $val=array();
            $tmp_values=${$name};
            if(is_array($tmp_values)) {
                foreach ($tmp_values as $v) {
                    if ($v!="") {
                        $val[]=$v;
                    }
                }
            }
        }
        if ($field['MANDATORY']==1) {
            if ((!count($val))||((count($val)==1)&&($val[0]==""))) {
                $check_message=sprintf($msg["parperso_field_is_needed"],$field['ALIAS']);
                return 0;
            }
        }
        
        $check_datatype_message="";
        $val_1=chk_datatype($field,$val,$check_datatype_message);
        if ($check_datatype_message) {
            $check_message=$check_datatype_message;
            return 0;
        }
        ${$name}=$val_1;
        
        return 1;
    }
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $options=$field['OPTIONS'][0];
        $values=format_output($field,$value);
        $ret = "";
        if (count($values)) {
            $marclist_type = marc_list_collection::get_instance($options['DATA_TYPE'][0]['value']);
            if($ret)$ret.=$pmb_perso_sep;
            foreach($values as $value) {
                if(isset($marclist_type->table[$value])) {
                    if($ret)$ret.=$pmb_perso_sep;
                    $ret.= $marclist_type->table[$value];
                }
            }
        }
        return $ret;
    }
    
    public static function aff($field,&$check_scripts,$script="") {
        global $charset;
        global $base_path;
        
        $_custom_prefixe_=$field["PREFIX"];
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        $ret = "";
        
        switch($options['DATA_TYPE'][0]['value']){
            case 'lang' : $completion='langue';
            break;
            case 'function' : $completion='fonction';
            break;
            default:
                $completion=$options['DATA_TYPE'][0]['value'];
                break;
        }
        $marclist_type = marc_list_collection::get_instance($options['DATA_TYPE'][0]['value']);
        
        if ($options["AUTORITE"][0]["value"]!="yes") {
            $ret="<select id=\"".$field['NAME']."\" name=\"".$field['NAME'];
            $ret.="[]";
            $ret.="\" ";
            if ($script) $ret.=$script." ";
            if ($options['MULTIPLE'][0]['value']=="yes") $ret.="multiple";
            $ret.=" data-form-name='".$field['NAME']."' >\n";
            if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
            }
            if (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
                asort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
                ksort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
                arsort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
                krsort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="3") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
                $marclist_type->table = array_reverse($marclist_type->table, true);
            }
            // Sinon on ne fait rien, le tableau est déjà trié avec l'attribut order
            
            reset($marclist_type->table);
            if (count($marclist_type->table)) {
                foreach ($marclist_type->table as $code=>$label) {
                    $ret .= "<option value=\"".$code."\"";
                    if (count($values)) {
                        $as=array_search($code,$values);
                        if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                    }
                    $ret .= ">".$label."</option>";
                }
            }
            $ret.= "</select>\n";
        } else {
            $libelles=array();
            $caller = get_form_name();
            if (count($values)) {
                $values_received=$values;
                $values=array();
                $i=0;
                foreach ($values_received as $value) {
                    $as=array_key_exists($value,$marclist_type->table);
                    if (($as!==null)&&($as!==false)) {
                        $values[$i]=$value;
                        $libelles[$i]=$marclist_type->table[$value];
                        $i++;
                    }
                }
            }
            $readonly='';
            $n=count($values);
            if(($options['MULTIPLE'][0]['value']=="yes") )	$val_dyn=1;
            else $val_dyn=0;
            if ($n==0) {
                $n=1;
                $libelles[0] = '';
                $values[0] = '';
            }
            if ($options['MULTIPLE'][0]['value']=="yes") {
                $readonly='';
                $ret.= get_custom_dnd_on_add();
                $ret.="<script>
			function fonction_selecteur_".$field["NAME"]."() {
				name=this.getAttribute('id').substring(4);
				name_id = name;
				openPopUp('".$base_path."/select.php?what=perso&caller=$caller&p1='+name_id+'&p2=f_'+name_id+'&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']."', 'selector');
			}
			function fonction_raz_".$field["NAME"]."() {
				name=this.getAttribute('id').substring(4);
				document.getElementById(name).value='';
				document.getElementById('f_'+name).value='';
			}
			function add_".$field["NAME"]."() {
				suffixe = eval('document.$caller.n_".$field["NAME"].".value');
				    
				var node_dnd_id = get_custom_dnd_on_add('div_".$field["NAME"]."', 'customfield_marclist_".$field["NAME"]."', suffixe);
				var buttonAdd = document.getElementById('button_add_".$field["NAME"]."_".$field["ID"]."')
				    
				var nom_id = '".$field["NAME"]."_'+suffixe;
				var f_perso = document.createElement('input');
				f_perso.setAttribute('name','f_".$field["NAME"]."[]');
				f_perso.setAttribute('id','f_'+nom_id);
				f_perso.setAttribute('completion','".$completion."');
				f_perso.setAttribute('persofield','".$field["NAME"]."');
				f_perso.setAttribute('autfield',nom_id);
				f_perso.setAttribute('type','text');
				f_perso.className='saisie-50emr';
				$readonly
				f_perso.setAttribute('value','');
				
				var del_f_perso = document.createElement('input');
				del_f_perso.setAttribute('id','del_".$field["NAME"]."_'+suffixe);
				del_f_perso.onclick=fonction_raz_".$field["NAME"].";
				del_f_perso.setAttribute('type','button');
				del_f_perso.className='bouton';
				del_f_perso.setAttribute('readonly','');
				del_f_perso.setAttribute('value','X');
				    
				var f_perso_id = document.createElement('input');
				f_perso_id.name='".$field["NAME"]."[]';
				f_perso_id.setAttribute('type','hidden');
				f_perso_id.setAttribute('id',nom_id);
				f_perso_id.setAttribute('value','');
				
				var perso = document.getElementById(node_dnd_id);
				perso.appendChild(f_perso);
				perso.appendChild(document.createTextNode(' '));
				perso.appendChild(del_f_perso);
				perso.appendChild(f_perso_id);
				perso.appendChild(buttonAdd);
				
				document.$caller.n_".$field["NAME"].".value=suffixe*1+1*1 ;
				ajax_pack_element(document.getElementById('f_'+nom_id));
			}
			</script>
			";
            }
            $ret.="<input type='hidden' value='$n' name='n_".$field["NAME"]."'/>\n<div id='div_".$field["NAME"]."'>";
            $browse_link = $base_path."/select.php?what=perso&caller=$caller&p1=".$field["NAME"]."_0&p2=f_".$field["NAME"]."_0&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME'];
            $ret.= static::get_button_browse($field, $browse_link);
            $readonly='';
            if($options['MULTIPLE'][0]['value']=="yes") {
                $ret .= static::get_button_add($field, 'add_'.$field["NAME"].'();');
                $ret .= get_js_function_dnd('marclist', $field['NAME']);
            }
            for ($i=0; $i<$n; $i++) {
                $display_temp ="<input type='text' class='saisie-50emr' id='f_".$field["NAME"]."_$i' completion='".$completion."' persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."[]'  data-form-name='f_".$field["NAME"]."_' $readonly value=\"".htmlentities($libelles[$i],ENT_QUOTES,$charset)."\" />\n";
                $display_temp.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."[]' data-form-name='".$field["NAME"]."_' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
                
                $display_temp.=static::get_button_raz($field, "this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value='';")."\n";
                if($options['MULTIPLE'][0]['value']=="yes") {
                    $add_button = '';
                    if (($n -1) == $i) {
                        $add_button = "<input id='button_add_".$field['NAME']."_".$field['ID']."' type='button' class='bouton' value='+' onClick=\"add_".$field["NAME"]."();\"/>";
                    }
                    $ret.=get_block_dnd('marclist', $field['NAME'], $i, $display_temp.$add_button, $libelles[$i]);
                } else {
                    $ret.=$display_temp."<br />";
                }
            }
            $ret.="</div>";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $charset;
        global $base_path;
        
        $_custom_prefixe_=$field["PREFIX"];
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        
        $marclist_type = marc_list_collection::get_instance($options['DATA_TYPE'][0]['value']);
        
        if ($options["AUTORITE"][0]["value"]!="yes") {
            $ret="<select id=\"".$varname."\" name=\"".$varname;
            $ret.="[]";
            $ret.="\" ";
            //if ($script) $ret.=$script." ";
            $ret.="multiple";
            $ret.=" data-form-name='".$varname."' >\n";
            
            if (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
                asort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
                ksort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
                arsort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
                krsort($marclist_type->table);
            } elseif (($options['METHOD_SORT_VALUE'][0]['value']=="3") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
                $marclist_type->table = array_reverse($marclist_type->table, true);
            }
            // Sinon on ne fait rien, le tableau est déjà trié avec l'attribut order
            
            reset($marclist_type->table);
            if (count($marclist_type->table)) {
                foreach ($marclist_type->table as $code=>$label) {
                    $ret .= "<option value=\"".$code."\"";
                    $as=array_search($code,$values);
                    if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                    $ret .= ">".$label."</option>";
                }
            }
            $ret.= "</select>\n";
        } else {
            $ret="<script>
			function fonction_selecteur_".$varname."() {
				name=this.getAttribute('id').substring(4);
				name_id = name;
				openPopUp('".$base_path."/select.php?what=perso&caller=search_form&p1='+name_id+'&p2=f_'+name_id+'&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname."', 'selector');
			}
			function fonction_raz_".$varname."() {
				name=this.getAttribute('id').substring(4);
				document.getElementById(name).value='';
				document.getElementById('f_'+name).value='';
			}
			function add_".$varname."() {
				template = document.getElementById('div_".$varname."');
				perso=document.createElement('div');
				perso.className='row';
				    
				suffixe = eval('document.search_form.n_".$varname.".value');
				nom_id = '".$varname."_'+suffixe;
				f_perso = document.createElement('input');
				f_perso.setAttribute('name','f_".$varname."[]');
				f_perso.setAttribute('id','f_'+nom_id);
				f_perso.setAttribute('data-form-name','f_".$varname."[]');
				f_perso.setAttribute('completion','".$options['DATA_TYPE'][0]['value']."');
				f_perso.setAttribute('persofield','".$field["NAME"]."');
				f_perso.setAttribute('autfield',nom_id);
				f_perso.setAttribute('type','text');
				f_perso.className='saisie-20emr';
				f_perso.setAttribute('value','');
				    
				del_f_perso = document.createElement('input');
				del_f_perso.setAttribute('id','del_".$varname."_'+suffixe);
				del_f_perso.onclick=fonction_raz_".$varname.";
				del_f_perso.setAttribute('type','button');
				del_f_perso.className='bouton';
				del_f_perso.setAttribute('value','X');
				    
				f_perso_id = document.createElement('input');
				f_perso_id.setAttribute('name', '".$varname."[]');
				f_perso_id.setAttribute('type','hidden');
				f_perso_id.setAttribute('id',nom_id);
				f_perso_id.setAttribute('value','');
				    
				perso.appendChild(f_perso);
				space=document.createTextNode(' ');
				perso.appendChild(space);
				perso.appendChild(del_f_perso);
				perso.appendChild(f_perso_id);
				    
				template.appendChild(perso);
				    
				document.search_form.n_".$varname.".value=suffixe*1+1*1 ;
				ajax_pack_element(document.getElementById('f_'+nom_id));
			}
			</script>
			";
            $libelles=array();
            if (count($values)) {
                $values_received=$values;
                $values=array();
                foreach ($values_received as $i=>$value_received) {
                    $values[$i]=$value_received;
                    $libelles[$i]=$marclist_type->table[$value_received];
                }
            }
            $nb_values=count($values);
            if(!$nb_values){
                //Création de la ligne
                $nb_values=1;
                $libelles[0] = '';
                $values[0] = '';
            }
            $ret.="<input type='hidden' id='n_".$varname."' value='".$nb_values."'>";
            $browse_link = $base_path."/select.php?what=perso&caller=search_form&p1=".$varname."&p2=f_".$varname."&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname;
            $ret.= static::get_button_browse($field, $browse_link);
            $ret.= static::get_button_add($field, 'add_'.$varname.'();');
            $ret.="<div id='div_".$varname."'>";
            for($inc=0;$inc<$nb_values;$inc++){
                $ret.="<div class='row'>";
                $ret.="<input type='hidden' id='".$varname."_".$inc."' name='".$varname."[]' data-form-name='".$varname."[]' value=\"".htmlentities($values[$inc],ENT_QUOTES,$charset)."\">";
                $ret.="<input type='text' class='saisie-20emr' id='f_".$varname."_".$inc."' completion='".$options['DATA_TYPE'][0]['value']."' persofield='".$field["NAME"]."' autfield='".$varname."_".$inc."' name='f_".$varname."[]' data-form-name='f_".$varname."[]' value=\"".htmlentities($libelles[$inc],ENT_QUOTES,$charset)."\" />\n";
                $ret.=static::get_button_raz($field, "this.form.f_".$varname."_".$inc.".value=''; this.form.".$varname."_".$inc.".value='';")."\n";
                $ret.="</div>";
            }
            $ret.="</div>";
        }
        return $ret;
    }
}