<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_list.class.php,v 1.5.4.4 2025/05/22 07:57:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_list extends custom_fields {
	
    protected static $created_primary_key = [];
    
    protected static function has_chk_mandatory() {
        return true;
    }
    
    public static function val($field, $val) {
        global $pmb_perso_sep;
        global $options_;
        $_custom_prefixe_=$field['PREFIX'];
        
        if ($val=='') {
            return '';
        }
        
        if (!isset($options_[$_custom_prefixe_][$field['ID']]) || !$options_[$_custom_prefixe_][$field['ID']]) {
            $options_[$_custom_prefixe_][$field['ID']] = static::get_custom_list_values_from_id($field, 'text');
        }
        if (!is_array($options_[$_custom_prefixe_][$field['ID']])) {
            return '';
        }
        $exec_func_array_flip = true;
        foreach ($val as $value) {
            if ($value == "") $exec_func_array_flip = false;
        }
        if ($exec_func_array_flip) $val_r=array_flip($val);
        else $val_r=array();
        if ($field["OPTIONS"][0]["AUTORITE"][0]["value"]!="yes") {
            $val_c=array_intersect_key($options_[$_custom_prefixe_][$field['ID']],$val_r);
        } else {
            // CP de type "Autorité", nous conservons l'ordre de saisie
            $val_c=array();
            foreach ($val_r as $key_r=>$value_r) {
                if(!empty($options_[$_custom_prefixe_][$field['ID']][$key_r])) {
                    $val_c[$key_r] = $options_[$_custom_prefixe_][$field['ID']][$key_r];
                }
            }
        }
        if ($val_c=='') {
            $val_c=array();
        }
        $val_=implode($pmb_perso_sep,$val_c);
        return $val_;
    }
    
    public static function aff($field,&$check_scripts,$script="") {
        global $charset;
        global $_custom_prefixe_;
        global $base_path, $msg;
        
        $ret = '';
        $_custom_prefixe_=$field["PREFIX"];
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if ($values=="") {
            $values=array();
        }
        if ($options["AUTORITE"][0]["value"]!="yes") {
            if ($field['MANDATORY'] == 1) {
                $check_scripts.= '
						var fieldName = "'.$field['NAME'].'";
						var fields = document.querySelectorAll("[data-form-name=\'"+fieldName+"\']");
						fields = fields.length ? fields : document.querySelectorAll("[name=\'"+fieldName+"[]\']")
						if (("SELECT" == fields[0].tagName) && ((fields[0].selectedOptions.length && "" == fields[0].selectedOptions[0].value) || (-1 == fields[0].options.selectedIndex))) {
							return cancel_submit("'.sprintf($msg["parperso_field_is_needed"],$field['ALIAS']).'");
						}
							    
						if ("checkbox" == fields[0].type) {
							var valid = false;
							fields.forEach(field => {
								if (field.checked) valid = true;
							})
							if (!valid) return cancel_submit("'.sprintf($msg["parperso_field_is_needed"],$field['ALIAS']).'");
						}';
            }
            if (!empty($options["CHECKBOX"][0]["value"]) && $options["CHECKBOX"][0]["value"]=="yes"){
                $i=0;
                $limit = (isset($options['CHECKBOX_NB_ON_LINE'][0]['value']) ? $options['CHECKBOX_NB_ON_LINE'][0]['value'] : 4);
                $ret.="<div class='customfieldsgroupcheckbox checkboxnbonline".$limit."'>";
                if ($options['MULTIPLE'][0]['value']=="yes") $type = "checkbox";
                else $type = "radio";
                if (($options['UNSELECT_ITEM'][0]['VALUE']!="")&&($options['UNSELECT_ITEM'][0]['value']!="")) {
                    $i=1;
                    $ret.="<span class='customfieldcheckbox'>";
                    $ret.= "<input id='".$field['NAME']."_".$options['UNSELECT_ITEM'][0]['VALUE']."' type='$type' name='".$field['NAME']."[]' checked=checked";
                    $ret.=" value='".$options['UNSELECT_ITEM'][0]['VALUE']."' /><span id='lib_".$field['NAME']."_".$options['UNSELECT_ITEM'][0]['VALUE']."'>&nbsp;".$options['UNSELECT_ITEM'][0]['value']."</span>";
                    $ret.="</span>";
                }
                $custom_list_values = static::get_custom_list_values_from_id($field, 'checkboxes');
                if (!empty($custom_list_values)) {
                    foreach ($custom_list_values as $custom_list_value) {
                        if($limit && $i>0 && $i%$limit == 0) $ret.="<br />";
                        $ret.="<span class='customfieldcheckbox'>";
                        $ret.= "<input id='".$field['NAME']."_".$custom_list_value['value']."' type='$type' name='".$field['NAME']."[]'";
                        if (count($values)) {
                            $as=in_array($custom_list_value['value'],$values);
                            if (($as!==FALSE)&&($as!==NULL)) $ret.=" checked=checked";
                        } else {
                            //Recherche de la valeur par défaut s'il n'y a pas de choix vide
                            if (($options['UNSELECT_ITEM'][0]['VALUE']=="") || ($options['UNSELECT_ITEM'][0]['value']=="")) {
                                //si aucune valeur par défaut, on coche le premier pour les boutons de type radio
                                if (($i==0)&&($type=="radio")&&($options['DEFAULT_VALUE'][0]['value']=="")) $ret.=" checked=checked";
                                elseif ($custom_list_value['value']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" checked=checked";
                            }
                        }
                        $ret.=" value='".$custom_list_value['value']."'/><span id='lib_".$field['NAME']."_".$custom_list_value['value']."'>&nbsp;".$custom_list_value['lib']."</span>";
                        $ret.="</span>";
                        $i++;
                    }
                }
                $ret.="</div>";
            }else{
                $ret.="<select id=\"".$field['NAME']."\" name=\"".$field['NAME'];
                $ret.="[]";
                $ret.="\" ";
                if ($script) $ret.=$script." ";
                if ($options['MULTIPLE'][0]['value']=="yes") $ret.="multiple";
                $ret.=" data-form-name='".$field['NAME']."' >\n";
                if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                    $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
                }
                $options['ITEMS'][0]['ITEM'] = static::get_custom_list_values_from_id($field, 'selector');
                if(is_array($options['ITEMS'][0]['ITEM']) && count($options['ITEMS'][0]['ITEM'])){
                    for ($i=0; $i<count($options['ITEMS'][0]['ITEM']); $i++) {
                        $ret.="<option value=\"".htmlentities($options['ITEMS'][0]['ITEM'][$i]['VALUE'],ENT_QUOTES,$charset)."\"";
                        if (count($values)) {
                            $as=array_search($options['ITEMS'][0]['ITEM'][$i]['VALUE'],$values);
                            if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                        } else {
                            //Recherche de la valeur par défaut
                            if ($options['ITEMS'][0]['ITEM'][$i]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
                        }
                        $ret.=">".htmlentities($options['ITEMS'][0]['ITEM'][$i]['value'],ENT_QUOTES,$charset)."</option>\n";
                    }
                }
                $ret.= "</select>\n";
            }
        } else {
            $libelles=array();
            $caller = get_form_name();
            if ($values) {
                $values_received=$values;
                $values=array();
                $list_values=static::get_custom_list_values_from_id($field, 'autorite');
                foreach ($values_received as $value_received) {
                    if (array_key_exists($value_received, $list_values)) {
                        $values[]=$value_received;
                        $libelles[]=$list_values[$value_received];
                    }
                }
            } else {
                //Recherche de la valeur par défaut
                if ($options['DEFAULT_VALUE'][0]['value']) {
                    $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." and ".$_custom_prefixe_."_custom_list_value='".$options['DEFAULT_VALUE'][0]['value']."'  order by ordre";
                    $resultat=pmb_mysql_query($requete);
                    while ($r=pmb_mysql_fetch_array($resultat)) {
                        $values[0]=$r[$_custom_prefixe_."_custom_list_value"];
                        $libelles[0]=$r[$_custom_prefixe_."_custom_list_lib"];
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
                $ret.=get_custom_dnd_on_add();
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
				    
				var node_dnd_id = get_custom_dnd_on_add('div_".$field["NAME"]."', 'customfield_list_".$field["NAME"]."', suffixe);
				    
				var nom_id = '".$field["NAME"]."_'+suffixe;
				var f_perso = document.createElement('input');
				f_perso.setAttribute('name','f_".$field["NAME"]."[]');
				f_perso.setAttribute('id','f_'+nom_id);
				f_perso.setAttribute('data-form-name','f_'+nom_id);
				f_perso.setAttribute('completion','perso_".$_custom_prefixe_."');
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
				    
				var buttonAdd = document.getElementById('button_add_".$field['NAME']."_".$field['ID']."');
				var perso = document.getElementById(node_dnd_id);
				perso.appendChild(f_perso);
				perso.appendChild(document.createTextNode(' '));
				perso.appendChild(del_f_perso);
				perso.appendChild(f_perso_id);
				if (buttonAdd) perso.appendChild(buttonAdd);
				
				document.$caller.n_".$field["NAME"].".value=suffixe*1+1*1 ;
				ajax_pack_element(document.getElementById('f_'+nom_id));
			}
			</script>
			";
            }
            $ret.="<input type='hidden' value='$n' name='n_".$field["NAME"]."' id='n_".$field["NAME"]."' />\n<div id='div_".$field["NAME"]."'>
			<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=perso&caller=$caller&p1=".$field["NAME"]."_0&p2=f_".$field["NAME"]."_0&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" /> ";
            $readonly='';
            if($options['MULTIPLE'][0]['value']=="yes") {
                $ret .= get_js_function_dnd('list', $field['NAME']);
                $ret.= " ".static::get_button_add($field, 'add_'.$field["NAME"].'();');
            }
            for ($i=0; $i<$n; $i++) {
                $display_temp ="<input type='text' class='saisie-50emr' id='f_".$field["NAME"]."_$i' completion='perso_".$_custom_prefixe_."' persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."[]' data-form-name='f_".$field["NAME"]."_' $readonly value=\"".htmlentities($libelles[$i],ENT_QUOTES,$charset)."\" />\n";
                $display_temp.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."[]' data-form-name='".$field["NAME"]."_' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
                
                $display_temp.=static::get_button_raz($field, "this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value='';")."\n";
                if($options['MULTIPLE'][0]['value']=="yes") {
                    $button_add = '';
                    if (($n - 1) == $i) {
                        $button_add = '<input id="button_add_'.$field['NAME'].'_'.$field['ID'].'" class="bouton" type="button" value="+" onclick="add_'.$field['NAME'].'(\''.$field['ID'].'\', \''.addslashes($field['NAME']).'\');" />';
                    }
                    $ret.=get_block_dnd('list', $field['NAME'], $i, $display_temp.$button_add, $libelles[$i]);
                } else {
                    $ret.=$display_temp."<br />";
                }
            }
            $ret.="</div>";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname,$script="") {
        global $charset;
        global $base_path;
        
        if(!empty($field['NUMBER'])) {
            $field_name = $field['NAME']."_".$field['NUMBER'];
        } else {
            $field_name = $field['NAME'];
        }
        $_custom_prefixe_=$field["PREFIX"];
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        if ($options["AUTORITE"][0]["value"]!="yes") {
            if ($options["CHECKBOX"][0]["value"]=="yes"){
                $i=0;
                $limit = (isset($options['CHECKBOX_NB_ON_LINE'][0]['value']) ? $options['CHECKBOX_NB_ON_LINE'][0]['value'] : 4);
                $ret="<div class='customfieldsgroupcheckbox checkboxnbonline".$limit."'>";
                if ($options['MULTIPLE'][0]['value']=="yes") $type = "checkbox";
                else $type = "radio";
                if (($options['UNSELECT_ITEM'][0]['VALUE']!="")&&($options['UNSELECT_ITEM'][0]['value']!="")) {
                    $i=1;
                    $ret.="<span class='customfieldcheckbox'>";
                    $ret.= "<input id='".$varname."_".$options['UNSELECT_ITEM'][0]['VALUE']."' type='$type' name='".$varname."[]' checked=checked";
                    $ret.=" value='".$options['UNSELECT_ITEM'][0]['VALUE']."' /><span id='lib_".$field_name."_".$options['UNSELECT_ITEM'][0]['VALUE']."'>&nbsp;".$options['UNSELECT_ITEM'][0]['value']."</span>";
                    $ret.="</span>";
                }
                $custom_list_values = static::get_custom_list_values_from_id($field, 'checkboxes');
                if (!empty($custom_list_values)) {
                    foreach ($custom_list_values as $custom_list_value) {
                        if($limit && $i>0 && $i%$limit == 0) $ret.="<br />";
                        $ret.="<span class='customfieldcheckbox'>";
                        $ret.= "<input id='".$varname."_".$custom_list_value['value']."' type='$type' name='".$varname."[]'";
                        if (count($values)) {
                            $as=in_array($custom_list_value['value'],$values);
                            if (($as!==FALSE)&&($as!==NULL)) $ret.=" checked=checked";
                        } else {
                            //Recherche de la valeur par défaut s'il n'y a pas de choix vide
                            if (($options['UNSELECT_ITEM'][0]['VALUE']=="") || ($options['UNSELECT_ITEM'][0]['value']=="")) {
                                //si aucune valeur par défaut, on coche le premier pour les boutons de type radio
                                if (($i==0)&&($type=="radio")&&($options['DEFAULT_VALUE'][0]['value']=="")) $ret.=" checked=checked";
                                elseif ($custom_list_value['value']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" checked=checked";
                            }
                        }
                        $ret.=" value='".$custom_list_value['value']."'/><span id='lib_".$field_name."_".$custom_list_value['value']."'>&nbsp;".$custom_list_value['lib']."</span>";
                        $ret.="</span>";
                        $i++;
                    }
                }
                $ret.="</div>";
            } else {
                $ret="<select id=\"".$varname."\" name=\"".$varname;
                $ret.="[]";
                $ret.="\" ";
                if ($script) $ret.=$script." ";
                $ret.="multiple";
                $ret.=" data-form-name='".$varname."' >\n";
                if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                    $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
                }
                $options['ITEMS'][0]['ITEM'] = static::get_custom_list_values_from_id($field, 'selector');
                if (!empty($options['ITEMS'][0]['ITEM'])) {
                    for ($i=0; $i<count($options['ITEMS'][0]['ITEM']); $i++) {
                        $ret.="<option value=\"".htmlentities($options['ITEMS'][0]['ITEM'][$i]['VALUE'],ENT_QUOTES,$charset)."\"";
                        $as=array_search($options['ITEMS'][0]['ITEM'][$i]['VALUE'],$values);
                        if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                        $ret.=">".htmlentities($options['ITEMS'][0]['ITEM'][$i]['value'],ENT_QUOTES,$charset)."</option>\n";
                    }
                }
                $ret.= "</select>\n";
            }
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
				f_perso.setAttribute('completion','perso_".$_custom_prefixe_."');
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
                $custom_list_values = static::get_custom_list_values_from_id($field, 'autorite');
                if (!empty($custom_list_values)) {
                    $i=0;
                    foreach ($custom_list_values as $custom_list_value=>$custom_list_lib) {
                        $as=array_search($custom_list_value,$values_received);
                        if (($as!==null)&&($as!==false)) {
                            $values[$i] = $custom_list_value;
                            $libelles[$i] = $custom_list_lib;
                            $i++;
                        }
                    }
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
            $ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=perso&caller=search_form&p1=".$varname."&p2=f_".$varname."&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
            $ret.= static::get_button_add($field, 'add_'.$varname.'();');
            $ret.="<div id='div_".$varname."'>";
            for($inc=0;$inc<$nb_values;$inc++){
                $ret.="<div class='row'>";
                $ret.="<input type='hidden' id='".$varname."_".$inc."' name='".$varname."[]' data-form-name='".$varname."[]' value=\"".htmlentities($values[$inc],ENT_QUOTES,$charset)."\">";
                $ret.="<input type='text' class='saisie-20emr' id='f_".$varname."_".$inc."' completion='perso_".$_custom_prefixe_."' persofield='".$field["NAME"]."' autfield='".$varname."_".$inc."' name='f_".$varname."[]' data-form-name='f_".$varname."[]' value=\"".htmlentities($libelles[$inc],ENT_QUOTES,$charset)."\" />\n";
                $ret.=static::get_button_raz($field, "this.form.f_".$varname."_".$inc.".value=''; this.form.".$varname."_".$inc.".value='';")."\n";
                $ret.="</div>";
            }
            $ret.="</div>";
        }
        return $ret;
    }
    
    public static function aff_filter($field,$varname,$multiple) {
        global $charset;
        
        $_custom_prefixe_=$field["PREFIX"];
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        
        $ret="<select id=\"".$varname."\" name=\"".$varname;
        $ret.="[]";
        $ret.="\" ";
        if ($multiple) $ret.="size=5 multiple";
        $ret.=" data-form-name='".$varname."' >\n";
        
        if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
            $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
        }
        $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
        $resultat=pmb_mysql_query($requete);
        if ($resultat) {
            $i=0;
            while ($r=pmb_mysql_fetch_array($resultat)) {
                $options['ITEMS'][0]['ITEM'][$i]['VALUE']=$r[$_custom_prefixe_."_custom_list_value"];
                $options['ITEMS'][0]['ITEM'][$i]['value']=$r[$_custom_prefixe_."_custom_list_lib"];
                $i++;
            }
        }
        for ($i=0; $i<count($options['ITEMS'][0]['ITEM']); $i++) {
            $ret.="<option value=\"".htmlentities($options['ITEMS'][0]['ITEM'][$i]['VALUE'],ENT_QUOTES,$charset)."\"";
            if (count($values)) {
                $as=array_search($options['ITEMS'][0]['ITEM'][$i]['VALUE'],$values);
                if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
            } else {
                //Recherche de la valeur par défaut
                //Désactivation au 20/05/19 - Demande #69211
                //if ($options['ITEMS'][0]['ITEM'][$i]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
            }
            $ret.=">".htmlentities($options['ITEMS'][0]['ITEM'][$i]['value'],ENT_QUOTES,$charset)."</option>\n";
        }
        $ret.= "</select>\n";
        return $ret;
    }
    
    protected static function get_custom_list_values_from_id($field, $display_mode='') {
        $list = [];
        $_custom_prefixe_=$field['PREFIX'];
        if(static::is_created_primary_key($_custom_prefixe_)) {
            $query = "SELECT id_{$_custom_prefixe_}_custom_list, {$_custom_prefixe_}_custom_list_value, {$_custom_prefixe_}_custom_list_lib";
        } else {
            $query = "SELECT {$_custom_prefixe_}_custom_list_value, {$_custom_prefixe_}_custom_list_lib";
        }
        $query .= " FROM {$_custom_prefixe_}_custom_lists WHERE {$_custom_prefixe_}_custom_champ=".$field['ID']." order by ordre";
        $result = pmb_mysql_query($query);
        if ($result) {
            while ($r=pmb_mysql_fetch_array($result)) {
                $list_value = $r["{$_custom_prefixe_}_custom_list_value"];
                if(static::is_created_primary_key($_custom_prefixe_)) {
                    $list_lib = translation::get_translated_text($r["id_{$_custom_prefixe_}_custom_list"], $_custom_prefixe_."_custom_lists", $_custom_prefixe_."_custom_list_lib", $r["{$_custom_prefixe_}_custom_list_lib"]);
                } else {
                    $list_lib = $r[$_custom_prefixe_.'_custom_list_lib'];
                }
                switch ($display_mode) {
                    case 'autorite':
                        $list[$list_value] = $list_lib;
                        break;
                    case 'selector':
                        $list[] = ['VALUE' => $list_value, 'value' => $list_lib];
                        break;
                    case 'checkboxes':
                        $list[] = ['value' => $list_value, 'lib' => $list_lib];
                        break;
                    case 'text':
                        $list[$list_value] = $list_lib;
                    default:
//                      $list[] = ['value' => $list_value, 'lib' => $list_lib];
                        break;
                }
            }
        }
        return $list;
    }
    
    protected static function is_created_primary_key($prefix) {
        if(!isset(static::$created_primary_key[$prefix])) {
            $query = "SHOW COLUMNS FROM {$prefix}_custom_lists LIKE 'id_{$prefix}_custom_list'";
            $result = pmb_mysql_query($query);
            static::$created_primary_key[$prefix] = pmb_mysql_num_rows($result);
        }
        return static::$created_primary_key[$prefix];
    }
    
    public static function get_ajax_list($field_id, $field_prefix) {
        $field = [ 
            'ID' => intval($field_id),
            'PREFIX' => $field_prefix
        ];
        return static::get_custom_list_values_from_id($field, 'autorite');
    }
    
    public static function get_rgaa_label($field, $varname) {
        $options=$field['OPTIONS'][0];
        if ($options["CHECKBOX"][0]["value"] == "yes"){
            return "";
        }
        if ($options["AUTORITE"][0]["value"]!="yes") {
            return $varname;
        } else {
            return $varname."_0";
        }
    }
	
}