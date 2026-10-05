<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fields.inc.php,v 1.41.2.1.2.3 2025/04/25 08:30:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path.'/fields_empr.inc.php');

global $aff_list, $chk_list, $val_list, $type_list, $options_list, $msg;
$aff_list=array("text"=>"aff_text","list"=>"aff_list","query_list"=>"aff_query_list","date_box"=>"aff_date_box","file_box"=>"aff_file_box","selector"=>"aff_selector");
$chk_list=array("text"=>"chk_text","list"=>"chk_list","query_list"=>"chk_query_list","date_box"=>"chk_date_box","file_box"=>"chk_file_box","selector"=>"chk_selector");
$val_list=array("text"=>"val_text","list"=>"val_list","query_list"=>"val_query_list","date_box"=>"val_date_box","file_box"=>"val_file_box","selector"=>"val_selector");
$type_list=array("text"=>$msg["parperso_text"],"list"=>$msg["parperso_choice_list"],"query_list"=>$msg["parperso_query_choice_list"],"date_box"=>$msg["parperso_date"],"file_box"=>$msg["parperso_file_box"],"selector"=>$msg["parperso_selector"]);
$options_list=array("text"=>"options_text.php","list"=>"options_list.php","query_list"=>"options_query_list.php","date_box"=>"options_date_box.php","file_box"=>"options_file_box.php","selector"=>"options_selector.php");

function aff_selector($field,&$check_scripts) {
	
	global $msg, $categ;
	
	if($field["OPTIONS"][0]["METHOD"]["0"]["value"]==1) {
		$text_name=$field['NAME']."_label";
		$hidden_name=$field['NAME'];
		$param = $hidden_name;
		global ${$param};
		$text_value = get_authority_isbd_from_field($field, ${$param});
		$hidden_value = ${$param};
	} else {
		$text_name=$field['NAME'];
		$hidden_name=$field['NAME']."_id";
		$param = $text_name;
		global ${$param};
		$text_value = ${$param};
		$hidden_value = '';
	}
	
	$selection_parameters = get_authority_selection_parameters($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]);
	$what = $selection_parameters['what'];
	$completion = $selection_parameters['completion'];
	$ret="<span style='width: 251px;'><input type='text' name='".$text_name."' id='".$text_name."' value='".$text_value."' class='saisie-30emr' completion='$completion' autfield='$hidden_name'></span>";
	
	switch ($categ) {
		case "planificateur" :
			$form_name = "planificateur_form";
			break;
		default :
			$form_name = "formulaire";
			break;	
	}

	$ret.="<input class='bouton' value='...' onclick=\"openPopUp('./select.php?what=".$what."".(($selection_parameters['element']) ? "&element=".($selection_parameters['element']) : "")."&dyn=&caller=".$form_name."&param1=".$hidden_name."&param2=".$text_name."&p1=".$hidden_name."&p2=".$text_name."&deb_rech='+".pmb_escape()."(''), 'selector')\" type='button'>";
	$ret.="<input name='".$hidden_name."' id='".$hidden_name."' value='".$hidden_value."' type='hidden'>";
	
	if ($field['MANDATORY']=="yes") $check_scripts.="if (document.".$form_name.".".$field['NAME'].".value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value'])."\");\n";
	return $ret;
}


function chk_selector($field,&$check_message) {
	return 1;
}


function val_selector($field) {
	
	$name=$field['NAME'];
	global ${$name};
	
	return ${$name};
}


function aff_file_box($field,&$check_scripts) {
	
	global $msg, $categ;
	
	//pré-remplissage
	$param = $field['NAME'];
	global ${$param};
	
	switch ($categ) {
		case "planificateur" :
			$form_name = "planificateur_form";
			break;
		default :
			$form_name = "formulaire";
			break;	
	}
	
	$ret="<input type=\"file\" name=\"".$field["NAME"]."\">";
	if ($field['MANDATORY']=="yes") $check_scripts.="if (document.".$form_name.".".$field['NAME'].".value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value'])."\");\n";
	return $ret;
}


function chk_file_box($field,&$check_message) {
	
	global $msg;
	global $_FILES;
	
	//Supression des vieux fichiers !
	$dir=opendir("temp");
	$files=array();
	while (false !== ($file=readdir($dir))) {
		$files[]=$file;
	}
	for ($i=0; $i<count($files); $i++) {
		$file=$files[$i];
		$date=filemtime("temp/".$file);
		if (((time()-$date)>=24*60*60)&&(substr($file,0,13)=="proc_actions_")) {
			unlink("temp/".$file);
		} 
	}
	
	if ($_FILES[$field['NAME']]["error"]) {
		$check_message=$msg['field_file_download'];
		return 0;
	} else {
		if ($_FILES[$field['NAME']]["tmp_name"]) {
			if (move_uploaded_file($_FILES[$field['NAME']]["tmp_name"],"temp/proc_actions_".basename($_FILES[$field['NAME']]["tmp_name"]))) {
				$field_name=$field['NAME'];
				global ${$field_name};
				$field_name_[0]="proc_actions_".basename($_FILES[$field['NAME']]["tmp_name"]);
				${$field_name}=$field_name_;
				return 1;
			} else {
				$check_message=$msg['field_file_copy'];
				return 0;
			}
		} else {	
			$field_name=$field['NAME'];
			global ${$field_name};
			$field_name_=${$field_name};
			if (file_exists("temp/".basename($field_name_[0]))) {
				return 1;
			} else {
				$check_message=$msg['field_file_not_exist'];
				return 0;
			}
		}
	}
}


function val_file_box($field) {
    global $default_tmp_storage_engine;

	if ($field ['OPTIONS'][0]['METHOD'][0]['value']=="") $field ['OPTIONS'][0]['METHOD'][0]['value']=1;
	if (($field ['OPTIONS'][0]['METHOD'][0]['value']==2)&&($field ['OPTIONS'][0]['DATA_TYPE'][0]['value']=="")) $field ['OPTIONS'][0]['DATA_TYPE'][0]['value']=1;
	$val=array();
	
	$field_name=$field['NAME'];
	global ${$field_name};
	$field_name_=${$field_name};
	
	if (($fp=@fopen("temp/".$field_name_[0],"r"))) {
		while (!feof($fp)) {
			$val_=@fgets($fp);
			$val_=rtrim($val_);
			$val[]=$val_;
		}
		fclose($fp);
		//unlink($_FILES[$field["NAME"]]["tmp_name"]);
		if ($field['OPTIONS'][0]['METHOD'][0]['value']==1) {
			$val = addslashes_array($val);
			$ret=implode("', '",$val);
			if ($ret!="") $ret="'".$ret."'";
			return $ret;
		} else {
			if ($field ['OPTIONS'][0]['DATA_TYPE'][0]['value']=="1") $data_type="varchar(255)"; else $data_type="integer";
			$requete="create temporary table ".$field['OPTIONS'][0]['TEMP_TABLE_NAME'][0]['value']." (val $data_type, INDEX (val)) ENGINE={$default_tmp_storage_engine} ";
			pmb_mysql_query($requete);
			foreach ($val as $key => $value) {
				$requete="insert into ".$field['OPTIONS'][0]['TEMP_TABLE_NAME'][0]['value']." values('".addslashes($value)."')";
				pmb_mysql_query($requete);
			}
			return $field['OPTIONS'][0]['TEMP_TABLE_NAME'][0]['value'];
		}
	}
}


function aff_text($field,&$check_scripts) {
	
	global $msg, $categ;
	
	//pré-remplissage
	$param = $field['NAME'];
	global ${$param};
	
	switch ($categ) {
		case "planificateur" :
			$form_name = "planificateur_form";
			break;
		default :
			$form_name = "formulaire";
			break;	
	}
	
	$options = $field['OPTIONS'][0] ?? [];
	$ret="<input type=\"text\"";
	if(array_key_exists("SIZE", $options) && array_key_exists(0, $options['SIZE']) && array_key_exists('value', $options['SIZE'][0])) {
		$ret .= " size=\"". $options['SIZE'][0]['value'] . "\"";
	}
	if(array_key_exists("MAXSIZE", $options) && array_key_exists(0, $options['SIZE']) && array_key_exists('value', $options['SIZE'][0])) {
		$ret .= " maxlength=\"".$options['MAXSIZE'][0]['value']."\"";
	}

	if(array_key_exists("NAME", $field)) {
		$ret .= " name=\"".$field['NAME']."\"";
	}
	$ret .= " value=\"".${$param}."\">";
	
	if ($field['MANDATORY']=="yes") $check_scripts.="if (document.".$form_name.".".$field['NAME'].".value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value'])."\");\n";
	return $ret;
}


function chk_text($field,&$check_message) {
	return 1;
}


function val_text($field) {
	
	$name=$field['NAME'];
	global ${$name};
	
	return ${$name};
}


function aff_date_box($field,&$check_scripts) {
	
	global $msg, $categ;
	
	//pré-remplissage
	$param = $field['NAME'];
	global ${$param};
	
	if (${$param} != '') {
		if(preg_match('`^\d{4}\-\d{2}\-\d{2}$`',${$param})) {
			$val=${$param};
			$val_popup=str_replace('-','',${$param});
		} else {
			$val=date("Y-m-d",${$param});
			$val_popup=date("Ymd",${$param});
		}
	} else {
		$val=date("Y-m-d",time());
		$val_popup=date("Ymd",time());	
	}
	
	switch ($categ) {
		case "planificateur" :
			$form_name = "planificateur_form";
			break;
		default :
			$form_name = "formulaire";
			break;	
	}
	
	$ret="<input type='hidden' name='".$field['NAME']."' value='$val' />
				<input class='bouton' type='button' name='".$field['NAME']."_lib' value='".formatdate($val_popup)."' onClick=\"openPopUp('./select.php?what=calendrier&caller=".$form_name."&date_caller=".$val_popup."&param1=".$field['NAME']."&param2=".$field['NAME']."_lib&auto_submit=NO&date_anterieure=YES', 'calendar')\" />";
	if ($field['MANDATORY']=="yes") $check_scripts.="if (document.".$form_name.".elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value'])."\");\n";
	return $ret;
}


function chk_date_box($field,&$check_message) {
	return 1;
}


function val_date_box($field) {
	
	$name=$field['NAME'];
	global ${$name};

	return stripslashes(${$name});
}


function aff_list($field,&$check_scripts) {
	
	global $charset;
	
	//pré-remplissage
	$param = $field['NAME'];
	global ${$param};

	$sel_param = array();
	if (is_array(${$param})) {
		foreach (${$param} as $aparam) {
			$sel_param[$aparam] = $aparam;
		}
	} else {
		$sel_param[${$param}] = ${$param};
	}
	
	$options=$field['OPTIONS'][0];
	$ret="<select name=\"".$field['NAME'];
	if ($options['MULTIPLE'][0]['value']=="yes") $ret.="[]";
	$ret.="\" ";
	if ($options['MULTIPLE'][0]['value']=="yes") $ret.="multiple";
	$ret.=">\n";
	if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
		$ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
	}
	if (!empty($options['ITEMS'][0]['ITEM']) && is_countable($options['ITEMS'][0]['ITEM'])) {
    	for ($i=0; $i<count($options['ITEMS'][0]['ITEM']); $i++) {
    		$ret.="<option value=\"".htmlentities($options['ITEMS'][0]['ITEM'][$i]['VALUE'],ENT_QUOTES,$charset)."\" ".(isset($sel_param[$options['ITEMS'][0]['ITEM'][$i]['VALUE']]) && $sel_param[$options['ITEMS'][0]['ITEM'][$i]['VALUE']] == $options['ITEMS'][0]['ITEM'][$i]['VALUE'] ? "selected" : "").">".htmlentities($options['ITEMS'][0]['ITEM'][$i]['value'],ENT_QUOTES,$charset)."</option>\n";
    	}
	}
	$ret.= "</select>\n";
	return $ret;
}


function chk_list($field,&$check_message) {
	
	global $msg;
	
	$name=$field['NAME'];
	global ${$name};
	$val=${$name};
	if ($field['MANDATORY']=="yes") {
    if ((!isset($val))||((is_array($val))&&(count($val)==1)&&($val[0]==""))||($val=="")) {
			$check_message=sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value']);
			return 0;
		}
	}
	return 1;
}


function val_list($field) {
	
	$name=$field['NAME'];
	global ${$name};
	
	$val=${$name};
	
	if ($field['OPTIONS'][0]['MULTIPLE'][0]['value']=="yes") {
		if (isset($field['OPTIONS'][0]['COLUMN_NAME'][0]['value']) && $field['OPTIONS'][0]['COLUMN_NAME'][0]['value']) {
		    if (is_array($val)) {
		        $val_=implode(",",$val);
		    } else {
		        $val_="";
		    }
			return stripslashes($val_);
		}
		if (is_array($val)) {
		    $val_=implode("','",$val);
		} else {
		    $val_="";
		}
		if ($val_!="") $val_="'".$val_."'";
		$val_=stripslashes($val_);
		return $val_;
	} else {
		$val=stripslashes($val);
		if (isset($field['OPTIONS'][0]['COLUMN_NAME'][0]['value']) && $field['OPTIONS'][0]['COLUMN_NAME'][0]['value']) {
			return $val;
		}
		return "'".$val."'";
	}
}


function aff_query_list($field,&$check_scripts) {
	
	global $charset;
	
	//pré-remplissage
	$param = $field['NAME'];
	global ${$param};

	if (is_array(${$param})) {
		foreach (${$param} as $aparam) {
			$sel_param[$aparam] = $aparam;
		}
	} else {
		$sel_param[${$param}] = ${$param};
	}

	$options=$field['OPTIONS'][0];
	$ret="<select name=\"".$field['NAME'];
	if ($options['MULTIPLE'][0]['value']=="yes") $ret.="[]";
	$ret.="\" ";
	if ($options['MULTIPLE'][0]['value']=="yes") $ret.="multiple";
	$ret.=">\n";
	if (($options['UNSELECT_ITEM'][0]['VALUE']!="")||($options['UNSELECT_ITEM'][0]['value']!="")) {
		$ret.="<option value=\"".htmlentities($options['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."\">".htmlentities($options['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."</option>\n";
	}
	$resultat=pmb_mysql_query($options['QUERY'][0]['value']);
	while (($r=pmb_mysql_fetch_row($resultat))) {
		$ret.="<option value=\"".htmlentities($r[0],ENT_QUOTES,$charset)."\" ".(isset($sel_param[$r[0]]) && $sel_param[$r[0]] == $r[0] ? "selected" : "").">".htmlentities($r[1],ENT_QUOTES,$charset)."</option>\n";
	}
	$ret.= "</select>\n";
	return $ret;
}


function chk_query_list($field,&$check_message) {
	
	global $msg;

	$name=$field['NAME'];
	global ${$name};
	$val=${$name};
	if ($field['MANDATORY']=="yes") {
	if ((!isset($val))||((is_array($val))&&(count($val)==1)&&($val[0]==""))||($val=="")) {
			$check_message=sprintf($msg["parperso_field_is_needed"],$field['ALIAS'][0]['value']);
			return 0;
		}
	}
	return 1;
}


function val_query_list($field) {
	
	$name=$field['NAME'];
	global ${$name};
	
	$val=${$name};
	
	if ($field['OPTIONS'][0]['MULTIPLE'][0]['value']=="yes") {
	    if (is_array($val)) {
	        $val_=implode("','",$val);
	    } else {
	        $val_="";
	    }
		if ($val_!="") $val_="'".$val_."'";
		//$val_=stripslashes($val_);
		return $val_;
	} else {
		//$val=stripslashes($val);
		return $val;
	}
}
?>