<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inhtml.inc.php,v 1.10 2017-07-12 15:15:01 tsamson Exp $

require_once ($include_path . "/misc.inc.php");

$func_format['if_logged']= 'aff_if_logged';
$func_format['if_logged_lang']= 'aff_if_logged_lang';
$func_format['message_lang']= 'aff_message_lang';
$func_format['if_param']= 'aff_if_param';
$func_format['eval_php']= 'aff_eval_php';
$func_format['perio_a2z']= 'aff_perio_a2z';
$func_format['if_session_param']= 'aff_if_session_param';



$var_format = array();

function aff_eval_php($param) {
	eval($param[0]);
	return $ret;
}

function aff_if_param($param) {
	//Nom de la variable a tester, valeur, si =, si <>
	$varname=$param[0];
	global ${$varname};
	if (${$varname}==$param[1]) $ret=$param[2]; else $ret=$param[3];
	return $ret;
}

function aff_if_session_param($param) {
	//Nom de la variable a tester, valeur, si =, si <>
	if ($_SESSION[$param[0]]==$param[1]) $ret=$param[2]; else $ret=$param[3];
	return $ret;
}

function aff_if_logged($param) {
	if ($_SESSION['id_empr_session']) {
		$ret = $param[0];
	}else {
		if($param[1]) $ret = $param[1];
		else $ret ="";
	}
	return $ret;
}

function aff_if_logged_lang($param) {
	global $lang;
	if ($lang==$param[2]) {
		if ($_SESSION['id_empr_session']) {
			$ret = $param[0];
		}else {
			if($param[1]) $ret = $param[1];
			else $ret ="";
		}
	} else $ret="";
	return $ret;
}

function aff_message_lang($param) {
	global $lang;
	if ($lang==$param[1])
	return $param[0]; else return "";
}

function aff_perio_a2z($param) {
	global $base_path;
	global $opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet;
	require_once($base_path."/classes/perio_a2z.class.php");		
	$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);		
	return $perio_a2z=$a2z->get_form();
}

?>