<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql_functions.inc.php,v 1.17.4.3 2025/02/14 14:57:06 dbellamy Exp $

global $class_path;
require_once($class_path.'/pmb_mysqli.class.php');
require_once($class_path.'/logs/MySQLi_log.class.php');

define("PMB_MYSQL_ASSOC", MYSQLI_ASSOC);
define("PMB_MYSQL_BOTH", MYSQLI_BOTH);
define("PMB_MYSQL_NUM", MYSQLI_NUM);

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_affected_rows($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->affected_rows;
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_close($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->close();
}

/**
 *
 * @param string $server
 * @param string $username
 * @param string $password
 * @param string $dbname
 * @param string $port
 * @param string $socket
 * @return mysqli
 */
function pmb_mysql_connect($server = null, $username = null, $password = null, $dbname = null, $port = null, $socket = null){
	if(strpos($server,":")!==false){
		$t = explode(":",$server);
		$server = $t[0];
		$port = $t[1];
	}
	$res = pmb_mysqli::init_connection($server, $username, $password, $dbname, $port, $socket);
	if ($res->connect_error) {
		return 0;
	}
	return $res;
}

/**
 *
 * @param mysqli_result $result
 * @param int $row_number
 */
function pmb_mysql_data_seek($result , $row_number){
	if($result === false){
		return false;
	}
	return $result->data_seek($row_number);
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_errno($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->errno;
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_error($link_identifier = null){
	if (is_object($link_identifier)) {
		return $link_identifier->error;
	}
	return pmb_mysqli::get_connection($link_identifier)->error;
}

/**
 *
 * @param string $unescaped_string
 */
function pmb_mysql_escape_string($unescaped_string){
	return pmb_mysqli::get_connection()->escape_string($unescaped_string);
}

/**
 *
 * @param mysqli_result $result
 * @param string $result_type
 */
function pmb_mysql_fetch_all($result, $result_type = PMB_MYSQL_NUM) {
	if($result === false){
		return false;
	}
	return $result->fetch_all($result_type);
}
/**
 *
 * @param mysqli_result $result
 * @param string $result_type
 */
function pmb_mysql_fetch_array($result, $result_type = PMB_MYSQL_BOTH){
	if($result === false){
		return false;
	}
	return $result->fetch_array($result_type);
}

/**
 *
 * @param mysqli_result $result
 */
function pmb_mysql_fetch_assoc($result){
	if($result === false){
		return false;
	}
	return $result->fetch_assoc();
}

/**
 *
 * @param mysqli_result $result
 * @param number $field_offset
 * @return integer
 */
function pmb_mysql_fetch_field($result, $field_offset = 0){
	if($result === false){
		return false;
	}
	if ($field_offset !== null) {
		$res = $result->field_seek($field_offset);
		if (!$res) {
			return $res;
		}
	}
	return $result->fetch_field();
}

/**
 *
 * @param mysqli_result $result
 * @param string $class_name
 * @param array $params
 */
function pmb_mysql_fetch_object($result, $class_name = "", $params = array()){
	if($result === false){
		return false;
	}
	if (!$class_name) {
		return $result->fetch_object();
	} elseif (!count($params)) {
		return $result->fetch_object($class_name);
	} else {
		return $result->fetch_object($class_name, $params);
	}
}

/**
 *
 * @param mysqli_result $result
 */
function pmb_mysql_fetch_row($result){
	if($result === false){
		return false;
	}
	return $result->fetch_row();
}

/**
 *
 * @param mysqli_result $result
 * @param number $field_offset
 * @return string
 */
function pmb_mysql_field_flags($result, $field_offset){
	if($result === false){
		return false;
	}
	$flags_num = $result->fetch_field_direct($field_offset)->flags;

	$res = "";
	foreach (pmb_mysqli::get_mysqli_flags() as $n => $t) {
		if ($flags_num & $n) {
			$res .= ' '.$t;
		}
	}

	if (empty($res)) {
		return $res;
	} else {
		return substr($res,1);
	}
}

/**
 *
 * @param mysqli_result $result
 * @param number $field_offset
 * @return NULL
 */
function pmb_mysql_field_len($result, $field_offset){
	if($result === false){
		return false;
	}
    $properties = $result->fetch_field_direct($field_offset);
    return is_object($properties) ? $properties->length : null;
}

/**
 *
 * @param mysqli_result $result
 * @param number $field_offset
 * @return NULL
 */
function pmb_mysql_field_name($result, $field_offset){
	if($result === false){
		return false;
	}
    $properties = $result->fetch_field_direct($field_offset);
    return is_object($properties) ? $properties->name : null;
}

/**
 *
 * @param mysqli_result $result
 * @param number $field_offset
 * @return NULL
 */
function pmb_mysql_field_table($result, $field_offset){
	if($result === false){
		return false;
	}
    $properties = $result->fetch_field_direct($field_offset);
    return is_object($properties) ? $properties->table : null;
}

/**
 *
 * @param mysqli_result $result
 * @param number $field_offset
 * @return NULL
 */
function pmb_mysql_field_type($result, $field_offset){
	if($result === false){
		return false;
	}
    $type_id = $result->fetch_field_direct($field_offset)->type;
    return array_key_exists($type_id, pmb_mysqli::get_mysqli_types())? pmb_mysqli::get_mysqli_types()[$type_id] : NULL;
}

/**
 *
 * @param mysqli_result $result
 */
function pmb_mysql_free_result(&$result){
	if ($result !== false && !empty($result->num_rows)) {
		$result->free_result();
		$result = null;
		return true;
	}
	return false;
}

/**
 *
 * @return string
 */
function pmb_mysql_get_client_info(){
	return pmb_mysqli::get_connection()->get_client_info();
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_get_host_info($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->host_info;
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_get_proto_info($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->protocol_version;
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_get_server_info($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->server_info;
}

/**
 *
 * @param resource $link_identifier
 */
function pmb_mysql_insert_id($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->insert_id;
}

/**
 *
 * @param string $database
 * @param resource $link_identifier
 * @return mixed
 */
function pmb_mysql_list_tables($database, $link_identifier = null){
	if(strpos($database, '-') !== false) {
		$res = pmb_mysql_query("SHOW TABLES FROM `".$database."`", $link_identifier);
	} else {
		$res = pmb_mysql_query("SHOW TABLES FROM ".$database, $link_identifier);
	}
	return $res;
}

/**
 *
 * @param mysqli_result $result
 */
function pmb_mysql_num_fields($result){
	if($result === false){
		return false;
	}
	return $result->field_count;
}

/**
 *
 * @param mysqli_result $result
 */
function pmb_mysql_num_rows($result){
	if($result === false){
		return false;
	}
	return $result->num_rows;
}

/**
 *
 * @param string $query
 * @param resource $link_identifier
 * @param string $resultmode
 * @return mixed
 */
function pmb_mysql_query($query, $link_identifier = null, $resultmode = null)
{
    if (empty($query)) {
        return false;
    }
    if (! isset($resultmode) || $resultmode === null) {
        // $uniqId = MySQLi_log::prepare_time($query);
        $result = pmb_mysqli::get_connection($link_identifier)->query($query);
        // MySQLi_log::register($uniqId);
        if (! $result) {
            print pmb_mysql_debug(debug_backtrace()[0]);
            if (strlen($query) > 1024) {
                $label = substr($query, 0, 1024) . "[...]";
            } else {
                $label = $query;
            }
            MySQLi_log::register(MySQLi_log::prepare_error($label), MySQLi_log::get_error());
        }
        return $result;
    } else {
        return pmb_mysqli::get_connection($link_identifier)->query($query, $resultmode);
    }
}

/**
 *
 * @param string $unescaped_string
 * @param resource $link_identifier
 * @return string
 */
function pmb_mysql_real_escape_string($unescaped_string, $link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->real_escape_string($unescaped_string);
}

/**
 *
 * @param mysqli_result $result
 * @param number $row
 * @param number $field
 * @return string
 */
function pmb_mysql_result($result, $row, $field = 0){
	if($result === false){
		return false;
	}
	if($result->num_rows==0) {
		return null;
	}
	$result->data_seek($row);
	$res = $result->fetch_array(PMB_MYSQL_BOTH);
	return $res[$field];
}

/**
 *
 * @param string $database_name
 * @param resource $link_identifier
 * @return boolean
 */
function pmb_mysql_select_db($database_name, $link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->select_db($database_name);
}

/**
 *
 * @param resource $link_identifier
 * @return string
 */
function pmb_mysql_stat($link_identifier = null){
	return pmb_mysqli::get_connection($link_identifier)->stat();
}

/**
 *
 * @param mysqli_result $result
 * @param int $i
 * @return string
 */
function pmb_mysql_tablename($result, $i){
	if($result === false){
		return false;
	}
	$res = pmb_mysql_result($result, $i);
	return $res;
}

function pmb_mysql_debug($backtrace) {
	global $msg;
	global $pmb_display_errors;

	$res = "";
	if($pmb_display_errors) {
		$res = "
		<div class='erreur'>".$msg[540]."</div>
		<div class='row pmb_mysql_debug'>
			<div class='colonne10'>
				<img src='".get_url_icon('error.gif')."'>
			</div>
			<div class='pmb_mysql_debug_content'>
				<strong>".$backtrace['file'].":".$backtrace['line']."</strong>
				<p>".$backtrace['args'][0]."</p>
                ".($pmb_display_errors == 2 ? "<p>".pmb_mysql_error()."</p>" : "")."
			</div>
		</div>";
	}
	return $res;
}

/**
 *
 * @param resource $link_identifier
 * @return boolean
 */
function pmb_mysql_ping($link_identifier = null){
    global $dbh;

    if($link_identifier == null){
        $link_identifier = $dbh;
    }
    if (pmb_mysql_insert_id($link_identifier)) {
        return true;
    }
    return pmb_mysqli::get_connection($link_identifier)->ping();
}