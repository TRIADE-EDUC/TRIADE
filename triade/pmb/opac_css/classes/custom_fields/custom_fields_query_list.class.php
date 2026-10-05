<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_query_list.class.php,v 1.2.4.4 2025/03/27 14:06:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_query_list extends custom_fields {
	   
    protected static function has_chk_mandatory() {
        return true;
    }
    
    public static function val($field, $val) {
        global $pmb_perso_sep;
        
        if ($val == "") {
            return "";
        }
        $val_c = [];
        $options_ = [];
        if ((!empty($field["OPTIONS"][0]["FIELD0"][0]["value"]))&&(!empty($field["OPTIONS"][0]["FIELD1"][0]["value"]))&&(isset($field["OPTIONS"][0]["OPTIMIZE_QUERY"][0]["value"]) && $field["OPTIONS"][0]["OPTIMIZE_QUERY"][0]["value"] == "yes")) {
            if (is_array($val) && count($val)) {
                $val_ads = array_map("addslashes",$val);
                $requete = "select * from (".$field['OPTIONS'][0]['QUERY'][0]['value'].") as sub1 where ".$field["OPTIONS"][0]["FIELD0"][0]["value"]." in (BINARY '".implode("',BINARY '",$val_ads)."')";
                $resultat = pmb_mysql_query($requete);
                if ($resultat && pmb_mysql_num_rows($resultat)) {
                    while ($r = pmb_mysql_fetch_row($resultat)) {
                        $val_c[] = $r[1];
                    }
                }
            }
        } else {
            $resultat = pmb_mysql_query($field['OPTIONS'][0]['QUERY'][0]['value']);
            if ($resultat && pmb_mysql_num_rows($resultat)) {
                while ($r = pmb_mysql_fetch_row($resultat)) {
                    $options_[$r[0]] = $r[1];
                }
            }
            for ($i = 0; $i < count($val); $i++) {
                if (isset($options_[$val[$i]])) {
                    $val_c[] = $options_[$val[$i]];
                }
            }
        }
        $val_ = implode($pmb_perso_sep, $val_c);
        return $val_;
    }
    
    public static function aff($field,&$check_scripts,$script="") {
        global $charset;
        global $_custom_prefixe_;
        global $base_path;
        global $lang;
        
        $ret = '';
        $values=$field['VALUES'];
        
        $options=$field['OPTIONS'][0];
        
        if ($values=="") $values=array();
        if ($options["AUTORITE"][0]["value"]!="yes") {
            if ($options["CHECKBOX"][0]["value"]=="yes"){
                if ($options['MULTIPLE'][0]['value']=="yes") $type = "checkbox";
                else $type = "radio";
                //on rajoute la langue si besoin dans le requete
                $query = str_replace('$lang', $lang, $options['QUERY'][0]['value']);
                $resultat=pmb_mysql_query($query);
                if ($resultat) {
                    $i=0;
                    $ret="<table><tr>";
                    $limit = intval($options['CHECKBOX_NB_ON_LINE'][0]['value']);
                    if($limit==0) $limit = 4;
                    while ($r=pmb_mysql_fetch_array($resultat)) {
                        if ($i>0 && $i%$limit == 0)$ret.="</tr><tr>";
                        $ret.= "<td><input id='".$field['NAME']."_$i' type='$type' name='".$field['NAME']."[]' ".(in_array($r[0],$values) ? "checked=checked" : "")." value='".$r[0]."'/><span id='lib_".$field['NAME']."_$i'>&nbsp;".$r[1]."</span></td>";
                        $i++;
                    }
                    $ret.="</tr></table>";
                }
            } else {
                $options=$field['OPTIONS'][0];
                $ret="<select id=\"".$field['NAME']."\" name=\"".$field['NAME'];
                $ret.="[]";
                $ret.="\" ";
                if ($script) $ret.=$script." ";
                if ($options['MULTIPLE'][0]['value']=="yes") $ret.="multiple";
                $ret.=" data-form-name='".$field['NAME']."' >\n";
                if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                    $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
                }
                //on rajoute la langue si besoin dans le requete
                $query = str_replace('$lang', $lang, $options['QUERY'][0]['value']);
                $resultat=pmb_mysql_query($query);
                while ($r=pmb_mysql_fetch_row($resultat)) {
                    $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
                    $as=array_search($r[0],$values);
                    if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                    $ret.=">".htmlentities($r[1],ENT_QUOTES,$charset)."</option>\n";
                }
                $ret.= "</select>\n";
            }
        } else {
            $caller = get_form_name();
            $libelles=array();
            if ($values) {
                $values_received=$values;
                $values_received_bis=$values;
                $values=array();
                //on rajoute la langue si besoin dans le requete
                $query = str_replace('$lang', $lang, $options['QUERY'][0]['value']);
                $resultat=pmb_mysql_query($query);
                $query_options = array();
                while ($r=pmb_mysql_fetch_row($resultat)) {
                    $query_options[$r[0]] = $r[1];
                }
                $i=0;
                foreach ($values_received as $key=>$value_received) {
                    if (!empty($query_options[$value_received])) {
                        $values[$i]=$value_received;
                        $libelles[$i]=$query_options[$value_received];
                        $i++;
                        unset($values_received_bis[$key]);
                    }
                }
                if ($options["INSERTAUTHORIZED"][0]["value"]=="yes") {
                    foreach ($values_received_bis as $key=>$val) {
                        $values[$i]="";
                        $libelles[$i]=$val;
                        $i++;
                    }
                }
            }
            $n=count($values);
            if(($options['MULTIPLE'][0]['value']=="yes") )	$val_dyn=1;
            else $val_dyn=0;
            if ($n==0) {
                $n=1;
                $libelles[0] = '';
                $values[0] = '';
            }
            if ($options['MULTIPLE'][0]['value']=="yes") {
                //			$readonly="f_perso.setAttribute('readonly','');";
                //			if($options["INSERTAUTHORIZED"][0]["value"]=="yes"){
                //				$readonly="";
                //			}
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
				template = document.getElementById('div_".$field["NAME"]."');
				perso=document.createElement('div');
				perso.className='row';
				    
				suffixe = document.getElementById('n_".$field["NAME"]."').value;
				var nom_id = '".$field["NAME"]."_'+suffixe
				var f_perso = document.createElement('input');
				f_perso.setAttribute('name','f_'+nom_id);
				f_perso.setAttribute('id','f_'+nom_id);
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
				f_perso_id.name=nom_id;
				f_perso_id.setAttribute('type','hidden');
				f_perso_id.setAttribute('id',nom_id);
				f_perso_id.setAttribute('value','');
				    
				perso.appendChild(f_perso);
				perso.appendChild(document.createTextNode(' '));
				perso.appendChild(document.createTextNode(' '));
				perso.appendChild(del_f_perso);
				perso.appendChild(f_perso_id);
				    
				template.appendChild(perso);
				    
				document.getElementById('n_".$field["NAME"]."').value=suffixe*1+1*1 ;
				ajax_pack_element(document.getElementById('f_'+nom_id));
			}
			</script>
			";
            }
            $ret.="<input type='hidden' value='$n' name='n_".$field["NAME"]."' id='n_".$field["NAME"]."' />\n<div id='div_".$field["NAME"]."'>";
            //		$readonly="readonly";
            //		if($options["INSERTAUTHORIZED"][0]["value"]=="yes"){
            //			$readonly="";
                //		}
                $readonly='';
                for ($i=0; $i<$n; $i++) {
                    $ret.="<input type='text' class='saisie-50emr' id='f_".$field["NAME"]."_$i' completion='perso_".$_custom_prefixe_."' persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."_$i' $readonly value=\"".htmlentities($libelles[$i],ENT_QUOTES,$charset)."\" />\n";
                    $ret.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."_$i' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
//                     $browse_link = "./select.php?what=perso&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME'];
//                     $ret.= static::get_button_browse($field, $browse_link);
                    $ret.=static::get_button_raz($field, "this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value='';")."\n";
                    if (($i==0)&&($options['MULTIPLE'][0]['value']=="yes")) {
                        $ret.= " ".static::get_button_add($field, 'add_'.$field["NAME"].'();');
                    }
                    $ret.="<br />";
                }
                $ret.="</div>";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname,$script="") {
        global $charset;
        global $base_path;
        
        $_custom_prefixe_=$field["PREFIX"];
        
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        $options=$field['OPTIONS'][0];
        if ($options["AUTORITE"][0]["value"]!="yes") {
            $ret="<select id=\"".$varname."\" name=\"".$varname;
            $ret.="[]";
            $ret.="\" ";
            if ($script) $ret.=$script." ";
            $ret.="multiple";
            $ret.=" data-form-name='".$varname."' >\n";
            if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
            }
            $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
            while ($r=pmb_mysql_fetch_row($resultat)) {
                $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
                $as=array_search($r[0],$values);
                if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                $ret.=">".htmlentities($r[1],ENT_QUOTES,$charset)."</option>\n";
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
//				space=document.createTextNode(' ');
//				perso.appendChild(space);
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
                $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
                $i=0;
                while ($r=pmb_mysql_fetch_array($resultat)) {
                    $as=array_search($r[0],$values_received);
                    if (($as!==null)&&($as!==false)) {
                        $values[$i]=$r[0];
                        $libelles[$i]=$r[1];
                        $i++;
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
            $browse_link = $base_path."/select.php?what=perso&caller=search_form&p1=".$varname."&p2=f_".$varname."&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname;
            $ret.= static::get_button_browse($field, $browse_link);
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
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if ($values=="") $values=array();
        
        $ret="<select id=\"".$varname."\" name=\"".$varname;
        $ret.="[]";
        $ret.="\" ";
        if ($multiple) $ret.="size=5 multiple";
        $ret.=" data-form-name='".$varname."' >\n";
        if ($options["AUTORITE"][0]["value"]!="yes") {
            if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
            }
            $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
            while ($r=pmb_mysql_fetch_row($resultat)) {
                $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
                $as=array_search($r[0],$values);
                if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
                $ret.=">".htmlentities($r[1],ENT_QUOTES,$charset)."</option>\n";
            }
        } else {
            
        }
        $ret.= "</select>\n";
        return $ret;
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