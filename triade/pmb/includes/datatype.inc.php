<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: datatype.inc.php,v 1.8.4.1 2025/04/02 09:44:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $datatype_list, $chk_type_list, $format_list;

$datatype_list = array(
        "small_text" => (isset($msg["parperso_datatype_text"]) ? $msg["parperso_datatype_text"] : ''),
        "text" => (isset($msg["parperso_datatype_huge_text"]) ? $msg["parperso_datatype_huge_text"] : ''),
        "integer" => (isset($msg["parperso_datatype_integer"]) ? $msg["parperso_datatype_integer"] : ''),
        "date" => (isset($msg["parperso_datatype_date"]) ? $msg["parperso_datatype_date"] : ''),
        "float" => (isset($msg["parperso_datatype_float"]) ? $msg["parperso_datatype_float"] : '')
);
$chk_type_list = array(
        "small_text"=>"chk_type_small_text",
        "text"=>"chk_type_text",
        "integer"=>"chk_type_integer",
        "date"=>"chk_type_date",
        "float"=>"chk_type_float"
);
$format_list = array(
        "small_text"=>"format_small_text",
        "text"=>"format_text",
        "integer"=>"format_integer",
        "date"=>"format_date",
        "float"=>"format_float"
);

function chk_type_small_text($value,&$chk_message) {
	$chk_message="";
	$value=substr($value,0,255);
	return $value;
}

function format_small_text($value) {
	return substr($value,0,255);
}

function chk_type_text($value,&$chk_message) {
	$chk_message="";
	return $value;
}

function format_text($value) {
	return $value;
}

function chk_type_integer($value,&$chk_message) {
	global $msg;
	
	$chk_message="";
	if ((string)round(intval($value))!=$value) {
		$chk_message=$msg["parperso_datatype_not_integer"];
		return $value;
	} else
		return $value;
}

function format_integer($value) {
	if ($value=="") return "";
	return round($value);
}

function chk_type_date($value,&$chk_message) {
	global $msg;
	
	$chk_message="";
	$d=explode("-",$value);
	if (!checkdate($d[1],$d[2],$d[0])) {
		$chk_message=$msg["parperso_datatype_not_date"];
		return $value;
	} else
		return $value;
}

function format_date($value) {
	if ($value=="") return "";
	return formatdate($value);
}

function format_date_input($value) {
	if ($value=="") return "";
	return formatdate_input($value);
}

function chk_type_float($value,&$chk_message) {
	global $msg;
	
	$chk_message="";
	if ((floatval($value))!=$value) {
		$chk_message=$msg["parperso_datatype_not_float"];
		return $value;
	} else
		return $value;
}

function format_float($value) {
	if ($value=="") return "";
	return round($value,2);
}
