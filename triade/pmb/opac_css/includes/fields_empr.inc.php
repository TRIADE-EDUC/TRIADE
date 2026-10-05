<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fields_empr.inc.php,v 1.144.4.3 2025/04/17 07:24:18 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
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
        "text" => (isset($msg["parperso_text"]) ? $msg["parperso_text"] : ''),
        "list" => (isset($msg["parperso_choice_list"]) ? $msg["parperso_choice_list"] : ''),
        "query_list" => (isset($msg["parperso_query_choice_list"]) ? $msg["parperso_query_choice_list"] : ''),
        "query_auth" => (isset($msg["parperso_authorities"]) ? $msg["parperso_authorities"] : ''),
        "date_box" => (isset($msg["parperso_date"]) ? $msg["parperso_date"] : ''),
        "comment" => (isset($msg["parperso_comment"]) ? $msg["parperso_comment"] : ''),
        "external" => (isset($msg["parperso_external"]) ? $msg["parperso_external"] : ''),
        "url" => (isset($msg["parperso_url"]) ? $msg["parperso_url"] : ''),
        "resolve" => (isset($msg["parperso_resolve"]) ? $msg["parperso_resolve"] : ''),
        "marclist" => (isset($msg["parperso_marclist"]) ? $msg["parperso_marclist"] : ''),
        "html" => (isset($msg["parperso_html"]) ? $msg["parperso_html"] : ''),
        "text_i18n" => (isset($msg["parperso_text_i18n"]) ? $msg["parperso_text_i18n"] : ''),
        "q_txt_i18n" => (isset($msg["parperso_q_txt_i18n"]) ? $msg["parperso_q_txt_i18n"] : ''),
        "date_inter" => (isset($msg["parperso_date_inter"]) ? $msg["parperso_date_inter"] : ''),
        "date_flot" => (isset($msg["parperso_date_flottante"]) ? $msg["parperso_date_flottante"] : '')
);

global $options_list_empr;
$options_list_empr = array(
    "text" => "options_text.php",
    "list" => "options_list.php",
    "query_list" => "options_query_list.php",
    "query_auth" => "options_query_auth.php",
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
    "date_flot" => "options_date_flot.php"
);


global $chk_type_list;
if (empty($chk_type_list)) {
    $chk_type_list = array(
        "small_text"=>"chk_type_small_text",
        "text"=>"chk_type_text",
        "integer"=>"chk_type_integer",
        "date"=>"chk_type_date",
        "float"=>"chk_type_float"
    );
}

global $format_list;
if (empty($format_list)) {
    $format_list=array(
        "small_text"=>"format_small_text",
        "text"=>"format_text",
        "integer"=>"format_integer",
        "date"=>"format_date",
        "float"=>"format_float"
    );
}
// formulaire de saisie des param perso des autorités
function aff_query_auth_empr($field,&$check_scripts,$script="") {
    return custom_fields_query_auth::aff($field, $check_scripts);
}

function aff_query_auth_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_query_auth::aff_search($field, $check_scripts, $varname);
}

// Sauvegarde du formulaire
function chk_query_auth_empr($field,&$check_message) {
    return custom_fields_query_auth::chk($field, $check_message);
}

// affichage de l'autorité
function val_query_auth_empr($field,$value) {
    return custom_fields_query_auth::val($field, $value);
}

function chk_datatype($field,$values,&$check_datatype_message) {
    global $chk_type_list;
    global $msg;
    if(! is_countable($values)) {
    	return $values;
    }
    if (((!count($values))||((count($values)==1)&&($values[0]=="")))&&($field['MANDATORY']!=1)) return $values;
    for ($i=0; $i<count($values); $i++) {
        $chk_message="";
        $val = "";
        if (!empty($chk_type_list[$field['DATATYPE']])) {
        	eval("\$val=".$chk_type_list[$field['DATATYPE']]."(stripslashes(\$values[\$i]),\$chk_message);");
        }
        if ($chk_message) {
            $check_datatype_message=sprintf($msg["parperso_chk_datatype"],$field['NAME'],$chk_message);
        }
        $values[$i]=addslashes($val);
    }
    return $values;
}

function format_output($field,$values) {
    if(! is_countable($values)) {
        return $values;
    }
    global $format_list;
    for ($i=0; $i<count($values); $i++) {
        eval("\$val=".$format_list[$field['DATATYPE']]."(\$values[\$i]);");
        $values[$i]=$val;
    }
    return $values;
}

//fonction de découpage d'une chaine trop longue
function cutlongwords($valeur) {
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
    return custom_fields_date_box::aff($field, $check_scripts);
}

function aff_date_box_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_date_box::aff_search($field, $check_scripts, $varname);
}

function chk_date_box_empr($field,&$check_message) {
    return custom_fields_date_box::chk($field, $check_message);
}

function val_date_box_empr($field,$value) {
    return custom_fields_date_box::val($field, $value);
}

function aff_text_empr($field,&$check_scripts) {
    return custom_fields_text::aff($field, $check_scripts);
}

function aff_text_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_text::aff_search($field, $check_scripts, $varname);
}

function chk_text_empr($field,&$check_message) {
    return custom_fields_text::chk($field, $check_message);
}

function val_text_empr($field,$value) {
    return custom_fields_text::val($field, $value);
}

function aff_comment_empr($field,&$check_scripts) {
    return custom_fields_comment::aff($field, $check_scripts);
}

function aff_comment_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_comment::aff_search($field, $check_scripts, $varname);
}

function chk_comment_empr($field,&$check_message) {
    return custom_fields_comment::chk($field, $check_message);
}

function val_comment_empr($field,$value) {
    return custom_fields_comment::val($field, $value);
}

function val_html_empr($field,$value) {
    return custom_fields_html::val($field, $value);
}

function aff_list_empr($field,&$check_scripts,$script="") {
    return custom_fields_list::aff($field, $check_scripts, $script);
}

function aff_list_empr_search($field,&$check_scripts,$varname,$script="") {
    return custom_fields_list::aff_search($field, $check_scripts, $varname,$script);
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
			$value=array();
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
    return custom_fields_list::chk($field, $check_message);
}

function val_list_empr($field,$val) {
    return custom_fields_list::val($field, $val);
}

function aff_query_list_empr($field,&$check_scripts,$script="") {
    return custom_fields_query_list::aff($field, $check_scripts, $script);
}

function aff_query_list_empr_search($field,&$check_scripts,$varname,$script="") {
    return custom_fields_query_list::aff_search($field, $check_scripts, $varname, $script);
}

function chk_query_list_empr($field,&$check_message) {
    return custom_fields_query_list::chk($field, $check_message);
}

function val_query_list_empr($field, $val) {
    return custom_fields_query_list::val($field, $val);
}

function aff_text_i18n_empr($field,&$check_scripts) {
    return custom_fields_text_i18n::aff($field, $check_scripts);
}

function aff_text_i18n_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_text_i18n::aff_search($field, $check_scripts, $varname);
}

function chk_text_i18n_empr($field,&$check_message) {
    return custom_fields_text_i18n::chk($field, $check_message);
}

function val_text_i18n_empr($field,$value) {
    return custom_fields_text_i18n::val($field, $value);
}

function aff_filter_comment_empr($field,$varname,$multiple) {
    return custom_fields_comment::aff_filter($field,$varname,$multiple);
}

function aff_filter_date_box_empr($field,$varname,$multiple) {
    return custom_fields_date_box::aff_filter($field,$varname,$multiple);
}

function aff_filter_text_empr($field,$varname,$multiple) {
    return custom_fields_text::aff_filter($field,$varname,$multiple);
}

function aff_filter_query_list_empr($field,$varname,$multiple) {
    return custom_fields_query_list::aff_filter($field,$varname,$multiple);
}

function aff_filter_list_empr($field,$varname,$multiple) {
    return custom_fields_list::aff_filter($field,$varname,$multiple);
}

function aff_external_empr($field,&$check_scripts) {
    return custom_fields_external::aff($field, $check_scripts);
}

function aff_external_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_external::aff_search($field, $check_scripts, $varname);
}

function chk_external_empr($field,&$check_message) {
    return custom_fields_external::chk($field, $check_message);
}

function val_external_empr($field,$value) {
    return custom_fields_external::val($field, $value);
}

function aff_url_empr($field,&$check_scripts){
    return custom_fields_url::aff($field, $check_scripts);
}

function aff_url_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_url::aff_search($field, $check_scripts, $varname);
}

function chk_url_empr($field,&$check_message) {
    return custom_fields_url::chk($field, $check_message);
}

function val_url_empr($field,$value) {
    return custom_fields_url::val($field, $value);
}

function aff_resolve_empr($field,&$check_scripts){
    return custom_fields_resolve::aff($field, $check_scripts);
}

function chk_resolve_empr($field,&$check_message) {
    return custom_fields_resolve::chk($field, $check_message);
}

function val_resolve_empr($field,$value) {
    return custom_fields_resolve::val($field, $value);
}

function aff_resolve_empr_search($field,&$check_scripts,$varname){
    return custom_fields_resolve::aff_search($field, $check_scripts, $varname);
}

function aff_html_empr($field,&$check_scripts) {
    return custom_fields_html::aff($field, $check_scripts);
}

function aff_marclist_empr($field,&$check_scripts,$script="") {
    return custom_fields_marclist::aff($field, $check_scripts, $script);
}

function chk_marclist_empr($field,&$check_message) {
    return custom_fields_marclist::chk($field, $check_message);
}

function val_marclist_empr($field,$value) {
    return custom_fields_marclist::val($field, $value);
}

function aff_marclist_empr_search($field,&$check_scripts,$varname){
    return custom_fields_marclist::aff_search($field, $check_scripts, $varname);
}

function aff_q_txt_i18n_empr($field,&$check_scripts) {
    global $charset, $base_path;
	global $msg;
    
	$langue_doc = get_langue_doc();
	$datatype = $field['DATATYPE'];
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
    if(!empty($options['MAXSIZE'][0]['value'])) {
        $maxlength = $options['MAXSIZE'][0]['value'];
        if ($datatype == "small_text" && $maxlength > 200) {
            //on force le maxlength à 200 pour pouvoir enregistrer la qualification et la langue.
            $maxlength = 200;
        }
    } else {
        $maxlength = 200;
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
        $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_q_txt_i18n_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.$options['SIZE'][0]['value'].'\', \''.$maxlength.'\')">';
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
        
        if ($options['TYPE'][0]['value'] == "textarea") {
            $ret = "<textarea
                        id='".$field['NAME']."_$count'
                        name='".$field['NAME']."[$count]'  data-form-name='".$field["NAME"]."_'
                        maxlength='$maxlength'
                    >".htmlentities($exploded_value[0], ENT_QUOTES, $charset)."</textarea>";
        } else {
            $ret = "<input id='".$field['NAME']."_$count' type='text' 
                            size='".$options['SIZE'][0]['value']."' 
                            maxlength='$maxlength'
                            name='".$field['NAME']."[$count]' data-form-name='".$field["NAME"]."_' 
                            value='".htmlentities($exploded_value[0],ENT_QUOTES,$charset)."'>";
        }
        $ret.="<input id=\"".$field['NAME']."_lang_".$count."\" class=\"saisie-10emr\" type=\"text\" value=\"".($exploded_value[1] ? htmlentities($langue_doc[$exploded_value[1]],ENT_QUOTES,$charset) : '')."\" autfield=\"".$field['NAME']."_lang_code_".$count."\" completion=\"langue\" autocomplete=\"off\" data-form-name='".$field["NAME"]."_lang_' >";
        $ret.="<input class=\"bouton\" type=\"button\" value=\"...\" onClick=\"openPopUp('".$base_path."/select.php?what=lang&caller='+this.form.name+'&p1=".$field['NAME']."_lang_code_".$count."&p2=".$field['NAME']."_lang_".$count."', 'select_lang', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')\">";
        $ret.="<input class=\"bouton\" type=\"button\" onclick=\"this.form.".$field['NAME']."_lang_".$count.".value=''; this.form.".$field['NAME']."_lang_code_".$count.".value=''; \" value=\"X\">";
        $ret.="<input id=\"".$field['NAME']."_lang_code_".$count."\" data-form-name='".$field["NAME"]."_lang_code_' type=\"hidden\" value=\"".($exploded_value[1] ? htmlentities($exploded_value[1], ENT_QUOTES, $charset) : '')."\" name=\"".$field['NAME']."_langs[".$count."]\">";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
            $ret.='<input class="bouton" type="button" value="+" onclick="add_custom_q_txt_i18n_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.$options['SIZE'][0]['value'].'\', \''.$maxlength.'\')">';
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
    global $pmb_perso_sep;
    
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
    return custom_fields_date_inter::aff($field, $check_scripts);
}

function aff_date_inter_empr_search($field,&$check_scripts,$varname) {
    return custom_fields_date_inter::aff_search($field, $check_scripts, $varname);
}

function aff_filter_date_inter_empr($field,$varname,$multiple) {
    return custom_fields_date_inter::aff_filter($field, $varname, $multiple);
}

function chk_date_inter_empr($field,&$check_message) {
    return custom_fields_date_inter::chk($field, $check_message);
}

function val_date_inter_empr($field,$value) {
    return custom_fields_date_inter::val($field, $value);
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
        case "collstate":
        	$caller="saisie_collstate";
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
        $return.= "<script src='".$base_path."/javascript/customfield_drop.js'></script>";
        $customfield_drop_already_included = true;
    }
    
    return $return."
		<script>
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
    return "
	<script>
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
            if(!is_numeric($id)){
                $id = onto_common_uri::get_id($id);
            }
            if(!$id) break;
            $aut = authorities_collection::get_authority('concept', $id);
            $isbd .= html_entity_decode($aut->get_display_label(),ENT_QUOTES, $charset);
            break;
        default:
            if ($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"] > 1000) {
                // autperso
                $aut = new authperso_data($id);
                $isbd .= html_entity_decode($aut->get_isbd(), ENT_QUOTES, $charset);
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
            if(!is_numeric($id)){
                $id = onto_common_uri::get_id($id);
            }
            if(!$id) break;
            return authorities_collection::get_authority('concept', $id);
        default:
            if ($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"] > 1000) {
                // autperso
                return new authperso_data($id);
            }
            break;
    }
    return null;
}

function aff_date_flottante_empr($field, &$check_scripts) {
    global $charset;
    global $msg;
    
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
	global $pmb_perso_sep, $msg;
    
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
