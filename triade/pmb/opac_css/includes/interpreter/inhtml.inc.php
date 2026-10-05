<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inhtml.inc.php,v 1.18.4.1 2024/10/31 10:07:43 qvarin Exp $

global $include_path, $func_format;

require_once $include_path . "/misc.inc.php";

if (empty($func_format)) {
    $func_format = [];
}

$func_format['if_logged'] = 'aff_if_logged';
$func_format['if_logged_lang'] = 'aff_if_logged_lang';
$func_format['message_lang'] = 'aff_message_lang';
$func_format['if_param'] = 'aff_if_param';
$func_format['perio_a2z'] = 'aff_perio_a2z';
$func_format['if_session_param'] = 'aff_if_session_param';
$func_format['hidden_global_var_form'] = 'aff_hidden_global_var_form';

$var_format = [];

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
        $ret = $param[0];
    } else {
        if ($param[1]) {
            $ret = $param[1];
        } else {
            $ret = "";
        }
    }
    return $ret;
}

function aff_if_logged_lang($param)
{
    global $lang;
    if ($lang == $param[2]) {
        if ($_SESSION['id_empr_session']) {
            $ret = $param[0];
        } else {
            if ($param[1]) {
                $ret = $param[1];
            } else {
                $ret = "";
            }
        }
    } else {
        $ret = "";
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

function aff_perio_a2z($param)
{
    global $base_path;
    global $opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet;
    require_once($base_path."/classes/perio_a2z.class.php");
    $a2z = new perio_a2z(0, $opac_perio_a2z_abc_search, $opac_perio_a2z_max_per_onglet);
    return $perio_a2z = $a2z->get_form();
}

function aff_hidden_global_var_form($param)
{
    if(empty($param[0])) {
        $param[0] = 'hidden_global_var_form';
    }
    $action = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    $hidden_form = "<form method=\"post\" name='".$param[0]."' action=\"".$action."\" >";
    $hidden_form .= get_hidden_global_var('POST');
    if(!empty($param[1]) && (empty($_POST) || (!empty($_POST) && array_key_exists($param[1], $_POST) === false))) {
        $hidden_form .= "<input type='hidden' name=\"".$param[1]."\" value='' />";
    }
    $hidden_form .= "</form>";
    return $hidden_form;
}
