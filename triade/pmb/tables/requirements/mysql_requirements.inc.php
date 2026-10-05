<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql_requirements.inc.php,v 1.3 2023/08/29 09:04:56 dbellamy Exp $

// prevents direct script access
if(preg_match('/requirements\.inc\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php');
	forbidden();
}

global $db_user, $user_password, $mysql_host;
global $install_msg, $install_lang, $php_requirements;

require_once(__DIR__.'/classes/verif.class.php');

$class_path = "../classes";
require_once "$base_path/includes/mysql_functions.inc.php";


$basePage = "<!DOCTYPE html>
<html>
	<head>
		<title>{$install_msg['req_window_title']}</title>
		<meta charset='utf-8'>
		<style type='text/css'>
			body {
				font-family: Verdana, Arial, sans-serif;
				background: #eeeae4;
                text-align:center;
			}
			.bouton {
				color: #fff;
				font-size: 12pt;
				font-weight: bold;
				border: 1px outset #D47800;
				background-color: #5483AC;
			}
            .error {
                text-align : center;
                color : red;
                font-size : 1.3em;
            }
			.bouton:hover {
				border-style: inset;
				border: 1px solid #ED8600;
				background-color: #7DC2FF;
			}
            th, td {
                border: 1px solid black;
                padding: 5px;
                text-align: left;
                word-wrap: anywhere;
            }
            table {
                border: 1px solid black;
                background-color : white;
            }
            div {
                margin : 8px;
                text-align : right;
            }
            button:disabled{
               background:  #fff;
               color: #ccc;
               cursor: no-drop;
            }
		</style>
	</head>
    <h1>{$install_msg['req_mysql_requirements_header']}</h1>";

$mysqlForm = "
<form style='display: inline-block; align-content: flex-start;' method='post' action='install.php'>
    <div>
        <label for='db_user'>{$install_msg['req_mysql_form_user']}</label>
        <input required name='db_user' id='db_user' type='text' value='$db_user' />
    </div>
    <div>
        <label for='user_password'>{$install_msg['req_mysql_form_password']}</label>
        <input required name='user_password' id='user_password' type='password' />
    </div>
    <div>
        <label for='mysql_host'>{$install_msg['req_mysql_form_host']}</label>
        <input required name='mysql_host' id='mysql_host' type='text' value='$mysql_host' />
    </div>
	<input type='hidden' name='install_lang' value='{$install_lang}' />
    <input type='hidden' name='install_step' value='mysql_requirements' />
    <button class='bouton' type='submit' >{$install_msg['req_continue_button_label']}</button>
</form>
";

$mysqlAlterVariablesForm = "
<p>
    <input type='checkbox' id='alter_session_variables' name='alter_session_variables' value='1' checked />
    <label for='alter_session_variables'>{$install_msg['req_mysql_alter_session_variables']}</label>
</p>
";

if(isset($db_user)){

    $connexion = pmb_mysql_connect($_POST['mysql_host'],$_POST['db_user'],$_POST['user_password']);
    if(!$connexion ){
        $err = "<div class='error'>{$install_msg['req_mysql_error']}</div></html>";
        $mysql_page = $basePage.$err.$mysqlForm."</html>";
        print $mysql_page;
        exit();
    }


    $verif = new verif($install_msg);

    //Verification des versions mariaDB ou MySQL
    $checkDbVersion = $verif->checkMySQLVersion($connexion);
    if(!$checkDbVersion['checked']){
        print $basePage."
            <p class='error'>
                {$install_msg['req_wrong_sql_version_1']} {$checkDbVersion['engine_type']} {$install_msg['req_wrong_sql_version_2']} <b>{$checkDbVersion['user_version']}</b>
                {$install_msg['req_wrong_sql_version_3']} ("
                 . "&gt;= <b>{$checkDbVersion['required_version']['min']}</b>"
                 . (empty($checkDbVersion['required_version']['max'])? "" : ", <= <b>{$checkDbVersion['required_version']['max']}</b>").
            ")</p>";
        exit();
    }

    $mysql_table = "
        <table>
            <tr>
                <th>{$install_msg['req_mysql_suggested_table_th_1']}</th>
                <th>{$install_msg['req_mysql_suggested_table_th_2']}</th>
                <th>{$install_msg['req_mysql_suggested_table_th_3']}</th>
                <th>{$install_msg['req_mysql_suggested_table_th_4']}</th>
            </tr>
        ";

    $mysql_check = $verif->checkMySQL($connexion);
    $missRequired = false;
    $badConfig = false;
    foreach($mysql_check as $value)
    {
        $state = '';
        $comment = '';
        switch($value['state']){
            case (verif::OK) :
                $state = "<img src='$base_path/images/tick.gif' style='height:16px;' />";
                break;
            case (verif::WARN) :
                $badConfig = true;
                $state = "<img src='$base_path/images/warning.gif' style='height:16px;' />";
                break;
            case (verif::KO) :
                $missRequired = true;
                $state = "<img src='$base_path/images/error.gif' style='height:16px;' />";
                break;
        }
        if( !empty($value['comment']) && array_key_exists($value['comment'], $install_msg) ) {
            $comment = $install_msg[$value['comment']];
        }

        $mysql_table .= "
        <tr>
            <td>{$value['name']}</td>
            <td>{$value['suggestedValue']}</td>
            <td>{$value['value']} $state</td>
            <td>$comment</td>
        </tr>";
    }
    $mysql_table .= "</table>";
    $validationForm = "
    <form style='margin-top:20px; margin-bottom:20px;' method='post' action='install.php'>
        <input type='hidden' id='mysql_requirements' name='mysql_requirements' value='mysql_requirements' />
		<input type='hidden' name='install_lang' value='{$install_lang}' />
    	<input type='hidden' name='install_step' value='install' />
        <input type='hidden' name='db_user' value='$db_user' />
        <input type='hidden' name='user_password' value='$user_password' />
        <input type='hidden' name='mysql_host' value='$mysql_host' />
        <input type='hidden' name='mysql_variables' value='".rawurlencode(json_encode($mysql_check))."' />
        <!-- alter_variables -->
        <button class='bouton'".(($missRequired == 1) ? "disabled" : "")." type='submit'>{$install_msg['req_continue_button_label']}</button>
    </form>";

    if($missRequired ==  true) {
        $page_header = "<p class='error'>{$install_msg['req_mysql_requirements_missing']}</p>";

    } elseif ($badConfig == true) {
        $page_header = "<p class='error'>{$install_msg['req_mysql_bad_config']}</p>";
        $validationForm = str_replace('<!-- alter_variables -->', $mysqlAlterVariablesForm, $validationForm);

    } else {
        $page_header = "<p>{$install_msg['req_check_mysql_infos']}</p>";
    }
    $mysql_page = $basePage.$page_header."<div style='display : flex; justify-content : center'>".$mysql_table."</div>".$validationForm."</html>";

} else {
    $infos = "<p>{$install_msg['req_mysql_form_desc']}</p>";
    $mysql_page = $basePage.$infos.$mysqlForm."</html>";
}

