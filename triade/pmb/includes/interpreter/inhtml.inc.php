<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inhtml.inc.php,v 1.4.6.1 2024/10/31 10:07:42 qvarin Exp $

global $include_path, $func_format;

require_once($include_path . "/misc.inc.php");

if(empty($func_format)) {
    $func_format = [];
}

$func_format['if_logged'] = 'aff_if_logged';
$func_format['message_lang'] = 'aff_message_lang';
$func_format['if_param'] = 'aff_if_param';
$func_format['if_session_param'] = 'aff_if_session_param';

function aff_if_param($param)
{
    //Nom de la variable a tester, valeur, si =, si <>
    $varname = $param[0];
    global ${$varname};
    if (${$varname} == $param[1]) {
        $ret = $param[2];
    } else {
        $ret = $param[3];
    }
    return $ret;
}

function aff_if_session_param($param)
{
    //Nom de la variable a tester, valeur, si =, si <>
    if ($_SESSION[$param[0]] == $param[1]) {
        $ret = $param[2];
    } else {
        $ret = $param[3];
    }
    return $ret;
}

function aff_if_logged($param)
{
    if ($_SESSION['id_empr_session']) {
        $ret = $param[1];
    } else {
        $ret = $param[2];
    }
    return $ret;
}

function aff_message_lang($param)
{
    global $lang;
    if ($lang == $param[1]) {
        return $param[0];
    } else {
        return "";
    }
}
