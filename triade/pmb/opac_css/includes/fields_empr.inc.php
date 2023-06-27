<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fields_empr.inc.php,v 1.115 2019-05-29 14:22:20 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/categories.class.php');
require_once($class_path.'/publisher.class.php');

global $aff_list_empr, $msg;
$aff_list_empr = array(
    "text" => "aff_text_empr",
    "list" => "aff_list_empr",
    "query_list" => "aff_query_list_empr",
    "query_auth" => "aff_query_auth_empr",
    "date_box" => "aff_date_box_empr",
    "comment" => "aff_comment_empr",
    "external" => "aff_external_empr",
    "url" => "aff_url_empr",
    "resolve" => "aff_resolve_empr",
    "marclist" => "aff_marclist_empr",
    "html" => "aff_html_empr",
    "text_i18n" => "aff_text_i18n_empr",
    "q_txt_i18n" => "aff_q_txt_i18n_empr",
    "date_inter" => "aff_date_inter_empr",
    "date_flot" => "aff_date_flottante_empr"
);

global $aff_list_empr_search;
$aff_list_empr_search = array(
    "text" => "aff_text_empr_search",
    "list" => "aff_list_empr_search",
    "query_list" => "aff_query_list_empr_search",
    "query_auth" => "aff_query_auth_empr_search",
    "date_box" => "aff_date_box_empr_search",
    "comment" => "aff_comment_empr_search",
    "external" => "aff_external_empr_search",
    "url" => "aff_url_empr_search",
    "resolve" => "aff_resolve_empr_search",
    "marclist" => "aff_marclist_empr_search",
    "html" => "aff_comment_empr_search",
    "text_i18n" => "aff_text_i18n_empr_search",
    "q_txt_i18n" => "aff_q_txt_i18n_empr_search",
    "date_inter" => "aff_date_inter_empr_search",
    "date_flot" => "aff_date_flottante_empr_search"
);
$aff_filter_list_empr = array(
    "text" => "aff_filter_text_empr",
    "list" => "aff_filter_list_empr",
    "query_list" => "aff_filter_query_list_empr",
    "query_auth" => "aff_filter_query_auth_empr",
    "date_box" => "aff_filter_date_box_empr",
    "comment" => "aff_filter_comment_empr",
    "external" => "aff_filter_external_empr",
    "url" => "aff_filter_resolve_empr",
    "resolve" => "aff_filter_resolve_empr",
    "marclist" => "aff_filter_marclist_empr",
    "html" => "aff_filter_comment_empr",
    "text_i18n" => "aff_filter_text_i18n_empr",
    "q_txt_i18n" => "aff_filter_q_txt_i18n_empr",
    "date_inter" => "aff_filter_date_inter_empr",
    "date_flot" => "aff_filter_date_flottante_empr"
);

global $chk_list_empr;
$chk_list_empr = array(
    "text" => "chk_text_empr",
    "list" => "chk_list_empr",
    "query_list" => "chk_query_list_empr",
    "query_auth" => "chk_query_auth_empr",
    "date_box" => "chk_date_box_empr",
    "comment" => "chk_comment_empr",
    "external" => "chk_external_empr",
    "url" => "chk_url_empr",
    "resolve" => "chk_resolve_empr",
    "marclist" => "chk_marclist_empr",
    "html" => "chk_comment_empr",
    "text_i18n" => "chk_text_i18n_empr",
    "q_txt_i18n" => "chk_q_txt_i18n_empr",
    "date_inter" => "chk_date_inter_empr",
    "date_flot" => "chk_date_flottante_empr"
);

global $val_list_empr;
$val_list_empr = array(
    "text" => "val_text_empr",
    "list" => "val_list_empr",
    "query_list" => "val_query_list_empr",
    "query_auth" => "val_query_auth_empr",
    "date_box" => "val_date_box_empr",
    "comment" => "val_comment_empr",
    "external" => "val_external_empr",
    "url" => "val_url_empr",
    "resolve" => "val_resolve_empr",
    "marclist" => "val_marclist_empr",
    "html" => "val_html_empr",
    "text_i18n" => "val_text_i18n_empr",
    "q_txt_i18n" => "val_q_txt_i18n_empr",
    "date_inter" => "val_date_inter_empr",
    "date_flot" => "val_date_flottante_empr"
);

global $type_list_empr;
$type_list_empr = array(
    "text" => $msg["parperso_text"],
    "list" => $msg["parperso_choice_list"],
    "query_list" => $msg["parperso_query_choice_list"],
    "query_auth" => $msg["parperso_authorities"],
    "date_box" => $msg["parperso_date"],
    "comment" => $msg["parperso_comment"],
    "external" => $msg["parperso_external"],
    "url" => $msg["parperso_url"],
    "resolve" => $msg["parperso_resolve"],
    "marclist" => $msg["parperso_marclist"],
    "html" => $msg["parperso_html"],
    "text_i18n" => $msg["parperso_text_i18n"],
    "q_txt_i18n" => $msg["parperso_q_txt_i18n"],
    "date_inter" => $msg["parperso_date_inter"],
    "date_flot" => $msg["parperso_date_flottante"]
);

global $options_list_empr;
$options_list_empr = array(
    "text" => "options_text.php",
    "list" => "options_list.php",
    "query_list" => "options_query_list.php",
    "query_auth" => "options_query_authorities.php",
    "date_box" => "options_date_box.php",
    "comment" => "options_comment.php",
    "external" => "options_external.php",
    "url" => "options_url.php",
    "resolve" => "options_resolve.php",
    "marclist" => "options_marclist.php",
    "html" => "options_html.php",
    "text_i18n" => "options_text_i18n.php",
    "q_txt_i18n" => "options_q_txt_i18n.php",
    "date_inter" => "options_date_inter.php",
    "date_flot" => "options_date_flottante.php"
);

// formulaire de saisie des param perso des autorités
function aff_query_auth_empr($field,&$check_scripts,$script="") {
    global $charset;
    global $_custom_prefixe_;
    global $msg,$lang;
    global $ajax_js_already_included;
    global $base_path;
    global $include_path;
    
    $id_thes_unique=$field["OPTIONS"][0]["ID_THES"]["0"]["value"];
    $att_id_filter= $params = $element = "";
    $selection_parameters = get_authority_selection_parameters($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]);
    $what = $selection_parameters['what'];
    $completion = $selection_parameters['completion'];
    switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
        case 2:
            $att_id_filter= $id_thes_unique;
            break;//categories
        case 9:
            $element="&element=concept&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
            if(isset($field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']) && $field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'] != -1){
                //concept_scheme;
                $params = " param1='".$field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']."'";
                $element.="&return_concept_id=1&unique_scheme=1&concept_scheme=".$field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'];
            }
            $att_id_filter= "http://www.w3.org/2004/02/skos/core#Concept";
            break;
    }
    
    $values=$field['VALUES'];
    
    $options=$field['OPTIONS'][0];
    
    if ($values=="") $values=array();
    $caller = get_form_name();
    
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
		function add_".$field["NAME"]."() {
			suffixe = eval('document.$caller.n_".$field["NAME"].".value');
			    
			var node_dnd_id = get_custom_dnd_on_add('div_".$field["NAME"]."', 'customfield_query_auth_".$field["NAME"]."', suffixe);
			    
			var nom_id = '".$field["NAME"]."_'+suffixe;
			var f_perso = document.createElement('input');
			f_perso.setAttribute('name','f_".$field["NAME"]."[]');
			f_perso.setAttribute('id','f_'+nom_id);
			f_perso.setAttribute('completion','$completion');
			f_perso.setAttribute('att_id_filter','".$att_id_filter."');
			f_perso.setAttribute('persofield','".$field["NAME"]."');
			f_perso.setAttribute('autfield',nom_id);";
        if (isset($field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']) && ($field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'] != -1) && ($what == "ontology")) {
            $ret.= "
			f_perso.setAttribute('param1','".$field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']."');";
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
			
			document.$caller.n_".$field["NAME"].".value=suffixe*1+1*1 ;
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
        switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
            case 9:// concept
                $element="&element=concept&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
                if(isset($field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']) && $field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'] != -1){
                    $element.="&return_concept_id=1&unique_scheme=1&concept_scheme=".$field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'];
                }
                break;
            default:
                if($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]>1000){
                    // autperso
                    $element="&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
                    $val_dyn=4;
                }
                break;
        }
        if (($i==0)) {
            if ($options['MULTIPLE'][0]['value']=="yes") {
                $ret .= get_js_function_dnd('query_auth', $field['NAME']);
                $ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=$what&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']
                ."&max_field=n_".$field["NAME"]."&field_id=".$field["NAME"]."_&field_name_id=f_".$field["NAME"]."_&add_field=add_".$field["NAME"]."&id_thes_unique=".urlencode($id_thes_unique).$element."', 'select_perso_".$field["ID"]
                ."', 700, 500, -2, -2,'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" /> ";
                $ret.=" <input type='button' class='bouton' value='+' onClick=\"add_".$field["NAME"]."();\"/>";
            }else {
                $ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=$what&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']
                ."&max_field=n_".$field["NAME"]."&field_id=".$field["NAME"]."_0&field_name_id=f_".$field["NAME"]."_0&add_field=add_".$field["NAME"]."&id_thes_unique=".urlencode($id_thes_unique).$element."', 'select_perso_".$field["ID"]
                ."', 700, 500, -2, -2,'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
                
            }
        }
        $display_temp ="<input type='text' att_id_filter='".$att_id_filter."' ".$params." completion='$completion' autfield='".$field["NAME"]."_$i' class='saisie-50emr' id='f_".$field["NAME"]."_$i'  persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."[]'  data-form-name='f_".$field["NAME"]."_'  $readonly value=\"".htmlentities($isbd,ENT_QUOTES,$charset)."\" />\n";
        $display_temp.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."[]' data-form-name='".$field["NAME"]."_' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
        $display_temp.="<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value=''; \" />\n";
        if ($options['MULTIPLE'][0]['value']=="yes") {
            $ret.=get_block_dnd('query_auth', $field['NAME'], $i, $display_temp, $isbd);
        } else {
            $ret.=$display_temp."<br />";
        }
    }
    $ret.="</div>";
    
    return $ret;
}

function aff_query_auth_empr_search($field,&$check_scripts,$varname) {
    global $msg,$lang, $categ,$charset;
    global $_custom_prefixe_, $caller;
    global $ajax_js_already_included;
    global $base_path;
    
    if($field["OPTIONS"][0]["METHOD"]["0"]["value"]==1) {
        $hidden_name=$field['NAME'];
    } else {
        $hidden_name=$field['NAME']."_id";
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
                $fnamevar_id = "linkfield=\"fieldvar_".substr($varname, 6)."_id_thesaurus\"";
                $id_thesaurus="<input  type='hidden' id='fieldvar_".substr($varname, 6)."_id_thesaurus' name='fieldvar_".substr($varname, 6)."_id_thesaurus' value='".$field["OPTIONS"][0]["ID_THES"]["0"]["value"]."'>";
                $id_thes_unique=$field["OPTIONS"][0]["ID_THES"]["0"]["value"];
                $att_id_filter = $id_thes_unique;
            }
            break;//categories
        case 9:
            $element="&element=concept&param1=".$field["NAME"]."&param2=f_".$field["NAME"];
            if(isset($field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']) && $field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'] != -1){
                $params = "param1='".$field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value']."'";
                $element.="&return_concept_id=1&unique_scheme=1&concept_scheme=".$field['OPTIONS'][0]['ID_SCHEME_CONCEP'][0]['value'];
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
    
    $val_dyn=3;
    $ret.="<input type='text'  att_id_filter='".$att_id_filter."' ".$params." completion='$completion' autfield='".$field["NAME"]."'  class='saisie-50emr' id='f_".$field["NAME"]."'  persofield='".$field["NAME"]."' autfield='".$field["NAME"]."' name='f_".$field["NAME"]."' data-form-name='f_".$field["NAME"]."' $fnamevar_id value=\"".htmlentities($libelle,ENT_QUOTES,$charset)."\" />\n";
    $ret.="<input type='hidden' id='".$field["NAME"]."' name='".$varname."[]'  data-form-name='".$field["NAME"]."'  value=\"".htmlentities($id,ENT_QUOTES,$charset)."\">";
    
    $ret.="<input type='button' class='bouton' value='...' title='".htmlentities($msg['title_select_from_list'],ENT_QUOTES,$charset)."' onclick=\"openPopUp('".$base_path."/select.php?what=$what&caller=$caller".$element."&p1=".$field["NAME"]."_0&p2=f_".$field["NAME"]."_0&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']
    ."&max_field=n_".$field["NAME"]."&field_id=".$field["NAME"]."&field_name_id=f_".$field["NAME"]."&add_field=add_".$field["NAME"].($id_thes_unique?"&id_thes_unique=".$id_thes_unique:"")."', 'select_perso_".$field["ID"]
    ."', 700, 500, -2, -2,'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
    $ret.="<input name='".$hidden_name."' id='".$hidden_name."'  value='".htmlentities($id,ENT_QUOTES,$charset)."' type='hidden'>$id_thesaurus";
    return $ret;
}

// Sauvegarde du formulaire
function chk_query_auth_empr($field,&$check_message) {
    global $charset;
    global $msg;
    $name=$field['NAME'];
    $options=$field['OPTIONS'][0];
    global ${$name};
    $val=array();
    $tmp_values=${$name};
    if(is_array($tmp_values)) {
        foreach ($tmp_values as $k=>$v) {
            if ($v!="") {
                $val[]=$v;
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

function val_query_auth_empr($field,$val) {
    global $lang,$pmb_perso_sep,$charset;
    
    $name=$field['NAME'];
    $options=$field['OPTIONS'][0];
    $isbd_s=array();
    $isbd_without=array();
    $details=array();
    if(!$val)return "";
    
    foreach($val as $id){
        if($id) {
            $isbd = get_authority_isbd_from_field($field, $id);
            $isbd_s[] = htmlentities($isbd, ENT_QUOTES, $charset);
            $isbd_without[] = $isbd;
            $details[] = get_authority_details_from_field($field, $id);
        }
    }
    return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$isbd_s), "withoutHTML" =>implode($pmb_perso_sep,$isbd_without), "details" => $details);
}

function chk_datatype($field,$values,&$check_datatype_message) {
    global $chk_type_list;
    global $msg;
    
    if (((!count($values))||((count($values)==1)&&($values[0]=="")))&&($field['MANDATORY']!=1)) return $values;
    for ($i=0; $i<count($values); $i++) {
        $chk_message="";
        eval("\$val=".$chk_type_list[$field['DATATYPE']]."(stripslashes(\$values[\$i]),\$chk_message);");
        if ($chk_message) {
            $check_datatype_message=sprintf($msg["parperso_chk_datatype"],$field['NAME'],$chk_message);
        }
        $values[$i]=addslashes($val);
    }
    return $values;
}

function format_output($field,$values) {
    global $format_list;
    for ($i=0; $i<count($values); $i++) {
        eval("\$val=".$format_list[$field['DATATYPE']]."(\$values[\$i]);");
        $values[$i]=$val;
    }
    return $values;
}

//fonction de découpage d'une chaine trop longue
function cutlongwords($valeur) {
    global $charset;
    $valeur=str_replace("\n"," ",$valeur);
    if (strlen($valeur)>=20) {
        $pos=strrpos(substr($valeur,0,20)," ");
        if ($pos) {
            $valeur=substr($valeur,0,$pos+1)."...";
        } else $valeur=substr($valeur,0,20)."...";
    }
    return $valeur;
}

function aff_date_box_empr($field,&$check_scripts) {
    global $charset;
    global $msg;
    global $base_path;
    
    $values = ($field['VALUES'] ? $field['VALUES'] : array(""));
    $options = $field['OPTIONS'][0];
    $afield_name = $field["ID"];
    $count = 0;
    $ret = "";
    foreach ($values as $value) {
        $d=explode("-",$value);
        
        if ((!@checkdate($d[1],$d[2],$d[0]))&&(!isset($options["DEFAULT_TODAY"][0]["value"]) || !$options["DEFAULT_TODAY"][0]["value"])) {
            $val=date("Y-m-d",time());
            $val_popup=date("Ymd",time());
        } else if ((!@checkdate($d[1],$d[2],$d[0]))&&(isset($options["DEFAULT_TODAY"][0]["value"]) && $options["DEFAULT_TODAY"][0]["value"])) {
            $val_popup="";
            $val="";
        } else {
            $val_popup=$d[0].$d[1].$d[2];
            $val=$value;
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

function aff_date_box_empr_search($field,&$check_scripts,$varname) {
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
			$d=explode("-",$values[0]);
			if (!@checkdate($d[1],$d[2],$d[0])) {
				$val='';
			} else {
				$val=$values[0];
			}
			$ret.="<input type='text' style='width: 10em;' name='".$varname."[]' id='".$varname."[]' value='".$val."'  data-dojo-type='dijit/form/DateTextBox' constraints=\"{datePattern:'".getDojoPattern($msg['format_date'])."'}\" required='false' />";
			break;
	}
	$ret.="
		</div>";
    
    $values=$field['VALUES1'];
    $d=explode("-",$values[0]);
    if (!@checkdate($d[1],$d[2],$d[0])) {
        $val='';
    } else {
        $val=$values[0];
    }
    $ret.="
		<div id='".$varname."_end_part[]' style='display: inline-block;'>
			 - <input type='text' style='width: 10em;' name='".$varname."_1[]' id='".$varname."_1[]' value='".$val."'  data-dojo-type='dijit/form/DateTextBox' constraints=\"{datePattern:'".getDojoPattern($msg['format_date'])."'}\" required='false' />
		</div>";
    return $ret;
}

function chk_date_box_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_date_box_empr($field,$value) {
    global $charset, $pmb_perso_sep;
    
    $return = "";
    $format_value = format_output($field,$value);
    if (!$value) $value = array();
    foreach ($value as $key => $val) {
        if ($val == "0000-00-00") $val = "";
        if ($val) {
            if ($return) $return .= $pmb_perso_sep;
            $return .= $format_value[$key];
        }
    }
    return $return;
}

function aff_text_empr($field,&$check_scripts) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if(!isset($values[0])) $values[0] = '';
    $ret="<input id=\"".$field['NAME']."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
    if ($field['MANDATORY']==1) $check_scripts.="if (document.forms[0].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    return $ret;
}

function aff_text_empr_search($field,&$check_scripts,$varname) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if(!isset($values[0])) $values[0] = '';
    $ret="<input id=\"".$varname."\" type=\"text\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
    return $ret;
}

function chk_text_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_text_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    
    $value=format_output($field,$value);
    if (!$value) $value=array();
    
    if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
    if($field["OPTIONS"][0]["ISHTML"][0]["value"]){
        return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
    }else{
        return implode($pmb_perso_sep,$value);
    }
}

function aff_comment_empr($field,&$check_scripts) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if(!isset($values[0])) $values[0] = '';
    $ret="<textarea id=\"".$field['NAME']."\" cols=\"".$options['COLS'][0]['value']."\"  rows=\"".$options['ROWS'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[]\" wrap=virtual>".htmlentities($values[0],ENT_QUOTES,$charset)."</textarea>";
    if ($field['MANDATORY']==1) $check_scripts.="if (document.forms[0].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    return $ret;
}

function aff_comment_empr_search($field,&$check_scripts,$varname) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if(!isset($values[0])) $values[0] = '';
    $ret="<textarea id=\"".$varname."\" cols=\"".$options['COLS'][0]['value']."\"  rows=\"".$options['ROWS'][0]['value']."\" name=\"".$varname."[]\" wrap=virtual>".htmlentities($values[0],ENT_QUOTES,$charset)."</textarea>";
    return $ret;
}

function chk_comment_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_comment_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    
    $value=format_output($field,$value);
    if (!$value) $value=array();
    
    if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
    if($field["OPTIONS"][0]["ISHTML"][0]["value"]){
        return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
    }else{
        return implode($pmb_perso_sep,$value);
    }
}

function val_html_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    
    $value=format_output($field,$value);
    if (!$value) $value=array();
    
    return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
}

function aff_list_empr($field,&$check_scripts,$script="") {
    global $charset;
    global $_custom_prefixe_;
    global $base_path;
    
    $ret = '';
    $_custom_prefixe_=$field["PREFIX"];
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if ($values=="") $values=array();
    
    if ($options["AUTORITE"][0]["value"]!="yes") {
        if ($options["CHECKBOX"][0]["value"]=="yes"){
            if ($options['MULTIPLE'][0]['value']=="yes") $type = "checkbox";
            else $type = "radio";
            if (($options['UNSELECT_ITEM'][0]['VALUE']!="")&&($options['UNSELECT_ITEM'][0]['value']!="")) {
                $ret.= "<input id='".$field['NAME']."_".$options['UNSELECT_ITEM'][0]['VALUE']."' type='$type' name='".$field['NAME']."[]' checked=checked";
                $ret.=" value='".$options['UNSELECT_ITEM'][0]['VALUE']."' /><span id='lib_".$field['NAME']."_".$options['UNSELECT_ITEM'][0]['VALUE']."'>&nbsp;".$options['UNSELECT_ITEM'][0]['value']."</span>";
            }
            $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
            $resultat=pmb_mysql_query($requete);
            if ($resultat) {
                $i=0;
                $limit = (isset($options['CHECKBOX_NB_ON_LINE'][0]['value']) ? $options['CHECKBOX_NB_ON_LINE'][0]['value'] : 4);
                while ($r=pmb_mysql_fetch_array($resultat)) {
                    if($limit && $i>0 && $i%$limit == 0) $ret.="<br />";
                    $ret.= "<input id='".$field['NAME']."_".$r[$_custom_prefixe_."_custom_list_value"]."' type='$type' name='".$field['NAME']."[]'";
                    if (count($values)) {
                        $as=in_array($r[$_custom_prefixe_."_custom_list_value"],$values);
                        if (($as!==FALSE)&&($as!==NULL)) $ret.=" checked=checked";
                    } else {
                        //Recherche de la valeur par défaut s'il n'y a pas de choix vide
                        if (($options['UNSELECT_ITEM'][0]['VALUE']=="") || ($options['UNSELECT_ITEM'][0]['value']=="")) {
                            //si aucune valeur par défaut, on coche le premier pour les boutons de type radio
                            if (($i==0)&&($type=="radio")&&($options['DEFAULT_VALUE'][0]['value']=="")) $ret.=" checked=checked";
                            elseif ($r[$_custom_prefixe_."_custom_list_value"]==$options['DEFAULT_VALUE'][0]['value']) $ret.=" checked=checked";
                        }
                    }
                    $ret.=" value='".$r[$_custom_prefixe_."_custom_list_value"]."'/><span id='lib_".$field['NAME']."_".$r[$_custom_prefixe_."_custom_list_value"]."'>&nbsp;".$r[$_custom_prefixe_."_custom_list_lib"]."</span>";
                    $i++;
                }
            }
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
                    if ($options['ITEMS'][0]['ITEM'][$i]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
                }
                $ret.=">".htmlentities($options['ITEMS'][0]['ITEM'][$i]['value'],ENT_QUOTES,$charset)."</option>\n";
            }
            $ret.= "</select>\n";
        }
    }else {
        $libelles=array();
        $caller = get_form_name();
        if ($values) {
            $values_received=$values;
            $values=array();
            $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
            $resultat=pmb_mysql_query($requete);
            $i=0;
            while ($r=pmb_mysql_fetch_array($resultat)) {
                $as=array_search($r[$_custom_prefixe_."_custom_list_value"],$values_received);
                if (($as!==null)&&($as!==false)) {
                    $values[$i]=$r[$_custom_prefixe_."_custom_list_value"];
                    $libelles[$i]=$r[$_custom_prefixe_."_custom_list_lib"];
                    $i++;
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
				template = document.getElementById('div_".$field["NAME"]."');
				perso=document.createElement('div');
				perso.className='row';
				    
				suffixe = document.getElementById('n_".$field["NAME"]."').value;
				var nom_id = '".$field["NAME"]."_'+suffixe;
				var f_perso = document.createElement('input');
				f_perso.setAttribute('name','f_'+nom_id);
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
        $readonly='';
        for ($i=0; $i<$n; $i++) {
            $ret.="<input type='text' class='saisie-50emr' id='f_".$field["NAME"]."_$i' completion='perso_".$_custom_prefixe_."' persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."_$i' $readonly value=\"".htmlentities($libelles[$i],ENT_QUOTES,$charset)."\" />\n";
            $ret.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."_$i' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
            
            //			$ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('./select.php?what=perso&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />
            $ret.="<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value=''; \" />\n";
            if (($i==0)&&($options['MULTIPLE'][0]['value']=="yes")) {
                $ret.=" <input type='button' class='bouton' value='+' onClick=\"add_".$field["NAME"]."();\"/>";
            }
            $ret.="<br />";
        }
        $ret.="</div>";
    }
    return $ret;
}

function aff_list_empr_search($field,&$check_scripts,$varname,$script="") {
    global $charset;
    global $base_path;
    
    $_custom_prefixe_=$field["PREFIX"];
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if ($values=="") $values=array();
    if ($options["AUTORITE"][0]["value"]!="yes") {
        $ret="<select id=\"".$varname."\" name=\"".$varname;
        $ret.="[]";
        $ret.="\" ";
        if ($script) $ret.=$script." ";
        $ret.="multiple";
        $ret.=" data-form-name='".$varname."' >\n";
        if (($options['UNSELECT_ITEM'][0]['VALUE']!="")) {
            $requete="select * from ".$_custom_prefixe_."_custom_values where ".$_custom_prefixe_."_custom_champ=".$field['ID']." and ".$_custom_prefixe_."_custom_".$field['DATATYPE']."='".$options['UNSELECT_ITEM'][0]['VALUE']."'";
            $resultat=pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
            }
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
            $as=array_search($options['ITEMS'][0]['ITEM'][$i]['VALUE'],$values);
            if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
            $ret.=">".htmlentities($options['ITEMS'][0]['ITEM'][$i]['value'],ENT_QUOTES,$charset)."</option>\n";
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
				f_perso.setAttribute('persofield','".substr($field["NAME"], 0, strrpos($field["NAME"], "_"))."');
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
            $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
            $resultat=pmb_mysql_query($requete);
            $i=0;
            while ($r=pmb_mysql_fetch_array($resultat)) {
                $as=array_search($r[$_custom_prefixe_."_custom_list_value"],$values_received);
                if (($as!==null)&&($as!==false)) {
                    $values[$i]=$r[$_custom_prefixe_."_custom_list_value"];
                    $libelles[$i]=$r[$_custom_prefixe_."_custom_list_lib"];
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
        $ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=perso&caller=search_form&p1=".$varname."&p2=f_".$varname."&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
        $ret.="<input type='button' class='bouton' value='+' onClick=\"add_".$varname."();\"/>";
        $ret.="<div id='div_".$varname."'>";
        for($inc=0;$inc<$nb_values;$inc++){
            $ret.="<div class='row'>";
            $ret.="<input type='hidden' id='".$varname."_".$inc."' name='".$varname."[]' data-form-name='".$varname."[]' value=\"".htmlentities($values[$inc],ENT_QUOTES,$charset)."\">";
            $ret.="<input type='text' class='saisie-20emr' id='f_".$varname."_".$inc."' completion='perso_".$_custom_prefixe_."' persofield='".substr($field["NAME"], 0, strrpos($field["NAME"], "_"))."' autfield='".$varname."_".$inc."' name='f_".$varname."[]' data-form-name='f_".$varname."[]' value=\"".htmlentities($libelles[$inc],ENT_QUOTES,$charset)."\" />\n";
            $ret.="<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$varname."_".$inc.".value=''; this.form.".$varname."_".$inc.".value=''; \" />\n";
            $ret.="</div>";
        }
        $ret.="</div>";
    }
    return $ret;
}

function aff_empr_search($field) {
    $table = array();
    $table['label'] = $field['TITRE'];
    $table['name'] = $field['NAME'];
    $table['type'] =$field['DATATYPE'];
    
    $_custom_prefixe_=$field['PREFIX'];
    $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
    $resultat=pmb_mysql_query($requete);
    if ($resultat) {
        while ($r=pmb_mysql_fetch_array($resultat)) {
            $value['value_id']=$r[$_custom_prefixe_."_custom_list_value"];
            $value['value_caption']=$r[$_custom_prefixe_."_custom_list_lib"];
            $table['values'][]=$value;
        }
    }else{
        $table['values'] = array();
    }
    return $table;
}

function chk_list_empr($field,&$check_message) {
    global $charset;
    global $msg;
    
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
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

function val_list_empr($field,$val) {
    global $charset,$pmb_perso_sep;
    global $options_;
    $_custom_prefixe_=$field['PREFIX'];
    
    if ($val=='') return '';
    
    if (!isset($options_[$_custom_prefixe_][$field['ID']]) || !$options_[$_custom_prefixe_][$field['ID']]) {
        $options_[$_custom_prefixe_][$field['ID']] = array();
        $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
        $resultat=pmb_mysql_query($requete);
        if ($resultat) {
            while ($r=pmb_mysql_fetch_array($resultat)) {
                $options_[$_custom_prefixe_][$field['ID']][$r[$_custom_prefixe_.'_custom_list_value']]=$r[$_custom_prefixe_.'_custom_list_lib'];
            }
        }
    }
    if (!is_array($options_[$_custom_prefixe_][$field['ID']])) return '';
    if(!isset($val[0])) $val[0] = '';
    if($val[0] != null){
        $val_r=array_flip($val);
        $val_c=array_intersect_key($options_[$_custom_prefixe_][$field['ID']],$val_r);
        if ($val_c=='') {
            $val_c=array();
        }
        $val_=implode($pmb_perso_sep,$val_c);
    }else{
        $val_ = '';
    }
    return $val_;
}

function aff_query_list_empr($field,&$check_scripts,$script="") {
    global $charset;
    global $_custom_prefixe_;
    global $base_path;
    
    $ret = '';
    $values=$field['VALUES'];
    
    $options=$field['OPTIONS'][0];
    
    if ($values=="") $values=array();
    if ($options["AUTORITE"][0]["value"]!="yes") {
        if ($options["CHECKBOX"][0]["value"]=="yes"){
            if ($options['MULTIPLE'][0]['value']=="yes") $type = "checkbox";
            else $type = "radio";
            $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
            if ($resultat) {
                $i=0;
                $ret="<table><tr>";
                $limit = $options['CHECKBOX_NB_ON_LINE'][0]['value'];
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
            $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
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
            $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
            $i=0;
            while ($r=pmb_mysql_fetch_row($resultat)) {
                $as=array_search($r[0],$values_received);
                if (($as!==null)&&($as!==false)) {
                    $values[$i]=$r[0];
                    $libelles[$i]=$r[1];
                    $i++;
                    unset($values_received_bis[$as]);
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
                //			$ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('./select.php?what=perso&caller=$caller&p1=".$field["NAME"]."_$i&p2=f_".$field["NAME"]."_$i&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=$val_dyn&perso_name=".$field['NAME']."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2,'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />
                $ret.="<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value=''; \" />\n";
                if (($i==0)&&($options['MULTIPLE'][0]['value']=="yes")) {
                    $ret.=" <input type='button' class='bouton' value='+' onClick=\"add_".$field["NAME"]."();\"/>";
                }
                $ret.="<br />";
            }
            $ret.="</div>";
    }
    return $ret;
}

function aff_query_list_empr_search($field,&$check_scripts,$varname,$script="") {
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
				f_perso.setAttribute('persofield','".substr($field["NAME"], 0, strrpos($field["NAME"], "_"))."');
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
        $ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=perso&caller=search_form&p1=".$varname."&p2=f_".$varname."&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
        $ret.="<input type='button' class='bouton' value='+' onClick=\"add_".$varname."();\"/>";
        $ret.="<div id='div_".$varname."'>";
        for($inc=0;$inc<$nb_values;$inc++){
            $ret.="<div class='row'>";
            $ret.="<input type='hidden' id='".$varname."_".$inc."' name='".$varname."[]' data-form-name='".$varname."[]' value=\"".htmlentities($values[$inc],ENT_QUOTES,$charset)."\">";
            $ret.="<input type='text' class='saisie-20emr' id='f_".$varname."_".$inc."' completion='perso_".$_custom_prefixe_."' persofield='".substr($field["NAME"], 0, strrpos($field["NAME"], "_"))."' autfield='".$varname."_".$inc."' name='f_".$varname."[]' data-form-name='f_".$varname."[]' value=\"".htmlentities($libelles[$inc],ENT_QUOTES,$charset)."\" />\n";
            $ret.="<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$varname."_".$inc.".value=''; this.form.".$varname."_".$inc.".value=''; \" />\n";
            $ret.="</div>";
        }
        $ret.="</div>";
    }
    return $ret;
}

function chk_query_list_empr($field,&$check_message) {
    global $charset;
    global $msg;
    
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
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

function val_query_list_empr($field,$val) {
    global $charset,$pmb_perso_sep;
    
    if ($val=="") return "";
    $val_c=[];
    if (($field["OPTIONS"][0]["FIELD0"][0]["value"])&&($field["OPTIONS"][0]["FIELD1"][0]["value"])&&($field["OPTIONS"][0]["OPTIMIZE_QUERY"][0]["value"]=="yes")) {
		if(is_array($val) && count($val)) {
       	 	$val_ads=array_map("addslashes",$val);
        	$requete="select * from (".$field['OPTIONS'][0]['QUERY'][0]['value'].") as sub1 where ".$field["OPTIONS"][0]["FIELD0"][0]["value"]." in (BINARY '".implode("',BINARY '",$val_ads)."')";
        	$resultat=pmb_mysql_query($requete);
        	if ($resultat && pmb_mysql_num_rows($resultat)) {
           		 while ($r=pmb_mysql_fetch_row($resultat)) {
                	$val_c[]=$r[1];
            	}
        	}
		}
    } else {
        $resultat=pmb_mysql_query($field['OPTIONS'][0]['QUERY'][0]['value']);
        if($resultat && pmb_mysql_num_rows($resultat)){
            while ($r=pmb_mysql_fetch_row($resultat)) {
                $options_[$r[0]]=$r[1];
            }
        }
        
        for ($i=0; $i<count($val); $i++) {
            if(isset($val[$i])) {
                $val_c[$i]=$options_[$val[$i]];
            }
        }
    } 
    $val_=implode($pmb_perso_sep,$val_c);
    return $val_;
}

function aff_text_i18n_empr($field,&$check_scripts) {
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
        $ret.="<input id=\"".$field['NAME']."_".$count."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[".$count."]\" data-form-name='".$field["NAME"]."_' value=\"".htmlentities($exploded_value[0],ENT_QUOTES,$charset)."\">";
        $ret.="<input id=\"".$field['NAME']."_lang_".$count."\" class=\"saisie-10emr\" type=\"text\" value=\"".($exploded_value[1] ? htmlentities($langue_doc[$exploded_value[1]],ENT_QUOTES,$charset) : '')."\" autfield=\"".$field['NAME']."_lang_code_".$count."\" completion=\"langue\" autocomplete=\"off\" data-form-name='".$field["NAME"]."_lang_' >";
        $ret.="<input class=\"bouton\" type=\"button\" value=\"...\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$field['NAME']."_lang_code_".$count."&p2=".$field['NAME']."_lang_".$count."', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
        $ret.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$field['NAME']."_lang_".$count.".value=''; this.form.".$field['NAME']."_lang_code_".$count.".value=''; \" value=\"X\">";
        $ret.="<input id=\"".$field['NAME']."_lang_code_".$count."\" data-form-name='".$field["NAME"]."_lang_code_' type=\"hidden\" value=\"".($exploded_value[1] ? htmlentities($exploded_value[1], ENT_QUOTES, $charset) : '')."\" name=\"".$field['NAME']."_langs[".$count."]\">";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_text_i18n_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.$options['SIZE'][0]['value'].'\', \''.$options['MAXSIZE'][0]['value'].'\')">';
            $ret.="<br />";
            $count++;
    }
    if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
        $ret.='<input id="customfield_text_i18n_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.$count.'">';
        $ret .= '<div id="spaceformorecustomfieldtexti18n_'.$afield_name.'"></div>';
        $ret .= get_custom_dnd_on_add();
        $ret.="<script>
			function add_custom_text_i18n_(field_id, field_name, field_size, field_maxlen) {
		        var count = document.getElementById('customfield_text_i18n_'+field_id).value;
				var text = document.createElement('input');
				text.setAttribute('id', field_name + '_' + count);
		        text.setAttribute('name',field_name+'[' + count + ']');
		        text.setAttribute('type','text');
		        text.setAttribute('value','');
		        text.setAttribute('size',field_size);
		        text.setAttribute('maxlength',field_maxlen);
            
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
				lang_code.setAttribute('value', '');
				lang_code.setAttribute('name', field_name + '_langs[' + count + ']');
					    
		        space=document.createElement('br');
					    
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(text);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(lang);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(select);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(del);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(lang_code);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(space);
					    
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

function aff_text_i18n_empr_search($field,&$check_scripts,$varname) {
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
    $ret.="<input id=\"".$varname."_lang\" class=\"saisie-10emr\" type=\"text\" value=\"".($values[0]['lang'] ? htmlentities($langue_doc[$values[0]['lang']],ENT_QUOTES,$charset) : '')."\" autfield=\"".$varname."_lang_code\" completion=\"langue\" autocomplete=\"off\" >";
    $ret.="<input class=\"bouton\" type=\"button\" value=\"".$msg['parcourir']."\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$varname."_lang_code&p2=".$varname."_lang', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
    $ret.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$varname."_lang.value=''; this.form.".$varname."_lang_code.value=''; \" value=\"".$msg['raz']."\">";
    $ret.="<input id=\"".$varname."_lang_code\" type=\"hidden\" value=\"".($values[0]['lang'] ? htmlentities($values[0]['lang'], ENT_QUOTES, $charset) : '')."\" name=\"".$varname."[0][lang]\">";
    return $ret;
}

function chk_text_i18n_empr($field,&$check_message) {
    $name=$field['NAME'];
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
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$final_value,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    
    ${$name}=$val_1;
    return 1;
}

function val_text_i18n_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    
	$langue_doc = get_langue_doc();
    $value=format_output($field,$value);
    if (!$value) $value=array();
    
    $formatted_values = array();
    foreach ($value as $val) {
        $exploded_val = explode("|||", $val);
        $formatted_values[] = $exploded_val[0]." ".($exploded_val[1] ? "(".$langue_doc[$exploded_val[1]].")" : '');
    }
    
    if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
    if($field["OPTIONS"][0]["ISHTML"][0]["value"]){
        return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$formatted_values), "withoutHTML" =>implode($pmb_perso_sep,$formatted_values));
    }else{
        return implode($pmb_perso_sep,$formatted_values);
    }
}

function aff_filter_comment_empr($field,$varname,$multiple) {
    global $charset;
    global $msg;
    
    $ret="<select id=\"".$varname."\" name=\"".$varname;
    $ret.="[]";
    $ret.="\" ";
    if ($multiple) $ret.="size=5 multiple";
    $ret.=" data-form-name='".$varname."' >\n";
    
    $values=$field['VALUES'];
    if ($values=="") $values=array();
    $options=$field['OPTIONS'][0];
    if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
        $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\"";
        if ($options['UNSELECT_ITEM'][0]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
        $ret.=">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
    }
    $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
    while ($r=pmb_mysql_fetch_row($resultat)) {
        $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
        $as=array_search($r[0],$values);
        if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
        $ret.=">".htmlentities(cutlongwords($r[0]),ENT_QUOTES,$charset)."</option>\n";
        
    }
    $ret.= "</select>\n";
    return $ret;
}

function aff_filter_date_box_empr($field,$varname,$multiple) {
    global $charset;
    global $msg;
    
    $ret="<select id=\"".$varname."\" name=\"".$varname;
    $ret.="[]";
    $ret.="\" ";
    if ($multiple) $ret.="size=5 multiple";
    $ret.=" data-form-name='".$varname."' >\n";
    
    $values=$field['VALUES'];
    if ($values=="") $values=array();
    $options=$field['OPTIONS'][0];
    if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
        $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\"";
        if ($options['UNSELECT_ITEM'][0]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
        $ret.=">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
    }
    $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
    while ($r=pmb_mysql_fetch_row($resultat)) {
        $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
        $as=array_search($r[0],$values);
        if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
        $ret.=">".htmlentities(formatdate($r[0]),ENT_QUOTES,$charset)."</option>\n";
    }
    $ret.= "</select>\n";
    return $ret;
}

function aff_filter_text_empr($field,$varname,$multiple) {
    global $charset;
    global $msg;
    
    $ret="<select id=\"".$varname."\" name=\"".$varname;
    $ret.="[]";
    $ret.="\" ";
    if ($multiple) $ret.="size=5 multiple";
    $ret.=" data-form-name='".$varname."' >\n";
    
    $values=$field['VALUES'];
    if ($values=="") $values=array();
    $options=$field['OPTIONS'][0];
    if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
        $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\"";
        if ($options['UNSELECT_ITEM'][0]['VALUE']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
        $ret.=">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
    }
    $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
    while ($r=pmb_mysql_fetch_row($resultat)) {
        $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
        $as=array_search($r[0],$values);
        if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";
        $ret.=">".htmlentities(cutlongwords($r[0]),ENT_QUOTES,$charset)."</option>\n";
    }
    $ret.= "</select>\n";
    return $ret;
}

function aff_filter_query_list_empr($field,$varname,$multiple) {
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
}

function aff_filter_list_empr($field,$varname,$multiple) {
    global $charset;
    global $msg;
    
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

function aff_external_empr($field,&$check_scripts) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    //Recherche du libellé
    $vallib=$values[0];
    if ($options["QUERY"][0]["value"]) {
        $rvalues=pmb_mysql_query(str_replace("!!id!!",$values[0],$options["QUERY"][0]["value"]));
        if ($rvalues) {
            $vallib=@pmb_mysql_result($rvalues,0,0);
        }
    }
    $ret="<input id=\"".$field["NAME"]."\" type=\"hidden\" name=\"".$field["NAME"]."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
    if (!$options["HIDE"][0]["value"]) {
        $ret.="<input id=\"".$field["NAME"]."_lib\" type=\"text\" readonly='readonly' size=\"".$options["SIZE"][0]["value"]."\" maxlength=\"".$options["MAXSIZE"][0]["value"]."\" name=\"".$field["NAME"]."_lib[]\" value=\"".htmlentities($vallib,ENT_QUOTES,$charset)."\">";
    }
    $ret.="&nbsp;<input type='button' id='".$field["NAME"]."_button' name='".$field["NAME"]."_button' class='bouton' value='".(($vallib&&($options["HIDE"][0]["value"]))?htmlentities($vallib,ENT_QUOTES,$charset):($options["BUTTONTEXT"][0]["value"]?htmlentities($options["BUTTONTEXT"][0]["value"],ENT_QUOTES,$charset):$msg["parperso_external_browse"]))."' onClick='openPopUp(\"".$options["URL"][0]["value"]."?field_val=".$field["NAME"]."&"."field_lib=".($options["HIDE"][0]["value"]?$field["NAME"]."_button":$field["NAME"]."_lib")."\",\"w_".$field["NAME"]."\",".($options["WIDTH"][0]["value"]?$options["WIDTH"][0]["value"]:"400").",".($options["HEIGHT"][0]["value"]?$options["HEIGHT"][0]["value"]:"600").",-2,-2,\"infobar=no, status=no, scrollbars=yes, menubar=no\");'/>";
    if ($options["DELETE"][0]["value"]) $ret.="&nbsp;<input type='button' class='bouton' value='X' onClick=\"document.getElementById('".$field["NAME"]."').value=''; document.getElementById('".($options["HIDE"][0]["value"]?$field["NAME"]."_button":$field["NAME"]."_lib")."').value='".($options["HIDE"][0]["value"]?($options["BUTTONTEXT"][0]["value"]?htmlentities($options["BUTTONTEXT"][0]["value"],ENT_QUOTES,$charset):$msg["parperso_external_browse"]):"")."';\"/>";
    if ($field["MANDATORY"]==1) {
        $caller = get_form_name();
        $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field["NAME"]."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field["ALIAS"])."\");\n";
    }
    return $ret;
}

function aff_external_empr_search($field,&$check_scripts,$varname) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    //Recherche du libellé
    $vallib=$values[0];
    if ($options["QUERY"][0]["value"]) {
        $rvalues=pmb_mysql_query(str_replace("!!id!!",$values[0],$options["QUERY"][0]["value"]));
        if ($rvalues) {
            $vallib=@pmb_mysql_result($rvalues,0,0);
        }
    }
    $ret="<input id=\"".$varname."\" type=\"hidden\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
    $ret.="<input id=\"".$varname."_lib\" type=\"text\" name=\"".$varname."_lib[]\" readonly=\"readonly\" value=\"".htmlentities($vallib,ENT_QUOTES,$charset)."\">";
    $ret.="&nbsp;<input type='button' name='".$varname."_button' class='bouton' value='".($options["BUTTONTEXT"][0]["value"]?$options["BUTTONTEXT"][0]["value"]:$msg["parperso_external_browse"])."' onClick='openPopUp(\"".$options["URL"][0]["value"]."?field_val=".$varname."&"."field_lib=".$varname."_lib"."\",\"w_".$varname."\",".($options["WIDTH"][0]["value"]?$options["WIDTH"][0]["value"]:"400").",".($options["HEIGHT"][0]["value"]?$options["HEIGHT"][0]["value"]:"600").",-2,-2,\"\");'/>";
    return $ret;
}

function chk_external_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_external_empr($field,$value) {
    global $charset;
    
    $options=$field['OPTIONS'][0];
    $value=format_output($field,$value);
    //Calcul du libelle
    if ($options["QUERY"][0]["value"]) {
        $rvalues=pmb_mysql_query(str_replace("!!id!!",$value[0],$options["QUERY"][0]["value"]));
        if ($rvalues) {
            return @pmb_mysql_result($rvalues,0,0);
        }
    }
    return $value[0];
}

function aff_url_empr($field,&$check_scripts){
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    $afield_name = $field["ID"];
    $ret = "";
    $count = 0;
    if (!$values) {
        $linktarget_default_checked = (isset($options['LINKTARGET'][0]['value']) && $options['LINKTARGET'][0]['value'] ? 1 : 0);
        $values = array("||".$linktarget_default_checked);
    }
    if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
        $ret .= get_js_function_dnd('url', $field['NAME']);
        $ret.="<input class='bouton' type='button' value='+' onclick=\"add_custom_url_('$afield_name', '".addslashes($field['NAME'])."', '".addslashes($options['SIZE'][0]['value'])."')\">";
    }
    foreach ($values as $avalues) {
        $avalues = explode("|",$avalues);
        $ret.="<div id='".$field['NAME']."_check_$count' style='display:inline'></div>";
        $ret.= $msg['persofield_url_link']."<input id='".$field['NAME']."_link".$count."' type='text' class='saisie-30em' name='".$field['NAME']."[link][".$count."]' data-form-name='".$field['NAME']."_link' onchange='cp_chklnk_".$field["NAME"]."(".$count.",this);' value='".htmlentities($avalues[0],ENT_QUOTES,$charset)."'>";
        $ret.=" <input class=\"bouton\" type='button' value='".$msg['persofield_url_check']."' onclick='cp_chklnk_".$field["NAME"]."(".$count.",this);'>";
        //$ret.="<br />";
        $ret.="&nbsp;".$msg['persofield_url_linklabel']."<input id='".$field['NAME']."_linkname".$count."' type='text' class='saisie-15em' size='".$options['SIZE'][0]['value']."' name='".$field['NAME']."[linkname][".$count."]' data-form-name='".$field['NAME']."_linkname' value='".htmlentities($avalues[1],ENT_QUOTES,$charset)."'>";
        $target_checked = 'checked="checked"';
        if (isset($avalues[2]) && ($avalues[2] == 0)) {
            $target_checked = '';
        }
        $ret.="&nbsp;<input id='".$field['NAME']."_linktarget".$count."' type='checkbox' name='".$field['NAME']."[linktarget][".$count."]' data-form-name='".$field['NAME']."_linktarget' value='1' ".$target_checked."><label for='".$field['NAME']."_linktarget".$count."'>&nbsp;".$msg['persofield_url_linktarget']."</label>&nbsp;";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
            $ret.="<input class='bouton' type='button' value='+' onclick=\"add_custom_url_('$afield_name', '".addslashes($field['NAME'])."', '".addslashes($options['SIZE'][0]['value'])."')\">";
            $ret.="<br />";
            $count++;
    }
    $ret.= "
	<script type='text/javascript'>
		function cp_chklnk_".$field["NAME"]."(indice,element){
			var link = element.form.elements['".$field['NAME']."[link]['+indice+']'];
			if(link.value != ''){
				var wait = document.createElement('img');
				wait.setAttribute('src','".get_url_icon('patience.gif')."');
				wait.setAttribute('align','top');
				while(document.getElementById('".$field['NAME']."_check_'+indice).firstChild){
					document.getElementById('".$field['NAME']."_check_'+indice).removeChild(document.getElementById('".$field['NAME']."_check_'+indice).firstChild);
				}
				document.getElementById('".$field['NAME']."_check_'+indice).appendChild(wait);
				var testlink = encodeURIComponent(link.value);
	 			var check = new http_request();
				if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&timeout=".$options['TIMEOUT'][0]['value']."&link='+testlink)){
					alert(check.get_text());
				}else{
					var result = check.get_text();
					var type_status=result.substr(0,1);
					var img = document.createElement('img');
					var src='';
			    	if(type_status == '2' || type_status == '3'){
						if((link.value.substr(0,7) != 'http://') && (link.value.substr(0,8) != 'https://')) link.value = 'http://'+link.value;
						//impec, on print un petit message de confirmation
						src = '".get_url_icon('tick.gif')."';
					}else{
						//problème...
						src = '".get_url_icon('error.png')."';
						img.setAttribute('style','height:1.5em;');
					}
					img.setAttribute('src',src);
					img.setAttribute('align','top');
					while(document.getElementById('".$field['NAME']."_check_'+indice).firstChild){
						document.getElementById('".$field['NAME']."_check_'+indice).removeChild(document.getElementById('".$field['NAME']."_check_'+indice).firstChild);
					}
					document.getElementById('".$field['NAME']."_check_'+indice).appendChild(img);
				}
			}
		}
	</script>";
    if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
        $ret.='<input id="customfield_text_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.($count).'">';
        //$ret.='<input class="bouton" type="button" value="+" onclick="add_custom_text_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
        $ret .= '<div id="spaceformorecustomfieldtext_'.$afield_name.'"></div>';
        $ret.=get_custom_dnd_on_add();
        $ret.="<script>
			function add_custom_url_(field_id, field_name, field_size) {
				cpt = document.getElementById('customfield_text_'+field_id).value;
				var check = document.createElement('div');
				check.setAttribute('id','".$field['NAME']."_check_'+cpt);
				check.setAttribute('style','display:inline');
				var link_label = document.createTextNode('".$msg['persofield_url_link']."');
				var chklnk = document.createElement('input');
				chklnk.setAttribute('type','button');
				chklnk.setAttribute('value','".$msg['persofield_url_check']."');
				chklnk.setAttribute('class','bouton');
				chklnk.setAttribute('onclick','cp_chklnk_".$field['NAME']."('+cpt+',this);');
				document.getElementById('customfield_text_'+field_id).value = cpt*1 +1;
				var link = document.createElement('input');
		        link.setAttribute('name',field_name+'[link]['+cpt+']');
		        link.setAttribute('id',field_name+'_link'+cpt);
		        link.setAttribute('type','text');
				link.setAttribute('class','saisie-30em');
		        link.setAttribute('size',field_size);
		        link.setAttribute('value','');
				link.setAttribute('onchange','cp_chklnk_".$field['NAME']."('+cpt+',this);');
				var lib_label = document.createTextNode('".$msg['persofield_url_linklabel']."');
				var lib = document.createElement('input');
		        lib.setAttribute('name',field_name+'[linkname]['+cpt+']');
		        lib.setAttribute('id',field_name+'_linkname'+cpt);
		        lib.setAttribute('type','text');
				lib.setAttribute('class','saisie-15em');
		        lib.setAttribute('size',field_size);
		        lib.setAttribute('value','');
				var target = document.createElement('input');
				target.setAttribute('name',field_name+'[linktarget]['+cpt+']');
		        target.setAttribute('id',field_name+'_linktarget'+cpt);
		        target.setAttribute('type','checkbox');
		        target.setAttribute('value','1');
		        target.setAttribute('checked','checked');
				var targetlabel = document.createElement('label');
				targetlabel.setAttribute('for',field_name+'_linktarget'+cpt);
				targetlabel.innerHTML = ' ".$msg['persofield_url_linktarget']."';
		        space=document.createElement('br');
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(check);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(link_label);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(link);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(chklnk);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(lib_label);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(lib);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(target);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(targetlabel);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(space);
			}
		</script>";
    }
    if ($field['MANDATORY']==1) {
        $caller = get_form_name();
        $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    }
    return $ret;
}

function aff_url_empr_search($field,&$check_scripts,$varname) {
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if(!isset($values[0])) $values[0] = '';
    $ret="<input id=\"".$varname."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
    return $ret;
}

function chk_url_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
    $value = array();
    for($i=0;$i<sizeof($val['link']);$i++){
        if($val['link'][$i] != "") {
            $linktarget = '|0';
            if ($val['linktarget'][$i]) {
                $linktarget = '|1';
            }
            $value[] = $val['link'][$i]."|".$val['linkname'][$i].$linktarget;
        }
    }
    $val = $value;
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_url_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    $cut = $field['OPTIONS'][0]['MAXSIZE'][0]['value'];
    $values=format_output($field,$value);
    $ret = "";
    $without = "";
    $details = array();
    for ($i=0;$i<count($values);$i++){
        $val = explode("|",$values[$i]);
        if (isset($val[1]) && $val[1])$lib = $val[1];
        else $lib = ($cut && strlen($val[0]) > $cut ? substr($val[0],0,$cut)."[...]" : $val[0] );
        if( $ret != "") $ret.= $pmb_perso_sep;
        $target = '_blank';
        if (isset($val[2]) && ($val[2] == 0)) {
            $target = '_self';
        }
        $ret .= "<a href='".$val[0]."' target='".$target."'>".htmlentities($lib, ENT_QUOTES, $charset)."</a>";
        if( $without != "") $without.= $pmb_perso_sep;
        $without .= $lib;
        $details[] = array('url' => $val[0], 'label' => $lib, 'target' => $target);
    }
    return array("ishtml" => true, "value"=>$ret, "withoutHTML" =>$without, "details" => $details);
}

function aff_resolve_empr($field,&$check_scripts){
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
    foreach ($values as $avalues) {
        $avalues = explode("|",$avalues);
        $ret.="<input id='".$field['NAME']."$count' type='text' size='".$options['SIZE'][0]['value']."' name='".$field['NAME']."[id][]' data-form-name='".$field['NAME']."' value='".htmlentities($avalues[0],ENT_QUOTES,$charset)."'>";
        $ret.="&nbsp;<select id='".$field['NAME']."_select$count'  name='".$field['NAME']."[resolve][]' data-form-name='".$field['NAME']."_select' >";
        foreach($options['RESOLVE'] as $elem){
            $ret.= "
			<option value='".$elem['ID']."' ".($avalues[1] == $elem['ID'] ? "selected=selected":"").">".htmlentities($elem['LABEL'],ENT_QUOTES,$charset)."</option>";
        }
        $ret.="
		</select>";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_resolve_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
            $ret.="<br />";
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
				f_aut0 = document.createElement('input');
		        f_aut0.setAttribute('name',field_name+'[id][]');
		        f_aut0.setAttribute('type','text');
		        f_aut0.setAttribute('size',field_size);
		        f_aut0.setAttribute('maxlen',field_size);
		        f_aut0.setAttribute('value','');
		        space=document.createElement('br');
				var select = document.createElement('select');
				select.setAttribute('name',field_name+'[resolve][]');
				";
        foreach($options['RESOLVE'] as $elem){
            $ret.="
				var option = document.createElement('option');
				option.setAttribute('value','".$elem['ID']."');
				var text = document.createTextNode('".htmlentities($elem['LABEL'],ENT_QUOTES,$charset)."');
				option.appendChild(text);
				select.appendChild(option);
";
        }
        $ret.="
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(f_aut0);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(select);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(space);
            
			}
		</script>";
    }
    if ($field['MANDATORY']==1) {
        $caller = get_form_name();
        $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[id][]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    }
    return $ret;
}

function chk_resolve_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name};
    $val=${$name};
    $value = array();
    if(isset($val['id'])) {
        for($i=0;$i<sizeof($val['id']);$i++){
            if($val['id'][$i] != "")
                $value[] = $val['id'][$i]."|".$val['resolve'][$i];
        }
    }
    $val = $value;
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_resolve_empr($field,$value) {
    global $charset,$pmb_perso_sep,$opac_url_base,$use_opac_url_base;
    
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
            //$ret.= "<a href='$link' target='_blank'>".htmlentities($link,ENT_QUOTES,$charset)."</a>";
            if (!$use_opac_url_base) $ret.= htmlentities($label,ENT_QUOTES,$charset)." : $id <a href='$link' target='_blank'><img class='center' src='".get_url_icon("globe.gif")."' alt='$link' title='link'/></a>";
            else $ret.= htmlentities($label,ENT_QUOTES,$charset)." : $id <a href='$link' target='_blank'><img class='center' src='".get_url_icon("globe.gif", 1)."' alt='$link' title='link'/></a>";
            if($without)$without.=$pmb_perso_sep;
            $without.=$link;
        }else{
            if($without)$without.=$pmb_perso_sep;
            $without.=implode($pmb_perso_sep,$value);
        }
    }
    return array("ishtml" => true, "value"=>$ret,"withoutHTML"=> $without);
}

function aff_resolve_empr_search($field,&$check_scripts,$varname){
    global $charset;
    global $msg;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    if(!isset($values[0])) $values[0] = '';
    $ret="<input id='".$varname."' type='text' name='".$varname."[]' value='".htmlentities($values[0],ENT_QUOTES,$charset)."'>";
    return $ret;
}

function aff_html_empr($field,&$check_scripts) {
    global $charset;
    global $msg;
    global $cms_dojo_plugins_editor;
    
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    $ret="<input type='hidden' name='".$field['NAME']."[]' value=''/>
	<div data-dojo-type='dijit/Editor' $cms_dojo_plugins_editor	id='".$field['NAME']."' class='saisie-80em' wrap='virtual'>".$values[0]."</div>";
    $check_scripts.= "
	if(document.forms[0].elements['".$field['NAME']."[]']) document.forms[0].elements['".$field['NAME']."[]'].value = dijit.byId('".$field['NAME']."').get('value');";
    if ($field['MANDATORY']==1) {
        $caller = get_form_name();
        $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    }
    return $ret;
}

function aff_marclist_empr($field,&$check_scripts,$script="") {
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
            foreach ($values_received as $id=>$value) {
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
        $ret.="<input type='hidden' value='$n' name='n_".$field["NAME"]."'/>\n<div id='div_".$field["NAME"]."'>";
        $readonly='';
        for ($i=0; $i<$n; $i++) {
            $ret.="<input type='text' class='saisie-50emr' id='f_".$field["NAME"]."_$i' completion='perso_".$_custom_prefixe_."' persofield='".$field["NAME"]."' autfield='".$field["NAME"]."_$i' name='f_".$field["NAME"]."_$i' $readonly value=\"".htmlentities($libelles[$i],ENT_QUOTES,$charset)."\" />\n";
            $ret.="<input type='hidden' id='".$field["NAME"]."_$i' name='".$field["NAME"]."_$i' value=\"".htmlentities($values[$i],ENT_QUOTES,$charset)."\">";
            
            $ret.="
			<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$field["NAME"]."_$i.value=''; this.form.".$field["NAME"]."_$i.value=''; \" />\n";
            if (($i==0)&&($options['MULTIPLE'][0]['value']=="yes")) {
                $ret.=" <input type='button' class='bouton' value='+' onClick=\"add_".$field["NAME"]."();\"/>";
            }
            $ret.="<br />";
        }
        $ret.="</div>";
    }
    
    return $ret;
}

function chk_marclist_empr($field,&$check_message) {
    global $charset;
    global $msg;
    
    $name=$field['NAME'];
    $options=$field['OPTIONS'][0];
    
    global ${$name};
    if ($options["AUTORITE"][0]["value"]!="yes") {
        $val=${$name};
    } else {
        $val=array();
        $nn="n_".$name;
        global ${$nn};
        $n=${$nn};
        for ($i=0; $i<$n; $i++) {
            $v=$field["NAME"]."_".$i;
            global ${$v};
            if (${$v}!="") {
                $val[]=${$v};
            }
        }
        if (count($val)==0) unset($val);
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

function val_marclist_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    
    $options=$field['OPTIONS'][0];
    $values=format_output($field,$value);
    $ret = "";
    if (count($values)) {
        $marclist_type = marc_list_collection::get_instance($options['DATA_TYPE'][0]['value']);
        if($ret)$ret.=$pmb_perso_sep;
        foreach($values as $id=>$value) {
            if(isset($marclist_type->table[$value])) {
                if($ret)$ret.=$pmb_perso_sep;
                $ret.= $marclist_type->table[$value];
            }
        }
    }
    return $ret;
}

function aff_marclist_empr_search($field,&$check_scripts,$varname){
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
				f_perso.setAttribute('persofield','".substr($field["NAME"], 0, strrpos($field["NAME"], "_"))."');
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
        $ret.="<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=perso&caller=search_form&p1=".$varname."&p2=f_".$varname."&perso_id=".$field["ID"]."&custom_prefixe=".$_custom_prefixe_."&dyn=1&perso_name=".$varname."', 'select_perso_".$field["ID"]."', 700, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" />";
        $ret.="<input type='button' class='bouton' value='+' onClick=\"add_".$varname."();\"/>";
        $ret.="<div id='div_".$varname."'>";
        for($inc=0;$inc<$nb_values;$inc++){
            $ret.="<div class='row'>";
            $ret.="<input type='hidden' id='".$varname."_".$inc."' name='".$varname."[]' data-form-name='".$varname."[]' value=\"".htmlentities($values[$inc],ENT_QUOTES,$charset)."\">";
            $ret.="<input type='text' class='saisie-20emr' id='f_".$varname."_".$inc."' completion='".$options['DATA_TYPE'][0]['value']."' persofield='".substr($field["NAME"], 0, strrpos($field["NAME"], "_"))."' autfield='".$varname."_".$inc."' name='f_".$varname."[]' data-form-name='f_".$varname."[]' value=\"".htmlentities($libelles[$inc],ENT_QUOTES,$charset)."\" />\n";
            $ret.="<input type='button' class='bouton' value='X' onclick=\"this.form.f_".$varname."_".$inc.".value=''; this.form.".$varname."_".$inc.".value=''; \" />\n";
            $ret.="</div>";
        }
        $ret.="</div>";
    }
    return $ret;
}

function aff_q_txt_i18n_empr($field,&$check_scripts) {
    global $charset, $base_path;
	global $msg;
    
	$langue_doc = get_langue_doc();
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    $afield_name = $field["ID"];
    $_custom_prefixe_=$field["PREFIX"];
    $ret = "";
    $count = 0;
    if (!$values) {
        if(isset($options['DEFAULT_LANG'][0]['value']) && $options['DEFAULT_LANG'][0]['value']) {
            $values = array("|||".$options['DEFAULT_LANG'][0]['value']."|||");
        } else {
            $values = array("");
        }
    }
    $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
    $resultat=pmb_mysql_query($requete);
    $options['ITEMS'] = array();
    if ($resultat) {
        $i=0;
        while ($r=pmb_mysql_fetch_array($resultat)) {
            $options['ITEMS'][$i]['value']=$r[$_custom_prefixe_."_custom_list_value"];
            $options['ITEMS'][$i]['label']=$r[$_custom_prefixe_."_custom_list_lib"];
            $i++;
        }
    }
    if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
        $ret .= get_js_function_dnd('q_txt_i18n', $field['NAME']);
        $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_q_txt_i18n_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.$options['SIZE'][0]['value'].'\', \''.$options['MAXSIZE'][0]['value'].'\')">';
    }
    foreach ($values as $value) {
        $exploded_value = explode("|||", $value);
        if(count($options['ITEMS']) == 1) {
            $type = "checkbox";
            $ret.= "<input id='".$field['NAME']."_qualification_".$count."' type='$type' name='".$field['NAME']."_qualifications[".$count."]'";
            if ($values[0] != "") {
                if($options['ITEMS'][0]['value'] == $exploded_value[2]) $ret.=" checked=checked";
            } else {
                //Recherche de la valeur par défaut s'il n'y a pas de choix vide
                if (($options['UNSELECT_ITEM'][0]['VALUE']=="") || ($options['UNSELECT_ITEM'][0]['value']=="")) {
                    if ($options['DEFAULT_VALUE'][0]['value']=="") $ret.=" checked=checked";
                    elseif ($options['ITEMS'][0]['value']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" checked=checked";
                }
            }
            $ret.=" value='".$options['ITEMS'][0]['value']."'/><span id='lib_".$field['NAME']."_".$options['ITEMS'][0]['value']."'>&nbsp;".$options['ITEMS'][0]['label']."</span>";
        } else {
            $ret.="<select id=\"".$field['NAME']."_qualification_".$count."\" name=\"".$field['NAME'];
            $ret.="_qualifications[".$count."]";
            $ret.="\" ";
            if ($script) $ret.=$script." ";
            $ret.=" data-form-name='".$field['NAME']."' >\n";
            if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
                $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
            }
            for ($i=0; $i<count($options['ITEMS']); $i++) {
                $ret.="<option value=\"".htmlentities($options['ITEMS'][$i]['value'],ENT_QUOTES,$charset)."\"";
                if ($values[0] != "") {
                    if($options['ITEMS'][$i]['value'] == $exploded_value[2]) $ret.=" selected";
                } else {
                    //Recherche de la valeur par défaut
                    if ($options['ITEMS'][$i]['value']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
                }
                $ret.=">".htmlentities($options['ITEMS'][$i]['label'],ENT_QUOTES,$charset)."</option>\n";
            }
            $ret.= "</select>\n";
        }
        $ret.="<input id=\"".$field['NAME']."_".$count."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" maxlength=\"".$options['MAXSIZE'][0]['value']."\" name=\"".$field['NAME']."[".$count."]\" data-form-name='".$field["NAME"]."_' value=\"".htmlentities($exploded_value[0],ENT_QUOTES,$charset)."\">";
        $ret.="<input id=\"".$field['NAME']."_lang_".$count."\" class=\"saisie-10emr\" type=\"text\" value=\"".($exploded_value[1] ? htmlentities($langue_doc[$exploded_value[1]],ENT_QUOTES,$charset) : '')."\" autfield=\"".$field['NAME']."_lang_code_".$count."\" completion=\"langue\" autocomplete=\"off\" data-form-name='".$field["NAME"]."_lang_' >";
        $ret.="<input class=\"bouton\" type=\"button\" value=\"...\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$field['NAME']."_lang_code_".$count."&p2=".$field['NAME']."_lang_".$count."', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
        $ret.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$field['NAME']."_lang_".$count.".value=''; this.form.".$field['NAME']."_lang_code_".$count.".value=''; \" value=\"X\">";
        $ret.="<input id=\"".$field['NAME']."_lang_code_".$count."\" data-form-name='".$field["NAME"]."_lang_code_' type=\"hidden\" value=\"".($exploded_value[1] ? htmlentities($exploded_value[1], ENT_QUOTES, $charset) : '')."\" name=\"".$field['NAME']."_langs[".$count."]\">";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_q_txt_i18n_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.$options['SIZE'][0]['value'].'\', \''.$options['MAXSIZE'][0]['value'].'\')">';
            $ret.="<br />";
            $count++;
    }
    if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
        $ret.='<input id="customfield_q_txt_i18n_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.$count.'">';
        $ret.= '<div id="spaceformorecustomfieldtexti18n_'.$afield_name.'"></div>';
        $ret.= get_custom_dnd_on_add();
        $ret.="<script>
			function add_custom_q_txt_i18n_(field_id, field_name, field_size, field_maxlen) {
		        var count = document.getElementById('customfield_q_txt_i18n_'+field_id).value;
            
				var qualification = document.getElementById(field_name+'_qualification_'+(count-1)).cloneNode(true);
				qualification.setAttribute('id', field_name + '_qualification_' + count);
		        qualification.setAttribute('name',field_name+'_qualifications[' + count + ']');
            
				var text = document.createElement('input');
				text.setAttribute('id', field_name + '_' + count);
		        text.setAttribute('name',field_name+'[' + count + ']');
		        text.setAttribute('type','text');
		        text.setAttribute('value','');
		        text.setAttribute('size',field_size);
		        text.setAttribute('maxlength',field_maxlen);
            
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
				lang_code.setAttribute('value', '');
				lang_code.setAttribute('name', field_name + '_langs[' + count + ']');
					    
		        space=document.createElement('br');
					    
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(qualification);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(text);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(lang);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(select);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(del);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(lang_code);
				document.getElementById('spaceformorecustomfieldtexti18n_'+field_id).appendChild(space);
					    
				document.getElementById('customfield_q_txt_i18n_'+field_id).value = document.getElementById('customfield_q_txt_i18n_'+field_id).value * 1 + 1;
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

function aff_q_txt_i18n_empr_search($field,&$check_scripts,$varname) {
    global $charset;
    global $msg;
    global $base_path;
    
	$langue_doc = get_langue_doc();	
    $options=$field['OPTIONS'][0];
    $values=$field['VALUES'];
    $_custom_prefixe_=$field["PREFIX"];
    $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
    $resultat=pmb_mysql_query($requete);
    $options['ITEMS'] = array();
    if ($resultat) {
        $i=0;
        while ($r=pmb_mysql_fetch_array($resultat)) {
            $options['ITEMS'][$i]['value']=$r[$_custom_prefixe_."_custom_list_value"];
            $options['ITEMS'][$i]['label']=$r[$_custom_prefixe_."_custom_list_lib"];
            $i++;
        }
    }
    
    $ret="<input id=\"".$varname."_txt\" class=\"saisie-30em\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" name=\"".$varname."[0][txt]\" value=\"".(isset($values[0]['txt']) ? htmlentities($values[0]['txt'],ENT_QUOTES,$charset) : '')."\">";
    if(count($options['ITEMS']) == 1) {
        $type = "checkbox";
        $ret.= "<input id='".$varname."_qualification' type='$type' name='".$varname."[0][qualification]'";
        if (isset($values[0]['qualification']) && $values[0]['qualification'] != "") {
            if($options['ITEMS'][0]['value'] == $values[0]['qualification']) $ret.=" checked=checked";
        } else {
            //Recherche de la valeur par défaut s'il n'y a pas de choix vide
            if (($options['UNSELECT_ITEM'][0]['VALUE']=="") || ($options['UNSELECT_ITEM'][0]['value']=="")) {
                if ($options['DEFAULT_VALUE'][0]['value']=="") $ret.=" checked=checked";
                elseif ($options['ITEMS'][0]['value']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" checked=checked";
            }
        }
        $ret.=" value='".$options['ITEMS'][0]['value']."'/><span id='lib_".$varname."'>&nbsp;".$options['ITEMS'][0]['label']."</span>";
    } else {
        $ret.="<select id=\"".$varname."_qualification\" name=\"".$varname."[0][qualification]\" ";
        if ($script) $ret.=$script." ";
        $ret.=" >\n";
        if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
            $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
        }
        for ($i=0; $i<count($options['ITEMS']); $i++) {
            $ret.="<option value=\"".htmlentities($options['ITEMS'][$i]['value'],ENT_QUOTES,$charset)."\"";
            if ($values[0]['qualification'] != "") {
                if($options['ITEMS'][$i]['value'] == $values[0]['qualification']) $ret.=" selected";
            } else {
                //Recherche de la valeur par défaut
                if ($options['ITEMS'][$i]['value']==$options['DEFAULT_VALUE'][0]['value']) $ret.=" selected";
            }
            $ret.=">".htmlentities($options['ITEMS'][$i]['label'],ENT_QUOTES,$charset)."</option>\n";
        }
        $ret.= "</select>";
    }
    $ret.="<input id=\"".$varname."_lang\" class=\"saisie-10emr\" type=\"text\" value=\"".(isset($values[0]['lang']) && $values[0]['lang'] ? htmlentities($langue_doc[$values[0]['lang']],ENT_QUOTES,$charset) : '')."\" autfield=\"".$varname."_lang_code\" completion=\"langue\" autocomplete=\"off\" >";
    $ret.="<input class=\"bouton\" type=\"button\" value=\"".$msg['parcourir']."\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$varname."_lang_code&p2=".$varname."_lang', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
    $ret.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$varname."_lang.value=''; this.form.".$varname."_lang_code.value=''; \" value=\"".$msg['raz']."\">";
    $ret.="<input id=\"".$varname."_lang_code\" type=\"hidden\" value=\"".(isset($values[0]['lang']) && $values[0]['lang'] ? htmlentities($values[0]['lang'], ENT_QUOTES, $charset) : '')."\" name=\"".$varname."[0][lang]\">";
    return $ret;
}

function chk_q_txt_i18n_empr($field,&$check_message) {
    $name=$field['NAME'];
    global ${$name}, ${$name."_langs"}, ${$name."_qualifications"};
    $val=${$name};
    $langs = (${$name."_langs"});
    $qualifications = (${$name."_qualifications"});
    $final_value = array();
    if(is_array($val)) {
        foreach ($val as $key => $value) {
            if ($value) {
                $final_value[] = $value."|||".($langs[$key] ? $langs[$key] : '')."|||".$qualifications[$key];
            }
        }
    }
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$final_value,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    
    ${$name}=$val_1;
    return 1;
}

function val_q_txt_i18n_empr($field,$value) {
    global $charset,$pmb_perso_sep;
    
	$langue_doc = get_langue_doc();
    $_custom_prefixe_ = $field['PREFIX'];
    $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$field['ID']." order by ordre";
    $resultat=pmb_mysql_query($requete);
    $items = array();
    if ($resultat) {
        while ($r=pmb_mysql_fetch_array($resultat)) {
            $items[$r[$_custom_prefixe_."_custom_list_value"]] = $r[$_custom_prefixe_."_custom_list_lib"];
        }
    }
    
    $value=format_output($field,$value);
    if (!$value) $value=array();
    
    $formatted_values = array();
    if(is_array($value)) {
        foreach ($value as $val) {
            $exploded_val = explode("|||", $val);
            $formatted_values[] = (isset($exploded_val[2]) && $exploded_val[2] ? "[".$items[$exploded_val[2]]."] " : "").$exploded_val[0]." ".(isset($exploded_val[1]) && $exploded_val[1] ? "(".$langue_doc[$exploded_val[1]].")" : "");
        }
    }
    
    if(!isset($field["OPTIONS"][0]["ISHTML"][0]["value"])) $field["OPTIONS"][0]["ISHTML"][0]["value"] = '';
    if($field["OPTIONS"][0]["ISHTML"][0]["value"]){
        return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$formatted_values), "withoutHTML" =>implode($pmb_perso_sep,$formatted_values));
    }else{
        return implode($pmb_perso_sep,$formatted_values);
    }
}

function aff_date_inter_empr($field,&$check_scripts) {
    global $charset;
    global $msg;
    global $base_path;
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
        } else if (!$timestamp_begin && !$timestamp_end && $options["DEFAULT_TODAY"][0]["value"]) {
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
        $ret .= "<script>
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
    if ($field['MANDATORY']==1) {
        $caller = get_form_name();
        $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    }
    return $ret;
}

function aff_date_inter_empr_search($field,&$check_scripts,$varname) {
    global $charset;
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

function aff_filter_date_inter_empr($field,$varname,$multiple) {
    global $charset;
    global $msg;
    
    $ret="<select id=\"".$varname."\" name=\"".$varname."[]\"";
    if ($multiple) {
        $ret.="size=5 multiple";
    }
    $ret.=">\n";
    
    $values = $field['VALUES'];
    if ($values=="") {
        $values=array();
    }
    
    $options = $field['OPTIONS'][0];
    if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
        $ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\"";
        if ($options['UNSELECT_ITEM'][0]['VALUE'] == $options['DEFAULT_VALUE'][0]['value']) {
            $ret.=" selected";
        }
        $ret.=">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
    }
    
    $resultat=pmb_mysql_query($options['QUERY'][0]['value']);
    while ($r=pmb_mysql_fetch_row($resultat)) {
        $ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\"";
        $as=array_search($r[0],$values);
        if (($as!==FALSE)&&($as!==NULL)) {
            $ret.=" selected";
        }
        $ret.=">".htmlentities(formatdate($r[0]),ENT_QUOTES,$charset)."</option>\n";
    }
    $ret.= "</select>\n";
    return $ret;
}

function chk_date_inter_empr($field,&$check_message) {
    $name=$field['NAME'];
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
    $val = $value;
    
    $check_datatype_message="";
    $val_1=chk_datatype($field,$val,$check_datatype_message);
    if ($check_datatype_message) {
        $check_message=$check_datatype_message;
        return 0;
    }
    ${$name}=$val_1;
    return 1;
}

function val_date_inter_empr($field,$value) {
    global $charset,$pmb_perso_sep, $msg;
    
    $without="";
    $options=$field['OPTIONS'][0];
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

function get_form_name() {
    global $_custom_prefixe_;
    
    $caller="";
    switch ($_custom_prefixe_) {
        case "empr":
            $caller="empr_form";
            break;
        case "notices":
            $caller="notice";
            break;
        case "expl":
            $caller="expl";
            break;
        case "gestfic0": // a modifier lorsque il y aura du multi fiches!
            $caller="formulaire";
            break;
        case "author":
            $caller="saisie_auteur";
            break;
        case "categ":
            $caller="categ_form";
            break;
        case "publisher":
            $caller="saisie_editeur";
            break;
        case "collection":
            $caller="saisie_collection";
            break;
        case "subcollection":
            $caller="saisie_sub_collection";
            break;
        case "serie":
            $caller="saisie_serie";
            break;
        case "tu":
            $caller="saisie_titre_uniforme";
            break;
        case "indexint":
            $caller="saisie_indexint";
            break;
        case "authperso":
            $caller="saisie_authperso";
            break;
        case "cms_editorial":
            global $elem;
            $caller="cms_".$elem."_edit";
            break;
        case "pret":
            $caller="pret_doc";
            break;
        case "demandes":
            $caller="modif_dmde";
            break;
        case "explnum":
            $caller="explnum";
            break;
        default:
            $caller="0";
            break;
    }
    return $caller;
}

function get_js_function_dnd($field_type, $field_name) {
    global $base_path, $customfield_drop_already_included;
    
    $return = "";
    
    if(empty($customfield_drop_already_included)){
        $return.= "<script type='text/javascript' src='".$base_path."/javascript/customfield_drop.js'></script>";
        $customfield_drop_already_included = true;
    }
    
    return $return."
		<script type='text/javascript'>
			allow_drag['customfield_".$field_type."_".$field_name."']=new Array();
			allow_drag['customfield_".$field_type."_".$field_name."']['customfield_".$field_type."_".$field_name."']=true;
			function customfield_".$field_type."_".$field_name."_customfield_".$field_type."_".$field_name."(dragged,target){
				element_drop(dragged,target,'customfield_".$field_type."_".$field_name."');
			}
		</script>";
}

function get_block_dnd($field_type, $field_name, $count, $html, $avalues='') {
    global $charset;
    
    return "
		<div id='customfield_".$field_type."_".$field_name."_".$count."'  class='row' dragtype='customfield_".$field_type."_".$field_name."' draggable='yes' recept='yes' recepttype='customfield_".$field_type."_".$field_name."' handler='customfield_".$field_type."_".$field_name."_".$count."_handle'
			dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($avalues,ENT_QUOTES,$charset)."\" downlight=\"customfield_downlight\" highlight=\"customfield_highlight\"
			order='".$count."' style='' >
			<span id=\"customfield_".$field_type."_".$field_name."_".$count."_handle\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>
			".$html."
		</div>";
}

function get_custom_dnd_on_add() {
    global $charset;
    
    return "
	<script type='text/javascript'>
		function get_custom_dnd_on_add(node_id, field_name, count) {
			var dnd_div = document.createElement('div');
			dnd_div.setAttribute('id', field_name + '_' + count);
			dnd_div.setAttribute('class', 'row');
			dnd_div.setAttribute('dragtype', field_name);
			dnd_div.setAttribute('draggable', 'yes');
			dnd_div.setAttribute('recept', 'yes');
			dnd_div.setAttribute('recepttype', field_name);
			dnd_div.setAttribute('handler', field_name + '_' + count + '_handle');
			dnd_div.setAttribute('dragicon', '".get_url_icon('icone_drag_notice.png')."');
			dnd_div.setAttribute('downlight', 'customfield_downlight');
			dnd_div.setAttribute('highlight', 'customfield_highlight');
			dnd_div.setAttribute('order', count);
			    
			var sort_span = document.createElement('span');
			sort_span.setAttribute('id', field_name + '_' + count + '_handle');
			sort_span.setAttribute('style', 'float:left; padding-right : 7px');
			var sort_icon = document.createElement('img');
			sort_icon.setAttribute('src', '".get_url_icon('sort.png')."');
			sort_icon.setAttribute('style', 'width:12px; vertical-align:middle');
			sort_span.appendChild(sort_icon);
			    
			dnd_div.appendChild(sort_span);
			document.getElementById(node_id).appendChild(dnd_div);
			parse_drag(dnd_div);
			return field_name + '_' + count;
		}
	</script>";
}

function get_authority_isbd_from_field($field, $id=0) {
    global $charset;
    global $lang;
    
    $isbd = '';
    switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
        case 1:// auteur
            $aut = authorities_collection::get_authority('author', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 2:// categories
            if (isset($field["OPTIONS"][0]["CATEG_SHOW"]["0"]["value"]) && $field["OPTIONS"][0]["CATEG_SHOW"]["0"]["value"]==1) {
                $isbd .= html_entity_decode(categories::getLibelle($id,$lang),ENT_QUOTES, $charset);
            } else {
                $isbd .= html_entity_decode(categories::listAncestorNames($id,$lang),ENT_QUOTES, $charset);
            }
            break;
        case 3:// Editeur
            $aut = authorities_collection::get_authority('publisher', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 4:// collection
            $aut = authorities_collection::get_authority('collection', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 5:// subcollection
            $aut = authorities_collection::get_authority('subcollection', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 6:// Titre de serie
            $aut = authorities_collection::get_authority('serie', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 7:// Indexation decimale
            $aut = authorities_collection::get_authority('indexint', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 8:// titre uniforme
            $aut = authorities_collection::get_authority('titre_uniforme', $id);
            $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            break;
        case 9://Concept
            if(!$id){
                $id = onto_common_uri::get_id($id);
            }
            if(!$id) break;
            $aut = authorities_collection::get_authority('concept', $id);
            $isbd .= html_entity_decode($aut->get_display_label(),ENT_QUOTES, $charset);
            break;
        default:
            if($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]>1000){
                // autperso
                $aut = new authperso_authority($id);
                $isbd .= html_entity_decode($aut->get_isbd(),ENT_QUOTES, $charset);
            }
            break;
    }
    return $isbd;
}

function get_authority_selection_parameters($authority_type) {
    $what = '';
    $completion = '';
    switch($authority_type) {
        case 1://auteurs
            $what="auteur";
            $completion='authors';
            break;
        case 2://categories
            $what="categorie";
            $completion="categories";
            break;
        case 3://Editeurs
            $what="editeur";
            $completion="publishers";
            break;
        case 4://collection
            $what="collection";
            $completion="collections";
            break;
        case 5:// subcollection
            $what="subcollection";
            $completion="subcollections";
            break;
        case 6://Titre de serie
            $what="serie";
            $completion="serie";
            break;
        case 7:// Indexation decimale
            $what="indexint";
            $completion="indexint";
            break;
        case 8:// titre uniforme
            $what="titre_uniforme";
            $completion="titre_uniforme";
            break;
        case 9:
            $what="ontology";
            $completion="onto";
            break;
        default:
            if($authority_type>1000){
                $what="authperso&authperso_id=".($authority_type-1000);
                $completion="authperso_".($authority_type-1000);
            }
            break;
    }
    return array(
        'what' => $what,
        'completion' => $completion
    );
}

function get_authority_details_from_field($field, $id=0) {
    switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
        case 1:// auteur
            return authorities_collection::get_authority('author', $id);
        case 2:// categories
            return authorities_collection::get_authority('category', $id);
        case 3:// Editeur
            return authorities_collection::get_authority('publisher', $id);
        case 4:// collection
            return authorities_collection::get_authority('collection', $id);
        case 5:// subcollection
            return authorities_collection::get_authority('subcollection', $id);
        case 6:// Titre de serie
            return authorities_collection::get_authority('serie', $id);
        case 7:// Indexation decimale
            return authorities_collection::get_authority('indexint', $id);
        case 8:// titre uniforme
            return authorities_collection::get_authority('titre_uniforme', $id);
        case 9://Concept
            if(!$id){
                $id = onto_common_uri::get_id($id);
            }
            if(!$id) break;
            return authorities_collection::get_authority('concept', $id);
        default:
            if($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]>1000){
                // autperso
                return new authperso_authority($id);
            }
            break;
    }
    return null;
}

function aff_date_flottante_empr($field, &$check_scripts) {
    global $charset;
    global $msg;
    global $base_path;
    
    $values = ($field['VALUES'] ? $field['VALUES'] : array(""));
    $options = $field['OPTIONS'][0];
    $afield_name = $field["ID"];
    $count = 0;
    $ret = "";
    
    $ret .= "
		<script>
			function date_flottante_type_onchange(field_name) {
				var type = document.getElementById(field_name + '_date_type').value;
				switch(type) {
					case '4' : // interval date
						document.getElementById(field_name + '_date_begin_zone_label').style.display = '';
						document.getElementById(field_name + '_date_end_zone').style.display = '';
						break;
					case '0' : // vers
					case '1' : // avant
					case '2' : // après
					case '3' : // date précise
					default :
						document.getElementById(field_name + '_date_begin_zone_label').style.display = 'none';
						document.getElementById(field_name + '_date_end_zone').style.display = 'none';
						break;
				}
			}
						    
			function date_flottante_reset_fields(field_name) {
				document.getElementById(field_name + '_date_begin').value = '';
				document.getElementById(field_name + '_date_end').value = '';
				document.getElementById(field_name + '_comment').value = '';
			}
		</script>
		";
    foreach ($values as $value) {
        // value:  type (vers: 0, avant: 1, après: 2, date précise: 3, interval date: 4)
        //		  1ere date
        //		  2eme date
        //		  zone commentaire
        // exemple: 1|||1950|||1960|||commentaires
        $data = explode("|||", $value);
        
		$date_type = (!empty($data[0]) ? $data[0] : "");
		$date_begin = (!empty($data[1]) ? $data[1] : "");
		$date_end = (!empty($data[2]) ? $data[2] : "");
		$comment = (!empty($data[3]) ? $data[3] : "");
        
        if (!$date_begin && !$date_end && !$options["DEFAULT_TODAY"][0]["value"]) {
            $time = time();
            $date_begin = date("Y-m-d", $time);
            $date_end = date("Y-m-d", $time);
        } elseif (!$date_begin && !$date_end && $options["DEFAULT_TODAY"][0]["value"]) {
            $date_begin = "";
            $date_end = "";
        } else {
            //$date_begin = date("Y-m-d", $date_begin);
            //$date_end = date("Y-m-d", $date_end);
        }
        $ret .= "<div>
					<select id='" . $field['NAME'] . "_" . $count . "_date_type' name='" . $field['NAME'] . "[" . $count . "][date_type]' onchange=\"date_flottante_type_onchange('" . $field['NAME'] . '_' . $count . "');\">
 						<option value='0' " . (!$date_type ? ' selected ' : '') . ">" . $msg['parperso_option_duration_type0'] . "</option>
 						<option value='1' " . ($date_type == 1 ? ' selected ' : '') . ">" . $msg['parperso_option_duration_type1'] . "</option>
 						<option value='2' " . ($date_type == 2 ? ' selected ' : '') . ">" . $msg['parperso_option_duration_type2'] . "</option>
 						<option value='3' " . ($date_type == 3 ? ' selected ' : '') . ">" . $msg['parperso_option_duration_type3'] . "</option>
 						<option value='4' " . ($date_type == 4 ? ' selected ' : '') . ">" . $msg['parperso_option_duration_type4'] . "</option>
					</select>
 					<span id='" . $field['NAME'] . "_" . $count . "_date_begin_zone'>
						<label id='" . $field['NAME'] . "_" . $count . "_date_begin_zone_label'>" . $msg['parperso_option_duration_begin'] . "</label>
						<input type='text' id='" . $field['NAME'] . "_" . $count . "_date_begin' name='" . $field['NAME'] . "[" . $count . "][date_begin]' value='" . $date_begin . "' placeholder='" . $msg["format_date_input_placeholder"] . "' maxlength='11' size='11' />
					</span>
 					<span id='" . $field['NAME'] . "_" . $count . "_date_end_zone'>
						<label id='" . $field['NAME'] . "_" . $count . "_date_end_zone_label'>" . $msg['parperso_option_duration_end'] . "</label>
						<input type='text' id='" . $field['NAME'] . "_" . $count . "_date_end' name='" . $field['NAME'] . "[" . $count . "][date_end]' value='" . $date_end . "' placeholder='" . $msg["format_date_input_placeholder"] . "' maxlength='11' size='11' />
					</span>
					<label>" . $msg['parperso_option_duration_comment'] . "</label>
					<input type='text' id='" . $field['NAME'] . "_" . $count . "_comment' name='" . $field['NAME'] . "[" . $count . "][comment]' value='" . htmlentities($comment, ENT_QUOTES, $charset) . "' class='saisie-30em'/>
					<input class='bouton' type='button' value='X' onClick=\"date_flottante_reset_fields('" . $field['NAME'] . '_' . $count . "');\"/>";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count) {
            $ret .= '<input class="bouton" type="button" value="+" onclick="add_custom_date_flottante_(\'' . $afield_name . '\', \'' . addslashes($field['NAME']) . '\',\'' . $options["DEFAULT_TODAY"][0]["value"] . '\')" >';
        }
        $ret .= "</div>
		<script>
			date_flottante_type_onchange('" . $field['NAME'] . '_' . $count . "');
		</script>";
        $count++;
    }
    /*
     if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
     $ret .= '<input id="customfield_date_flottante_'.$afield_name.'" type="hidden" name="customfield_date_flottante_'.$afield_name.'" value="'.$count.'">';
     $ret .= '<div id="spaceformorecustomfielddateinter_'.$afield_name.'"></div>';
     $ret .= get_custom_dnd_on_add();
     $ret .= "
     <script>
     function add_custom_date_flottante_(field_id, field_name, today) {
     
     }
     </script>";
     }
     */
    if ($field['MANDATORY']==1) {
        $caller = get_form_name();
        $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
    }
    return $ret;
}

function aff_date_flottante_empr_search($field, &$check_scripts, $varname) {
    global $charset;
    global $msg;
    
    $date_begin = '';
    $date_end = '';
    if (!empty($field['VALUES'][0])) $date_begin = $field['VALUES'][0];
    if (!empty($field['VALUES1'][0])) $date_end = $field['VALUES1'][0];
    $return = "
			<div>
 				<span id='" . $varname . "_date_begin_zone'>
					<label id='".$varname."_date_begin_zone_label'>" . htmlentities($msg['resa_planning_date_debut'], ENT_QUOTES, $charset) . "</label>
					<input type='text' id='" . $varname . "[]' name='" . $varname . "[]' value='" . htmlentities($date_begin, ENT_QUOTES, $charset) . "' placeholder='".$msg["format_date_input_placeholder"]."' maxlength='10' size='10' />
				</span>
 				<span id='".$varname."_date_end_zone'>
					<label id='" . $varname . "_date_end_zone_label'>" . $msg['resa_planning_date_fin'] . "</label>
					<input type='text' id='" . $varname . "_1[]' name='" . $varname . "_1[]' value='" . htmlentities($date_end, ENT_QUOTES, $charset) . "' placeholder='".$msg["format_date_input_placeholder"]."' maxlength='10' size='10' />
				</span>
			</div>";
    return $return;
}

function aff_filter_date_flottante_empr($field, $varname, $multiple) {
    global $charset;
    global $msg;
    
    $return = "<select id=\"" . $varname . "\" name=\"" . $varname. "[]\"";
    if ($multiple) {
        $return .= "size=5 multiple";
    }
    $return .= ">\n";
    
    $values = $field['VALUES'];
    if ($values == "") {
        $values = array();
    }
    
    $options = $field['OPTIONS'][0];
    if (($options['UNSELECT_ITEM'][0]['VALUE'] != "") || ($options['UNSELECT_ITEM'][0]['value'] != "")) {
        $return .= "<option value=\"" . htmlentities($options['UNSELECT_ITEM'][0]['VALUE'], ENT_QUOTES, $charset) . "\"";
        if ($options['UNSELECT_ITEM'][0]['VALUE'] == $options['DEFAULT_VALUE'][0]['value']) {
            $return .= " selected";
        }
        $return .= ">".htmlentities($options['UNSELECT_ITEM'][0]['value'], ENT_QUOTES, $charset) . "</option>\n";
    }
    
    $resultat = pmb_mysql_query($options['QUERY'][0]['value']);
    while ($r = pmb_mysql_fetch_row($resultat)) {
        $return .= "<option value=\"" . htmlentities($r[0], ENT_QUOTES, $charset) . "\"";
        $as = array_search($r[0], $values);
        if (($as !== FALSE) && ($as !== NULL)) {
            $return .= " selected";
        }
        $return .= ">" . htmlentities(formatdate($r[0]), ENT_QUOTES, $charset) . "</option>\n";
    }
    $return .= "</select>\n";
    return $return;
}

function chk_date_flottante_empr($field, &$check_message) {
    $name = $field['NAME'];
    global ${$name};
    $val = ${$name};
    $value = array();
    if (is_array($val)) {
        foreach ($val as $interval) {
            if (isset($interval['date_type']) && ($interval['date_begin'] || $interval['date_end'])) {
                $value[] = $interval['date_type'] . "|||" . $interval['date_begin'] . "|||" . $interval['date_end'] . "|||" . $interval['comment'];
            }
        }
    }
    $val = $value;
    $check_datatype_message = "";
    $val_1 = chk_datatype($field, $val, $check_datatype_message);
    if ($check_datatype_message) {
        $check_message = $check_datatype_message;
        return 0;
    }
    ${$name} = $val_1;
    return 1;
}

function val_date_flottante_empr($field, $value) {
    global $charset, $pmb_perso_sep, $msg;
    
    $without = "";
    $options = $field['OPTIONS'][0];
    $values = format_output($field, $value);
    $return = "";
    for ($i = 0; $i < count($values); $i++) {
        $interval = explode("|||", $values[$i]);
        if ($return) {
            $return .= " " . $pmb_perso_sep . " ";
        }
        switch ($interval[0]) {
            case '4': // interval date
                $return .= $msg['parperso_option_duration_entre']." " . $interval[1] . " ".$msg['parperso_option_duration_et']." " . $interval[2];
                break;
            case '0': // vers
            case '1': // avant
            case '2': // après
            case '3': // date précise
                $return .= $msg['parperso_option_duration_type'.$interval[0]];
                $return .= " " . $interval[1];
                break;
            case '4': // interval date
                $return .= $msg['parperso_option_duration_entre']." " . $interval[1] . " ".$msg['parperso_option_duration_et']." " . $interval[2];
                break;
                // Pour l'human query de la recherche, BETWEEN, NEAR, =, <=, >= ...
            case 'BETWEEN':
                $return .= $msg['parperso_option_duration_entre']." " . $interval[1] . " ".$msg['parperso_option_duration_et']." " . $interval[2];
                break;
            default:
				if (!empty($interval[1])) $return .= $interval[1];
                break;
        }
        // Commentaire
		if (!empty($interval[3])) {
            $return .= " (" . $interval[3] . ")";
        }
    }
    return $return;
}
function get_langue_doc() {
    global $langue_doc;
    
    if (!isset($langue_doc) || !count($langue_doc)) {
        $langue_doc = marc_list_collection::get_instance('lang');
        $langue_doc = $langue_doc->table;
    }
    return $langue_doc;
}
