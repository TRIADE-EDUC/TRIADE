<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_query_auth.class.php,v 1.2.4.3 2025/02/20 15:18:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_query_auth extends custom_fields {
	
    protected static function has_chk_mandatory() {
        return true;
    }
    
    protected static function get_chk_values($name) {
        global ${$name};
        $val=array();
        $tmp_values=${$name};
        if(is_array($tmp_values)) {
            foreach ($tmp_values as $v) {
                if ($v!="") {
                    $val[]=$v;
                }
            }
        }
        return $val;
    }
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $isbd_s=array();
        if(!$value) {
            return "";
        }
        foreach($value as $id){
            if($id) {
                $isbd_s[] = get_authority_isbd_from_field($field, $id, true);
            }
        }
        $aff=implode($pmb_perso_sep,$isbd_s);
        
        return array("ishtml" => true, "value"=> $aff, "withoutHTML" => strip_tags($aff));
    }
    
    public static function aff($field,&$check_scripts) {
        global $charset;
        global $_custom_prefixe_;
        global $ajax_js_already_included;
        global $base_path, $caller;
        
        $id_thes_unique=$field["OPTIONS"][0]["ID_THES"]["0"]["value"];
        $att_id_filter= $params = $element = "";
        $selection_parameters = get_authority_selection_parameters($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]);
        $what = $selection_parameters['what'];
        $completion = $selection_parameters['completion'];
        $concept_schemes = [];
        
        switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
            case 2:
                $att_id_filter= $id_thes_unique;
                break;//categories
            case 9:
                $concept_schemes = [];
                if(isset($field['OPTIONS'][0]['ID_SCHEME_CONCEP']))
                    for($i=0 ; $i<count($field['OPTIONS'][0]['ID_SCHEME_CONCEP']) ; $i++){
                        $concept_schemes[] = $field['OPTIONS'][0]['ID_SCHEME_CONCEP'][$i]['value'];
                }
                $element="&element=concept&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
                if(isset($concept_schemes[0]) && $concept_schemes[0] != -1){
                    //concept_scheme;
                    $params = " param1='".implode(',',$concept_schemes)."'";
                    $element.="&return_concept_id=1&unique_scheme=1&concept_scheme=".implode(',',$concept_schemes);
                }
                $att_id_filter= "http://www.w3.org/2004/02/skos/core#Concept";
                break;
        }
        
        $values=$field['VALUES'];
        
        $options=$field['OPTIONS'][0];
        
        if ($values=="") $values=array();
        
        if (get_form_name()) {
            $caller = get_form_name();
        }
        
        $n=count($values);
        $ret = "";
        if(empty($ajax_js_already_included)){
            $ajax_js_already_included = true;
            $ret.="<script src='javascript/ajax.js'></script>";
        }
        
        if (($n==0)||($options['MULTIPLE'][0]['value']!="yes")) $n=1;
        if ($options['MULTIPLE'][0]['value']=="yes") {
            $readonly='';
            $ret.= get_custom_dnd_on_add();
            $ret.="<script>
		function fonction_selecteur_".$field["NAME"]."() {
			name=this.getAttribute('id').substring(4);
			name_id = name;
			openPopUp('".$base_path."/select.php?what=$what&caller=$caller&p1='+name_id+'&p2=f_'+name_id+'&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&perso_name=".$field['NAME']."&id_thes_unique=".urlencode($id_thes_unique)."', 'selector');
		}
		function fonction_raz_".$field["NAME"]."() {
			name=this.getAttribute('id').substring(4);
			document.getElementById(name).value='';
			document.getElementById('f_'+name).value='';
		}
		function add_".$field["NAME"]."(node) {
	        if (node) {
    	        var formName = node.form.name;
		    } else if ('$caller') {
		        var formName = '$caller';
            } else {
		        // On remonte jusqu'au formulaire parent
                var div = this.document.getElementById('".$field["NAME"]."');
                var parent = '';
                while (parent != 'FORM') {
                    div = div.parentElement;
                    parent = div.nodeName;
                }
                var formName = div.name;
	        }
			suffixe = eval('document.'+formName+'.n_".$field["NAME"].".value');
			    
			var node_dnd_id = get_custom_dnd_on_add('div_".$field["NAME"]."', 'customfield_query_auth_".$field["NAME"]."', suffixe);
			    
			var nom_id = '".$field["NAME"]."_'+suffixe;
			var f_perso = document.createElement('input');
			f_perso.setAttribute('name','f_".$field["NAME"]."[]');
			f_perso.setAttribute('id','f_'+nom_id);
			f_perso.setAttribute('completion','$completion');
			f_perso.setAttribute('att_id_filter','".$att_id_filter."');
			f_perso.setAttribute('persofield','".$field["NAME"]."');
			f_perso.setAttribute('autfield',nom_id);";
            
            if (count($concept_schemes) && $concept_schemes[0] != -1 && ($what == "ontology")) {
                $ret.= "
			f_perso.setAttribute('param1','".implode(',',$concept_schemes)."');";
            }
            $ret.= "
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
			perso.appendChild(document.createTextNode(' '));
			perso.appendChild(del_f_perso);
			perso.appendChild(f_perso_id);
			    
			var buttonAdd = document.getElementById('button_add_".$field['NAME']."');
			if (buttonAdd) perso.appendChild(buttonAdd);
			    
			document[formName].n_".$field["NAME"].".value=suffixe*1+1*1 ;
			ajax_pack_element(document.getElementById('f_'+nom_id));
		}
		</script>
		";
        }
        $ret.="<input type='hidden' value='$n' name='n_".$field["NAME"]."' />\n<div id='div_".$field["NAME"]."'>";
        $readonly='';
        for ($i=0; $i<$n; $i++) {
            if(!isset($values[$i])) $values[$i] = '';
            $id=$values[$i];
            $val_dyn=3;
            
            $isbd="";
            if($id) {
                $isbd=get_authority_isbd_from_field($field, $id);
            }
            
            $autexclude = "";
            $no_display = "";
            
            switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
                case 9:// concept
                    $element="&element=concept&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
                    if(count($concept_schemes) && $concept_schemes[0] != -1){
                        $element.="&return_concept_id=1&unique_scheme=1&concept_scheme=".implode(',',$concept_schemes);
                    }
                    break;
                default:
                    if($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]>1000){
                        // autperso
                        $element="&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
                        $val_dyn=4;
                        if (array_key_exists("AUTHPERSO", $field) && $field["AUTHPERSO"] && $field["OPTIONS"][0]["DATA_TYPE"][0]['value'] > 1000) {
                            $attr_exclude = attr_exclude_id($field["ID_ORIGINE"], $field["OPTIONS"][0]["DATA_TYPE"][0]['value']);
                            if ($attr_exclude["exclude"]) {
                                $autexclude = "autexclude={$attr_exclude['autexclude']}";
                                $no_display = "&no_display={$attr_exclude['no_display']}";
                            }
                        }
                    }
                    break;
            }
            if (($i==0)) {
                if ($options['MULTIPLE'][0]['value']=="yes") {
                    $ret .= get_js_function_dnd('query_auth', $field['NAME']);
                    $browse_link = $base_path."/select.php?what=$what&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME'];
                    $browse_link .= "&max_field=n_".$field["NAME"]."&field_id=".$field["NAME"]."_&field_name_id=f_".$field["NAME"]."_&add_field=add_".$field["NAME"]."&id_thes_unique=".urlencode($id_thes_unique).$element. $no_display;
                    $ret.= static::get_button_browse($field, $browse_link);
                    $ret.= static::get_button_add($field, 'add_'.$field["NAME"].'(this);');
                }else {
                    $browse_link = $base_path."/select.php?what=$what&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME'];
                    $browse_link .= "&max_field=n_".$field["NAME"]."&field_id=".$field["NAME"]."_0&field_name_id=f_".$field["NAME"]."_0&add_field=add_".$field["NAME"]."&id_thes_unique=".urlencode($id_thes_unique).$element. $no_display;
                    $ret.= static::get_button_browse($field, $browse_link);
                }
            }
            $display_temp ="<input type='text' att_id_filter='".$att_id_filter."' ".$params." completion='$completion' class='saisie-50emr' id='f_".$field["NAME"]."_$i'  persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."[]' " . $autexclude . " data-form-name='f_".$field["NAME"]."_'  $readonly value=\"".htmlentities($isbd,ENT_QUOTES,$charset)."\" />\n";
            $display_temp.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."[]' data-form-name='".$field["NAME"]."_' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
            $display_temp.=static::get_button_raz($field, "this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value='';")."\n";
            if ($i == $n-1 && $options['MULTIPLE'][0]['value']=="yes") {
                $display_temp .= "<input id='button_add_".$field['NAME']."' type='button' class='bouton' value='+' onClick=\"add_".$field["NAME"]."(this);\"/>";
            }
            if ($options['MULTIPLE'][0]['value']=="yes") {
                $ret.=get_block_dnd('query_auth', $field['NAME'], $i, $display_temp, $isbd);
            } else {
                $ret.=$display_temp."<br />";
            }
        }
        $ret.="</div>";
        
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $msg, $categ,$charset;
        global $_custom_prefixe_, $caller;
        global $ajax_js_already_included;
        global $base_path;
        
        if(!empty($field['NUMBER'])) {
            $field_name = $field['NAME']."_".$field['NUMBER'];
        } else {
            $field_name = $field['NAME'];
        }
        if($field["OPTIONS"][0]["METHOD"]["0"]["value"]==1) {
            $hidden_name=$field_name;
        } else {
            $hidden_name=$field_name."_id";
        }
        $id=(isset($field['VALUES'][0]) ? $field['VALUES'][0] : '');
        
        $selection_parameters = get_authority_selection_parameters($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]);
        $what = $selection_parameters['what'];
        $completion = $selection_parameters['completion'];
        
        $params = $att_id_filter= "";
        $fnamevar_id = '';
        $id_thesaurus = '';
        $id_thes_unique=0;
        $element="";
        switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
            case 2:
                //Pour n'appeler que le thésaurus choisi en champ perso
                if(isset($field["OPTIONS"][0]["ID_THES"]["0"]["value"])){
                    $fnamevar_id = "linkfield=\"fieldvar_".$varname."_id_thesaurus\"";
                    $id_thesaurus="<input  type='hidden' id='fieldvar_".$varname."_id_thesaurus' name='fieldvar_".$varname."_id_thesaurus' value='".$field["OPTIONS"][0]["ID_THES"]["0"]["value"]."'>";
                    $id_thes_unique=$field["OPTIONS"][0]["ID_THES"]["0"]["value"];
                    $att_id_filter = $id_thes_unique;
                }
                break;//categories
            case 9:
                $concept_schemes = [];
                if (!empty($field['OPTIONS'][0]['ID_SCHEME_CONCEP'])) {
                    for($i=0 ; $i<count($field['OPTIONS'][0]['ID_SCHEME_CONCEP']) ; $i++){
                        $concept_schemes[] = $field['OPTIONS'][0]['ID_SCHEME_CONCEP'][$i]['value'];
                    }
                }
                $element="&element=concept&param1=".$field_name."&param2=f_".$field_name;
                if(!empty($concept_schemes[0]) && $concept_schemes[0] != -1){
                    //concept_scheme;
                    $params = " param1='".implode(',',$concept_schemes)."'";
                    $element.="&return_concept_id=1&unique_scheme=1&concept_scheme=".implode(',',$concept_schemes);
                }
                $att_id_filter= "http://www.w3.org/2004/02/skos/core#Concept";
                break;
        }
        $libelle="";
        if($id){
            $libelle = get_authority_isbd_from_field($field, $id);
        }
        
        $ret = "";
        if(empty($ajax_js_already_included)){
            $ajax_js_already_included = true;
            $ret = "<script src='javascript/ajax.js'></script>";
        }
        switch ($categ) {
            case "planificateur" :
                $form_name = "planificateur_form";
                break;
            default :
                $form_name = "formulaire";
                break;
        }
        
        $val_dyn=3;
        $ret.="<input type='text'  att_id_filter='".$att_id_filter."' ".$params." completion='$completion' autfield='".$field_name."'  class='saisie-50emr' id='f_".$field_name."' persofield='".$field["NAME"]."' name='f_".$field_name."' data-form-name='f_".$field_name."' $fnamevar_id value=\"".htmlentities($libelle,ENT_QUOTES,$charset)."\" />\n";
        $ret.="<input type='hidden' id='".$field_name."' name='".$varname."[]'  data-form-name='".$field_name."'  value=\"".htmlentities($id,ENT_QUOTES,$charset)."\">";
        
        $ret.="<input type='button' class='bouton' value='...' title='".htmlentities($msg['title_select_from_list'],ENT_QUOTES,$charset)."' onclick=\"openPopUp('".$base_path."/select.php?what=$what&caller=".($caller ? $caller : 'search_form').$element."&p1=".$field_name."_0&p2=f_".$field_name."_0&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']
        ."&max_field=n_".$field_name."&field_id=".$field_name."&field_name_id=f_".$field_name."&add_field=add_".$field_name.($id_thes_unique?"&id_thes_unique=".$id_thes_unique:"")."', 'select_perso_".$field["ID"]
        ."', 700, 500, -2, -2,'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
        $ret.="<input name='".$hidden_name."' id='".$hidden_name."'  value='".htmlentities($id,ENT_QUOTES,$charset)."' type='hidden'>$id_thesaurus";
        
        if ($field['MANDATORY']=="yes") $check_scripts.="if (document.".$form_name.".".$field_name.".value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value'])."\");\n";
        return $ret;
    }
    
    public static function get_rgaa_label($field, $varname) {
        return "f_".$field["NAME"]."_0";
    }
}